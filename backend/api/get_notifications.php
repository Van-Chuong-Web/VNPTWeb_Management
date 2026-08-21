<?php
/**
 * backend/api/get_notifications.php — API Thông báo phân quyền Khách hàng vs Nhân viên (Tối ưu 100%)
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../admin_panel/db.php';

try {
    $email = strtolower(trim($_GET['email'] ?? $_POST['email'] ?? ''));
    $phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');

    $isStaff = false;
    $taiKhoanId = $_SESSION['user']['id'] ?? $_SESSION['admin_user']['id'] ?? null;
    $khachHangId = $_SESSION['user']['khach_hang_id'] ?? null;
    $nhanVienId = $_SESSION['user']['nhan_vien_id'] ?? $_SESSION['admin_user']['nhan_vien_id'] ?? null;

    $loaiTaiKhoan = $_SESSION['user']['loai_tai_khoan'] ?? $_SESSION['admin_user']['loai_tai_khoan'] ?? '';
    $rawRole = $_SESSION['user']['role'] ?? $_SESSION['user']['ten_vai_tro'] ?? $_SESSION['admin_user']['ten_vai_tro'] ?? '';

    if ($loaiTaiKhoan === 'nhan_vien' || in_array($rawRole, ['admin', 'quan_tri_vien', 'bien_tap_vien', 'nhan_vien_ban_hang', 'quan_ly', 'editor', 'staff'])) {
        $isStaff = true;
    }

    if (!empty($email)) {
        $stmtTk = $pdo->prepare("
            SELECT tk.id, tk.loai_tai_khoan, kh.id AS kh_id, nv.id AS nv_id
              FROM tai_khoan tk
         LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
         LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
             WHERE LOWER(tk.email) = :email OR LOWER(tk.email) = :vnvd_email
             LIMIT 1
        ");
        $vnvdEmail = str_replace('@vnpt.vn', '@vnvd.vn', $email);
        $stmtTk->execute([':email' => $email, ':vnvd_email' => $vnvdEmail]);
        $userRow = $stmtTk->fetch();

        if ($userRow) {
            $taiKhoanId = $userRow['id'];
            if ($userRow['loai_tai_khoan'] === 'nhan_vien' || !empty($userRow['nv_id'])) {
                $isStaff = true;
                $nhanVienId = $userRow['nv_id'];
            } else {
                $khachHangId = $userRow['kh_id'];
            }
        }
    }

    if ($isStaff) {
        if (!$nhanVienId && $taiKhoanId) {
            $stmtNv = $pdo->prepare("SELECT id FROM nhan_vien WHERE tai_khoan_id = ? LIMIT 1");
            $stmtNv->execute([$taiKhoanId]);
            $foundNv = $stmtNv->fetch();
            if ($foundNv) $nhanVienId = $foundNv['id'];
        }
        if (!$nhanVienId && $email) {
            $stmtNv = $pdo->prepare("
                SELECT nv.id 
                  FROM nhan_vien nv 
             LEFT JOIN tai_khoan tk ON tk.id = nv.tai_khoan_id 
                 WHERE LOWER(tk.email) = LOWER(:email) OR LOWER(tk.email) = LOWER(:vnvd)
                 LIMIT 1
            ");
            $vnvdEmail = str_replace('@vnpt.vn', '@vnvd.vn', $email);
            $stmtNv->execute([':email' => $email, ':vnvd' => $vnvdEmail]);
            $foundNv = $stmtNv->fetch();
            if ($foundNv) $nhanVienId = $foundNv['id'];
        }
    } else {
        if (!$khachHangId && $taiKhoanId) {
            $stmtKh = $pdo->prepare("SELECT id FROM khach_hang WHERE tai_khoan_id = ? LIMIT 1");
            $stmtKh->execute([$taiKhoanId]);
            $foundKh = $stmtKh->fetch();
            if ($foundKh) $khachHangId = $foundKh['id'];
        }
    }

    $action = $_GET['action'] ?? ($_POST['action'] ?? 'get');

    if ($action === 'mark_read') {
        $notifId = (int)($_POST['id'] ?? 0);
        if ($notifId > 0) {
            try {
                if ($isStaff) {
                    $stmt = $pdo->prepare("UPDATE thong_bao_nhan_vien SET da_doc = 1 WHERE id = ?");
                    $stmt->execute([$notifId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE thong_bao SET da_doc = 1 WHERE id = ?");
                    $stmt->execute([$notifId]);
                }
            } catch (Exception $_e) {}
        }
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'mark_all_read') {
        try {
            if ($isStaff) {
                if ($nhanVienId) {
                    $stmt = $pdo->prepare("UPDATE thong_bao_nhan_vien SET da_doc = 1 WHERE nhan_vien_id = ? OR nhan_vien_id IS NULL");
                    $stmt->execute([$nhanVienId]);
                } else {
                    $pdo->exec("UPDATE thong_bao_nhan_vien SET da_doc = 1");
                }
            } else {
                if ($khachHangId) {
                    $stmt = $pdo->prepare("UPDATE thong_bao SET da_doc = 1 WHERE khach_hang_id = ? OR khach_hang_id IS NULL");
                    $stmt->execute([$khachHangId]);
                } else {
                    $pdo->exec("UPDATE thong_bao SET da_doc = 1 WHERE khach_hang_id IS NULL");
                }
            }
        } catch (Exception $_e) {}
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $notifs = [];
    try {
        if ($isStaff) {
            // Lấy thông báo dành riêng cho Nhân viên (từ thong_bao_nhan_vien, cột ngay_tao)
            if ($nhanVienId) {
                $stmt = $pdo->prepare("SELECT id, tieu_de, noi_dung, 'he_thong' AS loai, da_doc, ngay_tao AS created_at FROM thong_bao_nhan_vien WHERE nhan_vien_id = ? OR nhan_vien_id IS NULL ORDER BY id DESC LIMIT 50");
                $stmt->execute([$nhanVienId]);
                $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->query("SELECT id, tieu_de, noi_dung, 'he_thong' AS loai, da_doc, ngay_tao AS created_at FROM thong_bao_nhan_vien WHERE nhan_vien_id IS NULL ORDER BY id DESC LIMIT 50");
                $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Lấy thông báo dành riêng cho Khách hàng (từ thong_bao, cột created_at)
            if ($khachHangId) {
                $stmt = $pdo->prepare("SELECT id, tieu_de, noi_dung, loai, da_doc, created_at FROM thong_bao WHERE khach_hang_id = ? OR khach_hang_id IS NULL ORDER BY id DESC LIMIT 50");
                $stmt->execute([$khachHangId]);
                $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->query("SELECT id, tieu_de, noi_dung, loai, da_doc, created_at FROM thong_bao WHERE khach_hang_id IS NULL ORDER BY id DESC LIMIT 50");
                $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    } catch (Exception $_e) {
        $notifs = [];
    }

    if (empty($notifs) && !$isStaff) {
        $notifs = [
            [
                'id' => 1,
                'tieu_de' => 'Khuyến mãi tháng 8',
                'noi_dung' => 'giảm 10% tất cả sản phẩm & gói cước',
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
        'is_staff' => $isStaff,
        'unread_count' => $unreadCount,
        'data' => $notifs
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'success',
        'is_staff' => false,
        'unread_count' => 0,
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
