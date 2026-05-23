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

    if ($role)   { $where[] = "u.role = ?";   $params[] = $role; }
    if ($status) { $where[] = "u.status = ?"; $params[] = $status; }

    $sql  = "SELECT u.id, u.email, u.role, u.status, u.created_at,
                    COALESCE(a.name, t.name, s.name, p.name) AS name,
                    COALESCE(a.phone, t.phone, s.phone, p.phone) AS phone,
                    t.subject
             FROM user_accounts u
             LEFT JOIN admins a ON u.id = a.account_id AND u.role = 'admin'
             LEFT JOIN teachers t ON u.id = t.account_id AND u.role = 'teacher'
             LEFT JOIN students s ON u.id = s.account_id AND u.role = 'student'
             LEFT JOIN parents p ON u.id = p.account_id AND u.role = 'parent'";
             
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " ORDER BY u.created_at DESC";

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
    $role  = in_array($d['role'] ?? '', ['admin','teacher','student','parent']) ? $d['role'] : 'student';

    if (!$name || !$email) jsonOut(['error' => 'Thiếu tên hoặc email'], 400);

    $hash = $pwd;
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare(
            "INSERT INTO user_accounts (email, password, role, status)
             VALUES (?, ?, ?, 'active')"
        );
        $stmt->execute([$email, $hash, $role]);
        $accountId = $pdo->lastInsertId();

        if ($role === 'admin') {
            $pdo->prepare("INSERT INTO admins (account_id, name, phone) VALUES (?, ?, ?)")
                ->execute([$accountId, $name, $d['phone'] ?? null]);
        } elseif ($role === 'teacher') {
            $pdo->prepare("INSERT INTO teachers (account_id, name, phone, subject) VALUES (?, ?, ?, ?)")
                ->execute([$accountId, $name, $d['phone'] ?? null, $d['subject'] ?? null]);
        } elseif ($role === 'student') {
            $pdo->prepare("INSERT INTO students (account_id, name, phone) VALUES (?, ?, ?)")
                ->execute([$accountId, $name, $d['phone'] ?? null]);
        } elseif ($role === 'parent') {
            $pdo->prepare("INSERT INTO parents (account_id, name, phone) VALUES (?, ?, ?)")
                ->execute([$accountId, $name, $d['phone'] ?? null]);
        }

        $pdo->commit();
        jsonOut(['success' => true, 'id' => (int)$accountId], 201);
    } catch (PDOException $e) {
        $pdo->rollBack();
        if (str_contains($e->getMessage(), 'Duplicate')) {
            jsonOut(['error' => 'Email đã tồn tại'], 409);
        }
        jsonOut(['error' => 'Lỗi server'], 500);
    }
}

// ── PUT: sửa user ──────────────────────────────────────────
if ($method === 'PUT') {
    requireAdmin();
    $d  = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($d['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);

    $accFields = [];
    $accParams = [];
    $profileFields = [];
    $profileParams = [];

    // Lấy thông tin user hiện tại để biết role
    $stmt = $pdo->prepare("SELECT role FROM user_accounts WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) jsonOut(['error' => 'User không tồn tại'], 404);
    $currentRole = $user['role'];
    $newRole = $d['role'] ?? $currentRole;

    if (isset($d['email'])) { $accFields[] = "email = ?"; $accParams[] = $d['email']; }
    if (isset($d['status'])) { $accFields[] = "status = ?"; $accParams[] = $d['status']; }
    if (isset($d['role'])) { $accFields[] = "role = ?"; $accParams[] = $d['role']; }
    if (!empty($d['password'])) {
        $accFields[] = "password = ?";
        $accParams[] = $d['password'];
    }

    if (isset($d['name'])) { $profileFields[] = "name = ?"; $profileParams[] = $d['name']; }
    if (isset($d['phone'])) { $profileFields[] = "phone = ?"; $profileParams[] = $d['phone']; }
    if (isset($d['subject']) && $newRole === 'teacher') { $profileFields[] = "subject = ?"; $profileParams[] = $d['subject']; }

    try {
        $pdo->beginTransaction();
        
        // Update user_accounts
        if ($accFields) {
            $accParams[] = $id;
            $pdo->prepare("UPDATE user_accounts SET " . implode(', ', $accFields) . " WHERE id = ?")->execute($accParams);
        }

        // If role changed, we need to delete from old table and insert to new table
        if ($newRole !== $currentRole) {
            $oldTable = $currentRole . 's';
            $newTable = $newRole . 's';
            
            // Get old profile data
            $stmt = $pdo->prepare("SELECT * FROM $oldTable WHERE account_id = ?");
            $stmt->execute([$id]);
            $oldProfile = $stmt->fetch();
            
            // Delete from old
            $pdo->prepare("DELETE FROM $oldTable WHERE account_id = ?")->execute([$id]);
            
            // Insert to new
            $name = $d['name'] ?? $oldProfile['name'] ?? 'Unknown';
            $phone = $d['phone'] ?? $oldProfile['phone'] ?? null;
            $subject = $d['subject'] ?? null;
            
            if ($newRole === 'admin' || $newRole === 'student' || $newRole === 'parent') {
                $pdo->prepare("INSERT INTO $newTable (account_id, name, phone) VALUES (?, ?, ?)")
                    ->execute([$id, $name, $phone]);
            } else if ($newRole === 'teacher') {
                $pdo->prepare("INSERT INTO $newTable (account_id, name, phone, subject) VALUES (?, ?, ?, ?)")
                    ->execute([$id, $name, $phone, $subject]);
            }
        } 
        // Normal profile update without role change
        else if ($profileFields) {
            $table = $currentRole . 's';
            $profileParams[] = $id;
            $pdo->prepare("UPDATE $table SET " . implode(', ', $profileFields) . " WHERE account_id = ?")->execute($profileParams);
        }

        $pdo->commit();
        jsonOut(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonOut(['error' => 'Lỗi cập nhật'], 500);
    }
}

// ── DELETE: xóa user ────────────────────────────────────────
if ($method === 'DELETE') {
    requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonOut(['error' => 'Thiếu id'], 400);
    if ($id === (int)$_SESSION['user_id']) jsonOut(['error' => 'Không thể xóa chính mình'], 400);

    // Xóa trong user_accounts sẽ tự động cascade xóa bảng phụ tương ứng
    $pdo->prepare("DELETE FROM user_accounts WHERE id = ?")->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'Method không hợp lệ'], 405);
