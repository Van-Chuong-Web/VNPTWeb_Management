<?php
/**
 * backend/api/my_orders.php — API Truy xuất danh sách & chi tiết đơn hàng của người dùng (CSDL MySQL)
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $email = strtolower(trim($_GET['email'] ?? $_POST['email'] ?? $_SESSION['user']['email'] ?? ''));
    if (empty($email)) {
        $email = 'lannguyen@gmail.com';
    }

    $vnvdEmail = str_replace('@vnpt.vn', '@vnvd.vn', $email);

    // Tìm tất cả khach_hang_id có thể có liên quan đến email
    $stmtKh = $pdo->prepare("
        SELECT kh.id AS kh_id
        FROM tai_khoan tk
        LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
        WHERE LOWER(tk.email) = :email OR LOWER(tk.email) = :vnvd_email
    ");
    $stmtKh->execute([':email' => $email, ':vnvd_email' => $vnvdEmail]);
    $khRows = $stmtKh->fetchAll();
    
    $khIds = [];
    foreach ($khRows as $r) {
        if (!empty($r['kh_id'])) $khIds[] = intval($r['kh_id']);
    }

    if (empty($khIds)) {
        // Fallback default customer ID
        $khIds = [101];
    }

    $action = $_GET['action'] ?? 'list';

    // Chi tiết đơn hàng
    if ($action === 'detail') {
        $code = trim($_GET['code'] ?? '');
        $stmtOrder = $pdo->prepare("SELECT * FROM don_hang WHERE ma_don_hang = :code LIMIT 1");
        $stmtOrder->execute([':code' => $code]);
        $order = $stmtOrder->fetch();

        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmtItems = $pdo->prepare("
            SELECT ctdh.*, COALESCE(sp.ten_san_pham, ctdh.ten_san_pham_snapshot, 'Dịch vụ VNPT') AS ten
            FROM don_hang_chi_tiet ctdh
            LEFT JOIN san_pham sp ON sp.id = ctdh.san_pham_id
            WHERE ctdh.don_hang_id = :dhId
        ");
        $stmtItems->execute([':dhId' => $order['id']]);
        $items = $stmtItems->fetchAll() ?: [];

        echo json_encode([
            'status' => 'success',
            'order'  => $order,
            'items'  => $items
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Danh sách đơn hàng
    $inClause = implode(',', array_fill(0, count($khIds), '?'));
    $stmtOrders = $pdo->prepare("SELECT * FROM don_hang WHERE khach_hang_id IN ($inClause) ORDER BY id DESC LIMIT 50");
    $stmtOrders->execute($khIds);
    $orders = $stmtOrders->fetchAll() ?: [];

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
