<?php
/**
 * products.php — Quản lý Sản phẩm & Dịch vụ (Admin Panel)
 * Danh sách toàn bộ sản phẩm/dịch vụ, hỗ trợ tìm kiếm, lọc danh mục, trạng thái, xóa & điều hướng chỉnh sửa.
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
            <p class="mb-0">Chức năng Quản lý Sản phẩm &amp; Dịch vụ chỉ dành riêng cho tài khoản <strong>Quản trị viên (Admin)</strong> và <strong>Nhân viên Bán hàng</strong>. Tài khoản của bạn (<em>' . htmlspecialchars($currentRole) . '</em>) không được phép thực hiện thao tác này.</p>
          </div></div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$msg = '';
$msgType = 'success';

// ── Xử lý xóa sản phẩm ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        try {
            // Lấy thông tin sản phẩm trước khi xóa
            $checkStmt = $pdo->prepare("SELECT ten_san_pham FROM san_pham WHERE id = :id");
            $checkStmt->execute([':id' => $id]);
            $prod = $checkStmt->fetch();

            if ($prod) {
                $stmt = $pdo->prepare("DELETE FROM san_pham WHERE id = :id");
                $stmt->execute([':id' => $id]);
                logActivity($pdo, "Đã xóa sản phẩm ID #$id ('" . $prod['ten_san_pham'] . "')");
                $msg = "✅ Đã xóa sản phẩm <strong>" . htmlspecialchars($prod['ten_san_pham']) . "</strong> thành công!";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), '1451') || str_contains($e->getMessage(), 'foreign key')) {
                $msg = '⚠️ Không thể xóa sản phẩm này vì sản phẩm đã phát sinh đơn hàng trong hệ thống!';
            } else {
                $msg = "⚠️ Lỗi khi xóa sản phẩm: " . $e->getMessage();
            }
            $msgType = "danger";
        }
    } elseif ($action === 'toggle_status' && $id > 0) {
        $newStatus = $_POST['status'] ?? 'dang_ban';
        try {
            $stmt = $pdo->prepare("UPDATE san_pham SET trang_thai = :st WHERE id = :id");
            $stmt->execute([':st' => $newStatus, ':id' => $id]);
            logActivity($pdo, "Đã đổi trạng thái sản phẩm ID #$id sang $newStatus");
            $msg = "✅ Đã cập nhật trạng thái sản phẩm!";
        } catch (PDOException $e) {
            $msg = "Lỗi cập nhật trạng thái: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// ── Bộ lọc & Tìm kiếm ────────────────────────────────────────────────────
$search       = trim($_GET['q'] ?? '');
$catFilter    = (int)($_GET['cat'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');
$typeFilter   = trim($_GET['type'] ?? '');

$where = ["1=1"];
$params = [];

if ($search !== '') {
    $where[] = "(sp.ten_san_pham LIKE :q OR sp.ma_san_pham LIKE :q OR sp.thuong_hieu LIKE :q)";
    $params[':q'] = "%$search%";
}
if ($catFilter > 0) {
    $where[] = "sp.danh_muc_id = :cat";
    $params[':cat'] = $catFilter;
}
if ($statusFilter !== '') {
    $where[] = "sp.trang_thai = :st";
    $params[':st'] = $statusFilter;
}
if ($typeFilter !== '') {
    $where[] = "sp.loai_san_pham = :tp";
    $params[':tp'] = $typeFilter;
}

$whereSql = implode(' AND ', $where);

// ── Lấy dữ liệu sản phẩm ─────────────────────────────────────────────────
$sql = "
    SELECT sp.*, dm.ten_danh_muc
      FROM san_pham sp
 LEFT JOIN danh_muc_san_pham dm ON dm.id = sp.danh_muc_id
     WHERE $whereSql
  ORDER BY sp.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// ── Thống kê tổng quan ──────────────────────────────────────────────────
$totalProd   = count($products);
$totalActive = count(array_filter($products, fn($p) => $p['trang_thai'] === 'dang_ban'));
$totalStop   = count(array_filter($products, fn($p) => $p['trang_thai'] === 'ngung_ban'));
$totalDraft  = count(array_filter($products, fn($p) => $p['trang_thai'] === 'sap_ra_mat'));

// Lấy danh sách danh mục cho dropdown
$categories = $pdo->query("SELECT id, ten_danh_muc FROM danh_muc_san_pham ORDER BY ten_danh_muc ASC")->fetchAll();

$pageTitle  = 'Quản lý Sản phẩm & Dịch vụ';
$activeMenu = 'products';
require_once __DIR__ . '/header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Header Action & Title -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-box-archive text-primary me-2"></i>Quản lý Sản phẩm &amp; Dịch vụ</h4>
        <span class="text-muted small">Quản lý danh mục gói cước, giải pháp số, thiết bị và combo của VNPT</span>
    </div>
    <a href="product_edit.php" class="btn btn-primary shadow-sm">
        <i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm mới
    </a>
</div>

<!-- Stat Cards Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="small opacity-75 fw-semibold text-uppercase">Tổng sản phẩm</div>
                    <div class="fs-3 fw-bold mt-1"><?= $totalProd ?></div>
                </div>
                <div class="fs-1 opacity-25"><i class="fa-solid fa-boxes-stacked"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="small opacity-75 fw-semibold text-uppercase">Đang cung cấp</div>
                    <div class="fs-3 fw-bold mt-1"><?= $totalActive ?></div>
                </div>
                <div class="fs-1 opacity-25"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="small opacity-75 fw-semibold text-uppercase">Sắp ra mắt</div>
                    <div class="fs-3 fw-bold mt-1"><?= $totalDraft ?></div>
                </div>
                <div class="fs-1 opacity-25"><i class="fa-solid fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="small opacity-75 fw-semibold text-uppercase">Tạm ngưng</div>
                    <div class="fs-3 fw-bold mt-1"><?= $totalStop ?></div>
                </div>
                <div class="fs-1 opacity-25"><i class="fa-solid fa-ban"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="products.php" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm tên, mã SKU, thương hiệu..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $catFilter == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['ten_danh_muc']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="dang_ban" <?= $statusFilter === 'dang_ban' ? 'selected' : '' ?>>Đang bán</option>
                    <option value="sap_ra_mat" <?= $statusFilter === 'sap_ra_mat' ? 'selected' : '' ?>>Sắp ra mắt</option>
                    <option value="ngung_ban" <?= $statusFilter === 'ngung_ban' ? 'selected' : '' ?>>Tạm ngưng</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                <?php if ($search || $catFilter || $statusFilter): ?>
                <a href="products.php" class="btn btn-outline-secondary" title="Đặt lại bộ lọc"><i class="fa-solid fa-rotate-left"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Product Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list me-2"></i>Danh sách Sản phẩm (<?= count($products) ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#ID</th>
                    <th style="width: 60px;">Ảnh</th>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm / Dịch vụ</th>
                    <th>Danh mục</th>
                    <th>Loại</th>
                    <th>Giá niêm yết</th>
                    <th>Đơn vị</th>
                    <th>Trạng thái</th>
                    <th class="text-end" style="width: 130px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-box-open fs-1 d-block mb-3 opacity-50"></i>
                        Không tìm thấy sản phẩm nào phù hợp với điều kiện tìm kiếm.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><span class="text-muted fw-semibold">#<?= $p['id'] ?></span></td>
                    <td>
                        <?php if (!empty($p['hinh_anh_url'])): ?>
                        <img src="../frontend/<?= htmlspecialchars($p['hinh_anh_url']) ?>" alt="Thumbnail" style="width: 42px; height: 42px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0;">
                        <?php else: ?>
                        <div style="width: 42px; height: 42px; border-radius: 8px; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1rem;">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-light text-primary border border-primary-subtle font-monospace px-2 py-1">
                            <?= htmlspecialchars($p['ma_san_pham']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark mb-0">
                            <a href="product_edit.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark hover-primary">
                                <?= htmlspecialchars($p['ten_san_pham']) ?>
                            </a>
                        </div>
                        <?php if (!empty($p['thuong_hieu'])): ?>
                        <small class="text-muted">Thương hiệu: <?= htmlspecialchars($p['thuong_hieu']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                            <?= htmlspecialchars($p['ten_danh_muc'] ?? 'Chưa phân loại') ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $types = [
                            'dich_vu_so' => ['label' => 'Dịch vụ số', 'class' => 'bg-info-subtle text-info-emphasis'],
                            'combo' => ['label' => 'Combo', 'class' => 'bg-purple-subtle text-purple-emphasis'],
                            'thiet_bi' => ['label' => 'Thiết bị', 'class' => 'bg-warning-subtle text-warning-emphasis'],
                            'goi_cuoc_di_dong' => ['label' => 'Di động', 'class' => 'bg-success-subtle text-success-emphasis'],
                            'goi_internet_truyen_hinh' => ['label' => 'Internet', 'class' => 'bg-primary-subtle text-primary-emphasis'],
                        ];
                        $tp = $types[$p['loai_san_pham']] ?? ['label' => $p['loai_san_pham'], 'class' => 'bg-light text-dark'];
                        ?>
                        <span class="badge <?= $tp['class'] ?> px-2 py-1"><?= $tp['label'] ?></span>
                    </td>
                    <td>
                        <strong class="text-primary fs-6"><?= number_format($p['gia_niem_yet'], 0, ',', '.') ?> ₫</strong>
                        <?php if ($p['gia_khuyen_mai'] > 0): ?>
                        <div class="small text-danger text-decoration-line-through">
                            <?= number_format($p['gia_khuyen_mai'], 0, ',', '.') ?> ₫
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><span class="text-muted small">/ <?= htmlspecialchars($p['don_vi_tinh'] ?: 'sản phẩm') ?></span></td>
                    <td>
                        <?php if ($p['trang_thai'] === 'dang_ban'): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Đang bán</span>
                        <?php elseif ($p['trang_thai'] === 'sap_ra_mat'): ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i>Sắp ra mắt</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="fa-solid fa-ban me-1"></i>Tạm ngưng</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary" title="Chỉnh sửa sản phẩm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger" title="Xóa sản phẩm" onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['ten_san_pham'])) ?>')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Form Modal -->
<form id="deleteForm" method="POST" action="products.php" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId" value="">
</form>

<script>
function confirmDelete(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm "' + name + '" (#ID ' + id + ') không?\nHành động này không thể hoàn tác!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
