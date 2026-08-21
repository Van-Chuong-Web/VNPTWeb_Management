<?php
// Nếu truy cập trực tiếp URL chứa /frontend/index.php, tự động chuyển hướng về root index.php để bảo đảm 100% đường dẫn CSS & JavaScript chuẩn
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/frontend/index.php') !== false) {
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    $newUrl = preg_replace('/\/frontend\/index\.php.*/i', '/index.php' . $qs, $_SERVER['REQUEST_URI'] ?? '');
    header("Location: " . $newUrl);
    exit();
}

include 'components/header.php';
include 'components/body.php';
include 'components/footer.php';
?>