<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db/database.php';

function toCleanSlug($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

function fixImgPath($path) {
    if (empty($path)) return '';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
        return $path;
    }
    $clean = ltrim($path, '/');
    if (!str_starts_with($clean, 'frontend/') && !str_starts_with($clean, 'uploads/')) {
        $clean = 'frontend/assets/images/uploads/' . $clean;
    }
    return $clean;
}

$rawSlug = trim($_GET['slug'] ?? '');
if (!$rawSlug) {
    echo json_encode(['status' => 'error', 'message' => 'Slug không hợp lệ']);
    exit;
}

$cleanSlug = toCleanSlug($rawSlug);
$db = new Database();

// 1. Kiểm tra bài viết đơn lẻ (Single Post)
$sqlPost = "SELECT b.id, b.tieu_de, b.slug, b.tom_tat, b.noi_dung, b.anh_bia, b.ngay_xuat_ban,
                   c.ten AS ten_danh_muc, nv.ho_ten AS ten_tac_gia
              FROM bai_viet b
         LEFT JOIN danh_muc_bai_viet c ON c.id = b.danh_muc_bai_viet_id
         LEFT JOIN nhan_vien nv ON nv.id = b.tac_gia_id
             WHERE b.slug = ? OR b.slug = ? OR b.id = ? LIMIT 1";

$resPost = $db->select($sqlPost, "ssi", [$rawSlug, $cleanSlug, is_numeric($rawSlug) ? (int)$rawSlug : 0]);

if (!empty($resPost)) {
    $post = $resPost[0];
    
    // Lấy danh sách Tin liên quan (Sidebar)
    $sqlRelated = "SELECT b.id, b.tieu_de, b.slug, b.tom_tat, b.anh_bia, b.ngay_xuat_ban
                     FROM bai_viet b
                    WHERE b.trang_thai IN ('da_dang', 'xuat_ban', 'published', 'cong_khai')
                      AND b.id != ?
                 ORDER BY b.ngay_xuat_ban DESC, b.id DESC
                    LIMIT 5";
    $resRelated = $db->select($sqlRelated, "i", [(int)$post['id']]);
    $db->close();

    $relatedPosts = array_map(function($r) {
        return [
            'id'            => $r['id'],
            'title'         => $r['tieu_de'],
            'slug'          => $r['slug'],
            'tom_tat'       => $r['tom_tat'],
            'anh_bia'       => fixImgPath($r['anh_bia']),
            'ngay_xuat_ban' => $r['ngay_xuat_ban'] ? date('H:i | d/m/Y', strtotime($r['ngay_xuat_ban'])) : ''
        ];
    }, $resRelated ?: []);

    echo json_encode([
        'status' => 'success',
        'type'   => 'post',
        'data'   => [
            'id'            => $post['id'],
            'title'         => $post['tieu_de'],
            'subtitle'      => $post['tom_tat'],
            'noi_dung'      => $post['noi_dung'],
            'anh_bia'       => fixImgPath($post['anh_bia']),
            'danh_muc'      => $post['ten_danh_muc'] ?: 'Tin tức',
            'tac_gia'       => $post['ten_tac_gia'] ?: 'BQT VNPT',
            'ngay_xuat_ban' => $post['ngay_xuat_ban'] ? date('H:i | d/m/Y', strtotime($post['ngay_xuat_ban'])) : '',
            'related_posts' => $relatedPosts
        ]
    ]);
    exit;
}

// 2. Kiểm tra xem slug có phải là Chuyên mục bài viết không (Thông cáo báo chí, Blog công nghệ, Sự kiện...)
$sqlCat = "SELECT * FROM danh_muc_bai_viet WHERE slug = ? OR slug = ? LIMIT 1";
$resCat = $db->select($sqlCat, "ss", [$rawSlug, $cleanSlug]);

$isNewsSection = !empty($resCat) || in_array($cleanSlug, ['thong-cao-bao-chi', 'blog-cong-nghe', 'su-kien', 'tin-tuc']);

if ($isNewsSection) {
    $catName = !empty($resCat) ? $resCat[0]['ten'] : 'Tin tức & Thông tin';
    if ($cleanSlug === 'thong-cao-bao-chi') $catName = 'Thông cáo báo chí';
    if ($cleanSlug === 'blog-cong-nghe') $catName = 'Blog công nghệ';
    if ($cleanSlug === 'su-kien') $catName = 'Sự kiện';

    $catId = !empty($resCat) ? (int)$resCat[0]['id'] : 0;

    $query = "SELECT b.id, b.tieu_de, b.slug, b.tom_tat, b.anh_bia, b.ngay_xuat_ban,
                     c.ten AS ten_danh_muc, nv.ho_ten AS ten_tac_gia
                FROM bai_viet b
           LEFT JOIN danh_muc_bai_viet c ON c.id = b.danh_muc_bai_viet_id
           LEFT JOIN nhan_vien nv ON nv.id = b.tac_gia_id
               WHERE b.trang_thai IN ('da_dang', 'xuat_ban', 'published', 'cong_khai') ";
    
    $params = [];
    $types = "";

    if ($catId > 0 && $cleanSlug !== 'tin-tuc') {
        $query .= " AND b.danh_muc_bai_viet_id = ? ";
        $params[] = $catId;
        $types .= "i";
    }

    $query .= " ORDER BY b.ngay_xuat_ban DESC, b.id DESC";

    $posts = $db->select($query, $types, $params);

    $formattedPosts = array_map(function($p) {
        return [
            'id'            => $p['id'],
            'title'         => $p['tieu_de'],
            'slug'          => $p['slug'],
            'tom_tat'       => $p['tom_tat'],
            'anh_bia'       => fixImgPath($p['anh_bia']),
            'danh_muc'      => $p['ten_danh_muc'] ?: 'Tin tức',
            'tac_gia'       => $p['ten_tac_gia'] ?: 'BQT VNPT',
            'ngay_xuat_ban' => $p['ngay_xuat_ban'] ? date('d/m/Y H:i', strtotime($p['ngay_xuat_ban'])) : ''
        ];
    }, $posts ?: []);

    // Lấy toàn bộ danh mục bài viết công khai (loại bỏ Chưa được phân loại) cho thanh điều hướng/sidebar
    $allCats = $db->select("SELECT id, ten, slug FROM danh_muc_bai_viet WHERE slug != 'chua-duoc-phan-loai' AND ten NOT LIKE '%Chưa được phân loại%' ORDER BY id ASC");
    $db->close();

    echo json_encode([
        'status' => 'success',
        'type'   => 'category',
        'data'   => [
            'title'      => $catName,
            'subtitle'   => 'Kiến thức, sự kiện và thông cáo báo chí mới nhất từ VNPT',
            'posts'      => $formattedPosts,
            'categories' => $allCats ?: []
        ]
    ]);
    exit;
}

// 3. Tìm trong trang_tinh
$sqlTrang = "SELECT t.tieu_de, t.mo_ta, t.icon, t.ma_san_pham, s.gia_niem_yet 
               FROM trang_tinh t 
          LEFT JOIN san_pham s ON t.ma_san_pham = s.ma_san_pham 
              WHERE t.slug = ? OR t.slug = ?";
$resTrang = $db->select($sqlTrang, "ss", [$rawSlug, $cleanSlug]);

if (!empty($resTrang)) {
    $db->close();
    echo json_encode([
        'status' => 'success',
        'type'   => 'page',
        'data'   => [
            'title'        => $resTrang[0]['tieu_de'],
            'subtitle'     => $resTrang[0]['mo_ta'],
            'icon'         => $resTrang[0]['icon'],
            'ma_san_pham'  => $resTrang[0]['ma_san_pham'],
            'gia_niem_yet' => $resTrang[0]['gia_niem_yet']
        ]
    ]);
    exit;
}

$db->close();
echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy trang hoặc bài viết']);