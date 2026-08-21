<?php
/**
 * backend/api/update_profile.php — Cập nhật thông tin tài khoản & tải ảnh đại diện (Tối ưu 100%)
 */

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $rawInput = json_decode(file_get_contents('php://input'), true);
    $postData = !empty($rawInput) ? array_merge($_POST, $rawInput) : $_POST;

    $email = strtolower(trim($postData['email'] ?? $_GET['email'] ?? $_SESSION['user']['email'] ?? ''));
    $firstName = trim($postData['firstName'] ?? '');
    $lastName  = trim($postData['lastName'] ?? '');
    $phone     = trim($postData['phone'] ?? '');

    $hoTen = trim($firstName . ' ' . $lastName);

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp email tài khoản!'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (!empty($phone) && !preg_match('/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', $phone)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => '⚠️ Số điện thoại không hợp lệ! Vui lòng nhập số điện thoại chuẩn Việt Nam (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09 hoặc +84).'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $action = $_GET['action'] ?? 'update';

    // 1. Tìm thông tin tài khoản trong MySQL (cả tai_khoan, khach_hang, nhan_vien)
    $vnvdEmail = str_replace('@vnpt.vn', '@vnvd.vn', $email);
    $stmt = $pdo->prepare("
        SELECT tk.id AS tk_id, tk.email, tk.hinh_anh_url, tk.loai_tai_khoan,
               kh.id AS kh_id, kh.ho_ten AS kh_ho_ten, kh.so_dien_thoai AS kh_sdt,
               nv.id AS nv_id, nv.ho_ten AS nv_ho_ten
        FROM tai_khoan tk
        LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
        LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
        WHERE LOWER(tk.email) = :email OR LOWER(tk.email) = :vnvd_email
        LIMIT 1
    ");
    $stmt->execute([':email' => $email, ':vnvd_email' => $vnvdEmail]);
    $userRow = $stmt->fetch();

    if ($action === 'get') {
        if ($userRow) {
            $displayName = !empty($userRow['kh_ho_ten']) ? $userRow['kh_ho_ten'] : (!empty($userRow['nv_ho_ten']) ? $userRow['nv_ho_ten'] : $userRow['email']);
            $displayPhone = !empty($userRow['kh_sdt']) ? $userRow['kh_sdt'] : '';

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

    // 2. Xử lý ảnh đại diện (Tệp upload hoặc Chuỗi Base64 Data URL)
    $hinhAnhUrl = trim($postData['hinh_anh_url'] ?? $postData['avatar_url'] ?? $postData['avatar'] ?? ($userRow['hinh_anh_url'] ?? ''));

    // a) Xử lý tệp tin upload trực tiếp từ form
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        if ($file['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Không được tải file ảnh quá dung lượng cho phép! (Tối đa 5MB)'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
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
    // b) Xử lý nếu gửi ảnh dưới dạng Base64 (data:image/...)
    elseif (strpos($hinhAnhUrl, 'data:image/') === 0) {
        try {
            list($type, $data) = explode(';', $hinhAnhUrl);
            list(, $data)      = explode(',', $data);
            $imgData = base64_decode($data);
            if ($imgData !== false) {
                $ext = 'png';
                if (strpos($type, 'jpeg') !== false || strpos($type, 'jpg') !== false) $ext = 'jpg';
                elseif (strpos($type, 'webp') !== false) $ext = 'webp';

                $uploadDir = __DIR__ . '/../../uploads/avatars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = 'avatar_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                file_put_contents($uploadDir . $filename, $imgData);
                $hinhAnhUrl = 'uploads/avatars/' . $filename;
            }
        } catch (Exception $_e) {}
    }

    // 3. Cập nhật vào CSDL MySQL
    if ($userRow) {
        $tkId = $userRow['tk_id'];

        // Cập nhật đường dẫn ảnh đại diện trong tai_khoan
        if (!empty($hinhAnhUrl)) {
            $upTk = $pdo->prepare("UPDATE tai_khoan SET hinh_anh_url = :url WHERE id = :id");
            $upTk->execute([':url' => $hinhAnhUrl, ':id' => $tkId]);
        }

        // Cập nhật họ tên và SĐT trong khach_hang hoặc nhan_vien
        if (!empty($userRow['kh_id'])) {
            $upKh = $pdo->prepare("UPDATE khach_hang SET ho_ten = :name, so_dien_thoai = :phone WHERE id = :id");
            $upKh->execute([':name' => $hoTen, ':phone' => $phone, ':id' => $userRow['kh_id']]);
        } elseif (!empty($userRow['nv_id'])) {
            $upNv = $pdo->prepare("UPDATE nhan_vien SET ho_ten = :name WHERE id = :id");
            $upNv->execute([':name' => $hoTen, ':id' => $userRow['nv_id']]);
        } else {
            // Tự động tạo bản ghi khach_hang mới nếu chưa có
            $insKh = $pdo->prepare("INSERT INTO khach_hang (tai_khoan_id, ho_ten, so_dien_thoai) VALUES (:tkId, :name, :phone)");
            $insKh->execute([':tkId' => $tkId, ':name' => $hoTen, ':phone' => $phone]);
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
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['so_dien_thoai'] = $phone;
        }
    }

    echo json_encode([
        'status'  => 'success',
        'message' => 'Cập nhật thông tin cá nhân & ảnh đại diện thành công!',
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
