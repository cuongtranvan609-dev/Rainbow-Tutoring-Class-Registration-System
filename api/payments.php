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
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT p.*, s.name as student_name, c.name as class_name
                FROM payments p
                JOIN students s ON p.student_id = s.id
                JOIN classes c ON p.class_id = c.id";
                
        $params = [];
        
        if ($role === 'teacher') {
            $sql .= " JOIN teachers t ON c.teacher_id = t.id WHERE t.account_id = ?";
            $params[] = $account_id;
        } elseif ($role === 'parent') {
            $sql .= " JOIN parents p2 ON s.parent_id = p2.id WHERE p2.account_id = ?";
            $params[] = $account_id;
        } elseif ($role === 'student') {
            $sql .= " WHERE s.account_id = ?";
            $params[] = $account_id;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Map to format UI expects
        $formatted = array_map(function($h) {
            return [
                'id' => $h['id'],
                'studentId' => $h['student_id'],
                'studentName' => $h['student_name'],
                'amount' => $h['amount'],
                'method' => $h['payment_method'],
                'date' => $h['payment_date'],
                'status' => $h['status'],
                'sessions' => 0, // Mocked for now
                'note' => $h['note']
            ];
        }, $history);
        
        echo json_encode(['success' => true, 'data' => $formatted]);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action']) && $data['action'] === 'verify' && $role === 'admin') {
            $stmt = $pdo->prepare("UPDATE payments SET status = 'verified' WHERE id = ?");
            $stmt->execute([$data['id']]);
            echo json_encode(['success' => true, 'message' => 'Đã duyệt thanh toán!']);
            exit;
        }
        
        $student_id = $data['student_id'] ?? null;
        $class_id = $data['class_id'] ?? null;
        $amount = $data['amount'] ?? 0;
        $method = $data['method'] ?? 'Chuyển khoản';
        $date = $data['date'] ?? date('Y-m-d');
        $note = $data['note'] ?? '';
        
        // Admins default to verified, others to pending
        $status = ($role === 'admin') ? 'verified' : 'pending';
        
        $stmt = $pdo->prepare("INSERT INTO payments (student_id, class_id, amount, payment_method, note, status, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $class_id, $amount, $method, $note, $status, $date]);
        
        echo json_encode(['success' => true, 'message' => 'Đã ghi nhận thanh toán!']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
?>
