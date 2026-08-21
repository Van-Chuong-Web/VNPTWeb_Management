<?php
/**
 * customer_messages.php — Quản lý Phản hồi, Nhận xét & Tin nhắn Khách hàng (Admin Panel)
 * Hỗ trợ Quản trị viên & Biên tập viên quản lý, chỉnh sửa, phê duyệt & trả lời phản hồi từ khách hàng.
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Kiểm tra quyền truy cập: ADMIN & BIÊN TẬP VIÊN ĐỀU CÓ QUYỀN
$currentRole = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
$canAccess = in_array($currentRole, ['quan_tri_vien', 'superadmin', 'admin', 'bien_tap_vien', 'editor']);

if (!$canAccess) {
    require_once __DIR__ . '/header.php';
    echo '<div class="page-content"><div class="alert alert-danger shadow-sm border-danger p-4" style="border-radius: 12px;">
            <h5 class="fw-bold mb-2 text-danger"><i class="fa-solid fa-shield-cat me-2"></i>Truy cập bị từ chối</h5>
            <p class="mb-0">Chức năng Quản lý Phản hồi &amp; Tin nhắn chỉ dành cho <strong>Quản trị viên</strong> và <strong>Biên tập viên</strong>.</p>
          </div></div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$pageTitle  = 'Phản hồi & Nhận xét Khách hàng';
$activeMenu = 'customer_messages';

$tab = $_GET['tab'] ?? 'reviews'; // 'reviews' (Nhận xét & Đánh giá) hoặc 'support' (Yêu cầu tư vấn/tin nhắn)
$msg = '';
$msgType = 'success';

// ── XỬ LÝ POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // === XỬ LÝ NHẬN XÉT / ĐÁNH GIÁ (REVIEWS / TESTIMONIALS) ===
    if ($action === 'toggle_review_status') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $newStatus = $_POST['status'] ?? 'cho_duyet';
        if ($reviewId > 0) {
            $stmt = $pdo->prepare("UPDATE danh_gia_san_pham SET trang_thai_duyet = :st WHERE id = :id");
            $stmt->execute([':st' => $newStatus, ':id' => $reviewId]);
            $label = $newStatus === 'da_duyet' ? 'Phê duyệt' : ($newStatus === 'tu_choi' ? 'Từ chối' : 'Chờ duyệt');
            logActivity($pdo, "Đã $label nhận xét khách hàng #$reviewId");
            $msg = "✅ Đã <strong>$label</strong> nhận xét đánh giá của khách hàng thành công!";
        }
    } elseif ($action === 'add_review') {
        $name = trim($_POST['ho_ten'] ?? '');
        $company = trim($_POST['chuc_vu_cong_ty'] ?? '');
        $service = trim($_POST['ten_dich_vu'] ?? 'Cloud Enterprise');
        $stars = (int)($_POST['so_sao'] ?? 5);
        $title = trim($_POST['tieu_de'] ?? '');
        $content = trim($_POST['noi_dung'] ?? '');
        $status = $_POST['trang_thai_duyet'] ?? 'da_duyet';

        if (!empty($name) && !empty($content)) {
            $stmt = $pdo->prepare("
                INSERT INTO danh_gia_san_pham (ho_ten_nguoi_danh_gia, chuc_vu_cong_ty, ten_dich_vu, so_sao, tieu_de, noi_dung, trang_thai_duyet, created_at)
                VALUES (:name, :comp, :serv, :stars, :title, :cnt, :st, NOW())
            ");
            $stmt->execute([
                ':name' => $name,
                ':comp' => $company,
                ':serv' => $service,
                ':stars' => $stars,
                ':title' => $title,
                ':cnt' => $content,
                ':st' => $status
            ]);
            logActivity($pdo, "Thêm nhận xét đối tác mới: $name");
            $msg = '✅ Đã thêm nhận xét đánh giá từ khách hàng thành công!';
        } else {
            $msg = '⚠️ Vui lòng nhập đầy đủ Họ tên và Nội dung nhận xét!';
            $msgType = 'danger';
        }
    } elseif ($action === 'edit_review') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $name = trim($_POST['ho_ten'] ?? '');
        $company = trim($_POST['chuc_vu_cong_ty'] ?? '');
        $service = trim($_POST['ten_dich_vu'] ?? 'Cloud Enterprise');
        $stars = (int)($_POST['so_sao'] ?? 5);
        $title = trim($_POST['tieu_de'] ?? '');
        $content = trim($_POST['noi_dung'] ?? '');
        $status = $_POST['trang_thai_duyet'] ?? 'da_duyet';

        if ($reviewId > 0 && !empty($name) && !empty($content)) {
            $stmt = $pdo->prepare("
                UPDATE danh_gia_san_pham 
                   SET ho_ten_nguoi_danh_gia = :name, chuc_vu_cong_ty = :comp, ten_dich_vu = :serv,
                       so_sao = :stars, tieu_de = :title, noi_dung = :cnt, trang_thai_duyet = :st
                 WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':comp' => $company,
                ':serv' => $service,
                ':stars' => $stars,
                ':title' => $title,
                ':cnt' => $content,
                ':st' => $status,
                ':id' => $reviewId
            ]);
            logActivity($pdo, "Cập nhật nhận xét đối tác ID #$reviewId");
            $msg = '✅ Đã chỉnh sửa thông tin nhận xét thành công!';
        }
    } elseif ($action === 'reply_review') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $reply = trim($_POST['phan_hoi_admin'] ?? '');

        if ($reviewId > 0 && !empty($reply)) {
            $stmt = $pdo->prepare("
                UPDATE danh_gia_san_pham 
                   SET phan_hoi_admin = :reply, ngay_phan_hoi_admin = NOW()
                 WHERE id = :id
            ");
            $stmt->execute([':reply' => $reply, ':id' => $reviewId]);
            logActivity($pdo, "Đã gửi phản hồi nhận xét ID #$reviewId");
            $msg = '✅ Đã lưu câu trả lời phản hồi cho khách hàng thành công!';
        }
    } elseif ($action === 'delete_review') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        if ($reviewId > 0) {
            $stmt = $pdo->prepare("DELETE FROM danh_gia_san_pham WHERE id = :id");
            $stmt->execute([':id' => $reviewId]);
            logActivity($pdo, "Đã xóa nhận xét ID #$reviewId");
            $msg = '🗑️ Đã xóa nhận xét đánh giá thành công.';
        }
    }

    // === XỬ LÝ YÊU CẦU TƯ VẤN / HỖ TRỢ (SUPPORT MESSAGES) ===
    elseif ($action === 'update_status') {
        $requestId = (int)($_POST['request_id'] ?? 0);
        $newStatus = trim($_POST['trang_thai'] ?? 'moi');
        $nhanVienId = $_SESSION['admin_user']['nhan_vien_id'] ?? null;
        if ($requestId > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE yeu_cau_ho_tro SET trang_thai = :st, nhan_vien_xu_ly_id = :nv, updated_at = NOW() WHERE id = :id");
                $stmt->execute([':st' => $newStatus, ':nv' => $nhanVienId, ':id' => $requestId]);
                logActivity($pdo, "Đã cập nhật trạng thái tin nhắn #$requestId thành '$newStatus'");
                $msg = '✅ Cập nhật trạng thái tin nhắn thành công!';
            } catch (PDOException $e) {
                $msg = '⚠️ Lỗi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    } elseif ($action === 'send_reply') {
        $requestId = (int)($_POST['request_id'] ?? 0);
        $noiDungPhanHoi = trim($_POST['noi_dung_phan_hoi'] ?? '');
        $taiKhoanId = $_SESSION['admin_user']['tai_khoan_id'] ?? 1;
        $nhanVienId = $_SESSION['admin_user']['nhan_vien_id'] ?? null;
        $capNhatTrangThai = trim($_POST['cap_nhat_trang_thai'] ?? 'da_giai_quyet');

        if ($requestId > 0 && !empty($noiDungPhanHoi)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO phan_hoi_ho_tro (yeu_cau_ho_tro_id, tai_khoan_id, noi_dung, created_at) VALUES (:yid, :tkid, :nd, NOW())");
                $stmt->execute([':yid' => $requestId, ':tkid' => $taiKhoanId, ':nd' => $noiDungPhanHoi]);

                $stmt2 = $pdo->prepare("UPDATE yeu_cau_ho_tro SET trang_thai = :st, nhan_vien_xu_ly_id = :nv, updated_at = NOW() WHERE id = :id");
                $stmt2->execute([':st' => $capNhatTrangThai, ':nv' => $nhanVienId, ':id' => $requestId]);

                logActivity($pdo, "Đã gửi phản hồi cho tin nhắn #$requestId");
                $msg = '✅ Đã gửi phản hồi cho tin nhắn hỗ trợ thành công!';
            } catch (PDOException $e) {
                $msg = '⚠️ Lỗi khi gửi phản hồi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    } elseif ($action === 'delete_support') {
        $requestId = (int)($_POST['request_id'] ?? 0);
        if ($requestId > 0) {
            try {
                $pdo->prepare("DELETE FROM phan_hoi_ho_tro WHERE yeu_cau_ho_tro_id = :id")->execute([':id' => $requestId]);
                $pdo->prepare("DELETE FROM yeu_cau_ho_tro WHERE id = :id")->execute([':id' => $requestId]);
                logActivity($pdo, "Đã xóa tin nhắn hỗ trợ #$requestId");
                $msg = '✅ Đã xóa tin nhắn khách hàng thành công!';
            } catch (PDOException $e) {
                $msg = '⚠️ Lỗi xóa tin nhắn: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }
}

// ── BỘ LỌC VÀ THỐNG KÊ ───────────────────────────────────────────────────
// 1. Thống kê Reviews
$reviewCountAll      = $pdo->query("SELECT COUNT(*) FROM danh_gia_san_pham")->fetchColumn();
$reviewCountPending  = $pdo->query("SELECT COUNT(*) FROM danh_gia_san_pham WHERE trang_thai_duyet = 'cho_duyet'")->fetchColumn();
$reviewCountApproved = $pdo->query("SELECT COUNT(*) FROM danh_gia_san_pham WHERE trang_thai_duyet = 'da_duyet'")->fetchColumn();
$reviewAvgRating     = $pdo->query("SELECT COALESCE(AVG(so_sao), 5.0) FROM danh_gia_san_pham WHERE trang_thai_duyet = 'da_duyet'")->fetchColumn();

// 2. Thống kê Support Tickets
$totalMoi          = $pdo->query("SELECT COUNT(*) FROM yeu_cau_ho_tro WHERE trang_thai = 'moi'")->fetchColumn();
$totalDangXuLy     = $pdo->query("SELECT COUNT(*) FROM yeu_cau_ho_tro WHERE trang_thai = 'dang_xu_ly'")->fetchColumn();
$totalDaGiaiQuyet  = $pdo->query("SELECT COUNT(*) FROM yeu_cau_ho_tro WHERE trang_thai = 'da_giai_quyet'")->fetchColumn();
$totalSupportAll   = $pdo->query("SELECT COUNT(*) FROM yeu_cau_ho_tro")->fetchColumn();

// Query Reviews
$revStatus = $_GET['rev_status'] ?? 'all';
$revSearch = trim($_GET['rev_search'] ?? '');
$whereRev  = ["1=1"];
$paramsRev = [];

if ($revStatus !== 'all') {
    $whereRev[] = "trang_thai_duyet = :st";
    $paramsRev[':st'] = $revStatus;
}
if ($revSearch !== '') {
    $whereRev[] = "(ho_ten_nguoi_danh_gia LIKE :kw OR chuc_vu_cong_ty LIKE :kw OR noi_dung LIKE :kw OR tieu_de LIKE :kw)";
    $paramsRev[':kw'] = "%$revSearch%";
}

$whereRevSql = implode(' AND ', $whereRev);
$stmtRev = $pdo->prepare("SELECT * FROM danh_gia_san_pham WHERE $whereRevSql ORDER BY id DESC");
$stmtRev->execute($paramsRev);
$reviews = $stmtRev->fetchAll(PDO::FETCH_ASSOC);

// Query Support Messages
$supStatus = $_GET['sup_status'] ?? 'all';
$supSearch = trim($_GET['sup_search'] ?? '');
$whereSup  = ["1=1"];
$paramsSup = [];

if ($supStatus !== 'all') {
    $whereSup[] = "y.trang_thai = :st";
    $paramsSup[':st'] = $supStatus;
}
if ($supSearch !== '') {
    $whereSup[] = "(y.tieu_de LIKE :kw OR y.noi_dung LIKE :kw OR k.ho_ten LIKE :kw OR t.email LIKE :kw)";
    $paramsSup[':kw'] = "%$supSearch%";
}

$whereSupSql = implode(' AND ', $whereSup);
$stmtSup = $pdo->prepare("
    SELECT y.*, k.ho_ten AS ten_khach_hang, t.email AS email_khach_hang, nv.ho_ten AS ten_nhan_vien_xu_ly
      FROM yeu_cau_ho_tro y
 LEFT JOIN khach_hang k ON k.id = y.khach_hang_id
 LEFT JOIN tai_khoan t ON t.id = k.tai_khoan_id
 LEFT JOIN nhan_vien nv ON nv.id = y.nhan_vien_xu_ly_id
     WHERE $whereSupSql
  ORDER BY y.id DESC
");
$stmtSup->execute($paramsSup);
$supportMessages = $stmtSup->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/header.php';
?>

<div class="page-content">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-comments text-primary me-2"></i>Quản lý Phản hồi &amp; Nhận xét Khách hàng</h4>
            <p class="text-muted small mb-0">Quản lý, chỉnh sửa, phê duyệt và phản hồi ý kiến đánh giá từ đối tác &amp; khách hàng.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddReview">
                <i class="fa-solid fa-plus me-1"></i> Thêm Nhận Xét Mới
            </button>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
    <div class="alert alert-<?= $msgType ?> alert-dismissible fade show shadow-sm" role="alert">
        <?= $msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs nav-tabs-bordered mb-4" id="feedbackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $tab === 'reviews' ? 'active fw-bold border-bottom border-primary border-3' : '' ?>" href="customer_messages.php?tab=reviews">
                <i class="fa-solid fa-star text-warning me-2"></i>Nhận xét &amp; Đánh giá từ Khách hàng
                <?php if ($reviewCountPending > 0): ?>
                <span class="badge bg-danger rounded-pill ms-2"><?= $reviewCountPending ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $tab === 'support' ? 'active fw-bold border-bottom border-primary border-3' : '' ?>" href="customer_messages.php?tab=support">
                <i class="fa-solid fa-headset text-primary me-2"></i>Tin nhắn Tư vấn &amp; Yêu cầu Hỗ trợ
                <?php if ($totalMoi > 0): ?>
                <span class="badge bg-danger rounded-pill ms-2"><?= $totalMoi ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>

    <?php if ($tab === 'reviews'): ?>
    <!-- ========================================================================= -->
    <!-- TAB 1: QUẢN LÝ NHẬN XẾT & ĐÁNH GIÁ (REVIEWS / TESTIMONIALS) -->
    <!-- ========================================================================= -->

    <!-- KPI Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">TỔNG NHẬN XẾT</div>
                        <div class="fs-3 fw-bold text-dark"><?= $reviewCountAll ?></div>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">CHỜ PHÊ DUYỆT</div>
                        <div class="fs-3 fw-bold text-warning"><?= $reviewCountPending ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">ĐÃ HIỂN THỊ</div>
                        <div class="fs-3 fw-bold text-success"><?= $reviewCountApproved ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">ĐÁNH GIÁ TRUNG BÌNH</div>
                        <div class="fs-3 fw-bold text-warning"><?= number_format($reviewAvgRating, 1) ?> ⭐</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="reviews">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="rev_search" class="form-control border-start-0 bg-light" placeholder="Tìm theo tên người đánh giá, công ty, nội dung..." value="<?= htmlspecialchars($revSearch) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="rev_status" class="form-select bg-light">
                        <option value="all" <?= $revStatus === 'all' ? 'selected' : '' ?>>-- Tất cả trạng thái --</option>
                        <option value="cho_duyet" <?= $revStatus === 'cho_duyet' ? 'selected' : '' ?>>⏳ Chờ phê duyệt</option>
                        <option value="da_duyet" <?= $revStatus === 'da_duyet' ? 'selected' : '' ?>>✅ Đã duyệt hiển thị</option>
                        <option value="tu_choi" <?= $revStatus === 'tu_choi' ? 'selected' : '' ?>>❌ Đã từ chối/Ẩn</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                    <a href="customer_messages.php?tab=reviews" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Người đánh giá &amp; Công ty</th>
                        <th>Dịch vụ quan tâm</th>
                        <th>Đánh giá</th>
                        <th>Nội dung nhận xét</th>
                        <th>Phản hồi Admin</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th style="width: 140px;" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-regular fa-comment-dots fs-1 d-block mb-2 opacity-50"></i>
                            Chưa có nhận xét hoặc đánh giá nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $idx => $r): ?>
                    <tr>
                        <td class="text-muted fw-bold"><?= $idx + 1 ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($r['ho_ten_nguoi_danh_gia'] ?: 'Khách hàng') ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($r['chuc_vu_cong_ty'] ?: 'Doanh nghiệp') ?></div>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                <?= htmlspecialchars($r['ten_dich_vu'] ?: 'Cloud Enterprise') ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-warning small fw-bold">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="fa-<?= $s <= (int)$r['so_sao'] ? 'solid' : 'regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($r['tieu_de']): ?>
                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($r['tieu_de']) ?></div>
                            <?php endif; ?>
                            <div class="text-secondary small text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($r['noi_dung']) ?>">
                                "<?= htmlspecialchars($r['noi_dung']) ?>"
                            </div>
                        </td>
                        <td>
                            <?php if ($r['phan_hoi_admin']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" title="<?= htmlspecialchars($r['phan_hoi_admin']) ?>">
                                    <i class="fa-solid fa-check me-1"></i>Đã phản hồi
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($r['trang_thai_duyet'] === 'da_duyet'): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-eye me-1"></i>Đã duyệt</span>
                            <?php elseif ($r['trang_thai_duyet'] === 'tu_choi'): ?>
                                <span class="badge bg-secondary"><i class="fa-solid fa-eye-slash me-1"></i>Đã ẩn</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chờ duyệt</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small font-monospace">
                            <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <!-- Nút Phê duyệt / Ẩn -->
                                <?php if ($r['trang_thai_duyet'] !== 'da_duyet'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_review_status">
                                    <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="status" value="da_duyet">
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Duyệt hiển thị trên website"><i class="fa-solid fa-check"></i></button>
                                </form>
                                <?php else: ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_review_status">
                                    <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="status" value="tu_choi">
                                    <button type="submit" class="btn btn-outline-warning btn-sm" title="Ẩn nhận xét này"><i class="fa-solid fa-eye-slash"></i></button>
                                </form>
                                <?php endif; ?>

                                <!-- Nút Trả lời -->
                                <button class="btn btn-outline-primary btn-sm" onclick="openReplyReviewModal(<?= htmlspecialchars(json_encode($r)) ?>)" title="Trả lời nhận xét"><i class="fa-solid fa-reply"></i></button>
                                
                                <!-- Nút Chỉnh sửa -->
                                <button class="btn btn-outline-secondary btn-sm" onclick="openEditReviewModal(<?= htmlspecialchars(json_encode($r)) ?>)" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                                
                                <!-- Nút Xóa -->
                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhận xét này?');">
                                    <input type="hidden" name="action" value="delete_review">
                                    <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa nhận xét"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>
    <!-- ========================================================================= -->
    <!-- TAB 2: QUẢN LÝ TIN NHẮN TƯ VẤN & YÊU CẦU HỖ TRỢ (SUPPORT MESSAGES) -->
    <!-- ========================================================================= -->

    <!-- KPI Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <a href="customer_messages.php?tab=support&sup_status=moi" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">TIN NHẮN MỚI</div>
                        <div class="fs-3 fw-bold text-danger"><?= $totalMoi ?></div>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="customer_messages.php?tab=support&sup_status=dang_xu_ly" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">ĐANG XỬ LÝ</div>
                        <div class="fs-3 fw-bold text-warning"><?= $totalDangXuLy ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-spinner"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="customer_messages.php?tab=support&sup_status=da_giai_quyet" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">ĐÃ GIẢI QUYẾT</div>
                        <div class="fs-3 fw-bold text-success"><?= $totalDaGiaiQuyet ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="customer_messages.php?tab=support&sup_status=all" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">TẤT CẢ TIN NHẮN</div>
                        <div class="fs-3 fw-bold text-dark"><?= $totalSupportAll ?></div>
                    </div>
                    <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="support">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="sup_search" class="form-control border-start-0 bg-light" placeholder="Tìm theo tiêu đề, nội dung, khách hàng..." value="<?= htmlspecialchars($supSearch) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="sup_status" class="form-select bg-light">
                        <option value="all" <?= $supStatus === 'all' ? 'selected' : '' ?>>-- Tất cả trạng thái --</option>
                        <option value="moi" <?= $supStatus === 'moi' ? 'selected' : '' ?>>🔴 Mới tiếp nhận</option>
                        <option value="dang_xu_ly" <?= $supStatus === 'dang_xu_ly' ? 'selected' : '' ?>>🟡 Đang xử lý</option>
                        <option value="da_giai_quyet" <?= $supStatus === 'da_giai_quyet' ? 'selected' : '' ?>>🟢 Đã giải quyết</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                    <a href="customer_messages.php?tab=support" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Support Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Khách hàng</th>
                        <th>Tiêu đề yêu cầu</th>
                        <th>Nội dung yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Người xử lý</th>
                        <th>Thời gian</th>
                        <th style="width: 140px;" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($supportMessages)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-regular fa-folder-open fs-1 d-block mb-2 opacity-50"></i>
                            Không tìm thấy tin nhắn hoặc yêu cầu hỗ trợ nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($supportMessages as $idx => $m): ?>
                    <tr>
                        <td class="text-muted fw-bold"><?= $idx + 1 ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($m['ten_khach_hang'] ?: 'Khách vãng lai') ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($m['email_khach_hang'] ?: '—') ?></div>
                        </td>
                        <td>
                            <span class="fw-semibold text-primary"><?= htmlspecialchars($m['tieu_de']) ?></span>
                        </td>
                        <td>
                            <div class="text-secondary small text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($m['noi_dung']) ?>">
                                <?= htmlspecialchars($m['noi_dung']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($m['trang_thai'] === 'moi'): ?>
                                <span class="badge bg-danger"><i class="fa-solid fa-circle-dot me-1"></i>Mới</span>
                            <?php elseif ($m['trang_thai'] === 'dang_xu_ly'): ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-spinner me-1"></i>Đang xử lý</span>
                            <?php else: ?>
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Đã giải quyết</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?= htmlspecialchars($m['ten_nhan_vien_xu_ly'] ?: 'Chưa phân công') ?>
                        </td>
                        <td class="text-muted small font-monospace">
                            <?= date('d/m/Y H:i', strtotime($m['created_at'])) ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm" onclick="openReplySupportModal(<?= htmlspecialchars(json_encode($m)) ?>)" title="Trả lời tin nhắn"><i class="fa-solid fa-reply"></i></button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này?');">
                                    <input type="hidden" name="action" value="delete_support">
                                    <input type="hidden" name="request_id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa tin nhắn"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- MODALS CHO REVIEWS & TESTIMONIALS -->
<!-- ========================================================================= -->

<!-- Modal 1: Thêm Nhận Xét Mới -->
<div class="modal fade" id="modalAddReview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-plus-circle me-2"></i>Thêm Nhận Xét Đánh Giá Mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_review">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ &amp; Tên người đánh giá <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" required placeholder="Nguyễn Thanh Tùng">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Chức vụ &amp; Công ty / Tổ chức</label>
                        <input type="text" name="chuc_vu_cong_ty" class="form-control" placeholder="CTO – Công ty CP Thương mại ABC">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tên dịch vụ / Giải pháp</label>
                            <input type="text" name="ten_dich_vu" class="form-control" value="Cloud Enterprise" placeholder="Cloud Enterprise">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số sao đánh giá (1-5 ⭐)</label>
                            <select name="so_sao" class="form-select">
                                <option value="5" selected>⭐⭐⭐⭐⭐ (5 sao - Thấu hiểu/Rất tốt)</option>
                                <option value="4">⭐⭐⭐⭐ (4 sao - Hài lòng)</option>
                                <option value="3">⭐⭐⭐ (3 sao - Khá)</option>
                                <option value="2">⭐⭐ (2 sao - Trung bình)</option>
                                <option value="1">⭐ (1 sao - Cần cải thiện)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tiêu đề nhận xét</label>
                        <input type="text" name="tieu_de" class="form-control" placeholder="Tốc độ xử lý ấn tượng">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nội dung nhận xét <span class="text-danger">*</span></label>
                        <textarea name="noi_dung" class="form-control" rows="4" required placeholder="Nhập nội dung phàn hồi/đánh giá chi tiết của khách hàng..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Trạng thái phê duyệt</label>
                        <select name="trang_thai_duyet" class="form-select">
                            <option value="da_duyet" selected>✅ Phê duyệt hiển thị ngay lên Trang chủ</option>
                            <option value="cho_duyet">⏳ Chờ duyệt sau</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-save me-1"></i>Lưu Nhận Xét</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Chỉnh Sửa Nhận Xét -->
<div class="modal fade" id="modalEditReview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh Sửa Nhận Xét Đánh Giá</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_review">
                <input type="hidden" name="review_id" id="editReviewId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ &amp; Tên người đánh giá</label>
                        <input type="text" name="ho_ten" id="editReviewName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Chức vụ &amp; Công ty / Tổ chức</label>
                        <input type="text" name="chuc_vu_cong_ty" id="editReviewCompany" class="form-control">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tên dịch vụ</label>
                            <input type="text" name="ten_dich_vu" id="editReviewService" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số sao (1-5 ⭐)</label>
                            <select name="so_sao" id="editReviewStars" class="form-select">
                                <option value="5">⭐⭐⭐⭐⭐ (5 sao)</option>
                                <option value="4">⭐⭐⭐⭐ (4 sao)</option>
                                <option value="3">⭐⭐⭐ (3 sao)</option>
                                <option value="2">⭐⭐ (2 sao)</option>
                                <option value="1">⭐ (1 sao)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tiêu đề</label>
                        <input type="text" name="tieu_de" id="editReviewTitle" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nội dung nhận xét</label>
                        <textarea name="noi_dung" id="editReviewContent" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Trạng thái hiển thị</label>
                        <select name="trang_thai_duyet" id="editReviewStatus" class="form-select">
                            <option value="da_duyet">✅ Đã phê duyệt (Hiển thị)</option>
                            <option value="cho_duyet">⏳ Chờ duyệt</option>
                            <option value="tu_choi">❌ Đã từ chối (Ẩn)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-save me-1"></i>Cập Nhập Nhận Xét</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Trả Lời Phản Hồi Nhận Xét -->
<div class="modal fade" id="modalReplyReview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-reply me-2"></i>Trả Lời Phản Hồi Cho Khách Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="reply_review">
                <input type="hidden" name="review_id" id="replyReviewId">
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="fw-bold text-dark" id="replyReviewAuthor"></div>
                        <div class="text-secondary small italic mt-1" id="replyReviewContent"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nội dung câu trả lời từ VNPT / VNPT Admin <span class="text-danger">*</span></label>
                        <textarea name="phan_hoi_admin" id="replyReviewText" class="form-control" rows="5" required placeholder="Cảm ơn Quý khách đã tin tưởng và sử dụng dịch vụ của VNPT. Chúng tôi sẽ tiếp tục nỗ lực đem lại trải nghiệm tốt nhất..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold"><i class="fa-solid fa-paper-plane me-1"></i>Gửi Câu Trả Lời</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: Trả Lời Tin Nhắn Support -->
<div class="modal fade" id="modalReplySupport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-headset me-2"></i>Trả Lời Yêu Cầu Hỗ Trợ Khách Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="send_reply">
                <input type="hidden" name="request_id" id="replySupportId">
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="fw-bold text-primary" id="replySupportTitle"></div>
                        <div class="text-muted small mt-1" id="replySupportCustomer"></div>
                        <div class="text-secondary small mt-2" id="replySupportContent"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nội dung phản hồi hỗ trợ <span class="text-danger">*</span></label>
                        <textarea name="noi_dung_phan_hoi" class="form-control" rows="5" required placeholder="Kính chào Quý khách, VNPT xin phản hồi thông tin hỗ trợ như sau..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cập nhật trạng thái tin nhắn</label>
                        <select name="cap_nhat_trang_thai" class="form-select">
                            <option value="da_giai_quyet" selected>🟢 Đã giải quyết xong</option>
                            <option value="dang_xu_ly">🟡 Đang xử lý</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-paper-plane me-1"></i>Gửi Phản Hồi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditReviewModal(r) {
    document.getElementById('editReviewId').value = r.id;
    document.getElementById('editReviewName').value = r.ho_ten_nguoi_danh_gia || '';
    document.getElementById('editReviewCompany').value = r.chuc_vu_cong_ty || '';
    document.getElementById('editReviewService').value = r.ten_dich_vu || 'Cloud Enterprise';
    document.getElementById('editReviewStars').value = r.so_sao || 5;
    document.getElementById('editReviewTitle').value = r.tieu_de || '';
    document.getElementById('editReviewContent').value = r.noi_dung || '';
    document.getElementById('editReviewStatus').value = r.trang_thai_duyet || 'da_duyet';

    const modal = new bootstrap.Modal(document.getElementById('modalEditReview'));
    modal.show();
}

function openReplyReviewModal(r) {
    document.getElementById('replyReviewId').value = r.id;
    document.getElementById('replyReviewAuthor').textContent = (r.ho_ten_nguoi_danh_gia || 'Khách hàng') + ' (' + (r.chuc_vu_cong_ty || 'Doanh nghiệp') + ')';
    document.getElementById('replyReviewContent').textContent = '"' + (r.noi_dung || '') + '"';
    document.getElementById('replyReviewText').value = r.phan_hoi_admin || '';

    const modal = new bootstrap.Modal(document.getElementById('modalReplyReview'));
    modal.show();
}

function openReplySupportModal(m) {
    document.getElementById('replySupportId').value = m.id;
    document.getElementById('replySupportTitle').textContent = m.tieu_de || 'Yêu cầu hỗ trợ';
    document.getElementById('replySupportCustomer').textContent = 'Khách hàng: ' + (m.ten_khach_hang || 'Khách vãng lai') + ' (' + (m.email_khach_hang || '—') + ')';
    document.getElementById('replySupportContent').textContent = m.noi_dung || '';

    const modal = new bootstrap.Modal(document.getElementById('modalReplySupport'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
