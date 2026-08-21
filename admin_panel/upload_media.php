<?php
/**
 * upload_media.php — Upload hình ảnh từ máy tính cho bài viết & media
 */
require_once __DIR__ . '/auth_check.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy file tải lên']);
    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi tải file (Mã: ' . $file['error'] . ')']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['status' => 'error', 'message' => 'Định dạng file không được hỗ trợ (Chỉ chấp nhận JPG, PNG, GIF, WEBP, SVG)']);
    exit;
}

// Tạo thư mục uploads trong frontend/assets/images/uploads nếu chưa có
$uploadDir = __DIR__ . '/../frontend/assets/images/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = 'img_' . date('Ymd_His') . '_' . rand(1000, 9999) . '.' . strtolower($ext);
$targetPath = $uploadDir . $newFilename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $relativeUrl = '../frontend/assets/images/uploads/' . $newFilename;
    echo json_encode([
        'status' => 'success',
        'url'    => $relativeUrl,
        'full_url' => $relativeUrl,
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không thể lưu file trên máy chủ']);
}
