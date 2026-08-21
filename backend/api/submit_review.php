<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();

    $name = trim($_POST['name'] ?? $_POST['ho_ten'] ?? '');
    $company = trim($_POST['company'] ?? $_POST['chuc_vu'] ?? '');
    $service = trim($_POST['service'] ?? $_POST['ten_dich_vu'] ?? 'Cloud Enterprise');
    $rating = (int)($_POST['rating'] ?? $_POST['so_sao'] ?? 5);
    $title = trim($_POST['title'] ?? $_POST['tieu_de'] ?? '');
    $content = trim($_POST['content'] ?? $_POST['noi_dung'] ?? '');

    if ($rating < 1) $rating = 1;
    if ($rating > 5) $rating = 5;

    // Lấy thông tin tài khoản nếu người dùng đã đăng nhập
    $khachHangId = $_SESSION['user']['khach_hang_id'] ?? null;
    $email = $_SESSION['user']['email'] ?? '';

    if (empty($name)) {
        if (!empty($_SESSION['user']['ho_ten'])) {
            $name = $_SESSION['user']['ho_ten'];
        } elseif (!empty($email)) {
            $name = $email;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập Họ tên hoặc Email của bạn!'], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập nội dung đánh giá / nhận xét!'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $insertedId = $db->insert("
        INSERT INTO danh_gia_san_pham (khach_hang_id, ho_ten_nguoi_danh_gia, chuc_vu_cong_ty, ten_dich_vu, so_sao, tieu_de, noi_dung, trang_thai_duyet, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'da_duyet', NOW())
    ", [$khachHangId, $name, $company, $service, $rating, $title, $content]);

    if ($insertedId) {
        $initials = mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
        echo json_encode([
            'status' => 'success',
            'message' => 'Cảm ơn bạn đã gửi nhận xét! Đánh giá của bạn đã được phê duyệt và hiển thị trực tiếp trên trang chủ.',
            'review' => [
                'id' => (int)$insertedId,
                'khach_hang_id' => $khachHangId ? (int)$khachHangId : null,
                'name' => $name,
                'initials' => $initials,
                'company' => $company ?: 'Đối tác Doanh nghiệp',
                'service' => $service,
                'stars' => (int)$rating,
                'title' => $title,
                'content' => $content,
                'admin_reply' => '',
                'admin_reply_time' => '',
                'created_at' => date('d/m/Y')
            ]
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
