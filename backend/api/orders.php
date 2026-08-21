<?php
/**
 * backend/api/orders.php — API Đơn hàng & Thanh toán 100% CSDL MySQL
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $rawInput = json_decode(file_get_contents('php://input'), true);
    $input = !empty($rawInput) ? array_merge($_POST, $rawInput) : $_POST;

    $method = $_SERVER['REQUEST_METHOD'];
    $email  = strtolower(trim($input['email'] ?? $_GET['email'] ?? $_SESSION['user']['email'] ?? ''));

    // 1. Xử lý tạo đơn hàng (POST)
    if ($method === 'POST' || isset($input['items']) || isset($input['cart'])) {
        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'error' => 'Vui lòng đăng nhập để tiến hành thanh toán!']);
            exit;
        }

        // Tìm khach_hang_id tương ứng với email
        $stmtKh = $pdo->prepare("
            SELECT kh.id AS kh_id, tk.id AS tk_id, tk.email,
                   COALESCE(kh.ho_ten, nv.ho_ten, tk.email) AS ho_ten
            FROM tai_khoan tk
            LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
            LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
            WHERE LOWER(tk.email) = :email OR LOWER(tk.email) = :vnvd_email
            LIMIT 1
        ");
        $vnvdEmail = str_replace('@vnpt.vn', '@vnvd.vn', $email);
        $stmtKh->execute([':email' => $email, ':vnvd_email' => $vnvdEmail]);
        $userRow = $stmtKh->fetch();

        if (!$userRow) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'error' => 'Tài khoản không tồn tại trên hệ thống.']);
            exit;
        }

        $khachHangId = $userRow['kh_id'];
        $tkId = $userRow['tk_id'];

        // Tự động khởi tạo dòng khach_hang nếu chưa có
        if (empty($khachHangId)) {
            $insKh = $pdo->prepare("INSERT INTO khach_hang (tai_khoan_id, ho_ten) VALUES (:tkId, :name)");
            $insKh->execute([':tkId' => $tkId, ':name' => $userRow['ho_ten']]);
            $khachHangId = $pdo->lastInsertId();
        }

        $items = $input['items'] ?? $input['cart'] ?? $_SESSION['cart'] ?? [];
        $totalMoney = floatval($input['totalMoney'] ?? $input['tong_tien'] ?? 0);
        $maDonHang = trim($input['ma_don_hang'] ?? $input['orderCode'] ?? '');
        $note = trim($input['note'] ?? $input['ghi_chu'] ?? 'Đơn hàng đăng ký trực tuyến');

        if (empty($maDonHang)) {
            $maDonHang = 'DH' . date('Ymd') . rand(1000, 9999);
        }

        if ($totalMoney <= 0 && !empty($items)) {
            foreach ($items as $it) {
                $totalMoney += floatval($it['price'] ?? 0) * intval($it['qty'] ?? 1);
            }
        }

        // Chèn vào bảng don_hang trong CSDL MySQL
        $insDh = $pdo->prepare("
            INSERT INTO don_hang (ma_don_hang, khach_hang_id, tong_tien_hang, phi_van_chuyen, giam_gia, tong_thanh_toan, trang_thai_don_hang, ghi_chu)
            VALUES (:ma, :khId, :tongTien, 0, 0, :tongTien, 'cho_xac_nhan', :note)
        ");
        $insDh->execute([
            ':ma'       => $maDonHang,
            ':khId'     => $khachHangId,
            ':tongTien' => $totalMoney,
            ':note'     => $note
        ]);
        $donHangId = $pdo->lastInsertId();

        // Chèn từng mặt hàng vào don_hang_chi_tiet (nếu bảng tồn tại)
        if (!empty($items) && is_array($items)) {
            foreach ($items as $it) {
                try {
                    $tenSp = $it['name'] ?? 'Dịch vụ VNPT';
                    $gia = floatval($it['price'] ?? 0);
                    $sl = intval($it['qty'] ?? 1);
                    $thanhTien = $gia * $sl;
                    $insCt = $pdo->prepare("
                        INSERT INTO don_hang_chi_tiet (don_hang_id, san_pham_id, ten_san_pham_snapshot, so_luong, don_gia, thanh_tien)
                        VALUES (:dhId, 1, :ten, :sl, :gia, :tt)
                    ");
                    $insCt->execute([
                        ':dhId' => $donHangId,
                        ':ten'  => $tenSp,
                        ':sl'   => $sl,
                        ':gia'  => $gia,
                        ':tt'   => $thanhTien
                    ]);
                } catch (Exception $_e) {}
            }
        }

        // Xóa sạch giỏ hàng session
        $_SESSION['cart'] = [];

        echo json_encode([
            'status'     => 'success',
            'message'    => 'Đơn hàng #' . $maDonHang . ' đã được tạo thành công!',
            'orderCode'  => $maDonHang,
            'orderId'    => $donHangId,
            'totalMoney' => $totalMoney
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2. Lấy danh sách đơn hàng của người dùng (GET)
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'error' => 'Vui lòng cung cấp email!']);
        exit;
    }

    $stmtOrders = $pdo->prepare("
        SELECT dh.*,
               (SELECT COUNT(*) FROM don_hang_chi_tiet ct WHERE ct.don_hang_id = dh.id) AS so_luong_san_pham
        FROM don_hang dh
        JOIN khach_hang kh ON kh.id = dh.khach_hang_id
        JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id
        WHERE LOWER(tk.email) = :email OR LOWER(tk.email) = :vnvd_email
        ORDER BY dh.id DESC
    ");
    $vnvdEmail = str_replace('@vnpt.vn', '@vnvd.vn', $email);
    $stmtOrders->execute([':email' => $email, ':vnvd_email' => $vnvdEmail]);
    $orders = $stmtOrders->fetchAll();

    echo json_encode([
        'status' => 'success',
        'orders' => $orders
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'error' => 'Lỗi kết nối CSDL: ' . $e->getMessage()]);
}
