<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db/database.php';

$query = trim($_REQUEST['query'] ?? $_REQUEST['email_or_phone'] ?? '');

if (empty($query)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng nhập Email hoặc Số điện thoại để tra cứu.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = new Database();

    // Query support tickets matching email or phone or title or content
    $sql = "SELECT yc.id, yc.tieu_de, yc.noi_dung, yc.trang_thai, yc.created_at, yc.updated_at,
                   tk.email AS khach_hang_email,
                   kh.ho_ten AS khach_hang_ten, kh.so_dien_thoai AS khach_hang_sdt
              FROM yeu_cau_ho_tro yc
         LEFT JOIN tai_khoan tk ON tk.id = yc.khach_hang_id
         LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
             WHERE tk.email LIKE ? 
                OR kh.so_dien_thoai LIKE ? 
                OR yc.noi_dung LIKE ?
                OR yc.tieu_de LIKE ?
          ORDER BY yc.id DESC LIMIT 20";

    $searchTerm = "%$query%";
    $requests = $db->select($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);

    $results = [];
    foreach ($requests as $req) {
        $reqId = $req['id'];
        
        // Fetch staff replies for this ticket
        $repliesSql = "SELECT ph.id, ph.noi_dung, ph.created_at,
                              nv.ho_ten AS nhan_vien_ten,
                              vt.ten_vai_tro
                         FROM phan_hoi_ho_tro ph
                    LEFT JOIN tai_khoan tk ON tk.id = ph.tai_khoan_id
                    LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
                    LEFT JOIN vai_tro vt ON vt.id = tk.vai_tro_id
                        WHERE ph.yeu_cau_ho_tro_id = ?
                     ORDER BY ph.id ASC";
        $replies = $db->select($repliesSql, [$reqId]);

        $results[] = [
            'id' => $req['id'],
            'tieu_de' => $req['tieu_de'],
            'noi_dung' => $req['noi_dung'],
            'trang_thai' => $req['trang_thai'],
            'created_at' => $req['created_at'],
            'updated_at' => $req['updated_at'],
            'replies' => $replies
        ];
    }

    $db->close();

    echo json_encode([
        'status' => 'success',
        'total' => count($results),
        'data' => $results
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
