<?php
// ============================================================
//  api/applications.php  —  CRUD hồ sơ ứng tuyển giáo viên
// ============================================================

session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

function jsonOut(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: danh sách hồ sơ (admin) hoặc theo email ──────────
if ($method === 'GET') {
    if (empty($_SESSION['user_id'])) jsonOut(['error' => 'Chưa đăng nhập'], 401);

    if ($_SESSION['role'] === 'admin') {
        $status = $_GET['status'] ?? '';
        if ($status) {
            $stmt = $pdo->prepare("SELECT * FROM applications WHERE status = ? ORDER BY created_at DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("SELECT * FROM applications ORDER BY created_at DESC");
        }
        jsonOut(['success' => true, 'applications' => $stmt->fetchAll()]);
    }

    // Học sinh/giáo viên xem hồ sơ của mình qua email
    $stmt = $pdo->prepare("SELECT id,name,subject,status,created_at FROM applications WHERE email = ?");
    $stmt->execute([$_GET['email'] ?? '']);
    jsonOut(['success' => true, 'applications' => $stmt->fetchAll()]);
}

// ── POST: nộp hồ sơ (public, không cần login) ─────────────
if ($method === 'POST') {
    // Xử lý multipart form (có upload CV)
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $subject  = trim($_POST['subject']  ?? '');
    $education= trim($_POST['education']?? '');
    $bio      = trim($_POST['bio']      ?? '');
    $int_date = $_POST['interview_date'] ?? null;
    $int_time = $_POST['interview_time'] ?? null;
    $int_mode = $_POST['interview_mode'] ?? 'Online (Google Meet)';

    if (!$name || !$email || !$subject) {
        jsonOut(['error' => 'Vui lòng điền đầy đủ: tên, email, môn học'], 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonOut(['error' => 'Email không hợp lệ'], 400);
    }

    // Upload CV
    $cvFilename = null;
    if (!empty($_FILES['cv']['tmp_name'])) {
        $allowedExt = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            jsonOut(['error' => 'CV chỉ chấp nhận PDF, DOC, DOCX'], 400);
        }
        if ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
            jsonOut(['error' => 'CV không được vượt quá 5MB'], 400);
        }
        $cvFilename = uniqid('cv_') . '.' . $ext;
        $dest = __DIR__ . '/../uploads/' . $cvFilename;
        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $dest)) {
            jsonOut(['error' => 'Lỗi khi lưu CV'], 500);
        }
    }

    $stmt = $pdo->prepare(
        "INSERT INTO applications
         (name, email, phone, subject, education, bio, cv_file, interview_date, interview_time, interview_mode)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $name, $email, $phone, $subject, $education, $bio,
        $cvFilename, $int_date ?: null, $int_time ?: null, $int_mode
    ]);

    jsonOut(['success' => true, 'message' => 'Hồ sơ đã được gửi thành công!'], 201);
}

// ── PUT: cập nhật trạng thái (admin only) ─────────────────
if ($method === 'PUT') {
    if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        jsonOut(['error' => 'Không có quyền'], 403);
    }
    $d      = json_decode(file_get_contents('php://input'), true) ?? [];
    $id     = (int)($d['id'] ?? 0);
    $status = $d['status'] ?? '';

    $valid = ['pending', 'interview', 'approved', 'rejected'];
    if (!$id || !in_array($status, $valid)) {
        jsonOut(['error' => 'Dữ liệu không hợp lệ'], 400);
    }

    $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?")->execute([$status, $id]);

    // Nếu approved → tạo tài khoản teacher tự động
    if ($status === 'approved') {
        $app = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
        $app->execute([$id]);
        $app = $app->fetch();

        $exists = $pdo->prepare("SELECT id FROM user_accounts WHERE email = ?");
        $exists->execute([$app['email']]);
        if (!$exists->fetch()) {
            $tmpPwd = '123456'; // Default plain password
            try {
                $pdo->beginTransaction();
                $pdo->prepare(
                    "INSERT INTO user_accounts (email, password, role, status) VALUES (?, ?, 'teacher', 'active')"
                )->execute([$app['email'], $tmpPwd]);
                
                $accountId = $pdo->lastInsertId();
                
                $pdo->prepare("INSERT INTO teachers (account_id, name, phone, subject) VALUES (?, ?, ?, ?)")
                    ->execute([$accountId, $app['name'], $app['phone'], $app['subject']]);
                
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
            }
        }
    }

    jsonOut(['success' => true]);
}

// ── DELETE: xóa hồ sơ (admin only) ────────────────────────
if ($method === 'DELETE') {
    if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        jsonOut(['error' => 'Không có quyền'], 403);
    }
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);

    $pdo->prepare("DELETE FROM applications WHERE id = ?")->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'Method không hợp lệ'], 405);
