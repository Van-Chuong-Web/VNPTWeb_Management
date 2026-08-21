<?php
/**
 * social_login.php — Xử lý Đăng nhập Thật qua Google & Facebook (Lưu vào CSDL MySQL)
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pdo)) {
    $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
    $dbName = $_ENV['DB_NAME'] ?? 'website_vnpt';
    $dbUser = $_ENV['DB_USER'] ?? 'root';
    $dbPass = $_ENV['DB_PASSWORD'] ?? '';
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
    } catch (PDOException $e) {
        require_once __DIR__ . '/../../admin_panel/db.php';
    }
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$provider   = trim($input['provider'] ?? 'google');
$email      = trim($input['email'] ?? '');
$firstName  = trim($input['firstName'] ?? '');
$lastName   = trim($input['lastName'] ?? '');
$providerId = trim($input['providerId'] ?? '');
$avatar     = trim($input['avatar'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'error' => 'Vui lòng cung cấp Email hợp lệ từ tài khoản mạng xã hội.']);
    exit;
}

$hoTen = trim("$firstName $lastName");
if (empty($hoTen)) {
    $hoTen = ucfirst($provider) . " User";
}

try {
    // 1. Kiểm tra tài khoản trong tai_khoan
    $stmt = $pdo->prepare("
        SELECT tk.id AS tai_khoan_id, tk.email, tk.trang_thai, kh.id AS khach_hang_id, kh.ho_ten, kh.so_dien_thoai
          FROM tai_khoan tk
     LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
         WHERE tk.email = :email
         LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Tài khoản đã tồn tại
        if ($user['trang_thai'] === 'khoa') {
            echo json_encode(['status' => 'error', 'error' => '🚫 Tài khoản của bạn đã bị khóa bởi Quản trị viên.']);
            exit;
        }

        $taiKhoanId  = $user['tai_khoan_id'];
        $khachHangId = $user['khach_hang_id'];

        // Cập nhật avatar nếu có
        if (!empty($avatar)) {
            $stmtAvatar = $pdo->prepare("UPDATE tai_khoan SET hinh_anh_url = :avatar WHERE id = :id");
            $stmtAvatar->execute([':avatar' => $avatar, ':id' => $taiKhoanId]);
        }

    } else {
        // Tài khoản chưa tồn tại -> Tự động đăng ký mới
        $randomPass = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
        $pdo->beginTransaction();

        $stmtTk = $pdo->prepare("
            INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email, hinh_anh_url, created_at)
            VALUES (:email, :hash, 'khach_hang', 4, 'hoat_dong', 1, :avatar, NOW())
        ");
        $stmtTk->execute([
            ':email'  => $email,
            ':hash'   => $randomPass,
            ':avatar' => $avatar
        ]);
        $taiKhoanId = $pdo->lastInsertId();

        $stmtKh = $pdo->prepare("
            INSERT INTO khach_hang (tai_khoan_id, ho_ten, created_at)
            VALUES (:tk_id, :ho_ten, NOW())
        ");
        $stmtKh->execute([
            ':tk_id'  => $taiKhoanId,
            ':ho_ten' => $hoTen
        ]);
        $khachHangId = $pdo->lastInsertId();

        $pdo->commit();
    }

    // Gán dữ liệu phiên đăng nhập
    $sessionUser = [
        'id'            => $khachHangId,
        'tai_khoan_id'  => $taiKhoanId,
        'khach_hang_id' => $khachHangId,
        'email'         => $email,
        'ho_ten'        => $hoTen,
        'firstName'     => $firstName ?: $hoTen,
        'lastName'      => $lastName,
        'hinh_anh_url'  => $avatar,
        'role'          => 'customer',
        'provider'      => $provider
    ];

    $_SESSION['user'] = $sessionUser;

    echo json_encode([
        'status'  => 'success',
        'message' => 'Đăng nhập mạng xã hội thành công!',
        'user'    => $sessionUser,
        'token'   => 'token_social_' . md5($email . time())
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'error' => 'Lỗi CSDL khi đăng nhập mạng xã hội: ' . $e->getMessage()]);
    exit;
}
