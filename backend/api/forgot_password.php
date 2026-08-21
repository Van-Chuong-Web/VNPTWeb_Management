<?php
/**
 * forgot_password.php — API Khôi phục mật khẩu & Quên mật khẩu
 * Hỗ trợ tạo OTP 6 số, gửi Email thực tế và hiển thị mã xác nhận thử nghiệm trên môi trường Localhost.
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

$action = $input['action'] ?? '';

/**
 * Hàm gửi Email chứa mã OTP thực tế tới người nhận
 */
function sendOtpEmail($toEmail, $otpCode) {
    $subject = "=?UTF-8?B?" . base64_encode("VNPT - Mã xác thực OTP khôi phục mật khẩu") . "?=";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Mã xác thực OTP - VNPT</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; padding: 20px; margin: 0; }
            .email-card { max-width: 500px; margin: 20px auto; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
            .header { text-align: center; font-size: 22px; font-weight: 700; color: #0d6efd; margin-bottom: 20px; }
            .otp-box { background: #f8fafc; border: 2px dashed #0d6efd; padding: 18px; text-align: center; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0f172a; border-radius: 12px; margin: 24px 0; }
            .footer { font-size: 13px; color: #64748b; text-align: center; margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 16px; }
        </style>
    </head>
    <body>
        <div class='email-card'>
            <div class='header'>VNPT — Khôi Phục Mật Khẩu</div>
            <p>Xin chào,</p>
            <p>Bạn (hoặc ai đó) vừa gửi yêu cầu khôi phục mật khẩu cho tài khoản <strong>{$toEmail}</strong> trên hệ thống VNPT.</p>
            <p>Dưới đây là mã xác thực OTP của bạn:</p>
            <div class='otp-box'>{$otpCode}</div>
            <p>Mã OTP này có hiệu lực trong <strong>10 phút</strong>. Vui lòng không chia sẻ mã xác thực này cho bất kỳ ai để bảo vệ an toàn tài khoản.</p>
            <div class='footer'>© 2026 VNPT — Hệ thống Dịch vụ Số Toàn diện.</div>
        </div>
    </body>
    </html>
    ";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: VNPT Security System <no-reply@vnpt.vn>\r\n";
    $headers .= "Reply-To: no-reply@vnpt.vn\r\n";

    @mail($toEmail, $subject, $message, $headers);
}

// ── 1. HÀNH ĐỘNG: GỬI MÃ OTP ─────────────────────────────────────────────
if ($action === 'send_otp') {
    $email = trim($input['email'] ?? '');
    $type  = trim($input['type'] ?? 'frontend'); // 'frontend' hoặc 'admin'

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập địa chỉ Email hợp lệ.']);
        exit;
    }

    try {
        // Kiểm tra xem Email có tồn tại trong CSDL hay không
        $stmt = $pdo->prepare("SELECT id, email, trang_thai FROM tai_khoan WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Email này chưa được đăng ký trên hệ thống VNPT.']);
            exit;
        }

        if (($user['trang_thai'] ?? 'hoat_dong') === 'khoa') {
            echo json_encode(['success' => false, 'message' => 'Tài khoản này hiện đang bị khóa. Vui lòng liên hệ Quản trị viên.']);
            exit;
        }

        // Tạo mã OTP 6 chữ số ngẫu nhiên
        $otp = sprintf('%06d', mt_rand(100000, 999999));
        $expiryMinutes = 10;
        $expiryTime = date('Y-m-d H:i:s', strtotime("+$expiryMinutes minutes"));

        // Hủy bỏ các mã OTP cũ chưa sử dụng của email này
        $stmtInvalidate = $pdo->prepare("UPDATE ma_xac_nhan_otp SET da_su_dung = 1 WHERE email = :email");
        $stmtInvalidate->execute([':email' => $email]);

        // Ghi mã OTP mới vào CSDL
        $stmtOtp = $pdo->prepare("
            INSERT INTO ma_xac_nhan_otp (email, ma_otp, loai, het_han_luc, da_su_dung, created_at)
            VALUES (:email, :otp, :loai, :exp, 0, NOW())
        ");
        $stmtOtp->execute([
            ':email' => $email,
            ':otp'   => $otp,
            ':loai'  => $type,
            ':exp'   => $expiryTime,
        ]);

        // Thử gửi mail qua hàm mail()
        sendOtpEmail($email, $otp);

        // Trả về kết quả thành công kèm mã OTP để kiểm tra dễ dàng trên môi trường Localhost
        echo json_encode([
            'success' => true,
            'message' => "Mã OTP đã được khởi tạo cho email $email!",
            'otp'     => $otp,
            'expiry_minutes' => $expiryMinutes
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL: ' . $e->getMessage()]);
        exit;
    }
}

// ── 2. HÀNH ĐỘNG: XÁC THỰC MÃ OTP ───────────────────────────────────────
elseif ($action === 'verify_otp') {
    $email = trim($input['email'] ?? '');
    $otp   = trim($input['otp'] ?? '');

    if (empty($email) || empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ Email và Mã OTP.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT id, het_han_luc, da_su_dung 
              FROM ma_xac_nhan_otp 
             WHERE email = :email AND ma_otp = :otp AND da_su_dung = 0
             ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([':email' => $email, ':otp' => $otp]);
        $record = $stmt->fetch();

        if (!$record) {
            echo json_encode(['success' => false, 'message' => 'Mã OTP không chính xác hoặc đã qua sử dụng.']);
            exit;
        }

        if (strtotime($record['het_han_luc']) < time()) {
            echo json_encode(['success' => false, 'message' => 'Mã OTP này đã hết hạn. Vui lòng gửi lại mã mới.']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Mã OTP hợp lệ! Vui lòng nhập mật khẩu mới.']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi kiểm tra OTP: ' . $e->getMessage()]);
        exit;
    }
}

// ── 3. HÀNH ĐỘNG: ĐẶT LẠI MẬT KHẨU MỚI ────────────────────────────────
elseif ($action === 'reset_password') {
    $email       = trim($input['email'] ?? '');
    $otp         = trim($input['otp'] ?? '');
    $newPassword = trim($input['new_password'] ?? '');

    if (empty($email) || empty($otp) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
        exit;
    }

    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự.']);
        exit;
    }

    try {
        // Kiểm tra OTP lại 1 lần nữa trước khi cập nhật
        $stmt = $pdo->prepare("
            SELECT id, het_han_luc 
              FROM ma_xac_nhan_otp 
             WHERE email = :email AND ma_otp = :otp AND da_su_dung = 0
             ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([':email' => $email, ':otp' => $otp]);
        $record = $stmt->fetch();

        if (!$record || strtotime($record['het_han_luc']) < time()) {
            echo json_encode(['success' => false, 'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
            exit;
        }

        // Băm mật khẩu mới bằng Bcrypt
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        // Cập nhật mật khẩu mới vào bảng tai_khoan
        $stmtUpdate = $pdo->prepare("UPDATE tai_khoan SET mat_khau_hash = :hash, updated_at = NOW() WHERE email = :email");
        $stmtUpdate->execute([':hash' => $passwordHash, ':email' => $email]);

        // Đánh dấu mã OTP đã được sử dụng thành công
        $stmtUsed = $pdo->prepare("UPDATE ma_xac_nhan_otp SET da_su_dung = 1 WHERE id = :id");
        $stmtUsed->execute([':id' => $record['id']]);

        echo json_encode(['success' => true, 'message' => '🎉 Đổi mật khẩu thành công! Bạn có thể đăng nhập ngay bằng mật khẩu mới.']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi đổi mật khẩu: ' . $e->getMessage()]);
        exit;
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);
    exit;
}
