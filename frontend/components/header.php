<?php
// Sử dụng __DIR__ để lấy đường dẫn tuyệt đối từ thư mục components/ 
// lùi ra 2 cấp (components -> frontend -> gốc) rồi vào backend/db/
require_once __DIR__ . '/../../backend/db/database.php';

$menuTree = [];
try {
  $db = new Database();
  // Lấy toàn bộ menu đang hoạt động, sắp xếp theo thứ tự
  $menus = $db->select("SELECT * FROM menu WHERE trang_thai = 1 ORDER BY menu_cha_id, thu_tu ASC");
  
  // Lấy toàn bộ danh mục bài viết công khai (loại bỏ Chưa được phân loại) để đồng bộ vào menu Tin tức
  $allCategories = $db->select("SELECT id, ten, slug FROM danh_muc_bai_viet WHERE slug != 'chua-duoc-phan-loai' AND ten NOT LIKE '%Chưa được phân loại%' ORDER BY id ASC");
  $db->close();

  // Nhóm các menu lại theo menu_cha_id để dễ dàng tạo dropdown
  foreach ($menus as $menu) {
    $parentId = $menu['menu_cha_id'] === null ? 0 : $menu['menu_cha_id'];
    $menuTree[$parentId][] = $menu;
  }

  // Tự động bổ sung tất cả chuyên mục từ CSDL vào dropdown menu Tin tức
  $tinTucParentId = null;
  foreach ($menus as $m) {
      if (($m['slug'] === 'tin-tuc' || strpos($m['ten_menu'], 'Tin tức') !== false) && ($m['menu_cha_id'] === null || $m['menu_cha_id'] == 0)) {
          $tinTucParentId = $m['id'];
          break;
      }
  }

  if ($tinTucParentId && !empty($allCategories)) {
      $existingSubSlugs = [];
      if (isset($menuTree[$tinTucParentId])) {
          foreach ($menuTree[$tinTucParentId] as $sub) {
              if (!empty($sub['slug'])) $existingSubSlugs[] = $sub['slug'];
          }
      }

      foreach ($allCategories as $cat) {
          if (!in_array($cat['slug'], $existingSubSlugs)) {
              $menuTree[$tinTucParentId][] = [
                  'id' => 'cat_' . $cat['id'],
                  'ten_menu' => $cat['ten'],
                  'slug' => $cat['slug'],
                  'link' => '#',
                  'menu_cha_id' => $tinTucParentId,
                  'thu_tu' => 99,
                  'trang_thai' => 1
              ];
          }
      }
  }
} catch (Exception $e) {
  die("Lỗi lấy dữ liệu menu: " . $e->getMessage());
}

function renderMenuHTML($menuTree, $parentId = 0)
{
  if (!isset($menuTree[$parentId]))
    return '';

  $html = '';
  foreach ($menuTree[$parentId] as $item) {
    $hasChild = isset($menuTree[$item['id']]);
    $link = !empty($item['link']) ? $item['link'] : '#';
    $dataPage = !empty($item['slug']) ? ' data-page="' . htmlspecialchars($item['slug']) . '"' : '';

    if ($parentId == 0) {
      if ($hasChild) {
        $html .= '<div class="nav-item dropdown">';
        $html .= '<a href="' . $link . '" class="nav-link"' . $dataPage . '>' . htmlspecialchars($item['ten_menu']) . ' <i data-lucide="chevron-down" class="icon-sm"></i></a>';
        $html .= '<div class="dropdown-menu">';
        $html .= renderMenuHTML($menuTree, $item['id']);
        $html .= '</div>';
        $html .= '</div>';
      } else {
        $html .= '<a href="' . $link . '" class="nav-link"' . $dataPage . '>' . htmlspecialchars($item['ten_menu']) . '</a>';
      }
    } else {
      $html .= '<a href="' . $link . '"' . $dataPage . '>' . htmlspecialchars($item['ten_menu']) . '</a>';
    }
  }
  return $html;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>VNPT – Dịch Vụ Số Toàn Diện</title>
  <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/frontend/') !== false) ? 'assets/style.css' : 'frontend/assets/style.css'; ?>?v=<?php echo time(); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <!-- Google & Facebook Real OAuth SDKs -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js"></script>
</head>

<body>

  <div id="particles-bg"></div>

  <header class="site-header" id="navbar">

    <div class="top-bar">
      <div class="top-bar-container">
        <!-- Sửa href="#" thành "index.php" để click vào logo thì về trang chủ -->
        <a href="index.php" class="nav-logo">
          <div class="logo-icon">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
              <circle cx="16" cy="16" r="16" fill="url(#logoGrad)" />
              <path d="M8 16 L16 8 L24 16 L16 24 Z" fill="white" opacity="0.9" />
              <circle cx="16" cy="16" r="4" fill="white" />
              <defs>
                <linearGradient id="logoGrad" x1="0" y1="0" x2="32" y2="32">
                  <stop offset="0%" stop-color="#0066CC" />
                  <stop offset="100%" stop-color="#00AAFF" />
                </linearGradient>
              </defs>
            </svg>
          </div>
          <span class="logo-text">VNPT</span>
        </a>

        <span class="top-bar-slogan">Chuyển đổi số – Vươn tầm thế giới</span>

        <div class="top-bar-actions">
          <div class="search-wrapper">
            <button class="tb-btn btn-search" id="searchToggleBtn" aria-label="Tìm kiếm">
              <i data-lucide="search"></i>
              <span class="tb-btn-label">Tìm kiếm</span>
            </button>

            <div class="live-search-box" id="liveSearchBox" style="display: none;">
              <div class="live-search-input-wrap">
                <i data-lucide="search" style="color: #0066CC; width: 18px; height: 18px; flex-shrink: 0;"></i>
                <input type="text" id="liveSearchInput" placeholder="Nhập tên dịch vụ, gói cước..." autocomplete="off">
                <button type="button" id="closeSearchBoxBtn" style="background: transparent; border: none; color: #64748B; cursor: pointer; padding: 2px 6px; font-size: 1.25rem; line-height: 1; font-weight: 700;">&times;</button>
              </div>
              
              <div id="liveSearchResults" class="live-search-results-area">
                <div class="live-search-placeholder">
                  Gõ từ khóa để bắt đầu tìm kiếm
                </div>
              </div>
            </div>
          </div>

          <a href="javascript:void(0)" class="tb-btn btn-lang">
            <i data-lucide="globe" class="icon-sm"></i>
            <span class="tb-btn-label">VI</span>
          </a>

          <button class="tb-btn btn-cart" id="cartToggle" aria-label="Giỏ hàng">
            <i data-lucide="shopping-cart"></i>
            <span class="tb-btn-label">Giỏ hàng</span>
            <span class="cart-badge" id="cartBadge" style="display:none">0</span>
          </button>

          <button class="tb-btn" id="openCheckSupportBtn" title="Tra cứu tin nhắn &amp; Phản hồi tư vấn" style="gap:6px; color:#0066CC;">
            <i data-lucide="message-square"></i>
            <span class="tb-btn-label">Hòm thư tư vấn</span>
          </button>

          <button class="tb-btn" id="openUserNotifBtn" title="Thông báo từ VNPT" style="position:relative; gap:6px; color:#0066CC; display:none;">
            <i data-lucide="bell"></i>
            <span class="tb-btn-label">Thông báo</span>
            <span class="notif-badge" id="headerNotifBadge" style="display:none; position:absolute; top: -2px; right: -2px; background: #EF4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; font-weight: 800; align-items: center; justify-content: center; border: 2px solid white;">0</span>
          </button>

          <div class="tb-divider"></div>

          <div class="auth-btns" id="authBtns">
            <button class="btn-login" id="openLogin">Đăng nhập</button>
            <button class="btn-register" id="openRegister">Đăng ký</button>
          </div>

          <div class="user-menu" id="userMenu" style="display:none">
            <button class="user-avatar-btn" id="userAvatarBtn">
              <span class="user-avatar-circle" id="userAvatarCircle">U</span>
              <img id="userAvatarImg" class="user-avatar-img" style="display:none; width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1.5px solid #0066CC;" alt="Avatar">
              <span class="user-display-name" id="userDisplayName">Người dùng</span>
              <i data-lucide="chevron-down" class="icon-sm"></i>
            </button>
            <div class="user-dropdown" id="userDropdown">
              <div class="user-dropdown-header">
                <span id="userDropdownName">Người dùng</span>
                <span id="userDropdownEmail" class="user-dropdown-email"></span>
              </div>
              <a href="javascript:void(0)" class="user-dropdown-item" data-account="profile"><i data-lucide="user"></i> Hồ sơ cá nhân</a>
              <a href="javascript:void(0)" class="user-dropdown-item" data-account="orders"><i data-lucide="package"></i> Đơn hàng của tôi</a>
              <a href="javascript:void(0)" class="user-dropdown-item" data-account="settings"><i data-lucide="settings"></i> Cài đặt</a>
              <button class="user-dropdown-item admin-only admin-entry" id="openAdminBtn"><i
                  data-lucide="layout-dashboard"></i> Quản trị hệ thống</button>
              <div class="user-dropdown-divider"></div>
              <button class="user-dropdown-item logout-btn" id="logoutBtn"><i data-lucide="log-out"></i> Đăng
                xuất</button>
            </div>
          </div>

          <div class="tb-divider"></div>

          <a href="#contact" class="btn-cta">
            <i data-lucide="phone" style="width:15px;height:15px;"></i>
            Liên hệ ngay
          </a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>

    <div class="nav-bar">
      <div class="nav-bar-container">
        <nav class="nav-links" id="navLinks">
          <?php echo renderMenuHTML($menuTree, 0); ?>
        </nav>

        <div class="nav-bar-right">
          <i data-lucide="phone-call" style="width:15px;height:15px;color:#fff;"></i>
          <span>Hotline: <strong>1800 1260</strong></span>
        </div>
      </div>
    </div>

  </header>