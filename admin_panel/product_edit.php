<?php
/**
 * product_edit.php — Thêm mới & Chỉnh sửa Sản phẩm / Dịch vụ (Admin Panel)
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Kiểm tra quyền truy cập: QUẢN TRỊ VIÊN & NHÂN VIÊN BÁN HÀNG DỰA TRÊN PHÂN QUYỀN
$currentRole = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
$canManageProducts = in_array($currentRole, ['quan_tri_vien', 'superadmin', 'admin', 'nhan_vien_ban_hang', 'quan_ly', 'sales', 'staff']);
if (!$canManageProducts) {
    require_once __DIR__ . '/header.php';
    echo '<div class="page-content"><div class="alert alert-danger shadow-sm border-danger p-4" style="border-radius: 12px;">
            <h5 class="fw-bold mb-2 text-danger"><i class="fa-solid fa-shield-cat me-2"></i>Truy cập bị từ chối</h5>
            <p class="mb-0">Chức năng Thêm mới &amp; Chỉnh sửa Sản phẩm chỉ dành riêng cho tài khoản <strong>Quản trị viên (Admin)</strong> và <strong>Nhân viên Bán hàng</strong>. Tài khoản của bạn (<em>' . htmlspecialchars($currentRole) . '</em>) không được phép thực hiện thao tác này.</p>
          </div></div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;

$msg = '';
$msgType = 'danger';

// Dữ liệu sản phẩm mặc định (trống khi tạo mới)
$product = [
    'id' => 0,
    'ma_san_pham' => '',
    'ten_san_pham' => '',
    'slug' => '',
    'danh_muc_id' => 1,
    'loai_san_pham' => 'dich_vu_so',
    'thuong_hieu' => '',
    'mo_ta_ngan' => '',
    'mo_ta_chi_tiet' => '',
    'thong_so_ky_thuat' => '',
    'gia_niem_yet' => '',
    'gia_khuyen_mai' => null,
    'don_vi_tinh' => '',
    'hinh_anh_url' => '',
    'trang_thai' => 'dang_ban'
];

// Nếu là sửa, tải dữ liệu từ DB
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $product = array_merge($product, $existing);
    } else {
        die('<div class="alert alert-warning m-4">Không tìm thấy sản phẩm yêu cầu (ID #' . $id . ').</div>');
    }
}

// ── Xử lý POST lưu dữ liệu ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_san_pham      = trim($_POST['ma_san_pham'] ?? '');
    $ten_san_pham     = trim($_POST['ten_san_pham'] ?? '');
    $slug             = trim($_POST['slug'] ?? '');
    $danh_muc_id      = (int)($_POST['danh_muc_id'] ?? 1);
    $loai_san_pham    = trim($_POST['loai_san_pham'] ?? 'dich_vu_so');
    $thuong_hieu      = trim($_POST['thuong_hieu'] ?? 'VNPT');
    $gia_niem_yet     = (float)($_POST['gia_niem_yet'] ?? 0);
    $gia_khuyen_mai   = !empty($_POST['gia_khuyen_mai']) ? (float)$_POST['gia_khuyen_mai'] : null;
    $don_vi_tinh      = trim($_POST['don_vi_tinh'] ?? 'tháng');
    $hinh_anh_url     = trim($_POST['hinh_anh_url'] ?? '');
    $trang_thai       = trim($_POST['trang_thai'] ?? 'dang_ban');
    $mo_ta_ngan       = trim($_POST['mo_ta_ngan'] ?? '');
    $mo_ta_chi_tiet   = trim($_POST['mo_ta_chi_tiet'] ?? '');
    $thong_so_ky_thuat = trim($_POST['thong_so_ky_thuat'] ?? '');

    // Nếu chọn danh mục Thiết bị công nghệ (ID = 6), ép cố định loại hình và đơn vị tính
    if ($danh_muc_id === 6) {
        $loai_san_pham = 'thiet_bi';
        $don_vi_tinh   = 'thiết bị';
    }

    // Xử lý Upload file ảnh từ máy tính (nếu có chọn)
    if (isset($_FILES['hinh_anh_file']) && $_FILES['hinh_anh_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['hinh_anh_file']['tmp_name'];
        $fileName = $_FILES['hinh_anh_file']['name'];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . '/../frontend/assets/images/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newFileName = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $targetPath  = $uploadDir . $newFileName;
            
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $hinh_anh_url = 'assets/images/uploads/products/' . $newFileName;
            }
        }
    }

    // Đánh giá dữ liệu nhập
    if (empty($ten_san_pham)) {
        $msg = 'Vui lòng nhập tên sản phẩm / dịch vụ!';
    } else {
        if (empty($ma_san_pham)) {
            $ma_san_pham = 'svc-' . date('YmdHis');
        }

        if (empty($slug)) {
            // Tự tạo slug từ tên sản phẩm
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $ten_san_pham)));
        }

        try {
            if ($isEdit) {
                // Cập nhật sản phẩm hiện tại
                $sql = "UPDATE san_pham SET
                            ma_san_pham = :ma,
                            ten_san_pham = :ten,
                            slug = :slug,
                            danh_muc_id = :cat,
                            loai_san_pham = :loai,
                            thuong_hieu = :brand,
                            gia_niem_yet = :gia,
                            gia_khuyen_mai = :giakm,
                            don_vi_tinh = :dvt,
                            hinh_anh_url = :img,
                            trang_thai = :st,
                            mo_ta_ngan = :mtn,
                            mo_ta_chi_tiet = :mtct,
                            thong_so_ky_thuat = :tskt,
                            updated_at = NOW()
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':ma'    => $ma_san_pham,
                    ':ten'   => $ten_san_pham,
                    ':slug'  => $slug,
                    ':cat'   => $danh_muc_id,
                    ':loai'  => $loai_san_pham,
                    ':brand' => $thuong_hieu,
                    ':gia'   => $gia_niem_yet,
                    ':giakm' => $gia_khuyen_mai,
                    ':dvt'   => $don_vi_tinh,
                    ':img'   => $hinh_anh_url,
                    ':st'    => $trang_thai,
                    ':mtn'   => $mo_ta_ngan,
                    ':mtct'  => $mo_ta_chi_tiet,
                    ':tskt'  => $thong_so_ky_thuat,
                    ':id'    => $id
                ]);

                logActivity($pdo, "Đã cập nhật sản phẩm ID #$id ('$ten_san_pham')");
                $msg = '✅ Cập nhật sản phẩm thành công!';
                $msgType = 'success';
            } else {
                // Thêm sản phẩm mới
                $sql = "INSERT INTO san_pham (ma_san_pham, ten_san_pham, slug, danh_muc_id, loai_san_pham, thuong_hieu, gia_niem_yet, gia_khuyen_mai, don_vi_tinh, hinh_anh_url, trang_thai, mo_ta_ngan, mo_ta_chi_tiet, thong_so_ky_thuat, created_at)
                        VALUES (:ma, :ten, :slug, :cat, :loai, :brand, :gia, :giakm, :dvt, :img, :st, :mtn, :mtct, :tskt, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':ma'    => $ma_san_pham,
                    ':ten'   => $ten_san_pham,
                    ':slug'  => $slug,
                    ':cat'   => $danh_muc_id,
                    ':loai'  => $loai_san_pham,
                    ':brand' => $thuong_hieu,
                    ':gia'   => $gia_niem_yet,
                    ':giakm' => $gia_khuyen_mai,
                    ':dvt'   => $don_vi_tinh,
                    ':img'   => $hinh_anh_url,
                    ':st'    => $trang_thai,
                    ':mtn'   => $mo_ta_ngan,
                    ':mtct'  => $mo_ta_chi_tiet,
                    ':tskt'  => $thong_so_ky_thuat
                ]);
                $newId = $pdo->lastInsertId();
                logActivity($pdo, "Đã tạo sản phẩm mới ID #$newId ('$ten_san_pham')");

                header("Location: product_edit.php?id=$newId&created=1");
                exit;
            }

            // Reload dữ liệu mới
            $product = array_merge($product, [
                'ma_san_pham' => $ma_san_pham,
                'ten_san_pham' => $ten_san_pham,
                'slug' => $slug,
                'danh_muc_id' => $danh_muc_id,
                'loai_san_pham' => $loai_san_pham,
                'thuong_hieu' => $thuong_hieu,
                'gia_niem_yet' => $gia_niem_yet,
                'gia_khuyen_mai' => $gia_khuyen_mai,
                'don_vi_tinh' => $don_vi_tinh,
                'hinh_anh_url' => $hinh_anh_url,
                'trang_thai' => $trang_thai,
                'mo_ta_ngan' => $mo_ta_ngan,
                'mo_ta_chi_tiet' => $mo_ta_chi_tiet,
                'thong_so_ky_thuat' => $thong_so_ky_thuat
            ]);

        } catch (PDOException $e) {
            $msg = '⚠️ Lỗi cơ sở dữ liệu: ' . $e->getMessage();
            $msgType = 'danger';
        }
    }
}

if (isset($_GET['created'])) {
    $msg = '✅ Đã thêm sản phẩm mới thành công!';
    $msgType = 'success';
}

// Lấy danh sách danh mục
$categories = $pdo->query("SELECT id, ten_danh_muc FROM danh_muc_san_pham WHERE trang_thai = 1 ORDER BY thu_tu_hien_thi ASC, id ASC")->fetchAll();

$pageTitle  = $isEdit ? 'Chỉnh sửa Sản phẩm #' . $id : 'Thêm sản phẩm mới';
$activeMenu = 'products';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="fa-solid fa-box-archive text-primary me-2"></i>
            <?= $isEdit ? 'Chỉnh sửa sản phẩm: <span class="text-primary">' . htmlspecialchars($product['ten_san_pham']) . '</span>' : 'Thêm sản phẩm / dịch vụ mới' ?>
        </h4>
        <span class="text-muted small">Cập nhật thông tin giá cước, mô tả và phân loại cho sản phẩm</span>
    </div>
    <a href="products.php" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" action="product_edit.php<?= $isEdit ? '?id=' . $id : '' ?>">
    <div class="row g-4">
        <!-- Cột trái: Thông tin chính sản phẩm -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-signature me-2"></i>Thông tin cơ bản</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên sản phẩm / dịch vụ <span class="text-danger">*</span></label>
                        <input type="text" name="ten_san_pham" class="form-control form-control-lg" placeholder="Nhập tên sản phẩm / dịch vụ..." value="<?= htmlspecialchars($product['ten_san_pham']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã sản phẩm / SKU</label>
                            <input type="text" name="ma_san_pham" class="form-control font-monospace" placeholder="svc-001" value="<?= htmlspecialchars($product['ma_san_pham']) ?>">
                            <small class="text-muted">Mã định danh duy nhất của sản phẩm trong hệ thống</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Slug (Đường dẫn tĩnh)</label>
                            <input type="text" name="slug" class="form-control font-monospace" placeholder="cloud-server-enterprise" value="<?= htmlspecialchars($product['slug']) ?>">
                            <small class="text-muted">Để trống hệ thống sẽ tự sinh từ tên sản phẩm</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả ngắn</label>
                        <textarea name="mo_ta_ngan" class="form-control" rows="3" placeholder="Tóm tắt ngắn gọn các đặc điểm nổi bật của dịch vụ..."><?= htmlspecialchars($product['mo_ta_ngan']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả chi tiết / Tổng quan sản phẩm</label>
                        <textarea id="mo_ta_chi_tiet" name="mo_ta_chi_tiet" class="form-control" rows="7" placeholder="Nhập bài viết mô tả chi tiết, đặc điểm giải pháp..."><?= htmlspecialchars($product['mo_ta_chi_tiet']) ?></textarea>
                    </div>

                    <!-- TRÌNH TẠO THÔNG SỐ KỸ THUẬT TRỰC QUAN KHÔNG CẦN CODE (Dành riêng cho Thiết bị công nghệ) -->
                    <div id="specs_field_block" class="mb-3" style="display: none;">
                        <div class="card border border-primary-subtle shadow-sm bg-light">
                            <div class="card-header bg-primary bg-opacity-10 py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <label class="form-label fw-bold mb-0 text-primary fs-6">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Bảng Nhập Thông Số Kỹ Thuật
                                </label>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary fw-semibold btn-spec-preset" data-preset="camera">
                                        <i class="fa-solid fa-video me-1"></i> Mẫu Camera
                                    </button>
                                    <button type="button" class="btn btn-outline-primary fw-semibold btn-spec-preset" data-preset="router">
                                        <i class="fa-solid fa-wifi me-1"></i> Mẫu Router Wifi
                                    </button>
                                    <button type="button" class="btn btn-outline-primary fw-semibold btn-spec-preset" data-preset="token">
                                        <i class="fa-solid fa-key me-1"></i> Mẫu USB Token
                                    </button>
                                    <button type="button" id="btnClearSpecRows" class="btn btn-outline-danger fw-semibold">
                                        <i class="fa-solid fa-trash-can me-1"></i> Xóa hết
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive mb-2">
                                    <table class="table table-bordered table-hover align-middle bg-white mb-0" id="specsBuilderTable">
                                        <thead class="table-secondary small text-uppercase fw-bold">
                                            <tr>
                                                <th style="width: 35%;">Tên thông số (Độ phân giải)</th>
                                                <th>Giá trị thông số (3 MP 1296p)</th>
                                                <th style="width: 50px;" class="text-center">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="specsBuilderTbody">
                                            <!-- Dynamic Rows Generated by JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                    <button type="button" id="btnAddSpecRow" class="btn btn-sm btn-success fw-bold px-3">
                                        <i class="fa-solid fa-circle-plus me-1"></i> + Thêm dòng thông số mới
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none small" data-bs-toggle="collapse" data-bs-target="#collapseRawSpecs">
                                        <i class="fa-solid fa-code me-1"></i> Xem/Chỉnh sửa thô dạng Mã HTML / Text
                                    </button>
                                </div>

                                <div class="collapse mt-2" id="collapseRawSpecs">
                                    <textarea id="thong_so_ky_thuat" name="thong_so_ky_thuat" class="form-control font-monospace" rows="6" placeholder="Mã HTML hoặc Văn bản tự động đồng bộ..."><?= htmlspecialchars($product['thong_so_ky_thuat'] ?? '') ?></textarea>
                                    <small class="text-muted">Nhân viên chỉ cần nhập tên và giá trị vào bảng trên, hệ thống sẽ tự sinh mã bảng chuẩn đẹp mà không cần gõ code.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Phân loại, Giá cước, Hình ảnh & Trạng thái -->
        <div class="col-lg-4">
            <!-- Card Upload Hình ảnh -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-image me-2 text-primary"></i>Hình ảnh sản phẩm / Thiết bị</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['hinh_anh_url'])): ?>
                    <div class="mb-3 text-center p-2 bg-light rounded border">
                        <img src="../frontend/<?= htmlspecialchars($product['hinh_anh_url']) ?>" alt="Ảnh sản phẩm" style="max-height: 160px; max-width: 100%; object-fit: contain;" class="rounded shadow-sm">
                        <div class="small text-muted mt-2 font-monospace text-truncate"><?= htmlspecialchars($product['hinh_anh_url']) ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tải ảnh mới từ máy tính</label>
                        <input type="file" name="hinh_anh_file" accept="image/*" class="form-control">
                        <small class="text-muted d-block mt-1">Dành cho Thiết bị công nghệ &amp; Sản phẩm. Hỗ trợ: JPG, PNG, WEBP, SVG</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Hoặc Đường dẫn ảnh (URL / Relative)</label>
                        <input type="text" name="hinh_anh_url" class="form-control" placeholder="assets/images/img01.jpg..." value="<?= htmlspecialchars($product['hinh_anh_url'] ?? '') ?>">
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-sliders me-2"></i>Cấu hình &amp; Trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái phát hành</label>
                        <select name="trang_thai" class="form-select">
                            <option value="dang_ban" <?= $product['trang_thai'] === 'dang_ban' ? 'selected' : '' ?>>🟢 Đang cung cấp / Đang bán</option>
                            <option value="sap_ra_mat" <?= $product['trang_thai'] === 'sap_ra_mat' ? 'selected' : '' ?>>🟡 Sắp ra mắt</option>
                            <option value="ngung_ban" <?= $product['trang_thai'] === 'ngung_ban' ? 'selected' : '' ?>>🔴 Tạm ngưng cung cấp</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Danh mục sản phẩm</label>
                        <select id="danh_muc_id" name="danh_muc_id" class="form-select">
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $product['danh_muc_id'] == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['ten_danh_muc']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold mb-0">Loại hình sản phẩm</label>
                            <span id="badgeLockType" class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="display: none;">🔒 Cố định cho Thiết bị</span>
                        </div>
                        <select id="loai_san_pham" name="loai_san_pham" class="form-select">
                            <option value="dich_vu_so" <?= $product['loai_san_pham'] === 'dich_vu_so' ? 'selected' : '' ?>>🌐 Dịch vụ số (Cloud/AI/Bảo mật)</option>
                            <option value="combo" <?= $product['loai_san_pham'] === 'combo' ? 'selected' : '' ?>>📦 Gói Combo Doanh nghiệp</option>
                            <option value="thiet_bi" <?= $product['loai_san_pham'] === 'thiet_bi' ? 'selected' : '' ?>>💻 Thiết bị công nghệ</option>
                            <option value="goi_cuoc_di_dong" <?= $product['loai_san_pham'] === 'goi_cuoc_di_dong' ? 'selected' : '' ?>>📱 Gói cước di động</option>
                            <option value="goi_internet_truyen_hinh" <?= $product['loai_san_pham'] === 'goi_internet_truyen_hinh' ? 'selected' : '' ?>>📡 Internet &amp; Truyền hình</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Thương hiệu / Nhà cung cấp</label>
                        <input type="text" name="thuong_hieu" class="form-control" placeholder="Nhập thương hiệu / nhà cung cấp..." value="<?= htmlspecialchars($product['thuong_hieu']) ?>">
                    </div>

                    <hr class="my-3">

                    <!-- Ô Nhập Giá Niêm Yết Đã Tích Hợp Ngắt Dòng Định Dạng Tiền Tệ -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá niêm yết (₫) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" id="gia_niem_yet" name="gia_niem_yet" class="form-control fs-5 fw-bold text-primary" step="1000" min="0" value="<?= $product['gia_niem_yet'] !== '' ? (float)$product['gia_niem_yet'] : '' ?>" placeholder="Nhập số tiền..." required>
                            <span class="input-group-text">₫</span>
                        </div>
                        <div id="gia_niem_yet_formatted" class="form-text text-primary fw-bold font-monospace mt-1"></div>
                    </div>

                    <!-- Ô Nhập Giá Khuyến Mại Đã Tích Hợp Ngắt Dòng Định Dạng Tiền Tệ -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá khuyến mại (₫)</label>
                        <div class="input-group">
                            <input type="number" id="gia_khuyen_mai" name="gia_khuyen_mai" class="form-control" step="1000" min="0" value="<?= $product['gia_khuyen_mai'] ? (float)$product['gia_khuyen_mai'] : '' ?>" placeholder="Bỏ trống nếu không có">
                            <span class="input-group-text">₫</span>
                        </div>
                        <div id="gia_khuyen_mai_formatted" class="form-text text-danger fw-bold font-monospace mt-1"></div>
                    </div>

                    <?php
                    $validUnits = [
                        'tháng'     => 'tháng — (Thuê bao / Dịch vụ hàng tháng)',
                        'năm'       => 'năm — (Thuê bao / Hợp đồng hàng năm)',
                        'gói'       => 'gói — (Đăng ký trọn gói / Gói cước)',
                        'bộ'        => 'bộ — (Bộ giải pháp / Phần mềm)',
                        'lần'       => 'lần — (Theo lượt sử dụng / Bóc tách OCR)',
                        'tài khoản' => 'tài khoản — (Theo User / Cặp khóa SmartCA)',
                        'thiết bị'  => 'thiết bị — (Theo máy chủ / Thiết bị mạng)',
                        'GB'        => 'GB — (Dung lượng băng thông / Cloud)',
                        'TB'        => 'TB — (Dung lượng dữ liệu lưu trữ lớn)'
                    ];
                    $currentDvt = mb_strtolower(trim($product['don_vi_tinh'] ?? 'tháng'), 'UTF-8');
                    ?>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold mb-0">Đơn vị tính</label>
                            <span id="badgeLockUnit" class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="display: none;">🔒 Cố định cho Thiết bị</span>
                        </div>
                        <select id="don_vi_tinh" name="don_vi_tinh" class="form-select">
                            <?php foreach ($validUnits as $uVal => $uLabel): ?>
                            <option value="<?= htmlspecialchars($uVal) ?>" <?= ($currentDvt === mb_strtolower($uVal, 'UTF-8')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($uLabel) ?>
                            </option>
                            <?php endforeach; ?>
                            <?php if (!empty($product['don_vi_tinh']) && !isset($validUnits[$currentDvt])): ?>
                            <option value="<?= htmlspecialchars($product['don_vi_tinh']) ?>" selected>
                                <?= htmlspecialchars($product['don_vi_tinh']) ?> (Khác)
                            </option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Lưu thông tin sản phẩm
                        </button>
                        <a href="products.php" class="btn btn-light border text-secondary">Hủy bỏ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const catSelect      = document.getElementById('danh_muc_id');
    const typeSelect     = document.getElementById('loai_san_pham');
    const unitSelect     = document.getElementById('don_vi_tinh');
    const badgeLockType  = document.getElementById('badgeLockType');
    const badgeLockUnit  = document.getElementById('badgeLockUnit');
    const specsField     = document.getElementById('specs_field_block');
    const btnInsert      = document.getElementById('btnInsertSpecsTemplate');
    const txtSpecs       = document.getElementById('thong_so_ky_thuat');

    const inputGiaNiemYet = document.getElementById('gia_niem_yet');
    const inputGiaKhuyenMai = document.getElementById('gia_khuyen_mai');
    const outGiaNiemYet   = document.getElementById('gia_niem_yet_formatted');
    const outGiaKhuyenMai = document.getElementById('gia_khuyen_mai_formatted');

    // 1. TỰ ĐỘNG KHÓA / MỞ KHÓA & RESET LOẠI HÌNH & ĐƠN VỊ TÍNH KHI CHỌN DANH MỤC
    function handleCategoryChange(isInitial = false) {
        const selectedVal = catSelect.value;
        const selectedText = catSelect.options[catSelect.selectedIndex].text.toLowerCase();

        if (selectedVal === '6' || selectedText.includes('thiết bị')) {
            // Hiển thị ô nhập Thông số kỹ thuật
            if (specsField) specsField.style.display = 'block';

            // Khóa Loại hình = thiet_bi
            typeSelect.value = 'thiet_bi';
            typeSelect.style.pointerEvents = 'none';
            typeSelect.style.backgroundColor = '#e9ecef';
            if (badgeLockType) badgeLockType.style.display = 'inline-block';

            // Khóa Đơn vị = thiết bị
            unitSelect.value = 'thiết bị';
            unitSelect.style.pointerEvents = 'none';
            unitSelect.style.backgroundColor = '#e9ecef';
            if (badgeLockUnit) badgeLockUnit.style.display = 'inline-block';
        } else {
            // Ẩn ô nhập Thông số kỹ thuật (Danh mục khác giữ nguyên)
            if (specsField) specsField.style.display = 'none';

            // Mở khóa Loại hình
            typeSelect.style.pointerEvents = 'auto';
            typeSelect.style.backgroundColor = '#ffffff';
            if (badgeLockType) badgeLockType.style.display = 'none';

            // Mở khóa Đơn vị tính
            unitSelect.style.pointerEvents = 'auto';
            unitSelect.style.backgroundColor = '#ffffff';
            if (badgeLockUnit) badgeLockUnit.style.display = 'none';

            // RESET LẠI HAI MỤC VỪA MỞ KHÓA VỀ GIÁ TRỊ MẶC ĐỊNH
            if (!isInitial && (typeSelect.value === 'thiet_bi' || unitSelect.value === 'thiết bị')) {
                if (selectedVal === '4') {
                    typeSelect.value = 'combo';
                } else if (selectedVal === '5') {
                    typeSelect.value = 'goi_internet_truyen_hinh';
                } else {
                    typeSelect.value = 'dich_vu_so';
                }
                unitSelect.value = 'tháng';
            }
        }
    }

    if (catSelect) {
        catSelect.addEventListener('change', function() {
            handleCategoryChange(false);
        });
        handleCategoryChange(true); // Chạy khởi tạo ban đầu khi tải trang
    }

    // 2. TRÌNH TẠO THÔNG SỐ KỸ THUẬT TRỰC QUAN KHÔNG CẦN CODE (VISUAL SPEC BUILDER)
    const tbodySpecs = document.getElementById('specsBuilderTbody');
    const btnAddRow  = document.getElementById('btnAddSpecRow');
    const btnClearRows = document.getElementById('btnClearSpecRows');

    const presets = {
        camera: [
            { key: 'Độ phân giải', val: '3 MP (1296p) Full HD' },
            { key: 'Góc nhìn & Xoay', val: '360° toàn cảnh (Xoay ngang 340°, dọc 55°)' },
            { key: 'Tầm nhìn ban đêm', val: 'Hồng ngoại 10m (Quan sát có màu ban đêm)' },
            { key: 'Tính năng thông minh', val: 'Phát hiện chuyển động AI, Đàm thoại 2 chiều, Cuộc gọi 1 chạm' },
            { key: 'Chuẩn kết nối', val: 'Wifi 6 2.4GHz / 5GHz & Cổng LAN RJ45' },
            { key: 'Bảo hành chính hãng', val: '24 tháng 1 đổi 1 tận nơi VNPT' }
        ],
        router: [
            { key: 'Chuẩn Wifi', val: 'Wifi 6 Dual-Band AX1800 (574 Mbps + 1201 Mbps)' },
            { key: 'Cổng kết nối', val: '1 Cổng WAN Gigabit + 3 Cổng LAN Gigabit' },
            { key: 'Số kết nối tối đa', val: 'Phục vụ đồng thời lên đến 64 thiết bị' },
            { key: 'Công nghệ Mesh', val: 'Tự động Roaming thông minh không đứt đoạn' },
            { key: 'Bảo hành chính hãng', val: '24 tháng chính hãng VNPT Technology' }
        ],
        token: [
            { key: 'Chuẩn mã hóa', val: 'RSA 2048-bit / FIPS 140-2 Level 3' },
            { key: 'Dung lượng bộ nhớ', val: '64 KB Secure EEPROM Chuyên dụng' },
            { key: 'Cổng giao tiếp', val: 'USB 2.0 / 3.0 cắm là chạy (Plug & Play)' },
            { key: 'Phần mềm hỗ trợ', val: 'VNPT CA, Ký Thuế, Hải Quan, BHXH, SmartCA, VNPT Invoice' },
            { key: 'Bảo hành chính hãng', val: '12 tháng 1 đổi 1 chính hãng VNPT' }
        ]
    };

    function syncBuilderToTextarea() {
        if (!tbodySpecs || !txtSpecs) return;
        const rows = tbodySpecs.querySelectorAll('tr');
        const pairs = [];
        rows.forEach(tr => {
            const kInput = tr.querySelector('.spec-key-input');
            const vInput = tr.querySelector('.spec-val-input');
            if (kInput && vInput) {
                const k = kInput.value.trim();
                const v = vInput.value.trim();
                if (k || v) {
                    pairs.push({ key: k, val: v });
                }
            }
        });

        if (pairs.length === 0) {
            txtSpecs.value = '';
            return;
        }

        let html = `<div class="specs-group-block">\n  <h5 class="specs-group-header"><i class="fa-solid fa-layer-group"></i> Thông số &amp; Đặc tính kỹ thuật</h5>\n  <table class="specs-detail-table">\n`;
        pairs.forEach(p => {
            const label = p.key ? (p.key.endsWith(':') ? p.key : p.key + ':') : 'Thông số:';
            const value = p.val || 'Có';
            html += `    <tr><th>${escapeHtml(label)}</th><td>${escapeHtml(value)}</td></tr>\n`;
        });
        html += `  </table>\n</div>`;
        txtSpecs.value = html;
    }

    function escapeHtml(text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function createSpecRow(key = '', val = '') {
        if (!tbodySpecs) return;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" class="form-control form-control-sm spec-key-input fw-semibold" placeholder="Nhập tên thông số..." value="${escapeHtml(key)}">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm spec-val-input" placeholder="Nhập giá trị thông số..." value="${escapeHtml(val)}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-remove-spec-row" title="Xóa dòng này">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;

        tr.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', syncBuilderToTextarea);
        });

        tr.querySelector('.btn-remove-spec-row').addEventListener('click', function() {
            tr.remove();
            syncBuilderToTextarea();
            if (tbodySpecs.children.length === 0) {
                createSpecRow('', '');
            }
        });

        tbodySpecs.appendChild(tr);
    }

    function loadInitialSpecsToBuilder() {
        if (!tbodySpecs || !txtSpecs) return;
        tbodySpecs.innerHTML = '';
        const rawContent = txtSpecs.value.trim();

        if (!rawContent) {
            createSpecRow('', '');
            return;
        }

        // Nếu là bảng HTML
        if (rawContent.includes('<table') || rawContent.includes('<tr>')) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(rawContent, 'text/html');
            const trs = doc.querySelectorAll('tr');
            let count = 0;
            trs.forEach(tr => {
                const th = tr.querySelector('th');
                const td = tr.querySelector('td');
                if (th || td) {
                    const k = th ? th.textContent.replace(/[:\s]+$/, '').trim() : 'Thông số';
                    const v = td ? td.textContent.trim() : '';
                    createSpecRow(k, v);
                    count++;
                }
            });
            if (count === 0) createSpecRow('', '');
        } else {
            // Nếu là văn bản thường xuống dòng (Key: Value hoặc dòng đơn)
            const lines = rawContent.split(/[\r\n]+/);
            let count = 0;
            lines.forEach(line => {
                line = line.trim().replace(/^[-•\*\d\.\s]+/u, '');
                if (!line) return;
                if (line.includes(':')) {
                    const parts = line.split(':');
                    const k = parts[0].trim();
                    const v = parts.slice(1).join(':').trim();
                    createSpecRow(k, v);
                } else {
                    createSpecRow('Đặc tính nổi bật', line);
                }
                count++;
            });
            if (count === 0) createSpecRow('', '');
        }
        syncBuilderToTextarea();
    }

    // Nút Thêm dòng mới
    btnAddRow?.addEventListener('click', function() {
        createSpecRow('', '');
    });

    // Nút Xóa hết
    btnClearRows?.addEventListener('click', function() {
        if (tbodySpecs) tbodySpecs.innerHTML = '';
        createSpecRow('', '');
        syncBuilderToTextarea();
    });

    // Các nút nạp mẫu Preset 1-Click
    document.querySelectorAll('.btn-spec-preset').forEach(btn => {
        btn.addEventListener('click', function() {
            const presetKey = btn.getAttribute('data-preset');
            if (presets[presetKey]) {
                if (tbodySpecs) tbodySpecs.innerHTML = '';
                presets[presetKey].forEach(p => createSpecRow(p.key, p.val));
                syncBuilderToTextarea();
            }
        });
    });

    // Khởi tạo bảng nhập trực quan khi tải trang
    loadInitialSpecsToBuilder();

    // Đồng bộ ngược từ Textarea thô nếu người dùng gõ vào ô thô
    txtSpecs?.addEventListener('input', function() {
        // Chỉ reload nếu là HTML hoặc có thay đổi lớn
    });

    // 3. NGẮT DÒNG & ĐỊNH DẠNG SỐ TIỀN VND TRỰC QUAN (VND Currency Formatting)
    function formatVNDDisplay(val) {
        if (!val || isNaN(val) || val <= 0) return '';
        const num = parseFloat(val);
        const formatted = new Intl.NumberFormat('vi-VN').format(num);

        let words = '';
        if (num >= 1000000000) {
            words = (num / 1000000000).toFixed(2) + ' tỷ VNĐ';
        } else if (num >= 1000000) {
            words = (num / 1000000).toFixed(2) + ' triệu VNĐ';
        } else if (num >= 1000) {
            words = (num / 1000).toFixed(0) + ' ngàn VNĐ';
        }

        return `👉 Số tiền: ${formatted} ₫ ${words ? '(' + words + ')' : ''}`;
    }

    function updatePriceFormatters() {
        if (inputGiaNiemYet && outGiaNiemYet) {
            outGiaNiemYet.textContent = formatVNDDisplay(inputGiaNiemYet.value);
        }
        if (inputGiaKhuyenMai && outGiaKhuyenMai) {
            outGiaKhuyenMai.textContent = formatVNDDisplay(inputGiaKhuyenMai.value);
        }
    }

    inputGiaNiemYet?.addEventListener('input', updatePriceFormatters);
    inputGiaKhuyenMai?.addEventListener('input', updatePriceFormatters);
    updatePriceFormatters();
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
