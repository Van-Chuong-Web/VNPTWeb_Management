<?php
/**
 * posts.php — Quản lý Bài viết (WordPress Style)
 * Danh sách toàn bộ bài viết, lọc theo chuyên mục & trạng thái
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// RBAC Check: Chỉ Admin và Biên tập viên được vào
$currentRole = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
if (!in_array($currentRole, ['quan_tri_vien', 'superadmin', 'bien_tap_vien', 'admin'])) {
    die('<div class="alert alert-danger m-4">Bạn không có quyền truy cập trang quản lý bài viết.</div>');
}

// ── Xử lý POST (Xóa bài viết / Đổi trạng thái) ───────────────────────────
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM bai_viet WHERE id = :id");
            $stmt->execute([':id' => $id]);
            logActivity($pdo, "Đã xóa bài viết ID #$id");
            $msg = "✅ Đã xóa bài viết thành công!";
        } catch (PDOException $e) {
            $msg = "Lỗi khi xóa bài viết: " . $e->getMessage();
            $msgType = "danger";
        }
    } elseif ($action === 'toggle_status' && $id > 0) {
        $newStatus = $_POST['status'] ?? 'nhap';
        try {
            $stmt = $pdo->prepare("UPDATE bai_viet SET trang_thai = :st WHERE id = :id");
            $stmt->execute([':st' => $newStatus, ':id' => $id]);
            logActivity($pdo, "Đã đổi trạng thái bài viết ID #$id sang $newStatus");
            $msg = "✅ Cập nhật trạng thái bài viết thành công!";
        } catch (PDOException $e) {
            $msg = "Lỗi cập nhật: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// ── Query Danh sách Bài viết ─────────────────────────────────────────────
$search     = trim($_GET['q'] ?? '');
$catFilter  = (int)($_GET['cat'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');

$where = ["1=1"];
$params = [];

if ($search !== '') {
    $where[] = "(bv.tieu_de LIKE :q OR bv.tom_tat LIKE :q)";
    $params[':q'] = "%$search%";
}
if ($catFilter > 0) {
    $where[] = "bv.danh_muc_bai_viet_id = :cat";
    $params[':cat'] = $catFilter;
}
if ($statusFilter !== '') {
    $where[] = "bv.trang_thai = :st";
    $params[':st'] = $statusFilter;
}

$whereSql = implode(' AND ', $where);

$sql = "
    SELECT bv.*, dm.ten AS ten_danh_muc, nv.ho_ten AS ten_tac_gia
      FROM bai_viet bv
 LEFT JOIN danh_muc_bai_viet dm ON dm.id = bv.danh_muc_bai_viet_id
 LEFT JOIN nhan_vien nv ON nv.id = bv.tac_gia_id
     WHERE $whereSql
  ORDER BY bv.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Lấy danh sách chuyên mục cho dropdown lọc
$categories = $pdo->query("SELECT * FROM danh_muc_bai_viet ORDER BY ten ASC")->fetchAll();

$pageTitle  = 'Tất cả bài viết';
$activeMenu = 'posts';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-newspaper text-primary me-2"></i>Quản lý Bài viết</h4>
        <span class="text-muted small">Hệ thống biên tập tin tức &amp; truyền thông VNPT</span>
    </div>
    <a href="post_edit.php" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Viết bài mới
    </a>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Bộ lọc -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Tìm tiêu đề, tóm tắt..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select form-select-sm">
                    <option value="0">-- Tất cả chuyên mục --</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $catFilter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['ten']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="da_dang" <?= $statusFilter === 'da_dang' ? 'selected' : '' ?>>Đã đăng</option>
                    <option value="nhap" <?= $statusFilter === 'nhap' ? 'selected' : '' ?>>Bản nháp</option>
                    <option value="an" <?= $statusFilter === 'an' ? 'selected' : '' ?>>Đã ẩn</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                <a href="posts.php" class="btn btn-outline-secondary btn-sm" title="Bỏ lọc"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Bảng bài viết -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">Hình ảnh</th>
                        <th>Tiêu đề bài viết</th>
                        <th>Chuyên mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày xuất bản</th>
                        <th style="width: 140px;" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-newspaper fa-2x mb-2 d-block opacity-50"></i>
                            Chưa có bài viết nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($posts as $p): ?>
                    <tr>
                        <td>
                            <?php if (!empty($p['anh_bia'])): ?>
                                <img src="<?= htmlspecialchars($p['anh_bia']) ?>" alt="thumb" style="width:50px;height:38px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <div style="width:50px;height:38px;background:#e2e8f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><i class="fa-solid fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="post_edit.php?id=<?= $p['id'] ?>" class="fw-semibold text-decoration-none text-dark d-block">
                                <?= htmlspecialchars($p['tieu_de']) ?>
                            </a>
                            <small class="text-muted d-block text-truncate" style="max-width:350px;"><?= htmlspecialchars($p['tom_tat'] ?: 'Không có mô tả ngắn') ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($p['ten_danh_muc'] ?: 'Chưa phân loại') ?></span>
                        </td>
                        <td>
                            <small class="fw-medium text-secondary"><?= htmlspecialchars($p['ten_tac_gia'] ?: 'BQT') ?></small>
                        </td>
                        <td>
                            <?php if ($p['trang_thai'] === 'da_dang'): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Đã đăng</span>
                            <?php elseif ($p['trang_thai'] === 'nhap'): ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen-to-square me-1"></i>Bản nháp</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="fa-solid fa-eye-slash me-1"></i>Đã ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?= $p['ngay_xuat_ban'] ? date('d/m/Y H:i', strtotime($p['ngay_xuat_ban'])) : '—' ?></small>
                        </td>
                        <td class="text-end">
                            <?php if (!empty($p['slug'])): ?>
                                <a href="../frontend/index.php#page=<?= urlencode($p['slug']) ?>" target="_blank" class="btn btn-outline-info btn-action" title="Xem bài viết trên website">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            <?php endif; ?>
                            <a href="post_edit.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-action" title="Sửa bài viết">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
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
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
