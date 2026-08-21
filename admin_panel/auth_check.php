<?php
/**
 * auth_check.php — Bảo mật kiểm tra xác thực quyền đăng nhập Admin Panel
 * Tự động đồng bộ phiên nếu đã đăng nhập tài khoản Quản trị / Nhân viên từ Frontend,
 * hoặc chuyển hướng về login.php nếu chưa đăng nhập.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// 1. Tự động đồng bộ phiên nếu đã đăng nhập tài khoản Admin/Nhân viên ở Frontend
if (!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user']['email'])) {
    $candidateEmail = $_SESSION['user']['email'] ?? ($_GET['user_email'] ?? '');
    
    if (!empty($candidateEmail)) {
        try {
            $stmtSync = $pdo->prepare("
                SELECT tk.id AS tai_khoan_id, tk.email, tk.trang_thai, tk.hinh_anh_url, 
                       vt.ten_vai_tro, nv.id AS nhan_vien_id, nv.ho_ten
                  FROM tai_khoan tk
                  JOIN vai_tro vt ON vt.id = tk.vai_tro_id
                  JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
                 WHERE tk.email = :email AND tk.loai_tai_khoan = 'nhan_vien' AND tk.trang_thai = 'hoat_dong'
                 LIMIT 1
            ");
            $stmtSync->execute([':email' => $candidateEmail]);
            $adminAcc = $stmtSync->fetch();

            if ($adminAcc) {
                $_SESSION['admin_user'] = [
                    'id'            => $adminAcc['nhan_vien_id'],
                    'tai_khoan_id'  => $adminAcc['tai_khoan_id'],
                    'nhan_vien_id'  => $adminAcc['nhan_vien_id'],
                    'email'         => $adminAcc['email'],
                    'ho_ten'        => $adminAcc['ho_ten'],
                    'ten_vai_tro'   => $adminAcc['ten_vai_tro'],
                    'hinh_anh_url'  => $adminAcc['hinh_anh_url'] ?? ''
                ];
            }
        } catch (PDOException $e) {}
    }
}

// 2. Kiểm tra nếu vẫn chưa có phiên đăng nhập hợp lệ -> Chặn và chuyển hướng về login.php
if (!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user']['email'])) {
    header('Location: login.php?error=required');
    exit;
}

// 3. Kiểm tra trực tiếp trạng thái tài khoản trong CSDL MySQL (đảm bảo tài khoản chưa bị khóa)
try {
    $currentAccId = $_SESSION['admin_user']['tai_khoan_id'] ?? ($_SESSION['admin_user']['id'] ?? 0);
    $currentEmail = $_SESSION['admin_user']['email'] ?? '';

    $stmtCheck = $pdo->prepare("
        SELECT tk.id AS tai_khoan_id, tk.email, tk.trang_thai, tk.hinh_anh_url, 
               vt.ten_vai_tro, nv.id AS nhan_vien_id, nv.ho_ten
          FROM tai_khoan tk
          JOIN vai_tro vt ON vt.id = tk.vai_tro_id
          JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
         WHERE (tk.id = :id OR tk.email = :email) AND tk.loai_tai_khoan = 'nhan_vien'
         LIMIT 1
    ");
    $stmtCheck->execute([':id' => $currentAccId, ':email' => $currentEmail]);
    $currentUserData = $stmtCheck->fetch();

    if (!$currentUserData || $currentUserData['trang_thai'] === 'khoa') {
        unset($_SESSION['admin_user']);
        session_destroy();
        header('Location: login.php?error=locked');
        exit;
    }

    // Cập nhật lại thông tin mới nhất vào session
    $_SESSION['admin_user']['tai_khoan_id'] = $currentUserData['tai_khoan_id'];
    $_SESSION['admin_user']['nhan_vien_id'] = $currentUserData['nhan_vien_id'];
    $_SESSION['admin_user']['email']        = $currentUserData['email'];
    $_SESSION['admin_user']['ho_ten']       = $currentUserData['ho_ten'];
    $_SESSION['admin_user']['ten_vai_tro']  = $currentUserData['ten_vai_tro'];
    $_SESSION['admin_user']['hinh_anh_url'] = $currentUserData['hinh_anh_url'] ?? '';

} catch (PDOException $e) {
    // Giữ nguyên session nếu xảy ra sự cố CSDL tạm thời
}
