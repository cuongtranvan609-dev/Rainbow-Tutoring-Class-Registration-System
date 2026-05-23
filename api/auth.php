<?php
// ============================================================
//  api/auth.php  —  Xác thực: login / logout / register
// ============================================================

session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_POST['action'] ?? '';

// ── Helper ───────────────────────────────────────────────────
function jsonOut(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy thông tin phụ thuộc vào role
function getProfileData($pdo, $accountId, $role) {
    $table = $role . 's'; // admins, teachers, students, parents
    if (!in_array($role, ['admin', 'teacher', 'student', 'parent'])) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE account_id = ?");
    $stmt->execute([$accountId]);
    return $stmt->fetch();
}

// ── Đăng nhập ────────────────────────────────────────────────
if ($action === 'login') {
    $email = trim($input['email'] ?? '');
    $pwd   = $input['password'] ?? '';

    if (!$email || !$pwd) {
        jsonOut(['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $account = $stmt->fetch();

    if (!$account || $pwd !== $account['password']) {
        jsonOut(['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'], 401);
    }

    if ($account['status'] === 'pending') {
        jsonOut(['success' => false, 'message' => 'Tài khoản chưa được kích hoạt'], 403);
    }

    $profile = getProfileData($pdo, $account['id'], $account['role']);
    if (!$profile) {
        jsonOut(['success' => false, 'message' => 'Hồ sơ người dùng không hợp lệ'], 500);
    }

    $_SESSION['user_id'] = $account['id']; // account_id
    $_SESSION['role']    = $account['role'];
    $_SESSION['name']    = $profile['name'];

    unset($account['password']);
    
    // Merge account and profile data for frontend
    $userResponse = array_merge($account, $profile);
    
    jsonOut(['success' => true, 'user' => $userResponse]);
}

// ── Đăng xuất ────────────────────────────────────────────────
if ($action === 'logout') {
    session_destroy();
    jsonOut(['success' => true]);
}

// ── Đăng ký ──────────────────────────────────────────────────
if ($action === 'register') {
    $name    = trim($input['name'] ?? '');
    $email   = trim($input['email'] ?? '');
    $phone   = trim($input['phone'] ?? '');
    $pwd     = $input['password'] ?? '';
    $role    = in_array($input['role'] ?? '', ['student', 'teacher', 'parent']) ? $input['role'] : 'student';
    $subject = trim($input['subject'] ?? '');

    if (!$name || !$email || !$pwd || !$phone) {
        jsonOut(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin'], 400);
    }

    if (strlen($pwd) < 6) {
        jsonOut(['success' => false, 'message' => 'Mật khẩu phải ít nhất 6 ký tự'], 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonOut(['success' => false, 'message' => 'Email không hợp lệ'], 400);
    }

    $hash = $pwd; // Bỏ mã hóa mật khẩu theo yêu cầu
    $status = $role === 'teacher' ? 'pending' : 'active';

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "INSERT INTO user_accounts (email, password, role, status) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$email, $hash, $role, $status]);
        $accountId = $pdo->lastInsertId();

        if ($role === 'student') {
            $stmt = $pdo->prepare("SELECT id FROM parents WHERE phone = ? LIMIT 1");
            $stmt->execute([$phone]);
            $parent = $stmt->fetch();
            $parentId = $parent ? $parent['id'] : null;

            $pdo->prepare("INSERT INTO students (account_id, name, phone, parent_id) VALUES (?, ?, ?, ?)")
                ->execute([$accountId, $name, $phone, $parentId]);
        } elseif ($role === 'teacher') {
            $pdo->prepare("INSERT INTO teachers (account_id, name, phone, subject) VALUES (?, ?, ?, ?)")
                ->execute([$accountId, $name, $phone, $subject]);
        } elseif ($role === 'parent') {
            $pdo->prepare("INSERT INTO parents (account_id, name, phone) VALUES (?, ?, ?)")
                ->execute([$accountId, $name, $phone]);
            $parentId = $pdo->lastInsertId();
            
            $pdo->prepare("UPDATE students SET parent_id = ? WHERE phone = ? AND parent_id IS NULL")
                ->execute([$parentId, $phone]);
        }

        $pdo->commit();
        jsonOut(['success' => true, 'message' => 'Đăng ký thành công!', 'status' => $status]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        if (str_contains($e->getMessage(), 'Duplicate')) {
            jsonOut(['success' => false, 'message' => 'Email đã được sử dụng'], 409);
        }
        jsonOut(['success' => false, 'message' => 'Lỗi server'], 500);
    }
}

// ── Lấy thông tin user hiện tại ──────────────────────────────
if ($action === 'me') {
    if (empty($_SESSION['user_id'])) {
        jsonOut(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
    }
    
    $accountId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT id, email, role, status FROM user_accounts WHERE id = ?");
    $stmt->execute([$accountId]);
    $account = $stmt->fetch();
    
    if (!$account) {
        jsonOut(['success' => false, 'message' => 'Tài khoản không tồn tại'], 404);
    }
    
    $profile = getProfileData($pdo, $accountId, $account['role']);
    $user = array_merge($account, $profile ?? []);
    jsonOut(['success' => true, 'user' => $user]);
}

// ── Cập nhật hồ sơ ───────────────────────────────────────────
if ($action === 'update_profile') {
    if (empty($_SESSION['user_id'])) {
        jsonOut(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
    }
    
    $accountId = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    $name    = trim($input['name'] ?? '');
    $phone   = trim($input['phone'] ?? '');
    $address = trim($input['address'] ?? '');

    $table = $role . 's';
    if (!in_array($role, ['admin', 'teacher', 'student', 'parent'])) {
         jsonOut(['success' => false, 'message' => 'Vai trò không hợp lệ'], 400);
    }

    $stmt = $pdo->prepare(
        "UPDATE $table SET name=?, phone=?, address=? WHERE account_id=?"
    );
    $stmt->execute([$name, $phone, $address, $accountId]);
    
    // Cập nhật session name
    $_SESSION['name'] = $name;
    
    jsonOut(['success' => true, 'message' => 'Đã lưu thay đổi']);
}

// ── Đổi mật khẩu ───────────────────────────────────────────
if ($action === 'change_password') {
    if (empty($_SESSION['user_id'])) {
        jsonOut(['success' => false, 'error' => 'Chưa đăng nhập'], 401);
    }
    
    $accountId = $_SESSION['user_id'];
    $pwdCurrent = $input['pwdCurrent'] ?? '';
    $pwdNew = $input['pwdNew'] ?? '';
    
    if (!$pwdCurrent || !$pwdNew) {
        jsonOut(['success' => false, 'error' => 'Vui lòng nhập đầy đủ thông tin'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT password FROM user_accounts WHERE id=?");
    $stmt->execute([$accountId]);
    $user = $stmt->fetch();
    
    if (!$user || $user['password'] !== $pwdCurrent) {
        jsonOut(['success' => false, 'error' => 'Mật khẩu hiện tại không đúng'], 400);
    }
    
    $stmt = $pdo->prepare("UPDATE user_accounts SET password=? WHERE id=?");
    $stmt->execute([$pwdNew, $accountId]);
    
    jsonOut(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
}

jsonOut(['error' => 'Action không hợp lệ'], 400);
