<?php
/**
 * login.php — Đăng nhập Admin Panel
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu đã đăng nhập trước đó, tự động chuyển đến trang chủ Admin tương ứng
if (isset($_SESSION['admin_user']['email']) && !empty($_SESSION['admin_user']['email'])) {
    $role = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
    if (in_array($role, ['quan_tri_vien', 'superadmin', 'admin'])) {
        header('Location: index.php');
    } elseif (in_array($role, ['bien_tap_vien', 'editor'])) {
        header('Location: posts.php');
    } else {
        header('Location: orders.php');
    }
    exit;
}

require_once __DIR__ . '/db.php';

$msg = '';
$msgType = 'danger';
$email = '';

$errorParam = $_GET['error'] ?? '';
if ($errorParam === 'required') {
    $msg = '🔒 Vui lòng đăng nhập tài khoản Quản trị / Nhân viên để tiếp tục truy cập.';
    $msgType = 'warning';
} elseif ($errorParam === 'locked') {
    $msg = '⚠️ Tài khoản nhân viên của bạn hiện đã bị khóa. Vui lòng liên hệ Quản trị hệ thống.';
    $msgType = 'danger';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['mat_khau'] ?? '');

    if (!$email || !$password) {
        $msg = 'Vui lòng nhập đầy đủ Email và Mật khẩu.';
        $msgType = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT tk.id AS tai_khoan_id, tk.email, tk.mat_khau_hash, tk.trang_thai, vt.ten_vai_tro, nv.id AS nhan_vien_id, nv.ho_ten
                  FROM tai_khoan tk
                  JOIN vai_tro vt ON vt.id = tk.vai_tro_id
                  JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
                 WHERE tk.email = :email AND tk.loai_tai_khoan = 'nhan_vien'
                 LIMIT 1
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                $msg = 'Email hoặc mật khẩu không chính xác hoặc bạn không có quyền truy cập.';
            } elseif ($user['trang_thai'] !== 'hoat_dong') {
                $msg = 'Tài khoản nhân viên của bạn hiện đã bị khóa.';
            } else {
                $ok = password_verify($password, $user['mat_khau_hash']);
                if (!$ok && $user['mat_khau_hash'] === $password) {
                    $ok = true;
                    // Cập nhật băm bcrypt nếu mật khẩu đang là text thuần
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $updateStmt = $pdo->prepare("UPDATE tai_khoan SET mat_khau_hash = :hash WHERE id = :id");
                    $updateStmt->execute([':hash' => $newHash, ':id' => $user['tai_khoan_id']]);
                }

                if ($ok) {
                    $_SESSION['admin_user'] = [
                        'id'           => $user['tai_khoan_id'],
                        'tai_khoan_id' => $user['tai_khoan_id'],
                        'nhan_vien_id' => $user['nhan_vien_id'],
                        'email'        => $user['email'],
                        'ho_ten'       => $user['ho_ten'],
                        'ten_vai_tro'  => $user['ten_vai_tro'],
                    ];
                    header('Location: index.php');
                    exit;
                } else {
                    $msg = 'Email hoặc mật khẩu không chính xác.';
                }
            }
        } catch (PDOException $e) {
            $msg = 'Lỗi kết nối CSDL: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | VNPT Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
        }
        .brand-logo {
            color: #0d6efd;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa-solid fa-shield-halved me-2"></i>VNPT Admin
    </div>
    <p class="text-center text-muted mb-4" style="font-size: 14px;">Đăng nhập hệ thống quản trị viên</p>

    <?php if ($msg): ?>
        <div class="alert alert-danger py-2" style="font-size: 14px;">
            <i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size: 14px;">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" placeholder="admin@vnpt.vn" required autofocus>
        </div>
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-semibold mb-0" style="font-size: 14px;">Mật khẩu</label>
                <a href="forgot_password.php" class="text-decoration-none small text-primary fw-semibold">Quên mật khẩu?</a>
            </div>
            <input type="password" name="mat_khau" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="fa-solid fa-right-to-bracket me-1"></i>Đăng nhập
        </button>
    </form>
</div>

</body>
</html>
