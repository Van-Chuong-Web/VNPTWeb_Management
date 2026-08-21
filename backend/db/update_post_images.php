<?php
/**
 * update_post_images.php — Tự động cập nhật ảnh bìa chuẩn cho bài viết mẫu trong MySQL
 */
require_once __DIR__ . '/../../admin_panel/db.php';

$imageMap = [
    1 => 'frontend/assets/images/uploads/img_20260731_035650_3412.jpg',
    2 => 'frontend/assets/images/uploads/img_20260731_091331_7815.jpg',
    3 => 'frontend/assets/images/uploads/img_20260803_021808_2671.jpg',
    4 => 'frontend/assets/images/uploads/img_20260805_082315_1739.jpg',
    5 => 'frontend/assets/images/uploads/img_20260807_044833_1924.jpg',
];

echo "Bắt đầu cập nhật đường dẫn ảnh bìa bài viết...\n";

foreach ($imageMap as $id => $imgPath) {
    $stmt = $pdo->prepare("UPDATE bai_viet SET anh_bia = :anh_bia WHERE id = :id AND (anh_bia LIKE '%banner%' OR anh_bia = '' OR anh_bia IS NULL OR anh_bia LIKE '../%')");
    $stmt->execute([':anh_bia' => $imgPath, ':id' => $id]);
    echo "Bài viết ID $id -> " . $imgPath . " (Đã cập nhật)\n";
}

// Cập nhật tất cả các bài viết còn lại có chứa '../frontend/' thành 'frontend/'
$pdo->exec("UPDATE bai_viet SET anh_bia = REPLACE(anh_bia, '../frontend/', 'frontend/') WHERE anh_bia LIKE '../frontend/%'");

echo "✅ Đã cập nhật xong toàn bộ ảnh bìa bài viết chuẩn xác 100%!\n";
?>
