<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();

    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service = trim($_POST['service'] ?? 'dich_vu_so');
    $message = trim($_POST['message'] ?? $_POST['noi_dung'] ?? '');

    if (empty($name) || empty($phone)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Vui lòng nhập đầy đủ Họ tên và Số điện thoại liên hệ!'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $cleanPhone = preg_replace('/\s+/', '', $phone);
    if (!preg_match('/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', $cleanPhone)) {
        echo json_encode([
            'status' => 'error',
            'message' => '⚠️ Số điện thoại không hợp lệ! Vui lòng nhập số chuẩn (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09 hoặc +84).'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (!empty($email) && (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email))) {
        echo json_encode([
            'status' => 'error',
            'message' => '⚠️ Địa chỉ Email không hợp lệ! Vui lòng nhập đúng định dạng (ví dụ: contact@company.vn).'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 1. Kiểm tra ID khách hàng từ Session hoặc tìm khách hàng sẵn có trong CSDL (KHÔNG TỰ TẠO TÀI KHOẢN MỚI)
    $khachHangId = $_SESSION['user']['khach_hang_id'] ?? null;

    if (!$khachHangId && (!empty($phone) || !empty($email))) {
        $existing = $db->select("
            SELECT kh.id 
              FROM khach_hang kh 
         LEFT JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id 
             WHERE kh.so_dien_thoai = ? OR (tk.email IS NOT NULL AND tk.email = ? AND tk.email != '')
             LIMIT 1
        ", [$phone, $email]);
        if (!empty($existing)) {
            $khachHangId = $existing[0]['id'];
        }
    }

    // 2. Thêm yêu cầu tư vấn vào bảng yeu_cau_ho_tro
    $tieuDe = "Yêu cầu tư vấn Chuyển đổi số (" . mb_strtoupper($service, 'UTF-8') . ")";
    $noiDungArr = [
        "KHÁCH HÀNG ĐĂNG KÝ TƯ VẤN BẮT ĐẦU NGAY",
        "- Tên/Doanh nghiệp: " . $name,
        "- Số điện thoại: " . $phone,
    ];
    if (!empty($email)) {
        $noiDungArr[] = "- Email doanh nghiệp: " . $email;
    }
    $noiDungArr[] = "- Dịch vụ quan tâm: " . $service;
    if (!empty($message)) {
        $noiDungArr[] = "- Nội dung cần tư vấn: " . $message;
    }
    $noiDungArr[] = "- Thời gian đăng ký: " . date('d/m/Y H:i:s');

    $noiDung = implode("\n", $noiDungArr);

    $insertedId = $db->insert(
        "INSERT INTO yeu_cau_ho_tro (khach_hang_id, tieu_de, noi_dung, loai_yeu_cau, trang_thai, created_at) VALUES (?, ?, ?, 'khac', 'moi', NOW())",
        [$khachHangId, $tieuDe, $noiDung]
    );

    if ($insertedId) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Ghi nhận yêu cầu thành công! VNPT sẽ phản hồi cho bạn trong thời gian sớm nhất.'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau!'
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
