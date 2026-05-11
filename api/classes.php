<?php
// ============================================================
//  api/classes.php  —  CRUD lớp học
//  GET    /api/classes.php            → danh sách lớp
//  GET    /api/classes.php?id=1       → chi tiết 1 lớp
//  POST   /api/classes.php            → thêm lớp (admin)
//  PUT    /api/classes.php            → sửa lớp (admin/teacher)
//  DELETE /api/classes.php?id=1       → xóa lớp (admin)
// ============================================================

session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

function jsonOut(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        jsonOut(['error' => 'Chưa đăng nhập'], 401);
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        jsonOut(['error' => 'Không có quyền truy cập'], 403);
    }
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: lấy danh sách / chi tiết ───────────────────────────
if ($method === 'GET') {
    // Chi tiết 1 lớp
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare(
            "SELECT c.*, u.name AS teacher_name
             FROM classes c
             LEFT JOIN users u ON c.teacher_id = u.id
             WHERE c.id = ?"
        );
        $stmt->execute([(int)$_GET['id']]);
        $cls = $stmt->fetch();
        if (!$cls) jsonOut(['error' => 'Không tìm thấy lớp'], 404);
        jsonOut(['success' => true, 'class' => $cls]);
    }

    // Danh sách — có filter môn / cấp độ / teacher
    $conditions = [];
    $params     = [];

    if (!empty($_GET['subject'])) {
        $conditions[] = "c.subject = ?";
        $params[]     = $_GET['subject'];
    }
    if (!empty($_GET['level'])) {
        $conditions[] = "c.level = ?";
        $params[]     = $_GET['level'];
    }
    if (!empty($_GET['teacher_id'])) {
        $conditions[] = "c.teacher_id = ?";
        $params[]     = (int)$_GET['teacher_id'];
    }

    $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";
    $sql   = "SELECT c.*, u.name AS teacher_name
              FROM classes c
              LEFT JOIN users u ON c.teacher_id = u.id
              $where
              ORDER BY c.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    jsonOut(['success' => true, 'classes' => $stmt->fetchAll()]);
}

// ── POST: thêm lớp (admin only) ─────────────────────────────
if ($method === 'POST') {
    requireAdmin();
    $d = json_decode(file_get_contents('php://input'), true) ?? [];

    $required = ['name', 'subject', 'level'];
    foreach ($required as $f) {
        if (empty($d[$f])) jsonOut(['error' => "Thiếu trường: $f"], 400);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO classes (name, subject, teacher_id, schedule, total_slots, level, location, description)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $d['name'],
        $d['subject'],
        $d['teacher_id'] ?? null,
        $d['schedule']   ?? null,
        $d['total_slots'] ?? 15,
        $d['level'],
        $d['location']   ?? 'Online',
        $d['description'] ?? null,
    ]);
    jsonOut(['success' => true, 'id' => (int)$pdo->lastInsertId()], 201);
}

// ── PUT: sửa lớp ────────────────────────────────────────────
if ($method === 'PUT') {
    requireLogin();
    $d  = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($d['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);

    // Giáo viên chỉ được sửa lớp của mình
    if ($_SESSION['role'] === 'teacher') {
        $check = $pdo->prepare("SELECT teacher_id FROM classes WHERE id = ?");
        $check->execute([$id]);
        $row = $check->fetch();
        if (!$row || (int)$row['teacher_id'] !== (int)$_SESSION['user_id']) {
            jsonOut(['error' => 'Không có quyền chỉnh sửa lớp này'], 403);
        }
    } elseif ($_SESSION['role'] !== 'admin') {
        jsonOut(['error' => 'Không có quyền'], 403);
    }

    $stmt = $pdo->prepare(
        "UPDATE classes
         SET name=?, subject=?, teacher_id=?, schedule=?, total_slots=?, level=?, location=?, description=?
         WHERE id=?"
    );
    $stmt->execute([
        $d['name']        ?? '',
        $d['subject']     ?? '',
        $d['teacher_id']  ?? null,
        $d['schedule']    ?? null,
        $d['total_slots'] ?? 15,
        $d['level']       ?? 'THPT',
        $d['location']    ?? 'Online',
        $d['description'] ?? null,
        $id,
    ]);
    jsonOut(['success' => true]);
}

// ── DELETE: xóa lớp (admin only) ────────────────────────────
if ($method === 'DELETE') {
    requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);

    $pdo->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'Method không hợp lệ'], 405);
