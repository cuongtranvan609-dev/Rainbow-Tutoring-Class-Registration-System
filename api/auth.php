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

// ── Đăng nhập ────────────────────────────────────────────────
if ($action === 'login') {
    $email = trim($input['email'] ?? '');
    $pwd   = $input['password'] ?? '';

    if (!$email || !$pwd) {
        jsonOut(['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($pwd, $user['password'])) {
        jsonOut(['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'], 401);
    }

    if ($user['status'] === 'pending') {
        jsonOut(['success' => false, 'message' => 'Tài khoản chưa được kích hoạt'], 403);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['name']    = $user['name'];

    unset($user['password']);
    jsonOut(['success' => true, 'user' => $user]);
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
    $pwd     = $input['password'] ?? '';
    $role    = in_array($input['role'] ?? '', ['student', 'teacher']) ? $input['role'] : 'student';
    $subject = trim($input['subject'] ?? '');

    if (!$name || !$email || !$pwd) {
        jsonOut(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin'], 400);
    }

    if (strlen($pwd) < 6) {
        jsonOut(['success' => false, 'message' => 'Mật khẩu phải ít nhất 6 ký tự'], 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonOut(['success' => false, 'message' => 'Email không hợp lệ'], 400);
    }

    $hash = password_hash($pwd, PASSWORD_BCRYPT);
    // Teacher cần admin duyệt → pending; student active ngay
    $status = $role === 'teacher' ? 'pending' : 'active';

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, role, status, subject)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $email, $hash, $role, $status, $subject]);
        jsonOut(['success' => true, 'message' => 'Đăng ký thành công!', 'status' => $status]);
    } catch (PDOException $e) {
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
    $stmt = $pdo->prepare("SELECT id,name,email,role,status,phone,address,subject FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    jsonOut(['success' => true, 'user' => $user]);
}

// ── Cập nhật hồ sơ ───────────────────────────────────────────
if ($action === 'update_profile') {
    if (empty($_SESSION['user_id'])) {
        jsonOut(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
    }
    $name    = trim($input['name'] ?? '');
    $phone   = trim($input['phone'] ?? '');
    $address = trim($input['address'] ?? '');

    $stmt = $pdo->prepare(
        "UPDATE users SET name=?, phone=?, address=? WHERE id=?"
    );
    $stmt->execute([$name, $phone, $address, $_SESSION['user_id']]);
    jsonOut(['success' => true, 'message' => 'Đã lưu thay đổi']);
}

jsonOut(['error' => 'Action không hợp lệ'], 400);
