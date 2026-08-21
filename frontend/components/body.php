<?php
// Function trợ giúp làm sạch văn bản UTF-8 an toàn, tránh lỗi cắt dở từ hay ký tự lạ
if (!function_exists('safeCleanText')) {
    function safeCleanText($text, $limit = 50) {
        if (empty($text)) return '';
        // Loại bỏ ký tự thay thế UTF-8 hỏng U+FFFD
        $text = preg_replace('/\x{FFFD}/u', '', $text);
        // Chuẩn hóa khoảng trắng
        $text = trim(preg_replace('/[\s\t\n\r]+/u', ' ', $text));
        // Loại bỏ các ký tự gạch đầu dòng thô ở đầu câu
        $text = preg_replace('/^[-\s•–\d\.\:\)\(]+/u', '', $text);
        $text = rtrim($text, '.:');

        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $limit - 3, 'UTF-8');
        $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
        if ($lastSpace !== false && $lastSpace > ($limit * 0.4)) {
            $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
        }
        return rtrim($truncated, '.,;:-–•') . '...';
    }
}

// LẤY TẤT CẢ DỮ LIỆU SẢN PHẨM TRỰC TIẾP TỪ CSDL MYSQL TRONG 1 BLOCK DUY NHẤT
$allServices = [];
$featuredServices = [];
$allCombos = [];

try {
    $db = new Database();
    $allServices = $db->select("SELECT * FROM san_pham WHERE trang_thai = 'dang_ban' ORDER BY id DESC");
    $featuredServices = $db->select("SELECT * FROM san_pham WHERE trang_thai = 'dang_ban' ORDER BY luot_ban DESC, id DESC LIMIT 3");
    $allCombos = $db->select("SELECT * FROM san_pham WHERE loai_san_pham = 'combo' AND trang_thai = 'dang_ban' ORDER BY gia_niem_yet ASC");
    $db->close();
} catch (Exception $e) {
    $allServices = [];
    $featuredServices = [];
    $allCombos = [];
}

if (empty($allServices)) {
    $allServices = [
        ['id' => 1, 'ma_san_pham' => 'svc-001', 'ten_san_pham' => 'Cloud Computing', 'mo_ta_ngan' => 'Hạ tầng điện toán đám mây linh hoạt, mở rộng theo nhu cầu.', 'gia_niem_yet' => 2500000, 'gia_khuyen_mai' => null, 'don_vi_tinh' => 'tháng', 'trang_thai' => 'dang_ban', 'loai_san_pham' => 'dich_vu_so'],
        ['id' => 2, 'ma_san_pham' => 'svc-002', 'ten_san_pham' => 'Bảo mật & An toàn số', 'mo_ta_ngan' => 'Giải pháp bảo mật toàn diện, giám sát 24/7.', 'gia_niem_yet' => 1800000, 'gia_khuyen_mai' => null, 'don_vi_tinh' => 'tháng', 'trang_thai' => 'dang_ban', 'loai_san_pham' => 'dich_vu_so'],
        ['id' => 3, 'ma_san_pham' => 'svc-003', 'ten_san_pham' => 'AI & Tự động hóa', 'mo_ta_ngan' => 'Ứng dụng trí tuệ nhân tạo tự động hóa quy trình nghiệp vụ.', 'gia_niem_yet' => 3200000, 'gia_khuyen_mai' => null, 'don_vi_tinh' => 'tháng', 'trang_thai' => 'dang_ban', 'loai_san_pham' => 'dich_vu_so'],
        ['id' => 4, 'ma_san_pham' => 'svc-004', 'ten_san_pham' => 'Hạ tầng mạng 5G', 'mo_ta_ngan' => 'Kết nối 5G/SD-WAN tốc độ cao, độ trễ thấp cho doanh nghiệp.', 'gia_niem_yet' => 4500000, 'gia_khuyen_mai' => null, 'don_vi_tinh' => 'tháng', 'trang_thai' => 'dang_ban', 'loai_san_pham' => 'dich_vu_so'],
        ['id' => 5, 'ma_san_pham' => 'svc-005', 'ten_san_pham' => 'Quản trị doanh nghiệp số', 'mo_ta_ngan' => 'Nền tảng quản trị, điều hành doanh nghiệp trên môi trường số.', 'gia_niem_yet' => 990000, 'gia_khuyen_mai' => null, 'don_vi_tinh' => 'tháng', 'trang_thai' => 'dang_ban', 'loai_san_pham' => 'dich_vu_so'],
        ['id' => 6, 'ma_san_pham' => 'svc-006', 'ten_san_pham' => 'Giao tiếp & Cộng tác', 'mo_ta_ngan' => 'Hội họp trực tuyến, cộng tác nhóm bảo mật.', 'gia_niem_yet' => 750000, 'gia_khuyen_mai' => null, 'don_vi_tinh' => 'tháng', 'trang_thai' => 'dang_ban', 'loai_san_pham' => 'dich_vu_so']
    ];
}

if (empty($featuredServices)) {
    $featuredServices = array_slice($allServices, 0, 3);
}

if (empty($allCombos)) {
    $allCombos = array_slice($allServices, 0, 3);
}
?>
<section class="hero" id="home">
  <div class="hero-bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>
    <br>
  <div class="floating-elements">
    
    <div class="float-card float-card-1">
      <i data-lucide="shield-check"></i>
      <span>Bảo mật 99.9%</span>
    </div>
    <div class="float-card float-card-2">
      <i data-lucide="zap"></i>
      <span>Tốc độ 5G</span>
    </div>
    <div class="float-card float-card-3">
      <i data-lucide="cloud"></i>
      <span>Cloud Ready</span>
    </div>
    <div class="float-badge float-badge-1">
      <span class="badge-num">500K+</span>
      <span class="badge-label">Khách hàng</span>
    </div>
    <div class="float-badge float-badge-2">
      <span class="badge-num">99.9%</span>
      <span class="badge-label">Uptime SLA</span>
    </div>
  </div>

  <div class="hero-content">
    <div class="hero-tag">
      <span class="tag-dot"></span>
      Nền tảng dịch vụ số hàng đầu Việt Nam
    </div>
    <h1 class="hero-title">
      Chuyển đổi số<br/>
      <span class="gradient-text">Toàn diện &amp; Bền vững</span>
    </h1>
    <p class="hero-desc">
      Hệ sinh thái dịch vụ số tích hợp — từ hạ tầng đến ứng dụng — AI thông minh —
      giúp doanh nghiệp bứt phá trong kỷ nguyên số.
    </p>
    <div class="hero-actions">
      <a href="#services" class="btn-primary">
        Khám phá dịch vụ
        <i data-lucide="arrow-right"></i>
      </a>
      <a href="#demo" class="btn-outline" id="openDemoVideoBtn">
        <i data-lucide="play-circle"></i>
        Xem demo
      </a>
    </div>
    <div class="hero-stats">
      <div class="hstat">
        <span class="hstat-num" data-target="500">500</span><span>K+</span>
        <span class="hstat-label">Khách hàng</span>
      </div>
      <div class="hstat-divider"></div>
      <div class="hstat">
        <span class="hstat-num" data-target="30">30</span><span>+</span>
        <span class="hstat-label">Năm kinh nghiệm</span>
      </div>
      <div class="hstat-divider"></div>
      <div class="hstat">
        <span class="hstat-num" data-target="99">99</span><span>.9%</span>
        <span class="hstat-label">Uptime SLA</span>
      </div>
    </div>
  </div>

  <div class="hero-visual">
    <div class="ring ring-outer">
      <div class="ring-dot ring-dot-1"><i data-lucide="wifi"></i></div>
      <div class="ring-dot ring-dot-2"><i data-lucide="database"></i></div>
      <div class="ring-dot ring-dot-3"><i data-lucide="cpu"></i></div>
      <div class="ring-dot ring-dot-4"><i data-lucide="globe"></i></div>
    </div>
    <div class="ring ring-mid">
      <div class="ring-dot ring-dot-5"><i data-lucide="lock"></i></div>
      <div class="ring-dot ring-dot-6"><i data-lucide="bar-chart-2"></i></div>
    </div>
    <div class="hero-center-icon">
      <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
        <circle cx="40" cy="40" r="40" fill="url(#heroGrad)"/>
        <path d="M20 40 L40 20 L60 40 L40 60 Z" fill="white" opacity="0.85"/>
        <circle cx="40" cy="40" r="10" fill="white"/>
        <defs>
          <linearGradient id="heroGrad" x1="0" y1="0" x2="80" y2="80">
            <stop offset="0%" stop-color="#0055BB"/>
            <stop offset="100%" stop-color="#00CCFF"/>
          </linearGradient>
        </defs>
      </svg>
    </div>
  </div>

  <div class="scroll-hint">
    <span>Cuộn xuống</span>
    <i data-lucide="chevrons-down" class="scroll-arrow"></i>
  </div>
</section>

<section class="hero-carousel" id="vnpt-promo" aria-label="Banner quảng cáo VNPT / Vinaphone 5G">
  <div class="vnpt-carousel" id="vnptBanner" aria-roledescription="carousel" aria-label="Banner quảng cáo VNPT">
    <div class="vc-viewport">
      <ul class="vc-track" id="vcTrack">
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="1 / 10">
          <img src="assets/images/img09.jpg" alt="Data roaming Vinaphone không giới hạn" loading="eager" fetchpriority="high">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="2 / 10">
          <img src="assets/images/img10.jpg" alt="Mạng 5G Vinaphone dẫn đầu khu vực" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="3 / 10">
          <img src="assets/images/img05.png" alt="eSIM 100% Online - Mua và kích hoạt trong 5 phút" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="4 / 10">
          <img src="assets/images/img02.jpg" alt="Đường tới World Cup - MyTV cùng VNPT" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="5 / 10">
          <img src="assets/images/img01.jpg" alt="Xác thực thông tin thuê bao qua VNeID/CCCD" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="6 / 10">
          <img src="assets/images/img03.jpg" alt="30 năm VinaPhone - Triệu quà tri ân" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="7 / 10">
          <img src="assets/images/img04.jpg" alt="VNPT XGSPON 10Gbps siêu tốc độ" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="8 / 10">
          <img src="assets/images/img06.jpg" alt="Các đầu số gọi ra VNPT" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="9 / 10">
          <img src="assets/images/img07.jpg" alt="Combo Wifi Mesh 6 và Camera AI" loading="lazy">
        </li>
        <li class="vc-slide" role="group" aria-roledescription="slide" aria-label="10 / 10">
          <img src="assets/images/img08.jpg" alt="Tổng đài tin cậy VNPT" loading="lazy">
        </li>
      </ul>
    </div>

    <button class="vc-nav vc-prev" id="vcPrev" aria-label="Slide trước" type="button">
      <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button class="vc-nav vc-next" id="vcNext" aria-label="Slide kế tiếp" type="button">
      <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>

    <div class="vc-dots" id="vcDots" role="tablist" aria-label="Chọn slide"></div>

    <div class="vc-progress" id="vcProgress"><span></span></div>
  </div>
</section>

<div class="marquee-strip">
  <div class="marquee-track">
    <span>Cloud Computing</span><span class="sep">✦</span>
    <span>5G Network</span><span class="sep">✦</span>
    <span>AI & Machine Learning</span><span class="sep">✦</span>
    <span>Cybersecurity</span><span class="sep">✦</span>
    <span>IoT Platform</span><span class="sep">✦</span>
    <span>Big Data Analytics</span><span class="sep">✦</span>
    <span>Digital Transformation</span><span class="sep">✦</span>
    <span>Smart City</span><span class="sep">✦</span>
    <span>eGovernment</span><span class="sep">✦</span>
    <span>Cloud Computing</span><span class="sep">✦</span>
    <span>5G Network</span><span class="sep">✦</span>
    <span>AI & Machine Learning</span><span class="sep">✦</span>
    <span>Cybersecurity</span><span class="sep">✦</span>
    <span>IoT Platform</span><span class="sep">✦</span>
    <span>Big Data Analytics</span><span class="sep">✦</span>
    <span>Digital Transformation</span><span class="sep">✦</span>
    <span>Smart City</span><span class="sep">✦</span>
    <span>eGovernment</span><span class="sep">✦</span>
  </div>
</div>

<?php
if (!function_exists('safeCleanText')) {
    function safeCleanText($text, $limit = 50) {
        if (empty($text)) return '';
        // Loại bỏ hoàn toàn thẻ HTML rác
        $text = strip_tags($text);
        // Loại bỏ ký tự thay thế UTF-8 hỏng U+FFFD
        $text = preg_replace('/\x{FFFD}/u', '', $text);
        // Chuẩn hóa khoảng trắng
        $text = trim(preg_replace('/[\s\t\n\r]+/u', ' ', $text));
        // Loại bỏ các ký tự gạch đầu dòng thô ở đầu câu
        $text = preg_replace('/^[-\s•–\d\.\:\)\(]+/u', '', $text);
        $text = rtrim($text, '.:');

        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $limit - 3, 'UTF-8');
        $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
        if ($lastSpace !== false && $lastSpace > ($limit * 0.4)) {
            $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
        }
        return rtrim($truncated, '.,;:-–•') . '...';
    }
}

// Function tự động phân tích Icon, Màu sắc, Thẻ danh mục và Tính năng trực tiếp từ bản ghi CSDL MySQL
function getDynamicProductUI($product) {
    $nameLower = mb_strtolower($product['ten_san_pham']);
    $catId = (int)($product['danh_muc_id'] ?? 1);
    $type = $product['loai_san_pham'] ?? '';
    
    $icon = 'package';
    $color = '#0066CC';
    $category = 'all';

    // ƯU TIÊN KIỂM TRA DANH MỤC "THIẾT BỊ CÔNG NGHỆ" (ID 6 hoặc type = thiet_bi)
    if ($catId === 6 || $type === 'thiet_bi' || strpos($nameLower, 'thiết bị') !== false || strpos($nameLower, 'router') !== false || strpos($nameLower, 'camera') !== false) {
        $icon = (strpos($nameLower, 'camera') !== false) ? 'video' : ((strpos($nameLower, 'wifi') !== false || strpos($nameLower, 'router') !== false) ? 'wifi' : 'hard-drive');
        $color = '#E65100';
        $category = 'thiet_bi all';
    } elseif ($catId === 1 || strpos($nameLower, 'cloud') !== false || strpos($nameLower, '5g') !== false) {
        $icon = (strpos($nameLower, '5g') !== false) ? 'wifi' : 'cloud';
        $color = (strpos($nameLower, '5g') !== false) ? '#8800CC' : '#0066CC';
        $category = 'cloud all';
    } elseif ($catId === 2 || strpos($nameLower, 'bảo mật') !== false || strpos($nameLower, 'sec') !== false) {
        $icon = 'shield-check';
        $color = '#FF6B00';
        $category = 'security all';
    } elseif ($catId === 3) {
        $icon = (strpos($nameLower, 'ai') !== false) ? 'cpu' : ((strpos($nameLower, 'data') !== false) ? 'database' : 'briefcase');
        $color = '#00AA55';
        $category = 'app all';
    } elseif ($catId === 4 || $type === 'combo' || strpos($nameLower, 'combo') !== false) {
        $icon = 'layers';
        $color = '#0066CC';
        $category = 'combo all';
    } elseif ($catId === 5 || strpos($nameLower, 'sport') !== false || strpos($nameLower, 'tv') !== false || strpos($nameLower, 'internet') !== false) {
        $icon = 'tv';
        $color = '#33BBDD';
        $category = 'internet all';
    } else {
        $category = 'all';
    }

    // Bóc tách tính năng nổi bật từ CSDL (Loại bỏ triệt để thẻ HTML)
    $rawSource = !empty($product['mo_ta_ngan']) ? (string)$product['mo_ta_ngan'] : (!empty($product['mo_ta_chi_tiet']) ? (string)$product['mo_ta_chi_tiet'] : '');
    $cleanTextSource = strip_tags(str_replace(['<th', '<td', '<br', '<p', '</tr>', '</div>'], ["\n", "\n", "\n", "\n", "\n", "\n"], (string)$rawSource));
    
    $feats = [];
    $fullFeats = [];
    if (!empty($cleanTextSource)) {
        $parts = preg_split('/[\n\r;–•]+/u', $cleanTextSource);
        foreach ($parts as $pText) {
            $pCleanFull = safeCleanText($pText, 300);
            if (empty($pCleanFull) || preg_match('/^(thông tin|ưu đãi|đặc điểm|tính năng|mô tả|chi tiết|bảo hành|gói cước|bảng giá|div|table|specs|tr|th|td)/i', trim($pText))) {
                continue;
            }
            
            $pCleanShort = safeCleanText($pText, 150);
            if (mb_strlen($pCleanShort, 'UTF-8') >= 6) {
                if (!in_array($pCleanShort, $feats)) {
                    $feats[] = $pCleanShort;
                    $fullFeats[] = $pCleanFull;
                }
            }
            if (count($feats) >= 4) break;
        }
    }

    if (empty($feats) || count($feats) < 2) {
        if (strpos($nameLower, 'sport') !== false || strpos($nameLower, 'tv') !== false) {
            $feats = [
                'Trực tiếp thể thao độc quyền & 100+ kênh HD',
                'Miễn phí 100% Data 4G/5G VinaPhone',
                'Kho VOD giải trí chất lượng không quảng cáo'
            ];
            $fullFeats = [
                'Phát trực tiếp toàn bộ các sự kiện thể thao lớn độc quyền (World Cup 2026, các giải Châu Âu)',
                'Miễn phí 100% Data 4G/5G tốc độ cao cho thuê bao VinaPhone khi truy cập ứng dụng MyTV',
                'Kho VOD phim điện ảnh, truyền hình giải trí chất lượng 4K không quảng cáo'
            ];
        } else {
            $defaults = [
                'Cam kết SLA chất lượng 99.9%',
                'Bảo mật tiêu chuẩn quốc tế ISO/IEC',
                'Hỗ trợ kỹ thuật ưu tiên 24/7/365'
            ];
            foreach ($defaults as $d) {
                if (count($feats) >= 3) break;
                if (!in_array($d, $feats)) {
                    $feats[] = $d;
                    $fullFeats[] = $d;
                }
            }
        }
    }

    return [
        'icon' => $icon,
        'color' => $color,
        'category' => $category,
        'feats' => $feats,
        'fullFeats' => $fullFeats
    ];
}
?>

<section class="premium-featured-section" id="featured-services">
  <div class="container">
    <div class="section-header" style="margin-bottom: 30px;">
      <div class="section-tag" style="background: rgba(255, 107, 0, 0.15); color: #FF6B00; border: 1px solid rgba(255, 107, 0, 0.3); font-weight: 600;">
        <i data-lucide="award" style="width: 16px; height: 16px; margin-right: 5px; display: inline-block; vertical-align: middle;"></i> 
        Lựa chọn hàng đầu
      </div>
      <h2 class="section-title" style="color: #ffffff; font-weight: 800;">Dịch vụ <span class="gradient-text">Nổi bật nhất</span></h2>
      <p class="section-desc" style="color: rgba(255,255,255,0.9); font-size: 17px;">Top 3 giải pháp được các doanh nghiệp và tập đoàn tin dùng nhất hiện nay</p>
    </div>

    <div class="premium-grid" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 26px; width: 100%; opacity: 1 !important; visibility: visible !important;">
      <?php 
      if (count($featuredServices) > 0):
          foreach ($featuredServices as $index => $service): 
              $ui = getDynamicProductUI($service);
              $icon = $ui['icon'];
              $color = $ui['color'];
              
              $rank = $index + 1;
              $isTopSeller = ($rank === 1);
              $orderClass = 'order-' . $rank;
              
              // TÍNH TOÁN GIÁ KHUYẾN MÃI
              $giaNiemYet = (int)$service['gia_niem_yet'];
              $giaKhuyenMai = (int)$service['gia_khuyen_mai'];
              $hasSale = ($giaKhuyenMai > 0 && $giaKhuyenMai < $giaNiemYet);
              $actualPrice = $hasSale ? $giaKhuyenMai : $giaNiemYet;
              
              $cleanShortDesc = safeCleanText($service['mo_ta_ngan'], 95);
      ?>
      <div class="premium-card <?php echo $isTopSeller ? 'rank-1' : ''; ?> <?php echo $orderClass; ?>" style="--card-color: <?php echo $color; ?>; display: flex !important; flex-direction: column; min-height: 400px; width: 100%; opacity: 1 !important; visibility: visible !important;" id="premium-<?php echo htmlspecialchars($service['ma_san_pham']); ?>" data-full-desc="<?php echo htmlspecialchars(safeCleanText($service['mo_ta_chi_tiet'] ?: $service['mo_ta_ngan'], 400)); ?>" data-full-features="<?php echo htmlspecialchars(json_encode($ui['fullFeats'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>">
          
          <?php if ($isTopSeller): ?>
            <div class="premium-badge"><i data-lucide="flame" style="width: 16px; height: 16px; margin-bottom: -2px;"></i> Bán chạy nhất</div>
          <?php elseif ($rank === 2): ?>
            <div class="premium-badge" style="background: linear-gradient(90deg, #0066CC, #00AAFF); box-shadow: 0 5px 15px rgba(0, 170, 255, 0.5);">Được quan tâm nhiều</div>
          <?php elseif ($rank === 3): ?>
            <div class="premium-badge" style="background: linear-gradient(90deg, #00AA55, #00FF88); box-shadow: 0 5px 15px rgba(0, 170, 85, 0.4); color: #003311;">Đánh giá cao</div>
          <?php endif; ?>
          
          <div class="card-icon-wrap" style="--card-color: <?php echo $color; ?>;">
            <i data-lucide="<?php echo $icon; ?>" class="card-icon"></i>
          </div>
          
          <h3><?php echo htmlspecialchars($service['ten_san_pham']); ?></h3>
          <p><?php echo htmlspecialchars($cleanShortDesc); ?></p>  
          
          <ul class="card-features" style="margin: 18px 0 24px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.12); display: flex; flex-direction: column; gap: 10px; flex-grow: 1;">
            <?php foreach ($ui['feats'] as $ft): ?>
              <li style="display: flex; align-items: flex-start; gap: 10px; color: rgba(255,255,255,0.92); font-size: 0.9rem; line-height: 1.45;">
                <i data-lucide="check-circle-2" style="color: <?php echo $color; ?>; width: 17px; height: 17px; flex-shrink: 0; margin-top: 2px;"></i> 
                <span><?php echo htmlspecialchars($ft); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
          
          <div class="card-price" style="font-size: 16px; color: rgba(255,255,255,0.9); display: flex; flex-direction: column; gap: 4px;">
            <?php if ($hasSale): ?>
              <span style="font-size: 0.85rem; color: rgba(255,255,255,0.5); text-decoration: line-through;">
                Gốc: <?= number_format($giaNiemYet, 0, ',', '.') ?> ₫
              </span>
              <div>
                Từ <strong style="font-size: 28px; color: #FFB347; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"><?= number_format($giaKhuyenMai, 0, ',', '.') ?> ₫</strong>/<?= htmlspecialchars($service['don_vi_tinh']); ?>
                <span style="background: #E53E3E; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; margin-left: 5px; vertical-align: middle;">ƯU ĐÃI</span>
              </div>
            <?php else: ?>
              Từ <strong style="font-size: 28px; color: #ffffff; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"><?= number_format($giaNiemYet, 0, ',', '.'); ?> ₫</strong>/<?php echo htmlspecialchars($service['don_vi_tinh']); ?>
            <?php endif; ?>
          </div>
          
          <div class="card-actions" style="margin-top: 30px; flex-direction: column; gap: 15px;">
            <button class="btn-add-cart" 
                    style="width: 100%; justify-content: center; font-size: 16px; font-weight: 600; padding: 12px; border-radius: 8px; border: none; cursor: pointer; transition: 0.3s; <?php echo $isTopSeller ? 'background: '.$color.'; color: #ffffff; box-shadow: 0 4px 15px '.$color.'80;' : 'background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid '.$color.';'; ?>"
                    data-id="<?php echo htmlspecialchars($service['ma_san_pham']); ?>" 
                    data-name="<?php echo htmlspecialchars($service['ten_san_pham']); ?>" 
                    data-price="<?php echo $actualPrice; ?>" 
                    data-icon="<?php echo $icon; ?>" 
                    data-color="<?php echo $color; ?>">
              <i data-lucide="shopping-cart"></i> Đăng ký ngay
            </button>
            <a href="javascript:void(0);" class="card-link open-product-detail-modal" 
               onclick="if(window.openProductDetailModal){ window.openProductDetailModal(this); } return false;"
               style="text-align: center; width: 100%; color: <?php echo $color; ?>; font-weight: 600;"
               data-id="<?php echo htmlspecialchars($service['ma_san_pham']); ?>" 
               data-name="<?php echo htmlspecialchars((string)($service['ten_san_pham'] ?? '')); ?>" 
               data-price="<?= number_format($actualPrice, 0, ',', '.') ?> ₫/<?php echo htmlspecialchars((string)($service['don_vi_tinh'] ?? 'tháng')); ?>" 
               data-type="<?php echo htmlspecialchars((string)($service['loai_san_pham'] ?? 'dich_vu_so')); ?>"
               data-img="<?php echo htmlspecialchars((string)($service['hinh_anh_url'] ?? '')); ?>"
               data-short-desc="<?php echo htmlspecialchars((string)($cleanShortDesc ?? '')); ?>"
               data-full-specs="<?php echo htmlspecialchars((string)(!empty($service['thong_so_ky_thuat']) ? $service['thong_so_ky_thuat'] : ($service['mo_ta_chi_tiet'] ?? ''))); ?>"
               data-full-desc="<?php echo htmlspecialchars((string)(!empty($service['mo_ta_chi_tiet']) ? $service['mo_ta_chi_tiet'] : ($service['mo_ta_ngan'] ?? ''))); ?>">
               Xem chi tiết <i data-lucide="arrow-right"></i>
            </a>
          </div>
        </div>
      <?php 
          endforeach; 
      endif;
      ?>
    </div>
  </div>
</section>

<section class="services" id="services">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Dịch vụ của chúng tôi</div>
      <h2 class="section-title">Hệ sinh thái dịch vụ số <span class="gradient-text">toàn diện</span></h2>
      <p class="section-desc">Từ hạ tầng đến ứng dụng — mọi giải pháp số bạn cần đều có tại đây</p>
    </div>

    <div class="service-filter-bar" style="display: flex; justify-content: center; gap: 12px; margin-bottom: 40px; flex-wrap: wrap;">
        <button class="filter-btn active" data-filter="all" style="padding: 8px 18px; border-radius: 20px; border: 1px solid #0066CC; background: #0066CC; color: white; cursor: pointer; font-weight: 600; transition: 0.3s;">Tất cả</button>
        <button class="filter-btn" data-filter="cloud" style="padding: 8px 18px; border-radius: 20px; border: 1px solid #ccc; background: transparent; color: #333; cursor: pointer; font-weight: 600; transition: 0.3s;">Hạ tầng &amp; Cloud</button>
        <button class="filter-btn" data-filter="security" style="padding: 8px 18px; border-radius: 20px; border: 1px solid #ccc; background: transparent; color: #333; cursor: pointer; font-weight: 600; transition: 0.3s;">Bảo mật an toàn</button>
        <button class="filter-btn" data-filter="app" style="padding: 8px 18px; border-radius: 20px; border: 1px solid #ccc; background: transparent; color: #333; cursor: pointer; font-weight: 600; transition: 0.3s;">Ứng dụng doanh nghiệp</button>
        <button class="filter-btn" data-filter="internet" style="padding: 8px 18px; border-radius: 20px; border: 1px solid #ccc; background: transparent; color: #333; cursor: pointer; font-weight: 600; transition: 0.3s;">Internet &amp; Truyền hình</button>
        <button class="filter-btn" data-filter="thiet_bi" style="padding: 8px 18px; border-radius: 20px; border: 1px solid #ccc; background: transparent; color: #333; cursor: pointer; font-weight: 600; transition: 0.3s;">Thiết bị công nghệ</button>
    </div>

    <div class="services-grid" id="servicesGrid" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 26px; width: 100%; opacity: 1 !important; visibility: visible !important;">
      <?php foreach ($allServices as $i => $sv): 
          $id = $sv['ma_san_pham'];
          $ui = getDynamicProductUI($sv);
          $icon = $ui['icon']; 
          $color = $ui['color']; 
          $feats = $ui['feats']; 
          $category = $ui['category'];
          $isHot = ($i === 0);

          // Làm sạch đoạn tóm tắt ngắn cho phần <p> (UTF-8 safe)
          $cleanShortDesc = safeCleanText($sv['mo_ta_ngan'], 85);

          // TÍNH TOÁN GIÁ KHUYẾN MÃI
          $giaNiemYet = (int)$sv['gia_niem_yet'];
          $giaKhuyenMai = (int)$sv['gia_khuyen_mai'];
          $hasSale = ($giaKhuyenMai > 0 && $giaKhuyenMai < $giaNiemYet);
          $actualPrice = $hasSale ? $giaKhuyenMai : $giaNiemYet;
      ?>
      <div class="service-card <?= $isHot ? 'featured' : '' ?>" data-category="<?= $category ?>" data-delay="<?= $i * 100 ?>" id="dich-vu-<?= $id ?>" style="display: flex; flex-direction: column; background: #ffffff; border: 1px solid #E4E9F0; border-radius: 16px; padding: 30px 24px; min-height: 380px; width: 100%; opacity: 1; visibility: visible;" data-full-desc="<?= htmlspecialchars(safeCleanText($sv['mo_ta_chi_tiet'] ?: $sv['mo_ta_ngan'], 400)) ?>" data-full-features="<?= htmlspecialchars(json_encode($ui['fullFeats'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>">
        
        <?php if ($isHot) echo '<div class="featured-badge">Mới &amp; Phổ biến</div>'; ?>
        
        <?php if (!empty($sv['hinh_anh_url'])): ?>
        <div class="card-img-wrap mb-3 text-center" style="width: 100%; height: 160px; overflow: hidden; border-radius: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center;">
          <img src="<?= htmlspecialchars($sv['hinh_anh_url']) ?>" alt="<?= htmlspecialchars($sv['ten_san_pham']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain; transition: transform 0.3s ease;">
        </div>
        <?php else: ?>
        <div class="card-icon-wrap" style="--card-color:<?= $color ?>">
          <i data-lucide="<?= $icon ?>" class="card-icon"></i>
        </div>
        <?php endif; ?>
        
        <h3><?= htmlspecialchars($sv['ten_san_pham']) ?></h3>
        <p><?= htmlspecialchars($cleanShortDesc) ?></p>
        
        <ul class="card-features">
          <?php foreach ($feats as $f) echo "<li><i data-lucide='check'></i> " . htmlspecialchars((string)($f ?? '')) . "</li>"; ?>
        </ul>
        
        <div class="card-price" style="margin: 12px 0 8px; color: var(--ink-soft); font-size: 0.9rem;">
          <?php if ($hasSale): ?>
            <span style="text-decoration: line-through; color: var(--ink-faint); margin-right: 8px;">
              <?= number_format($giaNiemYet, 0, ',', '.') ?> ₫
            </span>
            Từ <strong style="color: #E53E3E; font-size: 1.1rem; font-weight: 800;"><?= number_format($giaKhuyenMai, 0, ',', '.') ?> ₫</strong>/<?= htmlspecialchars((string)($sv['don_vi_tinh'] ?? '')) ?>
            <span style="background: #FFF5F5; color: #E53E3E; border: 1px solid #FED7D7; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; margin-left: 6px;">Giảm giá</span>
          <?php else: ?>
            Từ <strong style="color: var(--blue-600); font-size: 1rem; font-weight: 700;"><?= number_format($giaNiemYet, 0, ',', '.') ?> ₫</strong>/<?= htmlspecialchars((string)($sv['don_vi_tinh'] ?? '')) ?>
          <?php endif; ?>
        </div>
        <div class="card-actions">
          <a href="javascript:void(0);" class="card-link open-product-detail-modal" 
             onclick="if(window.openProductDetailModal){ window.openProductDetailModal(this); } return false;"
             data-id="<?= $id ?>" 
             data-name="<?= htmlspecialchars((string)($sv['ten_san_pham'] ?? '')) ?>" 
             data-price="<?= number_format($actualPrice, 0, ',', '.') ?> ₫/<?= htmlspecialchars((string)($sv['don_vi_tinh'] ?? 'tháng')) ?>" 
             data-type="<?= htmlspecialchars((string)($sv['loai_san_pham'] ?? 'dich_vu_so')) ?>"
             data-img="<?= htmlspecialchars((string)($sv['hinh_anh_url'] ?? '')) ?>"
             data-short-desc="<?= htmlspecialchars((string)($cleanShortDesc ?? '')) ?>"
             data-full-specs="<?= htmlspecialchars((string)(!empty($sv['thong_so_ky_thuat']) ? $sv['thong_so_ky_thuat'] : ($sv['mo_ta_chi_tiet'] ?? ''))) ?>"
             data-full-desc="<?= htmlspecialchars((string)(!empty($sv['mo_ta_chi_tiet']) ? $sv['mo_ta_chi_tiet'] : ($sv['mo_ta_ngan'] ?? ''))) ?>">
             Tìm hiểu thêm <i data-lucide="arrow-right"></i>
          </a>
          <button class="btn-add-cart" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($sv['ten_san_pham']) ?>" data-price="<?= $actualPrice ?>" data-icon="<?= $icon ?>" data-color="<?= $color ?>">
            <i data-lucide="shopping-cart"></i> Thêm vào giỏ
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="pricing-section" id="pricing">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Bảng giá dịch vụ</div>
      <h2 class="section-title">Chọn gói phù hợp với <span class="gradient-text">doanh nghiệp của bạn</span></h2>
      <p class="section-desc">Các gói dịch vụ linh hoạt, minh bạch — nâng cấp bất cứ lúc nào khi doanh nghiệp phát triển</p>
    </div>

    <div class="pricing-grid" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 26px; width: 100%; opacity: 1 !important; visibility: visible !important;">
      <?php foreach ($allCombos as $i => $combo): 
          $id = $combo['ma_san_pham'];
          $ui = getDynamicProductUI($combo);
          $icon = $ui['icon']; 
          $color = $ui['color']; 
          $isPop = ($i === 1 || count($allCombos) === 1);
          
          // Trích xuất các gạch đầu dòng tính năng cho gói cước từ CSDL
          $feats = [];
          if (!empty($ui['fullFeats']) && is_array($ui['fullFeats'])) {
              foreach ($ui['fullFeats'] as $fItem) {
                  $feats[] = htmlspecialchars($fItem);
                  if (count($feats) >= 4) break;
              }
          } elseif (!empty($combo['mo_ta_ngan'])) {
              $lines = array_filter(array_map('trim', preg_split('/[\n\r;–•]+/u', $combo['mo_ta_ngan'])));
              foreach ($lines as $l) {
                  $lClean = trim(preg_replace('/^[-\s•–]+/', '', $l));
                  if (mb_strlen($lClean, 'UTF-8') >= 5) {
                      $feats[] = htmlspecialchars($lClean);
                  }
                  if (count($feats) >= 4) break;
              }
          }
          if (count($feats) < 3) {
              $feats = [
                  'Tối ưu chi phí vận hành doanh nghiệp',
                  'Cam kết chất lượng SLA 99.9%',
                  'Hỗ trợ kỹ thuật chuyên nghiệp 24/7'
              ];
          }

          // TÍNH TOÁN GIÁ KHUYẾN MÃI COMBO
          $giaNiemYet = (int)$combo['gia_niem_yet'];
          $giaKhuyenMai = (int)$combo['gia_khuyen_mai'];
          $hasSale = ($giaKhuyenMai > 0 && $giaKhuyenMai < $giaNiemYet);
          $actualPrice = $hasSale ? $giaKhuyenMai : $giaNiemYet;
      ?>
      <div class="price-card <?= $isPop ? 'popular' : '' ?>" data-delay="<?= $i * 100 ?>" style="display: flex !important; flex-direction: column; background: #ffffff !important; border: 1px solid #E4E9F0 !important; border-radius: 20px; padding: 32px 26px; min-height: 400px; width: 100%; opacity: 1 !important; visibility: visible !important;">
        
        <?php if ($isPop) echo '<div class="price-popular-badge">Phổ biến nhất</div>'; ?>
        
        <h3 class="price-plan-name"><?= htmlspecialchars($combo['ten_san_pham']) ?></h3>
        <p class="price-plan-desc"><?= htmlspecialchars($combo['mo_ta_ngan']) ?></p>
        
        <div class="price-amount" style="display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap; margin-bottom: 1.4rem;">
          <?php if ($hasSale): ?>
            <span class="price-num" style="font-size: 2.2rem; font-weight: 800; color: #E53E3E;"><?= number_format($giaKhuyenMai, 0, ',', '.') ?> ₫</span>
            <span style="text-decoration: line-through; color: var(--ink-faint); font-size: 1rem; font-weight: 500; margin-right: 4px;">
              <?= number_format($giaNiemYet, 0, ',', '.') ?> ₫
            </span>
            <span class="price-unit" style="font-size: .9rem; color: var(--ink-faint);">/<?= htmlspecialchars($combo['don_vi_tinh']) ?></span>
            <span style="background: #E53E3E; color: white; padding: 3px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 4px 10px rgba(229, 62, 62, 0.2); margin-left: 4px;">SALE</span>
          <?php else: ?>
            <span class="price-num" style="font-size: 2.2rem; font-weight: 800; color: var(--blue-500);"><?= number_format($giaNiemYet, 0, ',', '.') ?> ₫</span>
            <span class="price-unit" style="font-size: .9rem; color: var(--ink-faint);">/<?= htmlspecialchars($combo['don_vi_tinh']) ?></span>
          <?php endif; ?>
        </div>
        
        <ul class="price-features">
          <?php foreach ($feats as $f) echo "<li><i data-lucide='check'></i> $f</li>"; ?>
        </ul>
        
        <button class="btn-price btn-add-cart" 
                data-id="<?= $id ?>" 
                data-name="<?= htmlspecialchars($combo['ten_san_pham']) ?>" 
                data-price="<?= $actualPrice ?>" 
                data-icon="<?= $icon ?>" 
                data-color="<?= $color ?>">
          <i data-lucide="shopping-cart"></i> Chọn gói này
        </button>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="stats-section" id="stats">
  <div class="stats-bg-shape"></div>
  <div class="container">
    <div class="stats-inner">
      <div class="stats-text">
        <div class="section-tag light">Tại sao chọn chúng tôi</div>
        <h2 class="section-title light">Tin tưởng bởi hàng trăm nghìn <span class="gold-text">doanh nghiệp</span></h2>
        <p class="section-desc light">Với hơn 30 năm kinh nghiệm, chúng tôi đã đồng hành cùng sự phát triển của hàng trăm nghìn doanh nghiệp Việt Nam trên hành trình chuyển đổi số.</p>
        <a href="#consultation" class="btn-primary" id="openConsultationBtn">Bắt đầu ngay <i data-lucide="arrow-right"></i></a>
      </div>
      <div class="stats-grid">
        <div class="stat-box clickable-stat" data-type="customers" title="Nhấp để xem danh sách tập đoàn đối tác">
          <div class="stat-icon"><i data-lucide="users"></i></div>
          <div class="stat-num counter" data-target="500000" data-suffix="+">500.000+</div>
          <div class="stat-label">Khách hàng tin dùng</div>
          <div class="stat-click-hint">Xem đối tác <i data-lucide="arrow-up-right"></i></div>
        </div>
        <div class="stat-box clickable-stat" data-type="servers" title="Nhấp để xem hạ tầng Data Center Tier III">
          <div class="stat-icon"><i data-lucide="server"></i></div>
          <div class="stat-num counter" data-target="10000" data-suffix="+">10.000+</div>
          <div class="stat-label">Server đang hoạt động</div>
          <div class="stat-click-hint">Xem hạ tầng <i data-lucide="arrow-up-right"></i></div>
        </div>
        <div class="stat-box clickable-stat" data-type="provinces" title="Nhấp để xem bản đồ phủ sóng 63 tỉnh thành">
          <div class="stat-icon"><i data-lucide="globe"></i></div>
          <div class="stat-num counter" data-target="63" data-suffix="/63">63/63</div>
          <div class="stat-label">Tỉnh thành phủ sóng</div>
          <div class="stat-click-hint">Xem phủ sóng <i data-lucide="arrow-up-right"></i></div>
        </div>
        <div class="stat-box clickable-stat" data-type="awards" title="Nhấp để xem danh hiệu & giải thưởng quốc tế">
          <div class="stat-icon"><i data-lucide="award"></i></div>
          <div class="stat-num counter" data-target="150" data-suffix="+">150+</div>
          <div class="stat-label">Giải thưởng công nghệ</div>
          <div class="stat-click-hint">Xem giải thưởng <i data-lucide="arrow-up-right"></i></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="features" id="demo">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Quy trình triển khai</div>
      <h2 class="section-title">Bắt đầu chỉ trong <span class="gradient-text">3 bước đơn giản</span></h2>
    </div>
    <div class="steps-wrap">
      <div class="step-line"></div>
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-icon"><i data-lucide="clipboard-list"></i></div>
        <h3>Tư vấn & Phân tích</h3>
        <p>Chuyên gia của chúng tôi sẽ phân tích nhu cầu và đề xuất giải pháp phù hợp nhất cho doanh nghiệp bạn.</p>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-icon"><i data-lucide="settings"></i></div>
        <h3>Triển khai & Tích hợp</h3>
        <p>Đội ngũ kỹ thuật triển khai nhanh chóng, tích hợp liền mạch với hệ thống hiện có của doanh nghiệp.</p>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-icon"><i data-lucide="trending-up"></i></div>
        <h3>Vận hành & Tối ưu</h3>
        <p>Hỗ trợ 24/7, giám sát liên tục và tối ưu hóa hiệu suất để đảm bảo hoạt động ổn định nhất.</p>
      </div>
    </div>
  </div>
</section>

<section class="testimonials" id="testimonials" style="padding: 70px 0;">
  <div class="container">
    <div class="section-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 28px;">
      <div>
        <div class="section-tag">Khách hàng nói gì</div>
        <h2 class="section-title" style="margin: 0;">Phản hồi từ <span class="gradient-text">đối tác</span></h2>
      </div>
      
      <div style="display: flex; align-items: center; gap: 14px;">
        <div class="testi-controls" style="display: flex; gap: 8px;">
          <button type="button" id="testiPrevBtn" aria-label="Xem nhận xét trước" style="width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid #CBD5E1; background: white; color: #1E293B; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"><i data-lucide="chevron-left" style="width:20px; height:20px;"></i></button>
          <button type="button" id="testiNextBtn" aria-label="Xem nhận xét tiếp theo" style="width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid #CBD5E1; background: white; color: #1E293B; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"><i data-lucide="chevron-right" style="width:20px; height:20px;"></i></button>
        </div>

        <button type="button" id="openAddReviewBtn" style="padding: 9px 18px; border-radius: 12px; border: 1.5px solid #0066CC; background: #F0F9FF; color: #0066CC; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 14px rgba(0,102,204,0.12);">
          <i data-lucide="message-square-plus" style="width: 16px; height: 16px;"></i>
          <span>Gửi Nhận Xét Của Khách Hàng</span>
        </button>
      </div>
    </div>

    <!-- CAROUSEL WRAPPER WITH SCROLLBAR FOR ALL TESTIMONIALS -->
    <div class="testi-carousel-container" style="overflow-x: auto; scroll-behavior: smooth; margin: 0 -10px; padding: 10px 10px 18px 10px;">
      <div class="testi-grid" id="testiGrid" style="display: flex; gap: 20px; transition: transform 0.45s cubic-bezier(0.25, 1, 0.5, 1); flex-wrap: nowrap;">
        <?php
        require_once __DIR__ . '/../../admin_panel/db.php';
        try {
            $approvedReviews = $pdo->query("
                SELECT * FROM danh_gia_san_pham
                 WHERE trang_thai_duyet = 'da_duyet'
              ORDER BY created_at DESC
                 LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($approvedReviews)):
                foreach ($approvedReviews as $idx => $r):
                    $isFeatured = ($idx % 2 === 1);
                    $name = $r['ho_ten_nguoi_danh_gia'] ?: 'Khách hàng';
                    $initials = mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                    $company = $r['chuc_vu_cong_ty'] ?: 'Đối tác Doanh nghiệp';
                    $service = $r['ten_dich_vu'] ?: 'Cloud Enterprise';
                    $stars = (int)$r['so_sao'];
                    $starsHtml = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
                    $badgeBg = $isFeatured ? 'rgba(0, 229, 255, 0.2)' : 'rgba(0, 102, 204, 0.1)';
                    $badgeColor = $isFeatured ? '#00E5FF' : '#0066CC';
                    $grad = $isFeatured ? 'linear-gradient(135deg,#FF6B00,#FFB347)' : 'gradient(135deg,#0066CC,#00AAFF)';
        ?>
        <div class="testi-card <?= $isFeatured ? 'featured-testi' : '' ?> clickable-testi" data-id="<?= $r['id'] ?>" onclick="if(window.openReviewDetailModal){ window.openReviewDetailModal(this); } return false;" title="Nhấp để xem chi tiết nhận xét &amp; phản hồi từ Admin" style="flex: 0 0 calc(33.333% - 14px); min-width: 300px; box-sizing: border-box; margin: 0; cursor: pointer;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
            <div class="testi-stars" style="color: #FFB347;"><?= $starsHtml ?></div>
            <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 10px;"><?= htmlspecialchars($service) ?></span>
          </div>
          <p style="font-size: 0.92rem; line-height: 1.55; margin-bottom: 16px;">"<?= htmlspecialchars($r['noi_dung']) ?>"</p>

          <?php if (!empty($r['phan_hoi_admin'])): ?>
          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-left: 4px solid #166534; padding: 10px 14px; border-radius: 0 10px 10px 0; margin-bottom: 14px; font-size: 0.84rem; color: #166534;">
            <strong style="color: #15803D; display: block; margin-bottom: 2px;"><i class="fa-solid fa-reply me-1"></i> VNPT Admin phản hồi:</strong>
            "<?= htmlspecialchars($r['phan_hoi_admin']) ?>"
          </div>
          <?php endif; ?>

          <div class="testi-author">
            <div class="testi-avatar" style="background:<?= $grad ?>"><?= htmlspecialchars($initials) ?></div>
            <div>
              <strong><?= htmlspecialchars($name) ?></strong>
              <span><?= htmlspecialchars($company) ?></span>
            </div>
          </div>
          <div class="testi-hint" style="margin-top: 12px; font-size: 0.8rem; color: #0066CC; font-weight: 700;">Xem chi tiết &amp; Phản hồi <i data-lucide="arrow-up-right" style="width:14px; height:14px;"></i></div>
        </div>
        <?php
                endforeach;
            else:
                echo '<div style="padding: 40px; text-align: center; color: #64748B; width: 100%;">Chưa có nhận xét nào được phê duyệt.</div>';
            endif;
        } catch (Exception $ex) {
            echo '<div style="padding: 40px; text-align: center; color: #64748B; width: 100%;">Chưa có nhận xét nào.</div>';
        }
        ?>
      </div>
    </div>

    <!-- PAGINATION DOTS -->
    <div id="testiDotsContainer" style="display: flex; justify-content: center; gap: 8px; margin-top: 20px;"></div>
  </div>
</section>
</section>

<section class="contact-cta" id="contact">
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="container">
    <div class="cta-inner">
      <div class="cta-text">
        <h2>Sẵn sàng chuyển đổi số<br/><span class="gold-text">doanh nghiệp của bạn?</span></h2>
        <p>Liên hệ ngay để được tư vấn miễn phí và nhận ưu đãi đặc biệt dành riêng cho doanh nghiệp.</p>
        <div class="cta-contacts">
          <a href="tel:18001260" class="cta-contact-item" title="Nhấp để gọi tổng đài miễn phí 1800 1260">
            <i data-lucide="phone"></i>
            <span>1800 1260 (Miễn phí)</span>
          </a>
          <a href="mailto:contact@vnpt.vn" class="cta-contact-item" title="Nhấp để gửi email hỗ trợ">
            <i data-lucide="mail"></i>
            <span>contact@vnpt.vn</span>
          </a>
          <a href="https://maps.google.com/?q=57+Huynh+Thuc+Khang+Ha+Noi" target="_blank" rel="noopener" class="cta-contact-item" title="Nhấp để xem chỉ đường Google Maps">
            <i data-lucide="map-pin"></i>
            <span>57 Huỳnh Thúc Kháng, Hà Nội</span>
          </a>
        </div>
      </div>
      <form class="contact-form" id="contactForm">
        <h3>Đăng ký tư vấn miễn phí</h3>
        <div class="form-row">
          <div class="form-group">
            <label>Họ và tên</label>
            <input type="text" id="bottomContactName" placeholder="Nguyễn Văn A" required />
          </div>
          <div class="form-group">
            <label>Số điện thoại</label>
            <input type="tel" id="bottomContactPhone" placeholder="0901234567" required pattern="(0|\+84)[35789][0-9]{8}" title="Vui lòng nhập số điện thoại hợp lệ (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09 hoặc +84)" />
          </div>
        </div>
        <div class="form-group">
          <label>Email doanh nghiệp</label>
          <input type="email" id="bottomContactEmail" placeholder="contact@company.vn" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Vui lòng nhập đúng định dạng Email hợp lệ (ví dụ: contact@company.vn)" />
        </div>
        <div class="form-group">
          <label>Dịch vụ quan tâm</label>
          <select id="bottomContactService">
            <option value="cloud">Cloud Computing &amp; Data Center</option>
            <option value="security">Bảo mật &amp; An toàn số ISO 27001</option>
            <option value="ai">AI &amp; Tự động hóa Doanh nghiệp</option>
            <option value="5g">Hạ tầng mạng 5G &amp; Truyền dẫn</option>
            <option value="enterprise">Quản trị doanh nghiệp số VNPT</option>
            <option value="combo">Gói giải pháp tổng thể Chuyển đổi số</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nội dung cần tư vấn</label>
          <textarea id="bottomContactMessage" rows="3" placeholder="Mô tả nhu cầu của doanh nghiệp bạn..."></textarea>
        </div>
        <button type="submit" class="btn-primary full-width">
          Gửi yêu cầu tư vấn <i data-lucide="send"></i>
        </button>
        <div class="form-success" id="formSuccess">
          <i data-lucide="check-circle"></i> Cảm ơn! Chúng tôi sẽ phản hồi cho bạn trong thời gian sớm nhất.
        </div>
      </form>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const serviceCards = document.querySelectorAll('.service-card');
    const servicesGrid = document.getElementById('servicesGrid');

    if (filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => {
                    b.style.background = 'transparent';
                    b.style.color = '#333';
                    b.style.border = '1px solid #ccc';
                    b.classList.remove('active');
                });
                
                this.style.background = '#0066CC';
                this.style.color = 'white';
                this.style.border = '1px solid #0066CC';
                this.classList.add('active');

                const filterValue = this.getAttribute('data-filter');
                const filterTitle = this.textContent.trim();
                let visibleCount = 0;
                
                serviceCards.forEach(card => {
                    const category = card.getAttribute('data-category') || 'all';
                    
                    if (filterValue === 'all' || category.includes(filterValue)) {
                        card.style.display = 'flex'; 
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // XỬ LÝ KHUNG EMPTY STATE SANG TRỌNG NẾU KHÔNG CÓ SẢN PHẨM TRONG DANH MỤC LỌC
                let emptyEl = document.getElementById('filterEmptyState');
                if (visibleCount === 0 && servicesGrid) {
                    if (!emptyEl) {
                        emptyEl = document.createElement('div');
                        emptyEl.id = 'filterEmptyState';
                        emptyEl.style.cssText = 'grid-column: 1 / -1; padding: 48px 24px; background: linear-gradient(135deg, #F8FAFC, #EDF2F7); border: 2px dashed #CBD5E1; border-radius: 20px; text-align: center; margin: 10px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.03);';
                        servicesGrid.appendChild(emptyEl);
                    }
                    emptyEl.style.display = 'block';
                    emptyEl.innerHTML = `
                        <div style="width: 76px; height: 76px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #0066CC, #00AAFF); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 12px 28px rgba(0,102,204,0.35); border: 4px solid #ffffff;">
                          <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin-bottom: 8px;">Danh mục "${filterTitle}" đang được cập nhật giải pháp mới</h3>
                        <p style="font-size: 0.95rem; color: #64748B; max-width: 540px; margin: 0 auto 24px; line-height: 1.6;">
                          Các dịch vụ thuộc danh mục này đang được VNPT chuẩn bị hạ tầng và sẽ bàn giao tới Quý khách trong thời gian sớm nhất. Quý khách có thể yêu cầu tư vấn gói giải pháp thiết kế riêng!
                        </p>
                        <button type="button" class="btn-primary btn-open-consultation" onclick="if(window.openConsultation) window.openConsultation();" style="padding: 12px 28px; border-radius: 12px; font-weight: 700; background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,102,204,0.3);">
                          <i class="fa-solid fa-headset"></i> Đăng ký tư vấn giải pháp riêng
                        </button>
                    `;
                } else {
                    if (emptyEl) emptyEl.style.display = 'none';
                }
            });
        });
    }
});
</script>

<!-- Modal Xem Chi Tiết & Thông Số Kỹ Thuật Sản Phẩm / Thiết Bị (Dynamic Height & Auto Scroll) -->
<div class="modal-backdrop" id="productDetailModal" style="display: none; position: fixed; inset: 0; z-index: 9999999 !important; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); align-items: center; justify-content: center; padding: 20px;">
  <div class="modal-card" style="background: #ffffff; border-radius: 20px; max-width: 820px; width: 100%; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: modalFadeIn 0.3s ease;">
    <!-- Header Modal (Cố định ở đỉnh) -->
    <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px 24px; color: #ffffff; position: relative; flex-shrink: 0;">
      <button type="button" id="closeProductDetailModal" style="position: absolute; top: 18px; right: 18px; background: rgba(255,255,255,0.15); border: none; color: #ffffff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;">
        <i data-lucide="x"></i>
      </button>
      <div style="display: flex; align-items: center; gap: 16px;">
        <div id="pDetailImageWrap" style="width: 65px; height: 65px; background: #ffffff; border-radius: 14px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px; flex-shrink: 0;">
          <img id="pDetailImg" src="" alt="Ảnh sản phẩm" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
        <div>
          <h3 id="pDetailName" style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin: 0 0 6px 0;">Tên sản phẩm</h3>
          <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <span id="pDetailPrice" style="color: #38BDF8; font-weight: 800; font-size: 1.05rem;">0 ₫</span>
            <span id="pDetailTag" style="background: rgba(56, 189, 248, 0.15); color: #38BDF8; border: 1px solid rgba(56, 189, 248, 0.3); padding: 2px 10px; border-radius: 12px; font-size: 0.78rem; font-weight: 700;">Dịch vụ số</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Body Modal: Specs & Detail Info Tabs (Tự động Cuộn Mượt Tùy Độ Dài Bài Viết) -->
    <div style="flex: 1; overflow-y: auto; padding: 24px; scrollbar-width: thin;">
      <!-- Tab Buttons (Segmented Control Pill Style với Icon FontAwesome Nổi Bật) -->
      <div class="product-spec-tabs-wrapper">
        <div class="product-spec-tabs" id="productSpecTabs">
          <button type="button" class="spec-tab-btn active" id="tabBtnSpecs" data-tab="specs">
            <i class="fa-solid fa-microchip"></i> <span>Thông số kỹ thuật</span>
          </button>
          <button type="button" class="spec-tab-btn" id="tabBtnInfo" data-tab="info">
            <i class="fa-solid fa-circle-info"></i> <span>Thông tin sản phẩm</span>
          </button>
        </div>
      </div>

      <!-- Tab Content 1: Thông số kỹ thuật -->
      <div id="tabContentSpecs" class="tab-pane-content" style="display: block;">
        <div id="pDetailSpecsBody">
          <!-- Bảng thông số kỹ thuật dạng key-value -->
        </div>
      </div>

      <!-- Tab Content 2: Thông tin sản phẩm -->
      <div id="tabContentInfo" class="tab-pane-content" style="display: none;">
        <div id="pDetailInfoBody" style="color: #334155; line-height: 1.7; font-size: 0.95rem;">
          <!-- Mô tả tổng quan -->
        </div>
      </div>
    </div>

    <!-- Footer Modal (Cố định ở đáy) -->
    <div style="background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
      <button type="button" id="closeProductDetailBtn" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 10px; border: 1px solid #CBD5E1; background: #ffffff; color: #475569; font-weight: 600; cursor: pointer;">Đóng cửa sổ</button>
      <button type="button" id="btnModalAddToCart" class="btn btn-primary" style="padding: 10px 24px; border-radius: 10px; border: none; background: #0066CC; color: #ffffff; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="shopping-cart"></i> Đăng ký / Thêm vào giỏ hàng
      </button>
    </div>
  </div>
</div>

<!-- Modal Gửi Nhận Xét & Đánh Giá Dịch Vụ Khách Hàng -->
<div class="auth-modal" id="customerReviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:2000; align-items:center; justify-content:center; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px);">
  <div class="modal-card" style="width: 100%; max-width: 520px; background: #ffffff; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #0066CC, #00AAFF); padding: 20px 24px; color: white; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: white;"><i data-lucide="message-square-plus" style="width:20px;height:20px;vertical-align:middle;margin-right:6px;"></i> Gửi Nhận Xét &amp; Đánh Giá</h3>
        <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: rgba(255,255,255,0.85);">Chia sẻ ý kiến trải nghiệm dịch vụ số VNPT / VNPT của bạn</p>
      </div>
      <button type="button" id="closeCustomerReviewModal" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>
    <form id="customerReviewForm" style="padding: 24px;">
      <div id="reviewAlertBox" style="display:none; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 0.88rem; font-weight: 600;"></div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Họ &amp; Tên hoặc Email của bạn <span style="color: #E53E3E;">*</span></label>
        <input type="text" id="reviewAuthorName" placeholder="Nguyễn Thanh Tùng" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Chức vụ &amp; Tên Doanh nghiệp / Tổ chức</label>
        <input type="text" id="reviewAuthorCompany" placeholder="CTO – Công ty CP Thương mại ABC" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Dịch vụ quan tâm</label>
          <select id="reviewService" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.88rem; outline: none; background: white;">
            <option value="Cloud Enterprise">Cloud Enterprise</option>
            <option value="Bảo Mật & SmartCA">Bảo Mật &amp; SmartCA</option>
            <option value="VNPT AI OCR">VNPT AI OCR</option>
            <option value="Combo SME">Combo SME</option>
            <option value="Giải Pháp Tập Đoàn">Giải Pháp Tập Đoàn</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Số sao đánh giá</label>
          <select id="reviewRating" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.88rem; outline: none; background: white;">
            <option value="5" selected>⭐⭐⭐⭐⭐ (5 sao)</option>
            <option value="4">⭐⭐⭐⭐ (4 sao)</option>
            <option value="3">⭐⭐⭐ (3 sao)</option>
            <option value="2">⭐⭐ (2 sao)</option>
            <option value="1">⭐ (1 sao)</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Tiêu đề đánh giá</label>
        <input type="text" id="reviewTitle" placeholder="Dịch vụ Cloud rất ấn tượng và ổn định" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none;">
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">Nội dung nhận xét chi tiết <span style="color: #E53E3E;">*</span></label>
        <textarea id="reviewContent" rows="4" required placeholder="Chia sẻ ý kiến phàn hồi hoặc trải nghiệm của bạn về sản phẩm/dịch vụ VNPT..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none; resize: vertical;"></textarea>
      </div>

      <div style="display: flex; gap: 14px; justify-content: flex-end; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #F1F5F9;">
        <button type="button" id="cancelCustomerReviewBtn" style="padding: 12px 22px; border-radius: 12px; border: 1.5px solid #E2E8F0; background: #FFFFFF; color: #64748B; font-weight: 600; font-size: 0.92rem; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;" onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#CBD5E1'; this.style.color='#334155';" onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#E2E8F0'; this.style.color='#64748B';">
          <i data-lucide="x-circle" style="width:16px; height:16px;"></i> Hủy bỏ
        </button>
        <button type="submit" id="submitCustomerReviewBtn" style="padding: 12px 28px; border-radius: 12px; border: none; background: linear-gradient(135deg, #0066CC 0%, #00AAFF 100%); color: #ffffff; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 6px 18px rgba(0,102,204,0.35); transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(0,102,204,0.45)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 18px rgba(0,102,204,0.35)';">
          <span>Gửi nhận xét ngay</span>
          <i data-lucide="send" style="width: 17px; height: 17px;"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  (function() {
    var dynamicStyle = document.getElementById('featured-visibility-style');
    if (!dynamicStyle) {
      dynamicStyle = document.createElement('style');
      dynamicStyle.id = 'featured-visibility-style';
      document.head.appendChild(dynamicStyle);
    }

    function enforceVisibility() {
      var hash = window.location.hash || '';
      var section = document.getElementById('featured-services');
      var targetStyle = '';
      
      if (hash.indexOf('page=') !== -1) {
        targetStyle = '#featured-services { display: none !important; opacity: 0 !important; height: 0 !important; padding: 0 !important; margin: 0 !important; overflow: hidden !important; pointer-events: none !important; visibility: hidden !important; }';
      } else {
        targetStyle = '#featured-services { display: block !important; opacity: 1 !important; height: auto !important; overflow: visible !important; visibility: visible !important; margin: 0 !important; padding: 40px 0 80px 0 !important; }';
        if (section && section.style.display === 'none') {
            section.style.display = '';
        }
      }

      if (dynamicStyle.innerHTML !== targetStyle) {
          dynamicStyle.innerHTML = targetStyle;
      }
    }

    enforceVisibility();
    window.addEventListener('hashchange', enforceVisibility);
    
    var lastHash = window.location.hash;
    setInterval(function() {
      if (window.location.hash !== lastHash) {
        lastHash = window.location.hash;
        enforceVisibility();
      }
    }, 200);
  })();
</script>