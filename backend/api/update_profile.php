<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db/database.php';

try {
    $db = new Database();

    $email = trim($_POST['email'] ?? $_GET['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');

    $hoTen = trim($firstName . ' ' . $lastName);

    if (empty($email)) {
        $email = $_SESSION['user']['email'] ?? '';
    }

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp email tài khoản!'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $action = $_GET['action'] ?? 'update';

    if ($action === 'get') {
        if (!empty($email)) {
            $found = $db->select("
                SELECT kh.id AS kh_id, kh.ho_ten, kh.so_dien_thoai, tk.id AS tk_id, tk.email, tk.hinh_anh_url
                  FROM tai_khoan tk
             LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
                 WHERE tk.email = ?
                 LIMIT 1
            ", [$email]);

            if (!empty($found)) {
                $row = $found[0];
                echo json_encode([
                    'status' => 'success',
                    'user' => [
                        'email' => $row['email'],
                        'ho_ten' => $row['ho_ten'],
                        'so_dien_thoai' => $row['so_dien_thoai'],
                        'phone' => $row['so_dien_thoai'],
                        'hinh_anh_url' => $row['hinh_anh_url'] ?? '',
                        'avatar' => $row['hinh_anh_url'] ?? ''
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit();
            }
        }
    }

    // Find customer by email
    $found = $db->select("
        SELECT kh.id AS kh_id, tk.id AS tk_id
          FROM khach_hang kh
     LEFT JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id
         WHERE tk.email = ?
         LIMIT 1
    ", [$email]);

    // Handle avatar upload if present
    $hinhAnhUrl = trim($_POST['hinh_anh_url'] ?? $_POST['avatar_url'] ?? '');
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

    if (!empty($found)) {
        $khId = $found[0]['kh_id'];
        $tkId = $found[0]['tk_id'];
        $db->execute("UPDATE khach_hang SET ho_ten = ?, so_dien_thoai = ? WHERE id = ?", [$hoTen, $phone, $khId]);
        if (!empty($hinhAnhUrl) && $tkId) {
            $db->execute("UPDATE tai_khoan SET hinh_anh_url = ? WHERE id = ?", [$hinhAnhUrl, $tkId]);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Cập nhật hồ sơ cá nhân thành công!',
        'user' => [
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'ho_ten' => $hoTen,
            'phone' => $phone,
            'so_dien_thoai' => $phone,
            'hinh_anh_url' => $hinhAnhUrl,
            'avatar' => $hinhAnhUrl
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
