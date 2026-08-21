<?php
/**
 * activity_log.php — Nhật ký hoạt động của Nhân viên
 * Xem toàn bộ lịch sử thao tác quan trọng (thêm/sửa/xóa/khóa...) trên hệ thống,
 * lọc theo nhân viên và khoảng thời gian.
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

$filterNv  = (int)($_GET['nhan_vien_id'] ?? 0);
$tuNgay    = $_GET['tu_ngay']  ?? '';
$denNgay   = $_GET['den_ngay'] ?? '';

$sql = "SELECT ls.*, nv.ho_ten
          FROM lich_su_nhan_vien ls
          JOIN nhan_vien nv ON nv.id = ls.nhan_vien_id
         WHERE 1=1";
$params = [];

if ($filterNv > 0) {
    $sql .= ' AND ls.nhan_vien_id = :nv';
    $params[':nv'] = $filterNv;
}
if ($tuNgay !== '') {
    $sql .= ' AND DATE(ls.thoi_gian) >= :tu';
    $params[':tu'] = $tuNgay;
}
if ($denNgay !== '') {
    $sql .= ' AND DATE(ls.thoi_gian) <= :den';
    $params[':den'] = $denNgay;
}
$sql .= ' ORDER BY ls.thoi_gian DESC LIMIT 200';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

$employeeList = $pdo->query("SELECT id, ho_ten FROM nhan_vien ORDER BY ho_ten")->fetchAll();

$pageTitle  = 'Nhật ký hoạt động';
$activeMenu = 'activity_log';
require_once __DIR__ . '/header.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Nhật ký hoạt động Nhân viên
    </div>

    <div class="card-body border-bottom pb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label small fw-semibold mb-1">Nhân viên</label>
                <select name="nhan_vien_id" class="form-select form-select-sm">
                    <option value="0">-- Tất cả nhân viên --</option>
                    <?php foreach ($employeeList as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $filterNv===(int)$e['id']?'selected':'' ?>><?= htmlspecialchars($e['ho_ten']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">Từ ngày</label>
                <input type="date" name="tu_ngay" class="form-control form-control-sm" value="<?= htmlspecialchars($tuNgay) ?>">
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">Đến ngày</label>
                <input type="date" name="den_ngay" class="form-control form-control-sm" value="<?= htmlspecialchars($denNgay) ?>">
            </div>
            <div class="col-sm-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fa-solid fa-filter me-1"></i>Lọc
                </button>
                <a href="activity_log.php" class="btn btn-outline-secondary btn-sm" title="Xóa bộ lọc">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nhân viên</th>
                        <th>Hành động</th>
                        <th style="width:180px">Thời gian</th>
                        <th style="width:120px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Chưa có nhật ký hoạt động nào.
                        </td>
                    </tr>
                <?php else: foreach ($logs as $i => $log): 
                    $logId = $log['id'];
                    $nvTen = $log['ho_ten'];
                    $hanhDong = $log['hanh_dong'];
                    $thoiGian = date('d/m/Y H:i:s', strtotime($log['thoi_gian']));
                    $ipAddr = $log['ip_address'] ?? '127.0.0.1';
                    $chiTiet = $log['chi_tiet'] ?? "Chi tiết thao tác:\n- Thao tác: " . $hanhDong . "\n- Thời gian: " . $thoiGian . "\n- IP: " . $ipAddr;
                ?>
                    <tr class="log-row" style="cursor: pointer;"
                        data-id="<?= $logId ?>"
                        data-nv="<?= htmlspecialchars($nvTen) ?>"
                        data-action="<?= htmlspecialchars($hanhDong) ?>"
                        data-time="<?= $thoiGian ?>"
                        data-ip="<?= htmlspecialchars($ipAddr) ?>"
                        data-detail="<?= htmlspecialchars($chiTiet) ?>">
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td class="fw-medium">
                            <i class="fa-solid fa-user-gear text-secondary me-1"></i>
                            <?= htmlspecialchars($nvTen) ?>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Log #<?= $logId ?></span>
                            <?= htmlspecialchars($hanhDong) ?>
                        </td>
                        <td class="text-muted" style="font-size:13px"><?= $thoiGian ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 view-log-btn" style="font-size: 0.8rem;">
                                <i class="fa-solid fa-eye me-1"></i> Chi tiết
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted" style="font-size:13px">
        Hiển thị <strong><?= count($logs) ?></strong> hoạt động gần nhất (tối đa 200)
    </div>
</div>

<!-- Modal Xem Chi Tiết Nhật Ký Hoạt Động -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white" style="background: linear-gradient(135deg, #0066CC, #00AAFF) !important;">
                <h5 class="modal-title fw-bold" id="logDetailTitle">
                    <i class="fa-solid fa-file-lines me-2"></i> Chi Tiết Nhật Ký Hoạt Động #<span id="logModalId"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <small class="text-muted d-block uppercase fs-7 fw-bold mb-1"><i class="fa-solid fa-user-gear text-primary me-1"></i> NHÂN VIÊN THỰC HIỆN</small>
                            <span class="fw-bold text-dark fs-6" id="logModalNv"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <small class="text-muted d-block uppercase fs-7 fw-bold mb-1"><i class="fa-regular fa-clock text-primary me-1"></i> THỜI GIAN GHI NHẬN</small>
                            <span class="fw-bold text-dark fs-6" id="logModalTime"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <small class="text-muted d-block uppercase fs-7 fw-bold mb-1"><i class="fa-solid fa-network-wired text-primary me-1"></i> ĐỊA CHỈ IP</small>
                            <span class="font-monospace fw-bold text-secondary fs-6" id="logModalIp"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-primary-subtle border border-primary-subtle rounded-3">
                            <small class="text-primary d-block uppercase fs-7 fw-bold mb-1"><i class="fa-solid fa-bolt me-1"></i> HÀNH ĐỘNG THỰC HIỆN</small>
                            <div class="fw-bold text-dark fs-6 text-break" id="logModalAction" style="line-height: 1.5; white-space: normal;"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-code me-1 text-primary"></i> Nội dung chi tiết thao tác:
                    </label>
                    <div class="p-3 bg-dark text-light rounded-3 font-monospace" style="font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;" id="logModalDetail"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logModalEl = document.getElementById('logDetailModal');
    if (!logModalEl) return;

    const bsModal = new bootstrap.Modal(logModalEl);

    document.querySelectorAll('.log-row').forEach(row => {
        row.addEventListener('click', function(e) {
            const id = this.getAttribute('data-id');
            const nv = this.getAttribute('data-nv');
            const action = this.getAttribute('data-action');
            const time = this.getAttribute('data-time');
            const ip = this.getAttribute('data-ip');
            const detail = this.getAttribute('data-detail');

            document.getElementById('logModalId').textContent = id;
            document.getElementById('logModalNv').textContent = nv;
            document.getElementById('logModalAction').textContent = action;
            document.getElementById('logModalTime').textContent = time;
            document.getElementById('logModalIp').textContent = ip;
            document.getElementById('logModalDetail').textContent = detail;

            bsModal.show();
        });
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
