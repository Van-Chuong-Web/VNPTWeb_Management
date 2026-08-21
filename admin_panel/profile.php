<?php
/**
 * profile.php — Hồ sơ cá nhân của Nhân viên đang đăng nhập
 * Cho phép cập nhật họ tên và đổi mật khẩu.
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$msg = '';
$msgType = 'success';
$taiKhoanId = $_SESSION['admin_user']['tai_khoan_id'];
$nhanVienId = $_SESSION['admin_user']['nhan_vien_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Cập nhật thông tin & Ảnh đại diện ──────────────────
    if ($action === 'update_info') {
        $hoTen = trim($_POST['ho_ten'] ?? '');
        $hinhAnhUrlInput = trim($_POST['hinh_anh_url'] ?? '');
        $finalAvatarUrl = $hinhAnhUrlInput;

        // Xử lý upload tệp ảnh từ máy tính (nếu có)
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $filename = 'avatar_admin_' . $taiKhoanId . '_' . time() . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['avatar_file']['tmp_name'], $targetFile)) {
                    $finalAvatarUrl = 'uploads/avatars/' . $filename;
                }
            }
        }

        if (!$hoTen) {
            $msg = 'Họ tên không được để trống.';
            $msgType = 'danger';
        } else {
            // Cập nhật họ tên nhân viên
            $stmt = $pdo->prepare('UPDATE nhan_vien SET ho_ten = :ho_ten WHERE id = :id');
            $stmt->execute([':ho_ten' => $hoTen, ':id' => $nhanVienId]);
            $_SESSION['admin_user']['ho_ten'] = $hoTen;

            // Cập nhật hinh_anh_url trong bảng tai_khoan
            $stmtTk = $pdo->prepare('UPDATE tai_khoan SET hinh_anh_url = :url WHERE id = :id');
            $stmtTk->execute([':url' => $finalAvatarUrl, ':id' => $taiKhoanId]);
            $_SESSION['admin_user']['hinh_anh_url'] = $finalAvatarUrl;

            logActivity($pdo, 'Cập nhật thông tin hồ sơ & ảnh đại diện');
            $msg = '✅ Đã cập nhật thông tin cá nhân và ảnh đại diện thành công!';
        }
    }

    // ── Đổi mật khẩu ───────────────────────────────────────
    elseif ($action === 'change_password') {
        $current = $_POST['mat_khau_hien_tai'] ?? '';
        $new     = $_POST['mat_khau_moi']      ?? '';
        $confirm = $_POST['xac_nhan_mat_khau']  ?? '';

        $stmt = $pdo->prepare('SELECT mat_khau_hash FROM tai_khoan WHERE id = :id');
        $stmt->execute([':id' => $taiKhoanId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, $row['mat_khau_hash'])) {
            $msg = 'Mật khẩu hiện tại không chính xác.';
            $msgType = 'danger';
        } elseif (strlen($new) < 6) {
            $msg = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            $msgType = 'danger';
        } elseif ($new !== $confirm) {
            $msg = 'Xác nhận mật khẩu mới không khớp.';
            $msgType = 'danger';
        } else {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE tai_khoan SET mat_khau_hash = :hash WHERE id = :id');
            $stmt->execute([':hash' => $hash, ':id' => $taiKhoanId]);
            logActivity($pdo, 'Đã đổi mật khẩu tài khoản');
            $msg = '✅ Đã đổi mật khẩu thành công!';
        }
    }
}

// ── Lấy thông tin hiện tại ─────────────────────────────────
$stmt = $pdo->prepare(
    "SELECT nv.ho_ten, tk.email, tk.hinh_anh_url, vt.ten_vai_tro, tk.created_at
       FROM nhan_vien nv
       JOIN tai_khoan tk ON tk.id = nv.tai_khoan_id
       JOIN vai_tro vt ON vt.id = tk.vai_tro_id
      WHERE nv.id = :id"
);
$stmt->execute([':id' => $nhanVienId]);
$me = $stmt->fetch();

$roleLabelMap = [
    'quan_tri_vien'      => 'Quản trị viên',
    'nhan_vien_ban_hang' => 'NV Bán hàng',
    'bien_tap_vien'      => 'Biên tập viên',
];

$pageTitle  = 'Hồ sơ cá nhân';
$activeMenu = 'profile';
require_once __DIR__ . '/header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-id-badge me-2 text-primary"></i>Thông tin tài khoản &amp; Ảnh đại diện</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_info">
                    
                    <!-- Avatar Preview Container -->
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded border">
                        <div class="position-relative">
                            <?php 
                            $myAvatar = !empty($me['hinh_anh_url']) ? $me['hinh_anh_url'] : '';
                            if (!empty($myAvatar)) {
                                if (!str_starts_with($myAvatar, 'http') && !str_starts_with($myAvatar, '/') && !str_starts_with($myAvatar, '../')) {
                                    $myAvatar = '../' . $myAvatar;
                                }
                            }
                            ?>
                            <?php if (!empty($myAvatar)): ?>
                            <img src="<?= htmlspecialchars($myAvatar) ?>" id="avatarPreview" class="rounded-circle border border-primary border-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" alt="Avatar" onerror="this.style.display='none'; if(document.getElementById('avatarFallback')) document.getElementById('avatarFallback').style.display='flex';">
                            <div id="avatarFallback" class="rounded-circle bg-primary text-white d-none align-items-center justify-content-center fw-bold fs-3 border border-3 border-white shadow-sm" style="width: 80px; height: 80px;">
                                <?= htmlspecialchars(mb_substr($me['ho_ten'], 0, 1, 'UTF-8')) ?>
                            </div>
                            <?php else: ?>
                            <div id="avatarFallback" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3 border border-3 border-white shadow-sm" style="width: 80px; height: 80px;">
                                <?= htmlspecialchars(mb_substr($me['ho_ten'], 0, 1, 'UTF-8')) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($me['ho_ten']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($me['email']) ?></div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">
                                <?= htmlspecialchars($roleLabelMap[$me['ten_vai_tro']] ?? $me['ten_vai_tro']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($me['ho_ten']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tải lên Ảnh Đại Diện mới (từ máy tính)</label>
                        <input type="file" name="avatar_file" id="avatarFileInput" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text">Hỗ trợ các định dạng: JPG, PNG, WEBP, GIF (Tối đa 5MB).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Hoặc Nhập Đường Dẫn URL Ảnh Đại Diện</label>
                        <input type="text" name="hinh_anh_url" id="hinh_anh_url" class="form-control form-control-sm" placeholder="uploads/avatars/avatar.jpg" value="<?= htmlspecialchars($me['hinh_anh_url'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu thông tin &amp; Ảnh đại diện
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-lock me-2 text-primary"></i>Đổi mật khẩu</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <input type="password" name="mat_khau_hien_tai" id="mat_khau_hien_tai" class="form-control" required>
                            <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="mat_khau_hien_tai" aria-label="Hiện/Ẩn mật khẩu">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mật khẩu mới</label>
                        <div class="input-group">
                            <input type="password" name="mat_khau_moi" id="mat_khau_moi" class="form-control" minlength="6" required>
                            <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="mat_khau_moi" aria-label="Hiện/Ẩn mật khẩu">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <input type="password" name="xac_nhan_mat_khau" id="xac_nhan_mat_khau" class="form-control" minlength="6" required>
                            <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="xac_nhan_mat_khau" aria-label="Hiện/Ẩn mật khẩu">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning text-white fw-bold">
                        <i class="fa-solid fa-key me-1"></i>Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarFileInput');
    const urlInput = document.getElementById('hinh_anh_url');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarFallback = document.getElementById('avatarFallback');

    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                if (urlInput) {
                    urlInput.value = 'uploads/avatars/' + file.name;
                }
                if (avatarPreview) {
                    avatarPreview.src = URL.createObjectURL(file);
                    avatarPreview.style.display = 'block';
                    if (avatarFallback) avatarFallback.classList.add('d-none');
                }
            }
        });
    }

    // Toggle hiện / ẩn mật khẩu (Mắt xem mật khẩu)
    document.querySelectorAll('.toggle-pwd').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
