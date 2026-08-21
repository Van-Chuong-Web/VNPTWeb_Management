<?php
/**
 * header.php — Layout chung: <head> + Sidebar + Navbar
 * Include ở đầu mỗi trang: require_once 'header.php';
 *
 * Biến cần khai báo TRƯỚC khi include:
 *   $pageTitle  = 'Tên trang';          // Tiêu đề tab trình duyệt
 *   $activeMenu = 'admins';             // 'dashboard' | 'admins' | 'customers'
 */

require_once __DIR__ . '/auth_check.php';

// Xác định trang hiện tại để highlight menu
$activeMenu = $activeMenu ?? 'dashboard';
$pageTitle  = $pageTitle  ?? 'Admin Panel — VNPT';

$adminUser = $_SESSION['admin_user'] ?? ['ho_ten' => 'Admin', 'ten_vai_tro' => 'nhan_vien'];
$adminName = $adminUser['ho_ten'];
$adminRole = $adminUser['ten_vai_tro'] === 'quan_tri_vien' ? 'Quản trị viên' : $adminUser['ten_vai_tro'];
$adminAvatarChar = mb_substr($adminName, 0, 1, 'UTF-8');

// Lấy thông báo nội bộ chưa đọc của nhân viên đang đăng nhập
$currentEmpId = $adminUser['nhan_vien_id'] ?? 0;
$unreadNotis = [];
if ($currentEmpId > 0 && isset($pdo)) {
    try {
        $stmtUnread = $pdo->prepare("SELECT * FROM thong_bao_nhan_vien WHERE nhan_vien_id = :eid AND da_doc = 0 ORDER BY id DESC LIMIT 5");
        $stmtUnread->execute([':eid' => $currentEmpId]);
        $unreadNotis = $stmtUnread->fetchAll();
    } catch (Exception $e) {}
}
$unreadCount = count($unreadNotis);

// Đường dẫn tương đối đến thư mục admin_panel (dùng cho link)
$base = '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | VNPT Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── Biến màu VNPT ─────────────────────────────── */
        :root {
            --vnpt-primary:   #0d6efd;
            --vnpt-dark:      #0a1628;
            --vnpt-sidebar:   #111827;
            --vnpt-sidebar-hover: #1f2937;
            --vnpt-sidebar-active: #1d4ed8;
            --vnpt-text-muted: #9ca3af;
            --sidebar-width:  260px;
        }

        /* ── Reset & Base ──────────────────────────────── */
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
        }

        /* ── Sidebar ───────────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--vnpt-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
            overflow-y: auto;
        }

        /* Logo */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: var(--vnpt-primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text { line-height: 1.2; }
        .sidebar-brand .brand-name {
            font-size: 16px; font-weight: 700;
            color: #fff; display: block;
        }
        .sidebar-brand .brand-sub {
            font-size: 11px; color: var(--vnpt-text-muted);
        }

        /* Nav */
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-section-label {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .08em;
            color: var(--vnpt-text-muted);
            padding: 12px 8px 6px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: background .2s, color .2s;
            margin-bottom: 2px;
        }
        .sidebar-link i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-link:hover {
            background: var(--vnpt-sidebar-hover);
            color: #fff;
        }
        .sidebar-link.active {
            background: var(--vnpt-sidebar-active);
            color: #fff;
        }
        .sidebar-link .badge-count {
            margin-left: auto;
            background: rgba(255,255,255,.15);
            color: #fff;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 20px;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
            font-size: 12px; color: var(--vnpt-text-muted);
        }

        /* ── Main content ──────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Top Navbar ────────────────────────────────── */
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            height: 64px;
            display: flex; align-items: center;
            position: sticky; top: 0; z-index: 1030;
            gap: 16px;
        }
        .top-navbar .page-title {
            font-size: 18px; font-weight: 600;
            color: #0f172a; flex: 1;
        }
        .top-navbar .btn-toggle-sidebar {
            display: none;
            background: none; border: none;
            font-size: 20px; color: #64748b;
            cursor: pointer; padding: 4px 8px;
        }
        .top-navbar .admin-avatar {
            width: 36px; height: 36px;
            background: var(--vnpt-primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 14px;
            cursor: pointer;
        }
        .top-navbar .admin-info { line-height: 1.2; }
        .top-navbar .admin-info .name { font-size: 13px; font-weight: 600; }
        .top-navbar .admin-info .role { font-size: 11px; color: #64748b; }

        /* ── Page content wrapper ──────────────────────── */
        .page-content {
            padding: 28px 28px 40px;
            flex: 1;
        }

        /* ── Cards ─────────────────────────────────────── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 12px 12px 0 0 !important;
            padding: 16px 20px;
            font-weight: 600;
        }

        /* ── Stat cards ─────────────────────────────────── */
        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            position: relative; overflow: hidden;
        }
        .stat-card .stat-icon {
            position: absolute; right: 16px; top: 16px;
            font-size: 40px; opacity: .2;
        }
        .stat-card .stat-value { font-size: 32px; font-weight: 700; }
        .stat-card .stat-label { font-size: 13px; opacity: .85; margin-top: 4px; }

        /* ── Table ──────────────────────────────────────── */
        .table thead th {
            background: #f8fafc;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }
        .table tbody td { padding: 12px 16px; vertical-align: middle; font-size: 14px; }
        .table tbody tr:hover { background: #f8fafc; }

        /* ── Badges ─────────────────────────────────────── */
        .badge-hoat-dong { background: #dcfce7; color: #166534; }
        .badge-khoa       { background: #fee2e2; color: #991b1b; }

        /* ── Buttons ────────────────────────────────────── */
        .btn-action { padding: 4px 10px; font-size: 12px; border-radius: 6px; }

        /* ── Alert ──────────────────────────────────────── */
        .alert { border-radius: 10px; font-size: 14px; }

        /* ── Responsive ─────────────────────────────────── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
            .top-navbar .btn-toggle-sidebar { display: block; }
            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.5);
                z-index: 1039;
            }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<nav id="sidebar">
        <?php
        $roleKey = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
        $isAdminRole   = in_array($roleKey, ['quan_tri_vien', 'superadmin', 'admin']);
        $isSalesRole   = in_array($roleKey, ['nhan_vien_ban_hang', 'quan_ly']);
        $isContentRole = in_array($roleKey, ['bien_tap_vien', 'editor']);
        $homeTarget    = $isAdminRole ? 'index.php' : ($isContentRole ? 'posts.php' : ($isSalesRole ? 'orders.php' : 'profile.php'));
        ?>

    <!-- Brand -->
    <a href="<?= $homeTarget ?>" class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <div class="brand-text">
            <span class="brand-name">VNPT Admin</span>
            <span class="brand-sub">Quản trị hệ thống</span>
        </div>
    </a>

    <!-- Nav links -->
    <div class="sidebar-nav">
        <div class="nav-section-label">Tổng quan</div>
        <?php if ($isAdminRole): ?>
        <a href="index.php"
           class="sidebar-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <?php endif; ?>

        <?php if ($isAdminRole || $isSalesRole): ?>
        <a href="products.php"
           class="sidebar-link <?= $activeMenu === 'products' ? 'active' : '' ?>">
            <i class="fa-solid fa-box-archive"></i> Sản phẩm &amp; Dịch vụ
        </a>
        <?php endif; ?>

        <?php if ($isAdminRole || $isContentRole): ?>
        <a href="posts.php"
           class="sidebar-link <?= $activeMenu === 'posts' ? 'active' : '' ?>">
            <i class="fa-solid fa-newspaper"></i> Bài viết &amp; Tin tức
        </a>
        <a href="customer_messages.php"
           class="sidebar-link <?= $activeMenu === 'customer_messages' ? 'active' : '' ?>">
            <i class="fa-solid fa-comments"></i> Phản hồi &amp; Tin nhắn
        </a>
        <?php endif; ?>

        <?php if ($isAdminRole): ?>
        <a href="admins.php"
           class="sidebar-link <?= $activeMenu === 'admins' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-shield"></i> Nhân viên &amp; Phân quyền
        </a>
        <?php endif; ?>

        <?php if ($isAdminRole || $isSalesRole): ?>
        <a href="customers.php"
           class="sidebar-link <?= $activeMenu === 'customers' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i> Khách hàng
        </a>
        <a href="orders.php"
           class="sidebar-link <?= $activeMenu === 'orders' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i> Hóa đơn &amp; Thống kê
        </a>
        <?php endif; ?>

        <?php if ($isAdminRole || $isSalesRole): ?>
        <a href="notifications.php"
           class="sidebar-link <?= $activeMenu === 'notifications' ? 'active' : '' ?>">
            <i class="fa-solid fa-bell"></i> Thông báo
        </a>
        <?php endif; ?>

        <div class="nav-section-label">Hệ thống</div>
        <?php if ($isAdminRole): ?>
        <a href="activity_log.php"
           class="sidebar-link <?= $activeMenu === 'activity_log' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i> Nhật ký hoạt động
        </a>
        <?php endif; ?>

        <a href="profile.php"
           class="sidebar-link <?= $activeMenu === 'profile' ? 'active' : '' ?>">
            <i class="fa-solid fa-id-badge"></i> Hồ sơ cá nhân
        </a>
        <a href="../index.php" target="_blank" class="sidebar-link">
            <i class="fa-solid fa-globe"></i> Xem website
            <i class="fa-solid fa-arrow-up-right-from-square ms-auto" style="font-size:10px;opacity:.5"></i>
        </a>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div>VNPT Admin Panel v1.0</div>
        <div style="margin-top:2px">PHP Native + MySQL (PDO)</div>
    </div>
</nav>

<!-- Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ── Main content ──────────────────────────────────────── -->
<div id="main-content">

    <!-- Top Navbar -->
    <header class="top-navbar">
        <button class="btn-toggle-sidebar" onclick="toggleSidebar()" title="Mở/đóng menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title"><?= htmlspecialchars($pageTitle) ?></div>

        <!-- Breadcrumb (desktop) -->
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0" style="font-size:13px">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                <?php if ($activeMenu !== 'dashboard'): ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></li>
                <?php endif; ?>
            </ol>
        </nav>

        <!-- Admin info & Notification Bell -->
        <div class="d-flex align-items-center gap-3 ms-auto">
            <!-- Dropdown Thông Báo Nội Bộ Real-time -->
            <div class="dropdown">
                <button type="button" class="btn btn-light position-relative rounded-circle p-0" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: #F1F5F9; border: 1px solid #E2E8F0;" title="Thông báo nội bộ">
                    <i class="fa-solid fa-bell text-secondary fs-6"></i>
                    <?php if ($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 9px; padding: 3px 6px;">
                        <?= $unreadCount ?>
                    </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="width: 330px; border-radius: 14px; overflow: hidden; z-index: 1055;">
                    <li class="dropdown-header bg-light py-2 px-3 fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-bell me-1 text-primary"></i> Thông báo nội bộ</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?= $unreadCount ?> chưa đọc</span>
                    </li>
                    <li><hr class="dropdown-divider m-0"></li>
                    <?php if (empty($unreadNotis)): ?>
                    <li class="px-3 py-4 text-center text-muted small">
                        <i class="fa-regular fa-bell-slash fs-4 d-block mb-1 opacity-50"></i>
                        Bạn không có thông báo chưa đọc nào.
                    </li>
                    <?php else: ?>
                        <?php foreach ($unreadNotis as $un): ?>
                        <li>
                            <a href="notifications.php?tab=nhan_vien&read_id=<?= $un['id'] ?>" class="dropdown-item py-2 px-3 border-bottom text-wrap">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;"><?= htmlspecialchars($un['tieu_de']) ?></div>
                                <div class="text-secondary small text-truncate mb-1" style="font-size: 0.8rem;"><?= htmlspecialchars($un['noi_dung'] ?? '') ?></div>
                                <div class="text-muted font-monospace" style="font-size: 0.72rem;"><i class="fa-regular fa-clock me-1"></i><?= date('H:i d/m/Y', strtotime($un['ngay_tao'])) ?></div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li>
                        <a href="notifications.php?tab=nhan_vien" class="dropdown-item text-center text-primary fw-bold small py-2 bg-light">
                            Xem lịch sử thông báo <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <a href="profile.php" class="admin-info d-none d-sm-block text-end text-decoration-none text-reset">
                <div class="name"><?= htmlspecialchars($adminName) ?></div>
                <div class="role"><?= htmlspecialchars($adminRole) ?></div>
            </a>
            <?php 
            $topAvatar = !empty($adminUser['hinh_anh_url']) ? $adminUser['hinh_anh_url'] : '';
            if (!empty($topAvatar)) {
                if (!str_starts_with($topAvatar, 'http') && !str_starts_with($topAvatar, '/') && !str_starts_with($topAvatar, '../')) {
                    $topAvatar = '../' . $topAvatar;
                }
            }
            ?>
            <?php if (!empty($topAvatar)): ?>
            <a href="profile.php" title="<?= htmlspecialchars($adminName) ?>">
                <img src="<?= htmlspecialchars($topAvatar) ?>" class="admin-avatar" style="object-fit: cover; width: 36px; height: 36px; border-radius: 50%; border: 2px solid var(--vnpt-primary);" alt="Avatar" onerror="this.style.display='none'; if(this.parentElement.nextElementSibling) this.parentElement.nextElementSibling.style.display='flex';">
            </a>
            <a href="profile.php" class="admin-avatar text-decoration-none" style="display:none;" title="<?= htmlspecialchars($adminName) ?>"><?= htmlspecialchars($adminAvatarChar) ?></a>
            <?php else: ?>
            <a href="profile.php" class="admin-avatar text-decoration-none" title="<?= htmlspecialchars($adminName) ?>"><?= htmlspecialchars($adminAvatarChar) ?></a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline-danger btn-sm ms-1" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </header>

    <!-- Page content starts here -->
    <main class="page-content">

<!-- ── JS toggle sidebar (mobile) ───────────────────────── -->
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
</script>
