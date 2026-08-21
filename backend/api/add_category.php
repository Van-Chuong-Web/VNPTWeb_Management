<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

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

try {
    $db = new Database();
    $name = trim($_POST['name'] ?? $_POST['ten'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Tên chuyên mục không được để trống!'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $slug = toCleanSlug($name);
    
    // 1. Kiểm tra tồn tại trong danh_muc_bai_viet
    $existing = $db->select("SELECT id FROM danh_muc_bai_viet WHERE slug = ? OR ten = ?", [$slug, $name]);
    if (!empty($existing)) {
        $catId = $existing[0]['id'];
    } else {
        $catId = $db->insert("INSERT INTO danh_muc_bai_viet (ten, slug) VALUES (?, ?)", [$name, $slug]);
    }

    // 2. Tự động đồng bộ sang bảng menu nếu chưa có
    $parentTinTuc = $db->select("SELECT id FROM menu WHERE slug = 'tin-tuc' OR ten_menu LIKE '%Tin tức%' LIMIT 1");
    $parentId = !empty($parentTinTuc) ? $parentTinTuc[0]['id'] : null;

    if ($parentId) {
        $existingMenu = $db->select("SELECT id FROM menu WHERE slug = ? AND menu_cha_id = ?", [$slug, $parentId]);
        if (empty($existingMenu)) {
            $db->insert(
                "INSERT INTO menu (ten_menu, slug, link, menu_cha_id, thu_tu, trang_thai) VALUES (?, ?, '#', ?, 99, 1)",
                [$name, $slug, $parentId]
            );
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Đã thêm chuyên mục thành công!',
        'data' => [
            'id' => $catId,
            'ten' => $name,
            'slug' => $slug
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
