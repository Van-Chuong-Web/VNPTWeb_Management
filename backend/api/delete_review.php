<?php
/**
 * delete_review.php — API Xóa Đánh Giá Cho Khách Hàng / Admin
 */
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $reviewId = (int)($input['id'] ?? $input['review_id'] ?? 0);
    if ($reviewId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Mã nhận xét không hợp lệ.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $currentUser = $_SESSION['user'] ?? $input['user'] ?? null;

    if (!$currentUser && !empty($input['email'])) {
        $currentUser = [
            'email' => trim($input['email']),
            'ho_ten' => trim($input['ho_ten'] ?? $input['name'] ?? ''),
            'khach_hang_id' => $input['khach_hang_id'] ?? null
        ];
    }

    if (!$currentUser) {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thực hiện thao tác xóa.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Lấy thông tin bài đánh giá
    $review = $db->select("SELECT id, khach_hang_id, ho_ten_nguoi_danh_gia FROM danh_gia_san_pham WHERE id = ? LIMIT 1", [$reviewId]);
    if (empty($review)) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đánh giá cần xóa.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $revData = $review[0];
    $khachHangId = $currentUser['khach_hang_id'] ?? $currentUser['id'] ?? $currentUser['taiKhoanId'] ?? null;
    $userHoTen = $currentUser['ho_ten'] ?? (isset($currentUser['firstName']) ? trim("{$currentUser['firstName']} {$currentUser['lastName']}") : '');
    $email = strtolower($currentUser['email'] ?? '');
    $role = strtolower($currentUser['role'] ?? $currentUser['loai_tai_khoan'] ?? $currentUser['ten_vai_tro'] ?? '');

    $isAdminOrEditor = (
        $role === 'admin' || $role === 'quan_tri_vien' ||
        $role === 'bien_tap_vien' || $role === 'editor' ||
        $role === 'nhan_vien' || $role === 'nhan_vien_ban_hang' || $role === 'quan_ly' ||
        str_contains($email, 'editor') || str_contains($email, 'admin') || str_ends_with($email, '@vnpt.vn')
    );

    // Kiểm tra quyền xóa: Là chính chủ bài viết HOẶC là Biên tập viên / Quản trị viên
    $isOwner = ($khachHangId && (int)$revData['khach_hang_id'] === (int)$khachHangId) ||
               (!empty($userHoTen) && mb_strtolower(trim($revData['ho_ten_nguoi_danh_gia'])) === mb_strtolower(trim($userHoTen)));

    if (!$isOwner && !$isAdminOrEditor) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền xóa nhận xét này.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Thực hiện xóa khỏi CSDL
    $deleted = $db->execute("DELETE FROM danh_gia_san_pham WHERE id = ?", [$reviewId]);

    echo json_encode([
        'status' => 'success',
        'message' => '🗑️ Đã xóa nhận xét thành công!'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối CSDL: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
