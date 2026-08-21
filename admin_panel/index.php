<?php
/**
 * index.php — Dashboard Admin Panel
 */
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

// CHỈ CHO PHÉP QUẢN TRỊ VIÊN (ADMIN) TRUY CẬP DASHBOARD TỔNG QUAN
$currentRole = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
$isAdminRole = in_array($currentRole, ['quan_tri_vien', 'superadmin', 'admin']);

if (!$isAdminRole) {
    if (in_array($currentRole, ['bien_tap_vien', 'editor'])) {
        header('Location: posts.php');
        exit;
    } elseif (in_array($currentRole, ['nhan_vien_ban_hang', 'quan_ly'])) {
        header('Location: orders.php');
        exit;
    } else {
        header('Location: profile.php');
        exit;
    }
}

// ── Thống kê ─────────────────────────────────────────────
$totalAdmins     = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'nhan_vien'")->fetchColumn();
$activeAdmins    = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'nhan_vien' AND trang_thai = 'hoat_dong'")->fetchColumn();
$totalCustomers  = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'khach_hang'")->fetchColumn();
$activeCustomers = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'khach_hang' AND trang_thai = 'hoat_dong'")->fetchColumn();
$lockedCustomers = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'khach_hang' AND trang_thai = 'khoa'")->fetchColumn();
$newThisMonth    = $pdo->query("SELECT COUNT(*) FROM tai_khoan WHERE loai_tai_khoan = 'khach_hang' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();

// Thống kê hóa đơn / doanh thu
$tongDoanhThu   = $pdo->query("SELECT COALESCE(SUM(tong_thanh_toan),0) FROM don_hang WHERE trang_thai_don_hang = 'hoan_thanh'")->fetchColumn();
$donChoXacNhan  = $pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai_don_hang = 'cho_xac_nhan'")->fetchColumn();
$tongDonHang    = $pdo->query("SELECT COUNT(*) FROM don_hang")->fetchColumn();
$totalProducts  = $pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();

// 5 khách hàng mới nhất
$latestCustomers = $pdo->query("
    SELECT kh.id, kh.ho_ten, tk.email, tk.trang_thai, tk.created_at
      FROM khach_hang kh
      JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id
     ORDER BY tk.created_at DESC LIMIT 5
")->fetchAll();

// 5 admin mới nhất
$latestAdmins    = $pdo->query("
    SELECT nv.id, nv.ho_ten, tk.email, tk.trang_thai, tk.created_at
      FROM nhan_vien nv
      JOIN tai_khoan tk ON tk.id = nv.tai_khoan_id
     ORDER BY tk.created_at DESC LIMIT 5
")->fetchAll();

$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';
require_once __DIR__ . '/header.php';
?>

<!-- ── Stat cards ────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1d4ed8,#60a5fa)">
            <i class="fa-solid fa-user-shield stat-icon"></i>
            <div class="stat-value"><?= $totalAdmins ?></div>
            <div class="stat-label">Tổng Quản trị viên</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#c084fc)">
            <i class="fa-solid fa-users stat-icon"></i>
            <div class="stat-value"><?= $totalCustomers ?></div>
            <div class="stat-label">Tổng Khách hàng</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#34d399)">
            <i class="fa-solid fa-circle-check stat-icon"></i>
            <div class="stat-value"><?= $activeCustomers ?></div>
            <div class="stat-label">Khách hàng hoạt động</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
            <i class="fa-solid fa-user-plus stat-icon"></i>
            <div class="stat-value"><?= $newThisMonth ?></div>
            <div class="stat-label">Khách mới tháng này</div>
        </div>
    </div>
</div>

<!-- ── Stat cards: Hóa đơn / Doanh thu ─────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#34d399)">
            <i class="fa-solid fa-sack-dollar stat-icon"></i>
            <div class="stat-value" style="font-size:24px"><?= number_format($tongDoanhThu, 0, ',', '.') ?>đ</div>
            <div class="stat-label">Tổng doanh thu (hoàn thành)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#0f172a,#334155)">
            <i class="fa-solid fa-file-invoice stat-icon"></i>
            <div class="stat-value"><?= $tongDonHang ?></div>
            <div class="stat-label">Tổng số hóa đơn</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#f87171)">
            <i class="fa-solid fa-hourglass-half stat-icon"></i>
            <div class="stat-value"><?= $donChoXacNhan ?></div>
            <div class="stat-label">Hóa đơn chờ xác nhận</div>
        </div>
    </div>
</div>

<!-- ── Quick actions ─────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                <span class="fw-semibold me-2">Thao tác nhanh:</span>
                <?php if (in_array($_SESSION['admin_user']['ten_vai_tro'] ?? '', ['quan_tri_vien', 'superadmin', 'admin'])): ?>
                <a href="products.php" class="btn btn-info text-white btn-sm">
                    <i class="fa-solid fa-box-archive me-1"></i>Quản lý Sản phẩm
                </a>
                <?php endif; ?>
                <a href="admins.php" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-user-shield me-1"></i>Quản lý Nhân viên
                </a>
                <a href="customers.php" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-users me-1"></i>Quản lý Khách hàng
                </a>
                <a href="orders.php" class="btn btn-outline-success btn-sm">
                    <i class="fa-solid fa-file-invoice-dollar me-1"></i>Hóa đơn &amp; Thống kê
                </a>
                <a href="notifications.php" class="btn btn-outline-warning btn-sm">
                    <i class="fa-solid fa-bell me-1"></i>Gửi Thông báo
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ── Bảng tóm tắt ──────────────────────────────────────── -->
<div class="row g-3">
    <!-- Khách hàng mới nhất -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-users me-2 text-primary"></i>Khách hàng mới nhất</span>
                <a href="customers.php" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($latestCustomers as $c): ?>
                        <tr>
                            <td class="fw-medium"><?= htmlspecialchars($c['ho_ten']) ?></td>
                            <td style="font-size:13px"><?= htmlspecialchars($c['email']) ?></td>
                            <td>
                                <?php if ($c['trang_thai'] === 'khoa'): ?>
                                    <span class="badge badge-khoa">Đã khóa</span>
                                <?php elseif ($c['trang_thai'] === 'cho_xac_thuc'): ?>
                                    <span class="badge bg-warning text-dark">Chờ xác thực</span>
                                <?php else: ?>
                                    <span class="badge badge-hoat-dong">Hoạt động</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted" style="font-size:13px">
                                <?= date('d/m/Y', strtotime($c['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tóm tắt hệ thống -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-chart-pie me-2 text-primary"></i>Tóm tắt hệ thống
            </div>
            <div class="card-body">
                <!-- Admin -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold" style="font-size:14px">Quản trị viên hoạt động</span>
                        <span class="text-primary fw-bold"><?= $activeAdmins ?>/<?= $totalAdmins ?></span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:4px">
                        <div class="progress-bar bg-primary" style="width:<?= $totalAdmins > 0 ? round($activeAdmins/$totalAdmins*100) : 0 ?>%"></div>
                    </div>
                </div>

                <!-- Khách hàng hoạt động -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold" style="font-size:14px">Khách hàng hoạt động</span>
                        <span class="text-success fw-bold"><?= $activeCustomers ?>/<?= $totalCustomers ?></span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:4px">
                        <div class="progress-bar bg-success" style="width:<?= $totalCustomers > 0 ? round($activeCustomers/$totalCustomers*100) : 0 ?>%"></div>
                    </div>
                </div>

                <!-- Khách hàng bị khóa -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold" style="font-size:14px">Tài khoản bị khóa</span>
                        <span class="text-danger fw-bold"><?= $lockedCustomers ?></span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:4px">
                        <div class="progress-bar bg-danger" style="width:<?= $totalCustomers > 0 ? round($lockedCustomers/$totalCustomers*100) : 0 ?>%"></div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size:13px">
                        <i class="fa-solid fa-clock me-1"></i>
                        Cập nhật: <?= date('H:i d/m/Y') ?>
                    </span>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-rotate-right me-1"></i>Làm mới
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
