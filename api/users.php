<?php
// ============================================================
//  api/users.php  —  Quản lý người dùng (admin)
// ============================================================

session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

function jsonOut(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireAdmin(): void {
    if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        jsonOut(['error' => 'Không có quyền truy cập'], 403);
    }
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: danh sách users ────────────────────────────────────
if ($method === 'GET') {
    requireAdmin();

    $role   = $_GET['role']   ?? '';
    $status = $_GET['status'] ?? '';
    $params = [];
    $where  = [];

    if ($role)   { $where[] = "role = ?";   $params[] = $role; }
    if ($status) { $where[] = "status = ?"; $params[] = $status; }

    $sql  = "SELECT id, name, email, role, status, phone, subject, created_at FROM users";
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    jsonOut(['success' => true, 'users' => $stmt->fetchAll()]);
}

// ── POST: thêm user (admin) ─────────────────────────────────
if ($method === 'POST') {
    requireAdmin();
    $d = json_decode(file_get_contents('php://input'), true) ?? [];

    $name  = trim($d['name']  ?? '');
    $email = trim($d['email'] ?? '');
    $pwd   = $d['password'] ?? 'Rainbow@2026';
    $role  = in_array($d['role'] ?? '', ['admin','teacher','student']) ? $d['role'] : 'student';

    if (!$name || !$email) jsonOut(['error' => 'Thiếu tên hoặc email'], 400);

    $hash = password_hash($pwd, PASSWORD_BCRYPT);
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, role, status, phone, subject)
             VALUES (?, ?, ?, ?, 'active', ?, ?)"
        );
        $stmt->execute([$name, $email, $hash, $role, $d['phone'] ?? null, $d['subject'] ?? null]);
        jsonOut(['success' => true, 'id' => (int)$pdo->lastInsertId()], 201);
    } catch (PDOException $e) {
        jsonOut(['error' => 'Email đã tồn tại'], 409);
    }
}

// ── PUT: sửa user ──────────────────────────────────────────
if ($method === 'PUT') {
    requireAdmin();
    $d  = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($d['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);

    $fields = [];
    $params = [];

    foreach (['name','email','phone','role','status','subject'] as $f) {
        if (isset($d[$f])) { $fields[] = "$f = ?"; $params[] = $d[$f]; }
    }
    if (!empty($d['password'])) {
        $fields[] = "password = ?";
        $params[] = password_hash($d['password'], PASSWORD_BCRYPT);
    }
    if (!$fields) jsonOut(['error' => 'Không có dữ liệu cần cập nhật'], 400);

    $params[] = $id;
    $pdo->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    jsonOut(['success' => true]);
}

// ── DELETE: xóa user ────────────────────────────────────────
if ($method === 'DELETE') {
    requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);
    if ($id === (int)$_SESSION['user_id']) jsonOut(['error' => 'Không thể xóa chính mình'], 400);

    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'Method không hợp lệ'], 405);
