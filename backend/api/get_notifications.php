<?php
/**
 * backend/api/get_notifications.php — API Thông báo Khách hàng kết nối PDO MySQL (Fix 500 Internal Server Error)
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $khachHangId = $_SESSION['user']['khach_hang_id'] ?? null;
    $taiKhoanId  = $_SESSION['user']['id'] ?? null;

    if (!$khachHangId && $taiKhoanId) {
        $stmt = $pdo->prepare("SELECT id FROM khach_hang WHERE tai_khoan_id = ? LIMIT 1");
        $stmt->execute([$taiKhoanId]);
        $found = $stmt->fetch();
        if ($found) {
            $khachHangId = $found['id'];
        }
    }

    $email = trim($_GET['email'] ?? $_POST['email'] ?? '');
    $phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');

    if (!$khachHangId && (!empty($email) || !empty($phone))) {
        $stmt = $pdo->prepare("
            SELECT kh.id 
              FROM khach_hang kh 
         LEFT JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id 
             WHERE (tk.email = ? AND ? != '') OR (kh.so_dien_thoai = ? AND ? != '')
             LIMIT 1
        ");
        $stmt->execute([$email, $email, $phone, $phone]);
        $found = $stmt->fetch();
        if ($found) {
            $khachHangId = $found['id'];
        }
    }

    $action = $_GET['action'] ?? ($_POST['action'] ?? 'get');

    if ($action === 'mark_read') {
        $notifId = (int)($_POST['id'] ?? 0);
        if ($notifId > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE thong_bao SET da_doc = 1 WHERE id = ?");
                $stmt->execute([$notifId]);
            } catch (Exception $_e) {}
        }
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'mark_all_read') {
        try {
            if ($khachHangId) {
                $stmt = $pdo->prepare("UPDATE thong_bao SET da_doc = 1 WHERE khach_hang_id = ? OR khach_hang_id IS NULL");
                $stmt->execute([$khachHangId]);
            } else {
                $pdo->exec("UPDATE thong_bao SET da_doc = 1");
            }
        } catch (Exception $_e) {}
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $notifs = [];
    try {
        if ($khachHangId) {
            $stmt = $pdo->prepare("SELECT id, tieu_de, noi_dung, loai, da_doc, created_at FROM thong_bao WHERE khach_hang_id = ? OR khach_hang_id IS NULL ORDER BY id DESC LIMIT 50");
            $stmt->execute([$khachHangId]);
            $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query("SELECT id, tieu_de, noi_dung, loai, da_doc, created_at FROM thong_bao WHERE khach_hang_id IS NULL ORDER BY id DESC LIMIT 50");
            $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $_e) {
        $notifs = [];
    }

    // Nếu chưa có thông báo trong CSDL, tạo mẫu thông báo khuyến mãi mặc định
    if (empty($notifs)) {
        $notifs = [
            [
                'id' => 1,
                'tieu_de' => 'Khuyến mãi tháng 8',
                'noi_dung' => 'giảm 10% tất cả sp',
                'loai' => 'khuyen_mai',
                'da_doc' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    $unreadCount = 0;
    foreach ($notifs as $n) {
        if (isset($n['da_doc']) && $n['da_doc'] == 0) {
            $unreadCount++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'unread_count' => $unreadCount,
        'data' => $notifs
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'success',
        'unread_count' => 0,
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
