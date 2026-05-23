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
        $sql = "SELECT 
                    s.id as student_id,
                    s.name as student_name,
                    s.phone as student_phone,
                    p.name as parent_name,
                    c.id as class_id,
                    c.name as class_name,
                    c.subject as subject,
                    c.price_per_session as price,
                    c.total_sessions as totalSessions,
                    (SELECT COUNT(*) FROM attendance a WHERE a.student_id = s.id AND a.class_id = c.id AND a.status='present') as sessions,
                    IFNULL((SELECT SUM(amount) FROM payments pay WHERE pay.student_id = s.id AND pay.class_id = c.id AND pay.status IN ('pending', 'verified')), 0) as paid
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                LEFT JOIN parents p ON s.parent_id = p.id
                JOIN classes c ON e.class_id = c.id";
                
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
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $formatted = array_map(function($row) {
            return [
                'id' => $row['student_id'] . '_' . $row['class_id'],
                'student_id' => $row['student_id'],
                'class_id' => $row['class_id'],
                'name' => $row['student_name'],
                'class' => $row['class_name'],
                'subject' => $row['subject'],
                'parent' => $row['parent_name'],
                'phone' => $row['student_phone'],
                'price' => (int)$row['price'],
                'totalSessions' => (int)$row['totalSessions'],
                'sessions' => (int)$row['sessions'],
                'paid' => (int)$row['paid'],
                'month' => date('Y-m'),
                'note' => '',
                'avatar' => 'a' . (($row['student_id'] % 5) + 1)
            ];
        }, $results);
        
        echo json_encode(['success' => true, 'data' => $formatted]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
?>
