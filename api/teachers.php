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

if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT t.id, t.name, t.subject, t.phone, t.address, u.email 
            FROM teachers t
            JOIN user_accounts u ON t.account_id = u.id
            WHERE u.status = 'active'
        ");
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonOut(['success' => true, 'teachers' => $teachers]);
    } catch (Exception $e) {
        jsonOut(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

jsonOut(['error' => 'Action không hợp lệ'], 400);
