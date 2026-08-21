<?php
/**
 * logout.php — Đăng xuất hoàn toàn tài khoản khỏi Admin Panel và chuyển hướng về Trang chủ Frontend
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa sạch sẽ toàn bộ biến Session và Cookie
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đang đăng xuất...</title>
    <script>
        localStorage.removeItem('vnpt_user');
        localStorage.removeItem('vnpt_token');
        localStorage.setItem('vnpt_force_logout', '1');
        sessionStorage.clear();
        window.location.href = '../index.php?logged_out=1';
    </script>
</head>
<body>
    <p>Đang chuyển hướng về trang chủ...</p>
</body>
</html>
