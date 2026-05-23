<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../config/db.php';

try {
    
    $account_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($role !== 'admin' && $role !== 'teacher') {
            echo json_encode(['success' => false, 'message' => 'Chỉ giáo viên và admin mới có quyền điểm danh.']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $student_id = $data['student_id'] ?? null;
        $class_id = $data['class_id'] ?? null;
        $new_sessions = (int)($data['sessions'] ?? 0);
        
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("DELETE FROM attendance WHERE student_id = ? AND class_id = ?");
        $stmt->execute([$student_id, $class_id]);
        
        if ($new_sessions > 0) {
            $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, session_date, status) VALUES (?, ?, ?, 'present')");
            for ($i = 0; $i < $new_sessions; $i++) {
                $date = date('Y-m-d', strtotime("2025-05-01 + $i days"));
                $stmt->execute([$student_id, $class_id, $date]);
            }
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật số buổi học!']);
        exit;
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
?>
