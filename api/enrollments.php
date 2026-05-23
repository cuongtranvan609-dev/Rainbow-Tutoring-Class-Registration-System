<?php
// ============================================================
//  api/enrollments.php  —  Đăng ký / hủy lớp học
// ============================================================

session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

function jsonOut(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_SESSION['user_id'])) {
    jsonOut(['error' => 'Chưa đăng nhập'], 401);
}

$method    = $_SERVER['REQUEST_METHOD'];
$userId    = (int)$_SESSION['user_id']; // This is account_id
$userRole  = $_SESSION['role'];

function getActualStudentId($pdo, $accountId) {
    $stmt = $pdo->prepare("SELECT id FROM students WHERE account_id = ?");
    $stmt->execute([$accountId]);
    return $stmt->fetchColumn() ?: null;
}

// ── GET: lớp đã đăng ký của học sinh ─────────────────────
if ($method === 'GET') {
    $targetAccountId = ($userRole === 'admin' && !empty($_GET['student_id']))
        ? (int)$_GET['student_id']
        : $userId;

    $actual_student_id = getActualStudentId($pdo, $targetAccountId);

    if (!$actual_student_id) {
        jsonOut(['success' => true, 'classes' => []]);
    }

    $stmt = $pdo->prepare(
        "SELECT c.*, t.name AS teacher_name, e.joined_at
         FROM enrollments e
         JOIN classes c ON e.class_id = c.id
         LEFT JOIN teachers t ON c.teacher_id = t.id
         WHERE e.student_id = ?
         ORDER BY e.joined_at DESC"
    );
    $stmt->execute([$actual_student_id]);
    jsonOut(['success' => true, 'classes' => $stmt->fetchAll()]);
}

// ── POST: đăng ký vào lớp ────────────────────────────────
if ($method === 'POST') {
    if ($userRole !== 'student') jsonOut(['error' => 'Chỉ học sinh mới được đăng ký lớp'], 403);

    $d        = json_decode(file_get_contents('php://input'), true) ?? [];
    $classId  = (int)($d['class_id'] ?? 0);
    if (!$classId) jsonOut(['error' => 'Thiếu class_id'], 400);

    $actual_student_id = getActualStudentId($pdo, $userId);
    if (!$actual_student_id) jsonOut(['error' => 'Không tìm thấy hồ sơ học sinh'], 404);

    // Kiểm tra lớp còn chỗ
    $cls = $pdo->prepare("SELECT total_slots, enrolled FROM classes WHERE id = ?");
    $cls->execute([$classId]);
    $cls = $cls->fetch();
    if (!$cls) jsonOut(['error' => 'Lớp không tồn tại'], 404);
    if ($cls['enrolled'] >= $cls['total_slots']) jsonOut(['error' => 'Lớp đã đầy'], 400);

    try {
        $pdo->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (?, ?)")
            ->execute([$actual_student_id, $classId]);
        // Tăng số học sinh enrolled
        $pdo->prepare("UPDATE classes SET enrolled = enrolled + 1 WHERE id = ?")
            ->execute([$classId]);
        jsonOut(['success' => true, 'message' => 'Đăng ký lớp thành công!'], 201);
    } catch (PDOException $e) {
        jsonOut(['error' => 'Bạn đã đăng ký lớp này rồi'], 409);
    }
}

// ── DELETE: hủy đăng ký ───────────────────────────────────
if ($method === 'DELETE') {
    $classId = (int)($_GET['class_id'] ?? 0);
    if (!$classId) jsonOut(['error' => 'Thiếu class_id'], 400);

    $actual_student_id = getActualStudentId($pdo, $userId);
    if (!$actual_student_id) jsonOut(['error' => 'Hồ sơ học sinh không hợp lệ'], 400);

    $stmt = $pdo->prepare(
        "DELETE FROM enrollments WHERE student_id = ? AND class_id = ?"
    );
    $stmt->execute([$actual_student_id, $classId]);

    if ($stmt->rowCount() > 0) {
        $pdo->prepare("UPDATE classes SET enrolled = GREATEST(enrolled - 1, 0) WHERE id = ?")
            ->execute([$classId]);
        jsonOut(['success' => true]);
    } else {
        jsonOut(['error' => 'Không tìm thấy đăng ký'], 404);
    }
}

jsonOut(['error' => 'Method không hợp lệ'], 405);
