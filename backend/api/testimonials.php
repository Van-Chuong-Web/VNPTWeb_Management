<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();

    $rows = $db->select("
        SELECT id, khach_hang_id, ho_ten_nguoi_danh_gia, chuc_vu_cong_ty, ten_dich_vu, so_sao, tieu_de, noi_dung, phan_hoi_admin, ngay_phan_hoi_admin, created_at
          FROM danh_gia_san_pham
         WHERE trang_thai_duyet = 'da_duyet'
      ORDER BY created_at DESC
         LIMIT 30
    ");

    $testimonials = array_map(function($r) {
        $name = $r['ho_ten_nguoi_danh_gia'] ?: 'Khách hàng VNPT';
        $initials = mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
        return [
            'id' => (int)$r['id'],
            'khach_hang_id' => $r['khach_hang_id'] ? (int)$r['khach_hang_id'] : null,
            'name' => $name,
            'initials' => $initials,
            'company' => $r['chuc_vu_cong_ty'] ?: 'Đối tác Doanh nghiệp',
            'service' => $r['ten_dich_vu'] ?: 'Cloud Enterprise',
            'stars' => (int)$r['so_sao'],
            'title' => $r['tieu_de'] ?: '',
            'content' => $r['noi_dung'] ?: '',
            'admin_reply' => $r['phan_hoi_admin'] ?: '',
            'admin_reply_time' => $r['ngay_phan_hoi_admin'] ? date('d/m/Y H:i', strtotime($r['ngay_phan_hoi_admin'])) : '',
            'created_at' => date('d/m/Y', strtotime($r['created_at']))
        ];
    }, $rows ?: []);

    echo json_encode([
        'status' => 'success',
        'data' => $testimonials
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi kết nối CSDL: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
