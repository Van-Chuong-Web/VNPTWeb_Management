<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();

    $khachHangId = $_SESSION['user']['khach_hang_id'] ?? null;
    $taiKhoanId  = $_SESSION['user']['id'] ?? null;

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

    $action = $_GET['action'] ?? 'list';

    if (!$khachHangId) {
        echo json_encode(['status' => 'success', 'orders' => []], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'detail') {
        $code = trim($_GET['code'] ?? '');
        $order = $db->select("SELECT * FROM don_hang WHERE ma_don_hang = ? LIMIT 1", [$code]);
        if (empty($order)) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng'], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $items = $db->select("
            SELECT ctdh.*, COALESCE(sp.ten_san_pham, ctdh.ten_san_pham_snapshot, 'Dịch vụ VNPT') AS ten 
              FROM don_hang_chi_tiet ctdh
         LEFT JOIN san_pham sp ON sp.id = ctdh.san_pham_id
             WHERE ctdh.don_hang_id = ?
        ", [$order[0]['id']]);

        echo json_encode([
            'status' => 'success',
            'order'  => $order[0],
            'items'  => $items
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $orders = $db->select("SELECT * FROM don_hang WHERE khach_hang_id = ? ORDER BY id DESC LIMIT 50", [$khachHangId]);

    echo json_encode([
        'status' => 'success',
        'orders' => $orders
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
