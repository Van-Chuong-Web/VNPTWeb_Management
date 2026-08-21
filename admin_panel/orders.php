<?php
/**
 * orders.php — Quản lý & Thống kê Hóa đơn (Đơn hàng)
 * - Xem danh sách, lọc theo trạng thái / khoảng ngày / tìm kiếm
 * - Điều chỉnh hóa đơn: đổi trạng thái, phí vận chuyển, giảm giá, ghi chú
 * - Thống kê doanh thu theo biểu đồ (Chart.js): doanh thu theo tháng, đơn theo trạng thái
 * - Xóa (hủy vĩnh viễn) hóa đơn
 */

require_once __DIR__ . '/auth_check.php'; // Đảm bảo đã đăng nhập trước khi xử lý bất kỳ hành động POST nào
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$msg = '';
$msgType = 'success';

// ── Nhãn trạng thái ───────────────────────────────────────
$statusLabels = [
    'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
    'da_xac_nhan'  => ['Đã xác nhận',  'info'],
    'dang_giao'    => ['Đang giao',    'primary'],
    'hoan_thanh'   => ['Hoàn thành',   'success'],
    'da_huy'       => ['Đã hủy',       'danger'],
];

// ── Xử lý Form (POST) ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nhanVienId = $_SESSION['admin_user']['nhan_vien_id'] ?? null;

    // ── ĐIỀU CHỈNH hóa đơn (trạng thái, phí ship, giảm giá, ghi chú) ──
    if ($action === 'adjust') {
        $id       = (int)($_POST['id'] ?? 0);
        $trangThai= $_POST['trang_thai_don_hang'] ?? 'cho_xac_nhan';
        $phiVcRaw = preg_replace('/[^0-9]/', '', $_POST['phi_van_chuyen'] ?? '0');
        $giamGiaRaw = preg_replace('/[^0-9]/', '', $_POST['giam_gia'] ?? '0');
        $phiVc    = (float)($phiVcRaw !== '' ? $phiVcRaw : 0);
        $giamGia  = (float)($giamGiaRaw !== '' ? $giamGiaRaw : 0);
        $ghiChu   = trim($_POST['ghi_chu'] ?? '');

        if (!$id || !isset($statusLabels[$trangThai]) || $phiVc < 0 || $giamGia < 0) {
            $msg = 'Dữ liệu điều chỉnh không hợp lệ.';
            $msgType = 'danger';
        } else {
            try {
                $pdo->beginTransaction();

                $stmtTong = $pdo->prepare("SELECT tong_tien_hang, trang_thai_don_hang FROM don_hang WHERE id = :id");
                $stmtTong->execute([':id' => $id]);
                $row = $stmtTong->fetch();

                if (!$row) {
                    throw new Exception('Không tìm thấy hóa đơn.');
                }

                $tongThanhToan = max(0, (float)$row['tong_tien_hang'] + $phiVc - $giamGia);

                $stmt = $pdo->prepare(
                    "UPDATE don_hang
                        SET trang_thai_don_hang = :ts, phi_van_chuyen = :pvc,
                            giam_gia = :gg, tong_thanh_toan = :tt, ghi_chu = :gc
                      WHERE id = :id"
                );
                $stmt->execute([
                    ':ts'  => $trangThai,
                    ':pvc' => $phiVc,
                    ':gg'  => $giamGia,
                    ':tt'  => $tongThanhToan,
                    ':gc'  => $ghiChu ?: null,
                    ':id'  => $id,
                ]);

                if ($row['trang_thai_don_hang'] !== $trangThai) {
                    $stmtLog = $pdo->prepare(
                        "INSERT INTO lich_su_trang_thai_don_hang (don_hang_id, trang_thai, ghi_chu, nhan_vien_id)
                         VALUES (:did, :ts, :gc, :nv)"
                    );
                    $stmtLog->execute([
                        ':did' => $id,
                        ':ts'  => $trangThai,
                        ':gc'  => 'Cập nhật bởi quản trị viên',
                        ':nv'  => $nhanVienId,
                    ]);
                }

                $pdo->commit();
                logActivity($pdo, "Đã điều chỉnh hóa đơn ID $id (trạng thái: $trangThai)");
                $msg = '✅ Đã điều chỉnh hóa đơn thành công!';
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $msg = 'Lỗi: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }

    // ── XÓA hóa đơn ────────────────────────────────────────
    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM don_hang WHERE id = :id");
                $stmt->execute([':id' => $id]);
                logActivity($pdo, "Đã xóa hóa đơn ID $id");
                $msg = '🗑️ Đã xóa hóa đơn thành công.';
            } catch (PDOException $e) {
                $msg = 'Lỗi khi xóa: ' . (str_contains($e->getMessage(), 'foreign key') ? 'Không thể xóa vì hóa đơn liên kết dữ liệu khác.' : $e->getMessage());
                $msgType = 'danger';
            }
        }
    }
}

// ── Bộ lọc ─────────────────────────────────────────────────
$search       = trim($_GET['q']          ?? '');
$filterStatus = $_GET['trang_thai']      ?? '';
$tuNgay       = $_GET['tu_ngay']         ?? '';
$denNgay      = $_GET['den_ngay']        ?? '';

$sql = "SELECT dh.*, kh.ho_ten AS khach_hang_ten, tk.email AS khach_hang_email
          FROM don_hang dh
          JOIN khach_hang kh ON kh.id = dh.khach_hang_id
          JOIN tai_khoan tk ON tk.id = kh.tai_khoan_id
         WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= ' AND (dh.ma_don_hang LIKE :q OR kh.ho_ten LIKE :q OR tk.email LIKE :q)';
    $params[':q'] = "%$search%";
}
if ($filterStatus !== '' && isset($statusLabels[$filterStatus])) {
    $sql .= ' AND dh.trang_thai_don_hang = :ts';
    $params[':ts'] = $filterStatus;
}
if ($tuNgay !== '') {
    $sql .= ' AND DATE(dh.created_at) >= :tu';
    $params[':tu'] = $tuNgay;
}
if ($denNgay !== '') {
    $sql .= ' AND DATE(dh.created_at) <= :den';
    $params[':den'] = $denNgay;
}
$sql .= ' ORDER BY dh.created_at DESC LIMIT 100';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// ── Chi tiết từng hóa đơn (để hiển thị modal) ────────────────
$orderItemsMap = [];
if ($orders) {
    $ids = array_column($orders, 'id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $stmtItems = $pdo->prepare("SELECT * FROM don_hang_chi_tiet WHERE don_hang_id IN ($in)");
    $stmtItems->execute($ids);
    foreach ($stmtItems->fetchAll() as $item) {
        $orderItemsMap[$item['don_hang_id']][] = $item;
    }
}

// ── Thống kê tổng quan ────────────────────────────────────
$tongDoanhThu = $pdo->query("SELECT COALESCE(SUM(tong_thanh_toan),0) FROM don_hang WHERE trang_thai_don_hang = 'hoan_thanh'")->fetchColumn();
$tongDonThangNay = $pdo->query("SELECT COUNT(*) FROM don_hang WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();
$choXacNhan = $pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai_don_hang = 'cho_xac_nhan'")->fetchColumn();
$hoanThanh  = $pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai_don_hang = 'hoan_thanh'")->fetchColumn();

// ── Dữ liệu biểu đồ: doanh thu 6 tháng gần nhất ──────────
$revenueRows = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
           SUM(tong_thanh_toan) AS revenue,
           COUNT(*) AS so_don
      FROM don_hang
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
       AND trang_thai_don_hang <> 'da_huy'
     GROUP BY ym ORDER BY ym
")->fetchAll();

$chartLabels = [];
$chartRevenue = [];
$chartCount = [];
foreach ($revenueRows as $r) {
    $chartLabels[]  = date('m/Y', strtotime($r['ym'] . '-01'));
    $chartRevenue[] = (float)$r['revenue'];
    $chartCount[]   = (int)$r['so_don'];
}

// ── Dữ liệu biểu đồ: phân bố trạng thái ──────────────────
$statusRows = $pdo->query("SELECT trang_thai_don_hang, COUNT(*) AS c FROM don_hang GROUP BY trang_thai_don_hang")->fetchAll();
$statusChartLabels = [];
$statusChartData = [];
$statusChartColors = [];
$colorMap = ['cho_xac_nhan'=>'#f59e0b','da_xac_nhan'=>'#0ea5e9','dang_giao'=>'#3b82f6','hoan_thanh'=>'#22c55e','da_huy'=>'#ef4444'];
foreach ($statusRows as $r) {
    $statusChartLabels[] = $statusLabels[$r['trang_thai_don_hang']][0] ?? $r['trang_thai_don_hang'];
    $statusChartData[]   = (int)$r['c'];
    $statusChartColors[] = $colorMap[$r['trang_thai_don_hang']] ?? '#94a3b8';
}

$pageTitle  = 'Hóa đơn & Thống kê';
$activeMenu = 'orders';
require_once __DIR__ . '/header.php';
?>

<!-- ── Thống kê nhanh ─────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#34d399)">
            <i class="fa-solid fa-sack-dollar stat-icon"></i>
            <div class="stat-value" style="font-size:22px"><?= number_format($tongDoanhThu, 0, ',', '.') ?>đ</div>
            <div class="stat-label">Tổng doanh thu (hoàn thành)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1d4ed8,#60a5fa)">
            <i class="fa-solid fa-file-invoice stat-icon"></i>
            <div class="stat-value"><?= $tongDonThangNay ?></div>
            <div class="stat-label">Đơn hàng tháng này</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
            <i class="fa-solid fa-hourglass-half stat-icon"></i>
            <div class="stat-value"><?= $choXacNhan ?></div>
            <div class="stat-label">Chờ xác nhận</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#c084fc)">
            <i class="fa-solid fa-circle-check stat-icon"></i>
            <div class="stat-value"><?= $hoanThanh ?></div>
            <div class="stat-label">Đơn hoàn thành</div>
        </div>
    </div>
</div>

<!-- ── Biểu đồ thống kê ──────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Doanh thu 6 tháng gần nhất</div>
            <div class="card-body">
                <canvas id="revenueChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Phân bố trạng thái đơn</div>
            <div class="card-body">
                <canvas id="statusChart" height="180"></canvas>
            </div>
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
        <span><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Danh sách Hóa đơn / Đơn hàng</span>
    </div>

    <!-- Bộ lọc / Tìm kiếm -->
    <div class="card-body border-bottom pb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label small fw-semibold mb-1">Tìm kiếm</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" name="q" class="form-control"
                           placeholder="Mã đơn, tên hoặc email khách hàng..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1">Trạng thái</label>
                <select name="trang_thai" class="form-select form-select-sm">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($statusLabels as $key => [$label, $color]): ?>
                        <option value="<?= $key ?>" <?= $filterStatus===$key?'selected':'' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1">Từ ngày</label>
                <input type="date" name="tu_ngay" class="form-control form-control-sm" value="<?= htmlspecialchars($tuNgay) ?>">
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1">Đến ngày</label>
                <input type="date" name="den_ngay" class="form-control form-control-sm" value="<?= htmlspecialchars($denNgay) ?>">
            </div>
            <div class="col-sm-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fa-solid fa-filter me-1"></i>Lọc
                </button>
                <a href="orders.php" class="btn btn-outline-secondary btn-sm" title="Xóa bộ lọc">
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
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Tổng thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th style="width:120px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Không tìm thấy hóa đơn nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $i => $o):
                        [$label, $color] = $statusLabels[$o['trang_thai_don_hang']] ?? [$o['trang_thai_don_hang'], 'secondary'];
                        $items = $orderItemsMap[$o['id']] ?? [];
                    ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($o['ma_don_hang']) ?></td>
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($o['khach_hang_ten']) ?></div>
                            <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($o['khach_hang_email']) ?></div>
                        </td>
                        <td class="fw-semibold"><?= number_format($o['tong_thanh_toan'], 0, ',', '.') ?>đ</td>
                        <td><span class="badge bg-<?= $color ?>"><?= $label ?></span></td>
                        <td class="text-muted" style="font-size:13px"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-outline-primary btn-action me-1"
                                    onclick='openOrderModal(<?= htmlspecialchars(json_encode([
                                        "id" => $o["id"],
                                        "ma_don_hang" => $o["ma_don_hang"],
                                        "khach_hang_ten" => $o["khach_hang_ten"],
                                        "khach_hang_email" => $o["khach_hang_email"],
                                        "tong_tien_hang" => $o["tong_tien_hang"],
                                        "phi_van_chuyen" => $o["phi_van_chuyen"],
                                        "giam_gia" => $o["giam_gia"],
                                        "tong_thanh_toan" => $o["tong_thanh_toan"],
                                        "trang_thai_don_hang" => $o["trang_thai_don_hang"],
                                        "ghi_chu" => $o["ghi_chu"],
                                        "items" => $items,
                                    ]), ENT_QUOTES) ?>)'
                                    title="Xem / Điều chỉnh hóa đơn">
                                <i class="fa-solid fa-file-pen"></i>
                            </button>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Xóa vĩnh viễn hóa đơn này? Hành động không thể hoàn tác.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
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
        Hiển thị <strong><?= count($orders) ?></strong> hóa đơn gần nhất (tối đa 100)
    </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- Modal Xem / Điều chỉnh Hóa đơn                         -->
<!-- ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalOrder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST">
                <input type="hidden" name="action" value="adjust">
                <input type="hidden" name="id" id="ordId">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>
                        Hóa đơn <span id="ordMaDon"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="fw-semibold" id="ordKhachTen"></div>
                        <div class="text-muted" style="font-size:13px" id="ordKhachEmail"></div>
                    </div>

                    <!-- Danh sách sản phẩm -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Sản phẩm</th><th class="text-end">SL</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr>
                            </thead>
                            <tbody id="ordItemsBody"></tbody>
                        </table>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Trạng thái đơn hàng</label>
                            <select name="trang_thai_don_hang" id="ordTrangThai" class="form-select">
                                <?php foreach ($statusLabels as $key => [$label, $color]): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6"></div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tiền hàng</label>
                            <input type="text" class="form-control" id="ordTienHang" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Phí vận chuyển (đ)</label>
                            <input type="text" name="phi_van_chuyen" id="ordPhiVc" class="form-control" placeholder="0" oninput="formatAndRecalc(this)">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Giảm giá (đ)</label>
                            <input type="text" name="giam_gia" id="ordGiamGia" class="form-control" placeholder="0" oninput="formatAndRecalc(this)">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tổng thanh toán (ước tính)</label>
                            <input type="text" class="form-control fw-bold text-primary" id="ordTongThanhToan" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Ghi chú điều chỉnh</label>
                            <textarea name="ghi_chu" id="ordGhiChu" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu điều chỉnh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Chart.js ──────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
let ordTienHangRaw = 0;

function parseDbVal(val) {
    if (val === null || val === undefined || val === '') return 0;
    return Math.round(parseFloat(val) || 0);
}

function parseInputVal(val) {
    if (!val) return 0;
    const cleanStr = String(val).replace(/[^0-9]/g, '');
    return parseInt(cleanStr, 10) || 0;
}

function fmtVND(n) {
    return Number(n || 0).toLocaleString('vi-VN') + 'đ';
}

function formatAndRecalc(inputEl) {
    const rawVal = parseInputVal(inputEl.value);
    inputEl.value = rawVal > 0 ? rawVal.toLocaleString('vi-VN') : (inputEl.value.trim() === '' ? '' : '0');
    recalcOrderTotal();
}

function recalcOrderTotal() {
    const phi = parseInputVal(document.getElementById('ordPhiVc').value);
    const gg  = parseInputVal(document.getElementById('ordGiamGia').value);
    const total = Math.max(0, ordTienHangRaw + phi - gg);
    document.getElementById('ordTongThanhToan').value = fmtVND(total);
}

function openOrderModal(data) {
    document.getElementById('ordId').value = data.id;
    document.getElementById('ordMaDon').textContent = data.ma_don_hang;
    document.getElementById('ordKhachTen').textContent = data.khach_hang_ten;
    document.getElementById('ordKhachEmail').textContent = data.khach_hang_email;
    document.getElementById('ordTrangThai').value = data.trang_thai_don_hang;

    const phiVcVal = parseDbVal(data.phi_van_chuyen);
    const giamGiaVal = parseDbVal(data.giam_gia);

    document.getElementById('ordPhiVc').value = phiVcVal > 0 ? phiVcVal.toLocaleString('vi-VN') : '0';
    document.getElementById('ordGiamGia').value = giamGiaVal > 0 ? giamGiaVal.toLocaleString('vi-VN') : '0';
    document.getElementById('ordGhiChu').value = data.ghi_chu || '';

    ordTienHangRaw = parseDbVal(data.tong_tien_hang);
    document.getElementById('ordTienHang').value = fmtVND(ordTienHangRaw);
    recalcOrderTotal();

    const body = document.getElementById('ordItemsBody');
    body.innerHTML = '';
    (data.items || []).forEach(it => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${it.ten_san_pham_snapshot}</td><td class="text-end">${it.so_luong}</td>` +
                        `<td class="text-end">${fmtVND(it.don_gia)}</td><td class="text-end">${fmtVND(it.thanh_tien)}</td>`;
        body.appendChild(tr);
    });
    if (!(data.items || []).length) {
        body.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Không có sản phẩm.</td></tr>';
    }

    new bootstrap.Modal(document.getElementById('modalOrder')).show();
}

// ── Biểu đồ doanh thu ─────────────────────────────────────
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Doanh thu (đ)',
            data: <?= json_encode($chartRevenue) ?>,
            backgroundColor: '#0d6efd99',
            borderRadius: 6,
            yAxisID: 'y',
        }, {
            label: 'Số đơn hàng',
            data: <?= json_encode($chartCount) ?>,
            type: 'line',
            borderColor: '#f59e0b',
            backgroundColor: '#f59e0b',
            yAxisID: 'y1',
            tension: .3,
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y:  { position: 'left', beginAtZero: true, ticks: { callback: v => (v/1000000).toFixed(1)+'tr' } },
            y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
        }
    }
});

// ── Biểu đồ trạng thái ────────────────────────────────────
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($statusChartLabels) ?>,
        datasets: [{
            data: <?= json_encode($statusChartData) ?>,
            backgroundColor: <?= json_encode($statusChartColors) ?>,
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
