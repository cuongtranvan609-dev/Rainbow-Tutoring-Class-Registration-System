<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_GET['action'] ?? '';

function jsonOut(array $data, int $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_SESSION['user_id'])) {
    jsonOut(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
}

$accountId = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT cr.*, 
                   u.email as requester_email,
                   t.name as teacher_name,
                   (SELECT COUNT(*) FROM class_request_votes crv WHERE crv.request_id = cr.id) as vote_count,
                   (SELECT COUNT(*) FROM class_request_votes crv2 WHERE crv2.request_id = cr.id AND crv2.account_id = $accountId) as has_voted
            FROM class_requests cr
            JOIN user_accounts u ON cr.requester_id = u.id
            LEFT JOIN teachers t ON cr.teacher_id = t.id
            ORDER BY vote_count DESC, cr.created_at DESC
        ");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($requests as &$req) {
            $req['requester_name'] = 'Người dùng';
            $reqStmt = $pdo->prepare("SELECT name FROM students WHERE account_id = ? UNION SELECT name FROM parents WHERE account_id = ? UNION SELECT name FROM teachers WHERE account_id = ? LIMIT 1");
            $reqStmt->execute([$req['requester_id'], $req['requester_id'], $req['requester_id']]);
            $r = $reqStmt->fetch();
            if ($r) $req['requester_name'] = $r['name'];
        }
        
        jsonOut(['success' => true, 'requests' => $requests]);
    } catch (Exception $e) {
        jsonOut(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

if ($action === 'create') {
    $subject = $input['subject'] ?? '';
    $level = $input['level'] ?? '';
    $format = $input['format'] ?? '';
    $teacher_id = !empty($input['teacher_id']) ? $input['teacher_id'] : null;
    $notes = $input['notes'] ?? '';

    if (!$subject || !$level || !$format) {
        jsonOut(['success' => false, 'message' => 'Vui lòng điền đủ Môn học, Khối lớp, Hình thức'], 400);
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO class_requests (requester_id, subject, level, format, teacher_id, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$accountId, $subject, $level, $format, $teacher_id, $notes]);
        $requestId = $pdo->lastInsertId();
        
        $pdo->prepare("INSERT INTO class_request_votes (request_id, account_id) VALUES (?, ?)")->execute([$requestId, $accountId]);
        
        $pdo->commit();
        jsonOut(['success' => true, 'message' => 'Gửi yêu cầu thành công!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonOut(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
    }
}

if ($action === 'vote') {
    $requestId = $input['request_id'] ?? 0;
    try {
        $stmt = $pdo->prepare("SELECT * FROM class_request_votes WHERE request_id = ? AND account_id = ?");
        $stmt->execute([$requestId, $accountId]);
        if ($stmt->fetch()) {
            jsonOut(['success' => false, 'message' => 'Bạn đã vote cho yêu cầu này rồi'], 400);
        }
        $pdo->prepare("INSERT INTO class_request_votes (request_id, account_id) VALUES (?, ?)")->execute([$requestId, $accountId]);
        jsonOut(['success' => true, 'message' => 'Vote thành công!']);
    } catch (Exception $e) {
        jsonOut(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
    }
}

if ($role !== 'admin') {
    jsonOut(['error' => 'Action không hợp lệ hoặc không có quyền'], 403);
}

if ($action === 'approve') {
    $requestId = $input['request_id'] ?? 0;
    $teacherId = !empty($input['teacher_id']) ? $input['teacher_id'] : null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM class_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $req = $stmt->fetch();
        
        if (!$req) jsonOut(['success' => false, 'message' => 'Không tìm thấy yêu cầu'], 404);
        
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE class_requests SET status = 'approved' WHERE id = ?")->execute([$requestId]);
        
        $className = "Lớp " . $req['subject'] . " " . $req['level'] . " (Yêu cầu)";
        $pdo->prepare("INSERT INTO classes (name, subject, teacher_id, level, location, description) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$className, $req['subject'], $teacherId, $req['level'], $req['format'], $req['notes']]);
            
        $pdo->commit();
        jsonOut(['success' => true, 'message' => 'Đã duyệt yêu cầu và tạo lớp thành công!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonOut(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
    }
}

if ($action === 'reject') {
    $requestId = $input['request_id'] ?? 0;
    $reply = $input['reply'] ?? '';
    
    try {
        $pdo->prepare("UPDATE class_requests SET status = 'rejected', admin_reply = ? WHERE id = ?")->execute([$reply, $requestId]);
        jsonOut(['success' => true, 'message' => 'Đã từ chối yêu cầu!']);
    } catch (Exception $e) {
        jsonOut(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
    }
}

jsonOut(['error' => 'Action không hợp lệ'], 400);
