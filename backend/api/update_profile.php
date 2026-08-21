<?php
/**
 * backend/api/update_profile.php — Cập nhật thông tin tài khoản & ảnh đại diện
 */

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $email = trim($_POST['email'] ?? $_GET['email'] ?? $_SESSION['user']['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');

    $hoTen = trim($firstName . ' ' . $lastName);

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp email tài khoản!'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $action = $_GET['action'] ?? 'update';

    // 1. Lấy thông tin tài khoản
    $stmt = $pdo->prepare("
        SELECT tk.id AS tk_id, tk.email, tk.hinh_anh_url, tk.loai_tai_khoan,
               kh.id AS kh_id, kh.ho_ten AS kh_ho_ten, kh.so_dien_thoai AS kh_sdt,
               nv.id AS nv_id, nv.ho_ten AS nv_ho_ten, nv.so_dien_thoai AS nv_sdt
        FROM tai_khoan tk
        LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
        LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
        WHERE tk.email = :email
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $userRow = $stmt->fetch();

    if ($action === 'get') {
        if ($userRow) {
            $displayName = !empty($userRow['kh_ho_ten']) ? $userRow['kh_ho_ten'] : (!empty($userRow['nv_ho_ten']) ? $userRow['nv_ho_ten'] : $userRow['email']);
            $displayPhone = !empty($userRow['kh_sdt']) ? $userRow['kh_sdt'] : ($userRow['nv_sdt'] ?? '');

            echo json_encode([
                'status' => 'success',
                'user' => [
                    'email'        => $userRow['email'],
                    'ho_ten'       => $displayName,
                    'so_dien_thoai'=> $displayPhone,
                    'phone'        => $displayPhone,
                    'hinh_anh_url' => $userRow['hinh_anh_url'] ?? '',
                    'avatar'       => $userRow['hinh_anh_url'] ?? ''
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // 2. Xử lý upload ảnh đại diện (nếu có tệp gửi lên)
    $hinhAnhUrl = trim($_POST['hinh_anh_url'] ?? $_POST['avatar_url'] ?? $userRow['hinh_anh_url'] ?? '');

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . '/../../uploads/avatars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = 'avatar_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $hinhAnhUrl = 'uploads/avatars/' . $filename;
            }
        }
    }

    // 3. Cập nhật thông tin trong CSDL
    if ($userRow) {
        $tkId = $userRow['tk_id'];

        // Cập nhật đường dẫn ảnh đại diện trong tai_khoan
        if (!empty($hinhAnhUrl)) {
            $upTk = $pdo->prepare("UPDATE tai_khoan SET hinh_anh_url = :url WHERE id = :id");
            $upTk->execute([':url' => $hinhAnhUrl, ':id' => $tkId]);
        }

        // Cập nhật họ tên và SĐT tùy theo loại tài khoản (khach_hang hoặc nhan_vien)
        if (!empty($hoTen)) {
            if (!empty($userRow['kh_id'])) {
                $upKh = $pdo->prepare("UPDATE khach_hang SET ho_ten = :name, so_dien_thoai = :phone WHERE id = :id");
                $upKh->execute([':name' => $hoTen, ':phone' => $phone, ':id' => $userRow['kh_id']]);
            }
            if (!empty($userRow['nv_id'])) {
                $upNv = $pdo->prepare("UPDATE nhan_vien SET ho_ten = :name, so_dien_thoai = :phone WHERE id = :id");
                $upNv->execute([':name' => $hoTen, ':phone' => $phone, ':id' => $userRow['nv_id']]);
            }
        }
    }

    // Cập nhật lại $_SESSION['user']
    if (isset($_SESSION['user'])) {
        $_SESSION['user']['hinh_anh_url'] = $hinhAnhUrl;
        $_SESSION['user']['avatar'] = $hinhAnhUrl;
        if (!empty($hoTen)) {
            $_SESSION['user']['ho_ten'] = $hoTen;
            $_SESSION['user']['firstName'] = $firstName;
            $_SESSION['user']['lastName'] = $lastName;
        }
    }

    echo json_encode([
        'status'  => 'success',
        'message' => 'Cập nhật hồ sơ cá nhân thành công!',
        'user'    => [
            'email'        => $email,
            'firstName'    => $firstName,
            'lastName'     => $lastName,
            'ho_ten'       => $hoTen,
            'phone'        => $phone,
            'so_dien_thoai'=> $phone,
            'hinh_anh_url' => $hinhAnhUrl,
            'avatar'       => $hinhAnhUrl
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
