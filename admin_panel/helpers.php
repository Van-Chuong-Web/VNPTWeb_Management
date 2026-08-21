<?php
/**
 * helpers.php — Các hàm tiện ích dùng chung cho Admin Panel
 *
 * Cách dùng:
 *   require_once __DIR__ . '/helpers.php';
 *   logActivity($pdo, 'Đã thêm nhân viên mới: Nguyễn Văn A');
 */

/**
 * Ghi lại nhật ký thao tác của nhân viên đang đăng nhập vào bảng lich_su_nhan_vien.
 * Tự động lấy nhan_vien_id từ session hiện tại; bỏ qua nếu chưa đăng nhập.
 */
function logActivity(PDO $pdo, string $hanhDong, ?string $chiTiet = null, ?string $ipAddress = null): void
{
    $nhanVienId = $_SESSION['admin_user']['nhan_vien_id'] ?? null;
    if (!$nhanVienId) {
        return;
    }
    $ip = $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO lich_su_nhan_vien (nhan_vien_id, hanh_dong, chi_tiet, ip_address) VALUES (:nv, :hd, :ct, :ip)'
        );
        $stmt->execute([':nv' => $nhanVienId, ':hd' => $hanhDong, ':ct' => $chiTiet, ':ip' => $ip]);
    } catch (PDOException $e) {
        try {
            $stmt = $pdo->prepare('INSERT INTO lich_su_nhan_vien (nhan_vien_id, hanh_dong) VALUES (:nv, :hd)');
            $stmt->execute([':nv' => $nhanVienId, ':hd' => $hanhDong]);
        } catch (PDOException $ex) {}
    }
}

/**
 * Chuyển đổi chuỗi Tiếng Việt có dấu thành URL Slug không dấu
 */
function createSlug(string $str): string
{
    $str = trim(mb_strtolower($str, 'UTF-8'));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return trim($str, '-') ?: 'bai-viet-' . time();
}
