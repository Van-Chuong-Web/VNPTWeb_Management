<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();

    $khachHangId = $_SESSION['user']['khach_hang_id'] ?? null;
    $taiKhoanId  = $_SESSION['user']['id'] ?? null;

    if (!$khachHangId && $taiKhoanId) {
        $foundKh = $db->select("SELECT id FROM khach_hang WHERE tai_khoan_id = ?", [$taiKhoanId]);
        if (!empty($foundKh)) {
            $khachHangId = $foundKh[0]['id'];
        }
    }

    $email = trim($_GET['email'] ?? $_POST['email'] ?? '');
    $phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');

    if (!$khachHangId && (!empty($email) || !empty($phone))) {
        $found = $db->select("
            SELECT kh.id 
              FROM khach_hang kh 
         LEFT JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id 
             WHERE (tk.email = ? AND ? != '') OR (kh.so_dien_thoai = ? AND ? != '')
             LIMIT 1
        ", [$email, $email, $phone, $phone]);

        if (!empty($found)) {
            $khachHangId = $found[0]['id'];
        }
    }

    $action = $_GET['action'] ?? 'get';

    if ($action === 'mark_read') {
        $notifId = (int)($_POST['id'] ?? 0);
        if ($notifId > 0) {
            $db->execute("UPDATE thong_bao SET da_doc = 1 WHERE id = ?", [$notifId]);
        }
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'mark_all_read') {
        if ($khachHangId) {
            $db->execute("UPDATE thong_bao SET da_doc = 1 WHERE khach_hang_id = ?", [$khachHangId]);
        }
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (!$khachHangId) {
        echo json_encode(['status' => 'success', 'unread_count' => 0, 'data' => []], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $notifs = $db->select("SELECT id, tieu_de, noi_dung, loai, da_doc, created_at FROM thong_bao WHERE khach_hang_id = ? ORDER BY id DESC LIMIT 50", [$khachHangId]);
    
    $unreadCount = 0;
    foreach ($notifs as $n) {
        if ($n['da_doc'] == 0) $unreadCount++;
    }

    echo json_encode([
        'status' => 'success',
        'unread_count' => $unreadCount,
        'data' => $notifs
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi kết nối hệ thống: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
