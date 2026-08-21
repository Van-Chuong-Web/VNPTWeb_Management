<?php
/**
 * admins.php — Quản lý tài khoản Quản trị viên
 * CRUD: Thêm / Sửa / Xóa / Khóa-Mở khóa
 * Mật khẩu mã hóa bằng password_hash() / password_verify()
 */

require_once __DIR__ . '/auth_check.php';

// Kiểm tra quyền: Chỉ Quản trị viên tối cao mới được vào Quản lý Nhân viên & Phân quyền
if (!in_array($_SESSION['admin_user']['ten_vai_tro'] ?? '', ['quan_tri_vien', 'admin', 'superadmin'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// ── Danh sách vai trò dành cho nhân viên (lấy động từ bảng vai_tro) ──
$rolesList = $pdo->query(
    "SELECT id, ten_vai_tro FROM vai_tro WHERE nhom_vai_tro = 'nhan_vien' ORDER BY id"
)->fetchAll();
$roleLabelMap = [
    'quan_tri_vien'      => ['Quản trị viên',   'danger'],
    'nhan_vien_ban_hang' => ['NV Bán hàng',     'primary'],
    'bien_tap_vien'      => ['Biên tập viên',   'secondary'],
];

// ── Xử lý Form (POST) ────────────────────────────────────
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── THÊM admin ────────────────────────────────────────
    if ($action === 'add') {
        $hoTen    = trim($_POST['ho_ten']    ?? '');
        $email    = trim($_POST['email']     ?? '');
        $matKhau  = trim($_POST['mat_khau']  ?? '');
        $vaiTroId = (int)($_POST['vai_tro_id'] ?? 0);
        $trangThai= $_POST['trang_thai']     ?? 'hoat_dong';

        if (!$hoTen || !$email || !$matKhau || $vaiTroId <= 0) {
            $msg = 'Vui lòng điền đầy đủ Họ tên, Email, Mật khẩu và chọn Vai trò.';
            $msgType = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $msg = '⚠️ Địa chỉ Email không hợp lệ! Vui lòng nhập đúng định dạng (ví dụ: nhanvien@vnpt.vn).';
            $msgType = 'danger';
        } elseif (strlen($matKhau) < 6) {
            $msg = 'Mật khẩu phải có ít nhất 6 ký tự.';
            $msgType = 'danger';
        } else {
            // Kiểm tra trùng Email
            $stmtCheckEmail = $pdo->prepare("SELECT id FROM tai_khoan WHERE email = :email LIMIT 1");
            $stmtCheckEmail->execute([':email' => $email]);
            if ($stmtCheckEmail->fetch()) {
                $msg = '⚠️ Địa chỉ email này đã được sử dụng bởi tài khoản khác.';
                $msgType = 'danger';
            } else {
                try {
                    $hash = $matKhau;
                    $pdo->beginTransaction();

                $stmt = $pdo->prepare(
                    "INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email)
                     VALUES (:email, :hash, 'nhan_vien', :vai_tro_id, :trang_thai, 1)"
                );
                $stmt->execute([
                    ':email'      => $email,
                    ':hash'       => $hash,
                    ':vai_tro_id' => $vaiTroId,
                    ':trang_thai' => $trangThai,
                ]);
                $tkId = $pdo->lastInsertId();

                $stmtNv = $pdo->prepare("INSERT INTO nhan_vien (tai_khoan_id, ho_ten) VALUES (:tk_id, :ho_ten)");
                $stmtNv->execute([':tk_id' => $tkId, ':ho_ten' => $hoTen]);

                $pdo->commit();
                logActivity($pdo, "Đã thêm nhân viên mới: $hoTen ($email)");
                $msg = "✅ Đã thêm quản trị viên <strong>" . htmlspecialchars($hoTen) . "</strong> thành công!";
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $msg = 'Lỗi: ' . (str_contains($e->getMessage(), 'Duplicate') ? 'Email đã tồn tại trong hệ thống.' : $e->getMessage());
                $msgType = 'danger';
            }
            }
        }
    }

    // ── SỬA admin ─────────────────────────────────────────
    elseif ($action === 'edit') {
        $id       = (int)($_POST['id']         ?? 0); // tai_khoan_id
        $hoTen    = trim($_POST['ho_ten']       ?? '');
        $email    = trim($_POST['email']        ?? '');
        $vaiTroId = (int)($_POST['vai_tro_id'] ?? 0);
        $trangThai= $_POST['trang_thai']        ?? 'hoat_dong';
        $matKhau  = trim($_POST['mat_khau']     ?? '');

        if (!$id || !$hoTen || !$email || $vaiTroId <= 0) {
            $msg = 'Dữ liệu không hợp lệ. Vui lòng chọn vai trò.';
            $msgType = 'danger';
        } else {
            try {
                $pdo->beginTransaction();

                $stmtNv = $pdo->prepare("UPDATE nhan_vien SET ho_ten = :ho_ten WHERE tai_khoan_id = :id");
                $stmtNv->execute([':ho_ten' => $hoTen, ':id' => $id]);

                if ($matKhau !== '') {
                    if (strlen($matKhau) < 6) {
                        throw new Exception('Mật khẩu mới phải có ít nhất 6 ký tự.');
                    }
                    $hash = $matKhau; // Lưu dạng mật khẩu thường theo yêu cầu Admin
                    $stmt = $pdo->prepare(
                        'UPDATE tai_khoan SET email=:email, mat_khau_hash=:hash,
                         vai_tro_id=:vai_tro_id, trang_thai=:trang_thai WHERE id=:id AND loai_tai_khoan=\'nhan_vien\''
                    );
                    $stmt->execute([
                        ':email'      => $email,
                        ':hash'       => $hash,
                        ':vai_tro_id' => $vaiTroId,
                        ':trang_thai' => $trangThai,
                        ':id'         => $id,
                    ]);
                } else {
                    $stmt = $pdo->prepare(
                        'UPDATE tai_khoan SET email=:email,
                         vai_tro_id=:vai_tro_id, trang_thai=:trang_thai WHERE id=:id AND loai_tai_khoan=\'nhan_vien\''
                    );
                    $stmt->execute([
                        ':email'      => $email,
                        ':vai_tro_id' => $vaiTroId,
                        ':trang_thai' => $trangThai,
                        ':id'         => $id,
                    ]);
                }

                $pdo->commit();
                logActivity($pdo, "Đã cập nhật thông tin nhân viên: $hoTen ($email)");
                $msg = "✅ Đã cập nhật thông tin quản trị viên <strong>" . htmlspecialchars($hoTen) . "</strong>!";
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $msg = 'Lỗi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }

    // ── XÓA admin ─────────────────────────────────────────
    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM tai_khoan WHERE id = :id AND loai_tai_khoan = 'nhan_vien'");
                $stmt->execute([':id' => $id]);
                logActivity($pdo, "Đã xóa tài khoản nhân viên (ID: $id)");
                $msg = '🗑️ Đã xóa quản trị viên thành công.';
            } catch (PDOException $e) {
                $msg = 'Lỗi khi xóa: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }

    // ── KHÓA / MỞ KHÓA ───────────────────────────────────
    elseif ($action === 'toggle_status') {
        $id         = (int)($_POST['id']          ?? 0);
        $newStatus  = $_POST['new_status']         ?? 'hoat_dong';
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE tai_khoan SET trang_thai = :ts WHERE id = :id AND loai_tai_khoan = 'nhan_vien'");
                $stmt->execute([':ts' => $newStatus, ':id' => $id]);
                $label = $newStatus === 'khoa' ? 'Khóa' : 'Mở khóa';
                logActivity($pdo, "Đã $label tài khoản nhân viên (ID: $id)");
                $msg = "🔒 Đã <strong>$label</strong> tài khoản thành công.";
            } catch (PDOException $e) {
                $msg = 'Lỗi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }
}

// ── Lấy danh sách admins ──────────────────────────────────
$search = trim($_GET['q'] ?? '');
$filterRole   = $_GET['vai_tro']    ?? '';
$filterStatus = $_GET['trang_thai'] ?? '';

$currentAdminId = (int)($_SESSION['admin_user']['id'] ?? 0);
$currentAdminEmail = trim($_SESSION['admin_user']['email'] ?? '');

$sql    = "SELECT nv.id AS nhan_vien_id, tk.id AS tai_khoan_id, tk.id AS id, nv.ho_ten, tk.email, tk.hinh_anh_url,
                  tk.mat_khau_hash, tk.vai_tro_id, vt.ten_vai_tro AS vai_tro, tk.trang_thai, tk.created_at
             FROM nhan_vien nv
             JOIN tai_khoan tk ON tk.id = nv.tai_khoan_id
             JOIN vai_tro vt ON vt.id = tk.vai_tro_id
            WHERE tk.loai_tai_khoan = 'nhan_vien'";
$params = [];

if ($currentAdminId > 0) {
    $sql .= ' AND tk.id != :cur_id';
    $params[':cur_id'] = $currentAdminId;
} elseif ($currentAdminEmail !== '') {
    $sql .= ' AND tk.email != :cur_email';
    $params[':cur_email'] = $currentAdminEmail;
}

if ($search !== '') {
    $sql .= ' AND (nv.ho_ten LIKE :q OR tk.email LIKE :q)';
    $params[':q'] = "%$search%";
}
if ($filterRole !== '') {
    $sql .= ' AND vt.ten_vai_tro = :vt';
    $params[':vt'] = $filterRole;
}
if ($filterStatus !== '') {
    $sql .= ' AND tk.trang_thai = :ts';
    $params[':ts'] = $filterStatus;
}
$sql .= ' ORDER BY tk.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$admins = $stmt->fetchAll();

// Đếm tổng
$total      = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'nhan_vien'")->fetchColumn();
$totalActive= $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'nhan_vien' AND trang_thai = 'hoat_dong'")->fetchColumn();
$totalLocked= $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'nhan_vien' AND trang_thai = 'khoa'")->fetchColumn();

// ── Layout ────────────────────────────────────────────────
$pageTitle  = 'Quản lý Nhân viên';
$activeMenu = 'admins';
require_once __DIR__ . '/header.php';
?>

<!-- ── Thống kê nhanh ─────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6)">
            <i class="fa-solid fa-user-shield stat-icon"></i>
            <div class="stat-value"><?= $total ?></div>
            <div class="stat-label">Tổng quản trị viên</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#34d399)">
            <i class="fa-solid fa-circle-check stat-icon"></i>
            <div class="stat-value"><?= $totalActive ?></div>
            <div class="stat-label">Đang hoạt động</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#f87171)">
            <i class="fa-solid fa-lock stat-icon"></i>
            <div class="stat-value"><?= $totalLocked ?></div>
            <div class="stat-label">Đã khóa</div>
        </div>
    </div>
</div>

<!-- ── Alert thông báo ───────────────────────────────────── -->
<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── Card danh sách ────────────────────────────────────── -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="fa-solid fa-user-shield me-2 text-primary"></i>Danh sách Quản trị viên</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="fa-solid fa-plus me-1"></i> Thêm Admin
        </button>
    </div>

    <!-- Bộ lọc / Tìm kiếm -->
    <div class="card-body border-bottom pb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-5">
                <label class="form-label small fw-semibold mb-1">Tìm kiếm</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" name="q" class="form-control"
                           placeholder="Họ tên hoặc email..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">Vai trò</label>
                <select name="vai_tro" class="form-select form-select-sm">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($rolesList as $r):
                        [$rl] = $roleLabelMap[$r['ten_vai_tro']] ?? [$r['ten_vai_tro']];
                    ?>
                        <option value="<?= htmlspecialchars($r['ten_vai_tro']) ?>" <?= $filterRole===$r['ten_vai_tro']?'selected':'' ?>><?= htmlspecialchars($rl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1">Trạng thái</label>
                <select name="trang_thai" class="form-select form-select-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="hoat_dong" <?= $filterStatus==='hoat_dong'?'selected':'' ?>>Hoạt động</option>
                    <option value="khoa"      <?= $filterStatus==='khoa'?'selected':'' ?>>Đã khóa</option>
                </select>
            </div>
            <div class="col-sm-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fa-solid fa-filter me-1"></i>Lọc
                </button>
                <a href="admins.php" class="btn btn-outline-secondary btn-sm" title="Xóa bộ lọc">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Mật khẩu</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th style="width:160px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Không tìm thấy quản trị viên nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admins as $i => $a): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php 
                                $adminAvatar = !empty($a['hinh_anh_url']) ? $a['hinh_anh_url'] : '';
                                if (!empty($adminAvatar)) {
                                    if (!str_starts_with($adminAvatar, 'http') && !str_starts_with($adminAvatar, '/') && !str_starts_with($adminAvatar, '../')) {
                                        $adminAvatar = '../' . $adminAvatar;
                                    }
                                }
                                ?>
                                <?php if (!empty($adminAvatar)): ?>
                                <img src="<?= htmlspecialchars($adminAvatar) ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;" class="border border-primary border-2 shadow-sm" alt="Avatar" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:none;align-items:center;justify-content:center;font-weight:700;color:#1d4ed8;font-size:13px;flex-shrink:0">
                                    <?= mb_strtoupper(mb_substr($a['ho_ten'], 0, 1, 'UTF-8')) ?>
                                </div>
                                <?php else: ?>
                                <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-weight:700;color:#1d4ed8;font-size:13px;flex-shrink:0">
                                    <?= mb_strtoupper(mb_substr($a['ho_ten'], 0, 1, 'UTF-8')) ?>
                                </div>
                                <?php endif; ?>
                                <span class="fw-medium"><?= htmlspecialchars($a['ho_ten']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td>
                            <span class="text-muted font-monospace">••••••••</span>
                        </td>
                        <td>
                            <?php
                            [$roleLabel, $roleColor] = $roleLabelMap[$a['vai_tro']] ?? [$a['vai_tro'], 'secondary'];
                            ?>
                            <span class="badge bg-<?= $roleColor ?>"><?= $roleLabel ?></span>
                        </td>
                        <td>
                            <?php if ($a['trang_thai'] === 'hoat_dong'): ?>
                                <span class="badge badge-hoat-dong">
                                    <i class="fa-solid fa-circle-check me-1"></i>Hoạt động
                                </span>
                            <?php else: ?>
                                <span class="badge badge-khoa">
                                    <i class="fa-solid fa-lock me-1"></i>Đã khóa
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size:13px">
                            <?= date('d/m/Y', strtotime($a['created_at'])) ?>
                        </td>
                        <td>
                            <!-- Nút Sửa -->
                            <button class="btn btn-outline-primary btn-action me-1"
                                    onclick="openEditModal(<?= htmlspecialchars(json_encode($a)) ?>)"
                                    title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <!-- Nút Khóa / Mở khóa -->
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('<?= $a['trang_thai']==='hoat_dong' ? 'Khóa' : 'Mở khóa' ?> tài khoản này?')">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <input type="hidden" name="new_status"
                                       value="<?= $a['trang_thai']==='hoat_dong' ? 'khoa' : 'hoat_dong' ?>">
                                <button type="submit"
                                        class="btn btn-action <?= $a['trang_thai']==='hoat_dong' ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                        title="<?= $a['trang_thai']==='hoat_dong' ? 'Khóa' : 'Mở khóa' ?>">
                                    <i class="fa-solid <?= $a['trang_thai']==='hoat_dong' ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                                </button>
                            </form>

                            <!-- Nút Xóa -->
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Xóa vĩnh viễn quản trị viên này?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-action" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer text-muted" style="font-size:13px">
        Hiển thị <strong><?= count($admins) ?></strong> / <strong><?= $total ?></strong> quản trị viên
    </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- Modal THÊM Admin                                       -->
<!-- ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>Thêm Quản trị viên
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control"
                               placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               placeholder="admin@vnpt.vn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="mat_khau" class="form-control"
                               placeholder="Tối thiểu 6 ký tự" required minlength="6">
                        <div class="form-text">Mật khẩu sẽ được mã hóa bằng bcrypt.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Vai trò</label>
                            <select name="vai_tro_id" class="form-select" required>
                                <option value="">-- Chọn vai trò --</option>
                                <?php foreach ($rolesList as $r):
                                    [$rl] = $roleLabelMap[$r['ten_vai_tro']] ?? [$r['ten_vai_tro']];
                                ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($rl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="hoat_dong">Hoạt động</option>
                                <option value="khoa">Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>Thêm Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- Modal SỬA Admin                                        -->
<!-- ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-pen text-warning me-2"></i>Sửa Quản trị viên
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" id="editHoTen" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu mới</label>
                        <input type="password" name="mat_khau" class="form-control"
                               placeholder="Để trống = giữ nguyên mật khẩu cũ" minlength="6">
                        <div class="form-text">Chỉ điền nếu muốn đổi mật khẩu.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Vai trò</label>
                            <select name="vai_tro_id" id="editVaiTro" class="form-select" required>
                                <?php foreach ($rolesList as $r):
                                    [$rl] = $roleLabelMap[$r['ten_vai_tro']] ?? [$r['ten_vai_tro']];
                                ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($rl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select name="trang_thai" id="editTrangThai" class="form-select">
                                <option value="hoat_dong">Hoạt động</option>
                                <option value="khoa">Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── JS: Mở modal Sửa với dữ liệu ─────────────────────── -->
<script>
function openEditModal(data) {
    document.getElementById('editId').value        = data.id;
    document.getElementById('editHoTen').value     = data.ho_ten;
    document.getElementById('editEmail').value     = data.email;
    document.getElementById('editVaiTro').value    = data.vai_tro_id;
    document.getElementById('editTrangThai').value = data.trang_thai;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
