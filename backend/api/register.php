<?php
/**
 * backend/api/register.php — API Đăng ký tài khoản mới 100% CSDL MySQL
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $firstName = trim($input['firstName'] ?? '');
    $lastName  = trim($input['lastName'] ?? '');
    $email     = strtolower(trim($input['email'] ?? ''));
    $phone     = trim($input['phone'] ?? '');
    $password  = trim($input['password'] ?? '');

    if (empty($firstName)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'error' => 'Vui lòng nhập họ tên của bạn.']);
        exit;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'error' => 'Địa chỉ Email không hợp lệ.']);
        exit;
    }

    if (empty($password) || strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'error' => 'Mật khẩu tối thiểu 6 ký tự.']);
        exit;
    }

    // 1. Kiểm tra Email đã tồn tại trong CSDL MySQL chưa
    $chkEmail = $pdo->prepare("SELECT id FROM tai_khoan WHERE LOWER(email) = :email LIMIT 1");
    $chkEmail->execute([':email' => $email]);
    if ($chkEmail->fetch()) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'error' => 'Email này đã được đăng ký. Vui lòng dùng email khác hoặc Đăng nhập.']);
        exit;
    }

    // 2. Kiểm tra Số điện thoại đã tồn tại trong khach_hang chưa (nếu có nhập)
    if (!empty($phone)) {
        $chkPhone = $pdo->prepare("SELECT id FROM khach_hang WHERE so_dien_thoai = :phone LIMIT 1");
        $chkPhone->execute([':phone' => $phone]);
        if ($chkPhone->fetch()) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'error' => 'Số điện thoại này đã được đăng ký. Vui lòng kiểm tra lại.']);
            exit;
        }
    }

    // 3. Mã hóa mật khẩu bcrypt
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $hoTen = trim($firstName . ' ' . $lastName);

    // 4. Thêm vào bảng tai_khoan (vai_tro_id = 4 là khách hàng)
    $insTk = $pdo->prepare("
        INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email)
        VALUES (:email, :hash, 'khach_hang', 4, 'hoat_dong', 1)
    ");
    $insTk->execute([
        ':email' => $email,
        ':hash'  => $hash
    ]);
    $taiKhoanId = $pdo->lastInsertId();

    // 5. Thêm vào bảng khach_hang
    $insKh = $pdo->prepare("
        INSERT INTO khach_hang (tai_khoan_id, ho_ten, so_dien_thoai)
        VALUES (:tkId, :hoTen, :phone)
    ");
    $insKh->execute([
        ':tkId'  => $taiKhoanId,
        ':hoTen' => $hoTen,
        ':phone' => !empty($phone) ? $phone : null
    ]);
    $khachHangId = $pdo->lastInsertId();

    // 6. Tạo thông tin User trả về
    $userData = [
        'id'            => $khachHangId,
        'tai_khoan_id'  => $taiKhoanId,
        'email'         => $email,
        'firstName'     => $firstName,
        'lastName'      => $lastName,
        'ho_ten'        => $hoTen,
        'phone'         => $phone,
        'so_dien_thoai' => $phone,
        'role'          => 'customer',
        'loai_tai_khoan'=> 'khach_hang',
        'avatar'        => '',
        'hinh_anh_url'  => ''
    ];

    $_SESSION['user'] = $userData;

    echo json_encode([
        'status'  => 'success',
        'message' => 'Đăng ký tài khoản thành công!',
        'user'    => $userData,
        'token'   => 'session_' . session_id() . '_' . time()
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'error' => 'Lỗi kết nối CSDL: ' . $e->getMessage()]);
}
