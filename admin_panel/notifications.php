<?php
/**
 * notifications.php — Quản lý Thông báo
 * - Gửi thông báo cho Khách hàng (bảng thong_bao) — gửi 1 người hoặc tất cả
 * - Gửi thông báo nội bộ cho Nhân viên (bảng thong_bao_nhan_vien) — gửi 1 người hoặc tất cả
 * - Xem lịch sử, xóa thông báo đã gửi
 */

require_once __DIR__ . '/auth_check.php'; // Đảm bảo đã đăng nhập trước khi xử lý bất kỳ hành động POST nào
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$currentRole = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
$isAdminRole = in_array($currentRole, ['quan_tri_vien', 'superadmin', 'admin']);
$currentEmpId = $_SESSION['admin_user']['nhan_vien_id'] ?? 0;

$msg = '';
$msgType = 'success';
$activeTab = $isAdminRole ? ($_GET['tab'] ?? 'khach_hang') : 'nhan_vien';

// ── Xử lý Form (POST) ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── GỬI thông báo cho KHÁCH HÀNG ──────────────────────
    if ($action === 'send_customer') {
        $mode    = $_POST['recipient_mode'] ?? 'all';
        $recvId  = (int)($_POST['recipient_id'] ?? 0);
        $tieuDe  = trim($_POST['tieu_de'] ?? '');
        $noiDung = trim($_POST['noi_dung'] ?? '');
        $loai    = $_POST['loai'] ?? 'he_thong';

        if (!$tieuDe || !$noiDung) {
            $msg = 'Vui lòng nhập đầy đủ Tiêu đề và Nội dung.';
            $msgType = 'danger';
        } else {
            try {
                if ($mode === 'single' && $recvId > 0) {
                    $ids = [$recvId];
                } else {
                    $ids = $pdo->query("SELECT id FROM khach_hang")->fetchAll(PDO::FETCH_COLUMN);
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO thong_bao (khach_hang_id, tieu_de, noi_dung, loai) VALUES (:kid, :td, :nd, :loai)"
                );
                foreach ($ids as $kid) {
                    $stmt->execute([':kid' => $kid, ':td' => $tieuDe, ':nd' => $noiDung, ':loai' => $loai]);
                }

                logActivity($pdo, "Đã gửi thông báo '$tieuDe' tới " . count($ids) . " khách hàng");
                $msg = "✅ Đã gửi thông báo tới <strong>" . count($ids) . "</strong> khách hàng!";
                $activeTab = 'khach_hang';
            } catch (PDOException $e) {
                $msg = 'Lỗi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }

    // ── GỬI thông báo cho NHÂN VIÊN ───────────────────────
    elseif ($action === 'send_employee') {
        $mode    = $_POST['recipient_mode'] ?? 'all';
        $recvId  = (int)($_POST['recipient_id'] ?? 0);
        $tieuDe  = trim($_POST['tieu_de'] ?? '');
        $noiDung = trim($_POST['noi_dung'] ?? '');

        if (!$tieuDe || !$noiDung) {
            $msg = 'Vui lòng nhập đầy đủ Tiêu đề và Nội dung.';
            $msgType = 'danger';
        } else {
            try {
                if ($mode === 'single' && $recvId > 0) {
                    $ids = [$recvId];
                } else {
                    // Loại trừ chính tài khoản Admin gửi thông báo
                    $stmtIds = $pdo->prepare("SELECT id FROM nhan_vien WHERE id != :senderId");
                    $stmtIds->execute([':senderId' => $currentEmpId]);
                    $ids = $stmtIds->fetchAll(PDO::FETCH_COLUMN);
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO thong_bao_nhan_vien (nhan_vien_id, tieu_de, noi_dung) VALUES (:nvid, :td, :nd)"
                );
                foreach ($ids as $nvid) {
                    if ((int)$nvid === (int)$currentEmpId && $mode !== 'single') continue; // Đảm bảo không gửi cho chính mình
                    $stmt->execute([':nvid' => $nvid, ':td' => $tieuDe, ':nd' => $noiDung]);
                }

                logActivity($pdo, "Đã gửi thông báo '$tieuDe' tới " . count($ids) . " nhân viên");
                $msg = "✅ Đã gửi thông báo tới <strong>" . count($ids) . "</strong> nhân viên!";
                $activeTab = 'nhan_vien';
            } catch (PDOException $e) {
                $msg = 'Lỗi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }

    // ── ĐÁNH DẤU ĐÃ ĐỌC thông báo nhân viên ────────────────
    elseif ($action === 'mark_read_employee_noti') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE thong_bao_nhan_vien SET da_doc = 1 WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $msg = '✅ Đã đánh dấu thông báo là đã đọc.';
        }
        $activeTab = 'nhan_vien';
    }

    // ── XÓA thông báo khách hàng ───────────────────────────
    elseif ($action === 'delete_customer_noti') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM thong_bao WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $msg = '🗑️ Đã xóa thông báo.';
        }
        $activeTab = 'khach_hang';
    }

    // ── XÓA thông báo nhân viên ─────────────────────────────
    elseif ($action === 'delete_employee_noti') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM thong_bao_nhan_vien WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $msg = '🗑️ Đã xóa thông báo.';
        }
        $activeTab = 'nhan_vien';
    }
}

// Xử lý tự động đánh dấu đã đọc khi nhấp từ link `read_id`
$readId = (int)($_GET['read_id'] ?? 0);
if ($readId > 0) {
    $stmtRead = $pdo->prepare("UPDATE thong_bao_nhan_vien SET da_doc = 1 WHERE id = :id");
    $stmtRead->execute([':id' => $readId]);
}

// ── Danh sách khách hàng / nhân viên (cho dropdown chọn người nhận) ──
$customerList = $pdo->query("
    SELECT kh.id, kh.ho_ten, tk.email FROM khach_hang kh
    JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id ORDER BY kh.ho_ten
")->fetchAll();

$stmtEmpList = $pdo->prepare("
    SELECT nv.id, nv.ho_ten, tk.email FROM nhan_vien nv
    JOIN tai_khoan tk ON tk.id = nv.tai_khoan_id 
    WHERE nv.id != :senderId
    ORDER BY nv.ho_ten
");
$stmtEmpList->execute([':senderId' => $currentEmpId]);
$employeeList = $stmtEmpList->fetchAll();

// ── Lịch sử thông báo khách hàng ──────────────────────────
$customerNotis = $pdo->query("
    SELECT tb.*, kh.ho_ten AS khach_hang_ten
      FROM thong_bao tb
      JOIN khach_hang kh ON kh.id = tb.khach_hang_id
     ORDER BY tb.created_at DESC LIMIT 50
")->fetchAll();

// ── Thông báo nhân viên ĐÃ NHẬN (Hộp thư đến) ────────────
$stmtEmpReceived = $pdo->prepare("
    SELECT tbn.*, 
           COALESCE(nv_gui.ho_ten, 'Quản trị viên / Hệ thống') AS nguoi_gui_ten
      FROM thong_bao_nhan_vien tbn
 LEFT JOIN nhan_vien nv_gui ON nv_gui.id = tbn.nguoi_gui_id
     WHERE tbn.nhan_vien_id = :eid
  ORDER BY tbn.ngay_tao DESC LIMIT 50
");
$stmtEmpReceived->execute([':eid' => $currentEmpId]);
$receivedEmpNotis = $stmtEmpReceived->fetchAll();

// ── Thông báo nhân viên ĐÃ GỬI (Hộp thư đi) ──────────────
if ($isAdminRole) {
    $sentEmpNotis = $pdo->query("
        SELECT tbn.*, 
               COALESCE(nv_nhan.ho_ten, 'Tất cả nhân viên') AS nhan_vien_ten,
               COALESCE(nv_gui.ho_ten, 'Quản trị viên') AS nguoi_gui_ten
          FROM thong_bao_nhan_vien tbn
     LEFT JOIN nhan_vien nv_nhan ON nv_nhan.id = tbn.nhan_vien_id
     LEFT JOIN nhan_vien nv_gui ON nv_gui.id = tbn.nguoi_gui_id
      ORDER BY tbn.ngay_tao DESC LIMIT 50
    ")->fetchAll();
} else {
    $stmtEmpSent = $pdo->prepare("
        SELECT tbn.*, 
               COALESCE(nv_nhan.ho_ten, 'Tất cả nhân viên') AS nhan_vien_ten,
               COALESCE(nv_gui.ho_ten, 'Quản trị viên') AS nguoi_gui_ten
          FROM thong_bao_nhan_vien tbn
     LEFT JOIN nhan_vien nv_nhan ON nv_nhan.id = tbn.nhan_vien_id
     LEFT JOIN nhan_vien nv_gui ON nv_gui.id = tbn.nguoi_gui_id
         WHERE tbn.nguoi_gui_id = :eid
      ORDER BY tbn.ngay_tao DESC LIMIT 50
    ");
    $stmtEmpSent->execute([':eid' => $currentEmpId]);
    $sentEmpNotis = $stmtEmpSent->fetchAll();
}

$loaiLabels = ['don_hang' => ['Đơn hàng','primary'], 'khuyen_mai' => ['Khuyến mãi','warning'], 'he_thong' => ['Hệ thống','secondary']];

$pageTitle  = 'Thông báo';
$activeMenu = 'notifications';
require_once __DIR__ . '/header.php';
?>

<!-- ── Alert thông báo ───────────────────────────────────── -->
<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
    <?= $msg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($isAdminRole): ?>
<!-- ── Tabs dành riêng cho Admin ────────────────────────── -->
<ul class="nav nav-tabs mb-3" id="notiTabs">
    <li class="nav-item">
        <button class="nav-link <?= $activeTab==='khach_hang'?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#tabKhachHang" type="button">
            <i class="fa-solid fa-users me-1"></i> Thông báo Khách hàng
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link <?= $activeTab==='nhan_vien'?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#tabNhanVien" type="button">
            <i class="fa-solid fa-user-tie me-1"></i> Thông báo Nhân viên
        </button>
    </li>
</ul>
<?php else: ?>
<div class="d-flex align-items-center justify-content-between mb-3 bg-white p-3 rounded shadow-sm border">
    <div>
        <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-inbox text-primary me-2"></i>Hộp Thư Thông Báo Nội Bộ Của Tôi</h5>
        <p class="text-muted small mb-0">Tài khoản: <strong><?= htmlspecialchars($adminName) ?></strong> (<?= htmlspecialchars($adminRole) ?>)</p>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill"><i class="fa-solid fa-bell me-1"></i> <?= count($receivedEmpNotis) ?> thông báo</span>
</div>
<?php endif; ?>

<div class="tab-content">
<?php if ($isAdminRole): ?>
<!-- ══════════════════════════════════════════════════════ -->
<!-- TAB: Thông báo Khách hàng (Admin)                      -->
<!-- ══════════════════════════════════════════════════════ -->
<div class="tab-pane fade <?= $activeTab==='khach_hang'?'show active':'' ?>" id="tabKhachHang">
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-primary"><i class="fa-solid fa-paper-plane me-2"></i>Soạn thông báo cho Khách hàng</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="send_customer">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Người nhận</label>
                            <select name="recipient_mode" class="form-select" id="custRecipientMode" onchange="document.getElementById('custRecipientPicker').style.display = this.value==='single' ? 'block':'none'">
                                <option value="all">📢 Tất cả khách hàng</option>
                                <option value="single">Một khách hàng cụ thể</option>
                            </select>
                        </div>
                        <div class="mb-3" id="custRecipientPicker" style="display:none">
                            <select name="recipient_id" class="form-select">
                                <?php foreach ($customerList as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['ho_ten']) ?> — <?= htmlspecialchars($c['email']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Loại thông báo</label>
                            <select name="loai" class="form-select">
                                <option value="he_thong">Hệ thống</option>
                                <option value="don_hang">Đơn hàng</option>
                                <option value="khuyen_mai">Khuyến mãi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="tieu_de" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nội dung <span class="text-danger">*</span></label>
                            <textarea name="noi_dung" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="fa-solid fa-paper-plane me-1"></i>Gửi thông báo
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Lịch sử thông báo Khách hàng
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:560px;overflow-y:auto">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Khách hàng</th><th>Tiêu đề</th><th>Loại</th><th>Trạng thái</th><th>Ngày gửi</th><th style="width:130px;" class="text-center">Thao tác</th></tr>
                            </thead>
                            <tbody>
                            <?php if (empty($customerNotis)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Chưa có thông báo nào.</td></tr>
                            <?php else: foreach ($customerNotis as $n):
                                [$ll, $lc] = $loaiLabels[$n['loai']] ?? [$n['loai'], 'secondary'];
                            ?>
                                <tr>
                                    <td class="fw-medium"><?= htmlspecialchars($n['khach_hang_ten']) ?></td>
                                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($n['noi_dung']) ?>">
                                        <?= htmlspecialchars($n['tieu_de']) ?>
                                    </td>
                                    <td><span class="badge bg-<?= $lc ?>"><?= $ll ?></span></td>
                                    <td><?= $n['da_doc'] ? '<span class="badge bg-light text-dark">Đã đọc</span>' : '<span class="badge bg-primary">Chưa đọc</span>' ?></td>
                                    <td class="text-muted" style="font-size:12px"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-read-customer-noti px-2 py-1"
                                                    data-id="<?= $n['id'] ?>"
                                                    data-recipient="<?= htmlspecialchars($n['khach_hang_ten']) ?>"
                                                    data-title="<?= htmlspecialchars($n['tieu_de']) ?>"
                                                    data-type="<?= htmlspecialchars($ll) ?>"
                                                    data-type-color="<?= $lc ?>"
                                                    data-content="<?= htmlspecialchars($n['noi_dung'] ?? '') ?>"
                                                    data-time="<?= date('d/m/Y H:i', strtotime($n['created_at'])) ?>"
                                                    title="Xem nội dung chi tiết">
                                                <i class="fa-solid fa-eye me-1"></i>Xem
                                            </button>
                                            <form method="POST" class="d-inline m-0" onsubmit="return confirm('Xóa thông báo này?')">
                                                <input type="hidden" name="action" value="delete_customer_noti">
                                                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                                <button class="btn btn-sm btn-outline-danger px-2 py-1" title="Xóa thông báo"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════ -->
<!-- TAB: Thông báo Nhân viên                               -->
<!-- ══════════════════════════════════════════════════════ -->
<div class="tab-pane fade <?= $activeTab==='nhan_vien'?'show active':'' ?>" id="tabNhanVien">
    <div class="row g-3">
        <?php if ($isAdminRole): ?>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-primary"><i class="fa-solid fa-paper-plane me-2"></i>Soạn thông báo nội bộ cho Nhân viên</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="send_employee">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Người nhận</label>
                            <select name="recipient_mode" class="form-select" id="empRecipientMode" onchange="document.getElementById('empRecipientPicker').style.display = this.value==='single' ? 'block':'none'">
                                <option value="all">📢 Tất cả nhân viên</option>
                                <option value="single">Một nhân viên cụ thể</option>
                            </select>
                        </div>
                        <div class="mb-3" id="empRecipientPicker" style="display:none">
                            <select name="recipient_id" class="form-select">
                                <?php foreach ($employeeList as $e): ?>
                                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['ho_ten']) ?> — <?= htmlspecialchars($e['email']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="tieu_de" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nội dung <span class="text-danger">*</span></label>
                            <textarea name="noi_dung" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="fa-solid fa-paper-plane me-1"></i>Gửi thông báo
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
        <?php else: ?>
        <div class="col-12">
        <?php endif; ?>

            <!-- ── CARD 1: HỘP THƯ ĐẾN (Thông báo ĐÃ NHẬN từ người khác) ── -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="fa-solid fa-inbox me-2"></i>Hộp Thư Đến — Thông Báo Đã Nhận (Từ Nhân viên khác)
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill">
                        <i class="fa-solid fa-bell me-1"></i> <?= count($receivedEmpNotis) ?> thông báo
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:300px;overflow-y:auto">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Người gửi</th>
                                    <th>Tiêu đề &amp; Nội dung tóm tắt</th>
                                    <th style="width:120px;">Trạng thái</th>
                                    <th style="width:140px;">Ngày nhận</th>
                                    <th style="width:80px;" class="text-center">Xem</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($receivedEmpNotis)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fa-regular fa-envelope-open fs-2 d-block mb-1 opacity-25"></i>
                                        Bạn chưa nhận thông báo nào từ nhân viên khác.
                                    </td>
                                </tr>
                            <?php else: foreach ($receivedEmpNotis as $rn): ?>
                                <tr class="<?= ($readId === (int)$rn['id']) ? 'table-warning' : '' ?>">
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($rn['nguoi_gui_ten']) ?></td>
                                    <td>
                                        <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($rn['tieu_de']) ?></div>
                                        <div class="small text-secondary text-truncate" style="max-width: 380px;">
                                            <?= htmlspecialchars($rn['noi_dung'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= $rn['da_doc'] 
                                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i> Đã đọc</span>' 
                                            : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-circle me-1" style="font-size:7px"></i> Chưa đọc</span>' ?>
                                    </td>
                                    <td class="small text-muted font-monospace"><?= date('d/m/Y H:i', strtotime($rn['ngay_tao'])) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-read-noti px-2 py-1" 
                                                data-id="<?= $rn['id'] ?>"
                                                data-recipient="<?= htmlspecialchars($rn['nguoi_gui_ten']) ?>"
                                                data-title="<?= htmlspecialchars($rn['tieu_de']) ?>"
                                                data-content="<?= htmlspecialchars($rn['noi_dung'] ?? '') ?>"
                                                data-time="<?= date('d/m/Y H:i', strtotime($rn['ngay_tao'])) ?>"
                                                title="Xem nội dung chi tiết">
                                            <i class="fa-solid fa-eye me-1"></i>Xem
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ── CARD 2: HỘP THƯ ĐI (Lịch sử thông báo ĐÃ GỬI cho người khác) ── -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="fa-solid fa-paper-plane me-2"></i>Lịch Sử Thông Báo Đã Gửi (Hộp Thư Đi)
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-pill">
                        <?= count($sentEmpNotis) ?> đã phát
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:300px;overflow-y:auto">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhân viên nhận</th>
                                    <th>Tiêu đề &amp; Nội dung tóm tắt</th>
                                    <th style="width:120px;">Trạng thái</th>
                                    <th style="width:140px;">Ngày gửi</th>
                                    <th style="width:110px;" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($sentEmpNotis)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fa-regular fa-paper-plane fs-2 d-block mb-1 opacity-25"></i>
                                        Chưa có lịch sử gửi thông báo nội bộ.
                                    </td>
                                </tr>
                            <?php else: foreach ($sentEmpNotis as $sn): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($sn['nhan_vien_ten']) ?></td>
                                    <td>
                                        <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($sn['tieu_de']) ?></div>
                                        <div class="small text-secondary text-truncate" style="max-width: 380px;">
                                            <?= htmlspecialchars($sn['noi_dung'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= $sn['da_doc'] 
                                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i> Đã đọc</span>' 
                                            : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i> Đã phát</span>' ?>
                                    </td>
                                    <td class="small text-muted font-monospace"><?= date('d/m/Y H:i', strtotime($sn['ngay_tao'])) ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-read-noti px-2 py-1" 
                                                    data-id="<?= $sn['id'] ?>"
                                                    data-recipient="<?= htmlspecialchars($sn['nhan_vien_ten']) ?>"
                                                    data-title="<?= htmlspecialchars($sn['tieu_de']) ?>"
                                                    data-content="<?= htmlspecialchars($sn['noi_dung'] ?? '') ?>"
                                                    data-time="<?= date('d/m/Y H:i', strtotime($sn['ngay_tao'])) ?>"
                                                    title="Xem nội dung chi tiết">
                                                <i class="fa-solid fa-eye me-1"></i>Xem
                                            </button>
                                            
                                            <?php if ($isAdminRole): ?>
                                            <form method="POST" class="d-inline m-0" onsubmit="return confirm('Xóa thông báo này?')">
                                                <input type="hidden" name="action" value="delete_employee_noti">
                                                <input type="hidden" name="id" value="<?= $sn['id'] ?>">
                                                <button class="btn btn-sm btn-outline-danger px-2 py-1" title="Xóa thông báo"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xem Chi Tiết Nội Dung Thông Báo Khách Hàng -->
<div class="modal fade" id="custNotiViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bell me-1"></i>
                    Chi Tiết Thông Báo Khách Hàng <span id="modalCustNotiId" class="badge bg-light text-primary font-monospace">#0</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="bg-light p-3 rounded border mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small d-block">Khách hàng nhận:</span>
                            <strong id="modalCustNotiRecipient" class="text-dark">...</strong>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Loại thông báo:</span>
                            <span id="modalCustNotiType" class="badge bg-primary">...</span>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top text-muted small">
                        <span>Thời gian gửi: </span><span id="modalCustNotiTime" class="font-monospace fw-semibold text-secondary">...</span>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold text-primary mb-2" id="modalCustNotiTitle">Tiêu đề thông báo</h6>
                    <div class="p-3 bg-primary bg-opacity-10 border border-primary-subtle rounded text-dark fs-6" style="line-height: 1.6; white-space: pre-line;" id="modalCustNotiContent">
                        Nội dung thông báo...
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xem Chi Tiết Nội Dung Thông Báo Nội Bộ -->
<div class="modal fade" id="notiViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bell text-primary"></i>
                    Chi Tiết Thông Báo Nội Bộ <span id="modalNotiId" class="badge bg-primary font-monospace">#0</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="bg-light p-3 rounded border mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small d-block">Người nhận:</span>
                            <strong id="modalNotiRecipient" class="text-dark">...</strong>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Thời gian gửi:</span>
                            <span id="modalNotiTime" class="small font-monospace fw-semibold text-secondary">...</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold text-primary mb-2" id="modalNotiTitle">Tiêu đề thông báo</h6>
                    <div class="p-3 bg-primary bg-opacity-10 border border-primary-subtle rounded text-dark fs-6" style="line-height: 1.6; white-space: pre-line;" id="modalNotiContent">
                        Nội dung thông báo...
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 px-4">
                <form method="POST" id="formMarkRead">
                    <input type="hidden" name="action" value="mark_read_employee_noti">
                    <input type="hidden" name="id" id="modalNotiInputId" value="0">
                    <button type="submit" class="btn btn-success btn-sm fw-bold">
                        <i class="fa-solid fa-check-double me-1"></i> Đã đọc xong
                    </button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sử dụng Event Delegation giúp nút "Xem" hoạt động 100% mọi lúc
    document.addEventListener('click', function(e) {
        // 1. Xem chi tiết Thông báo Nội bộ Nhân viên
        const btnEmp = e.target.closest('.btn-read-noti');
        if (btnEmp) {
            e.preventDefault();
            const id = btnEmp.getAttribute('data-id');
            const recipient = btnEmp.getAttribute('data-recipient');
            const title = btnEmp.getAttribute('data-title');
            const content = btnEmp.getAttribute('data-content');
            const time = btnEmp.getAttribute('data-time');

            if (document.getElementById('modalNotiId')) document.getElementById('modalNotiId').textContent = '#' + id;
            if (document.getElementById('modalNotiInputId')) document.getElementById('modalNotiInputId').value = id;
            if (document.getElementById('modalNotiRecipient')) document.getElementById('modalNotiRecipient').textContent = recipient;
            if (document.getElementById('modalNotiTitle')) document.getElementById('modalNotiTitle').textContent = title;
            if (document.getElementById('modalNotiContent')) document.getElementById('modalNotiContent').textContent = content || '(Không có nội dung chi tiết)';
            if (document.getElementById('modalNotiTime')) document.getElementById('modalNotiTime').textContent = time;

            fetch('notifications.php?read_id=' + id);

            const modalEl = document.getElementById('notiViewModal');
            if (modalEl) {
                if (window.bootstrap && window.bootstrap.Modal) {
                    let modalObj = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalObj.show();
                } else {
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                }
            }
            return;
        }

        // 2. Xem chi tiết Thông báo Khách hàng
        const btnCust = e.target.closest('.btn-read-customer-noti');
        if (btnCust) {
            e.preventDefault();
            const id = btnCust.getAttribute('data-id');
            const recipient = btnCust.getAttribute('data-recipient');
            const title = btnCust.getAttribute('data-title');
            const type = btnCust.getAttribute('data-type');
            const typeColor = btnCust.getAttribute('data-type-color') || 'primary';
            const content = btnCust.getAttribute('data-content');
            const time = btnCust.getAttribute('data-time');

            if (document.getElementById('modalCustNotiId')) document.getElementById('modalCustNotiId').textContent = '#' + id;
            if (document.getElementById('modalCustNotiRecipient')) document.getElementById('modalCustNotiRecipient').textContent = recipient;
            if (document.getElementById('modalCustNotiTitle')) document.getElementById('modalCustNotiTitle').textContent = title;
            if (document.getElementById('modalCustNotiContent')) document.getElementById('modalCustNotiContent').textContent = content || '(Không có nội dung chi tiết)';
            if (document.getElementById('modalCustNotiTime')) document.getElementById('modalCustNotiTime').textContent = time;

            const badgeType = document.getElementById('modalCustNotiType');
            if (badgeType) {
                badgeType.textContent = type;
                badgeType.className = 'badge bg-' + typeColor;
            }

            const modalEl = document.getElementById('custNotiViewModal');
            if (modalEl) {
                if (window.bootstrap && window.bootstrap.Modal) {
                    let modalObj = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalObj.show();
                } else {
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                }
            }
            return;
        }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
