<?php
/**
 * post_edit.php — Giao diện Soạn thảo / Thêm mới / Sửa bài viết (Chuẩn WordPress)
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// RBAC Check
$currentRole = $_SESSION['admin_user']['ten_vai_tro'] ?? 'nhan_vien';
if (!in_array($currentRole, ['quan_tri_vien', 'superadmin', 'bien_tap_vien', 'admin'])) {
    die('<div class="alert alert-danger m-4">Bạn không có quyền truy cập trang soạn thảo bài viết.</div>');
}

$id = (int)($_GET['id'] ?? 0);
$post = [
    'id'                   => 0,
    'tieu_de'              => '',
    'slug'                 => '',
    'danh_muc_bai_viet_id' => 0,
    'tom_tat'              => '',
    'noi_dung'             => '',
    'anh_bia'              => '',
    'trang_thai'           => 'nhap',
    'tu_khoa_seo'          => '',
    'the_tags'             => '',
];

$msg = '';
$msgType = 'success';

// Lấy dữ liệu nếu đang sửa bài viết
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM bai_viet WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $post = array_merge($post, $existing);
    }
}

// ── Xử lý POST (Thêm / Cập nhật bài viết) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieuDe      = trim($_POST['tieu_de'] ?? '');
    $catId       = (int)($_POST['danh_muc_bai_viet_id'] ?? 0);
    $tomTat      = trim($_POST['tom_tat'] ?? '');
    $noiDung     = trim($_POST['noi_dung'] ?? '');
    $anhBia      = trim($_POST['anh_bia'] ?? '');
    $trangThai   = $_POST['trang_thai'] ?? 'nhap';
    $tuKhoaSeo   = trim($_POST['tu_khoa_seo'] ?? '');
    $theTags     = trim($_POST['the_tags'] ?? '');
    $slug        = createSlug(trim($_POST['slug'] ?? '') ?: $tieuDe);

    if (!$tieuDe) {
        $msg = 'Vui lòng nhập tiêu đề bài viết!';
        $msgType = 'danger';
    } else {
        try {
            $tacGiaId = $_SESSION['admin_user']['nhan_vien_id'] ?? 1;

            if ($id > 0) {
                $sql = "UPDATE bai_viet 
                           SET tieu_de = :tieu_de, slug = :slug, danh_muc_bai_viet_id = :cat_id, 
                               tom_tat = :tom_tat, noi_dung = :noi_dung, anh_bia = :anh_bia, 
                               trang_thai = :trang_thai, ngay_xuat_ban = NOW()
                         WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':tieu_de'    => $tieuDe,
                    ':slug'       => $slug,
                    ':cat_id'     => $catId ?: null,
                    ':tom_tat'    => $tomTat,
                    ':noi_dung'   => $noiDung,
                    ':anh_bia'    => $anhBia,
                    ':trang_thai' => $trangThai,
                    ':id'         => $id,
                ]);
                logActivity($pdo, "Cập nhật bài viết: $tieuDe");
                $msg = "✅ Đã lưu bài viết thành công!";
            } else {
                $sql = "INSERT INTO bai_viet (tieu_de, slug, danh_muc_bai_viet_id, tom_tat, noi_dung, anh_bia, tac_gia_id, trang_thai, ngay_xuat_ban)
                        VALUES (:tieu_de, :slug, :cat_id, :tom_tat, :noi_dung, :anh_bia, :tac_gia_id, :trang_thai, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':tieu_de'    => $tieuDe,
                    ':slug'       => $slug,
                    ':cat_id'     => $catId ?: null,
                    ':tom_tat'    => $tomTat,
                    ':noi_dung'   => $noiDung,
                    ':anh_bia'    => $anhBia,
                    ':tac_gia_id' => $tacGiaId,
                    ':trang_thai' => $trangThai,
                ]);
                $id = $pdo->lastInsertId();
                logActivity($pdo, "Tạo bài viết mới: $tieuDe");
                header("Location: post_edit.php?id=$id&saved=1");
                exit;
            }
        } catch (PDOException $e) {
            $msg = "Lỗi CSDL: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

if (isset($_GET['saved'])) {
    $msg = "✅ Đã tạo bài viết thành công!";
}

// Lấy danh sách chuyên mục
$categories = $pdo->query("SELECT * FROM danh_muc_bai_viet ORDER BY id ASC")->fetchAll();
if (empty($categories)) {
    // Tạo 1 số chuyên mục mẫu nếu chưa có
    $pdo->exec("INSERT INTO danh_muc_bai_viet (ten, slug) VALUES 
        ('Dịch vụ số', 'dich-vu-so'),
        ('Chưa được phân loại', 'chua-duoc-phan-loai'),
        ('Giải pháp doanh nghiệp', 'giai-phap-doanh-nghiep'),
        ('Hạ tầng Cloud & Server', 'ha-tang-cloud-server'),
        ('Thông cáo báo chí', 'thong-cao-bao-chi'),
        ('Tin tức công nghệ', 'tin-tuc-cong-nghe')
    ");
    $categories = $pdo->query("SELECT * FROM danh_muc_bai_viet ORDER BY id ASC")->fetchAll();
}

$pageTitle  = $id > 0 ? 'Sửa bài viết' : 'Thêm bài viết mới';
$activeMenu = 'posts';
require_once __DIR__ . '/header.php';
?>

<style>
/* CSS Tùy chỉnh giao diện Đăng bài chuẩn WordPress */
.wp-editor-wrap {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
#editorVisual {
    position: relative;
}
#editorVisual:empty:before,
#editorVisual[data-empty="true"]:before {
    content: attr(data-placeholder);
    color: #64748b;
    opacity: 0.65;
    position: absolute;
    top: 15px;
    left: 15px;
    pointer-events: none;
    font-size: 14px;
}
.wp-title-input {
    font-size: 20px;
    font-weight: 500;
    padding: 12px 15px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    width: 100%;
    margin-bottom: 15px;
}
.wp-title-input:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1;
    outline: none;
}
.wp-toolbar {
    background: #f0f0f1;
    border-bottom: 1px solid #c3c4c7;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.wp-btn-tool {
    border: 1px solid #c3c4c7;
    background: #fff;
    border-radius: 3px;
    padding: 3px 8px;
    font-size: 13px;
    color: #2c3338;
    cursor: pointer;
}
.wp-btn-tool:hover {
    background: #f6f7f7;
    border-color: #8c8f94;
}
.wp-btn-media {
    border: 1px solid #2271b1;
    color: #2271b1;
    background: #fff;
    border-radius: 3px;
    padding: 4px 10px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    margin-bottom: 8px;
}
.wp-btn-media:hover {
    background: #f0f6fc;
}
.wp-sidebar-box {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-bottom: 20px;
}
.wp-sidebar-header {
    padding: 10px 15px;
    border-bottom: 1px solid #c3c4c7;
    font-weight: 600;
    font-size: 14px;
    color: #1d2327;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
.wp-sidebar-body {
    padding: 12px 15px;
    font-size: 13px;
}
.wp-cat-list {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #dcdcde;
    padding: 8px 12px;
    border-radius: 3px;
    background: #fcfcfc;
}
.wp-cat-item {
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.yoast-panel {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-top: 25px;
}
.yoast-header {
    background: #f6f7f7;
    border-bottom: 1px solid #c3c4c7;
    padding: 10px 15px;
    font-weight: 600;
}
.yoast-tabs {
    display: flex;
    border-bottom: 1px solid #c3c4c7;
    background: #f0f0f1;
}
.yoast-tab {
    padding: 8px 16px;
    font-size: 13px;
    cursor: pointer;
    border-right: 1px solid #c3c4c7;
    color: #50575e;
}
.yoast-tab.active {
    background: #fff;
    color: #1d2327;
    font-weight: 600;
    border-bottom: 1px solid #fff;
    margin-bottom: -1px;
}
.google-preview-box {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
}
.google-preview-title { color: #1a0dab; font-size: 18px; font-weight: 500; cursor: pointer; text-decoration: underline; }
.google-preview-url { color: #006621; font-size: 13px; margin: 2px 0 4px; }
.google-preview-desc { color: #545454; font-size: 13px; line-height: 1.4; }
</style>

<form method="POST" id="postForm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold m-0"><?= $id > 0 ? 'Sửa bài viết' : 'Thêm bài viết mới' ?></h4>
        <div class="d-flex gap-2">
            <?php if ($id > 0 && !empty($post['slug'])): ?>
                <a href="../frontend/index.php#page=<?= urlencode($post['slug']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Xem bài viết trên Website
                </a>
            <?php endif; ?>
            <a href="posts.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Trở về danh sách</a>
        </div>
    </div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
        <?= $msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- CỘT TRÁI (CHÍNH): Tiêu đề, Soạn thảo nội dung, Yoast SEO -->
        <div class="col-lg-8">
            <!-- 1. Tiêu đề -->
            <input type="text" name="tieu_de" class="wp-title-input" placeholder="Thêm tiêu đề" value="<?= htmlspecialchars($post['tieu_de']) ?>" required onblur="onTitleLeave()" onchange="onTitleLeave()">

            <!-- Slug -->
            <div class="mb-3 d-flex align-items-center gap-2" style="font-size: 13px; color: #64748b;">
                <strong>Liên kết tĩnh:</strong>
                <span>http://localhost:8080/website_vnpt/#page=thong-cao-bao-chi/</span>
                <input type="text" name="slug" class="form-control form-control-sm" style="width: 220px;" value="<?= htmlspecialchars($post['slug']) ?>" placeholder="tudong-tao-slug">
            </div>

            <!-- 6. Nút Thêm Media -->
            <div>
                <button type="button" class="wp-btn-media" onclick="openMediaModal('editor')">
                    <i class="fa-solid fa-photo-film me-1"></i> Thêm Media
                </button>
            </div>

            <!-- 5. Khung Soạn thảo (Editor) -->
            <div class="wp-editor-wrap mb-4">
                <div class="wp-toolbar">
                    <select class="form-select form-select-sm" style="width:110px; display:inline-block;" onchange="execCmd('formatBlock', this.value)">
                        <option value="p">Đoạn</option>
                        <option value="h1">Tiêu đề 1</option>
                        <option value="h2">Tiêu đề 2</option>
                        <option value="h3">Tiêu đề 3</option>
                    </select>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('bold')" title="Đậm (Ctrl+B)"><b>B</b></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('italic')" title="Nghiêng (Ctrl+I)"><i>I</i></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('insertUnorderedList')" title="Danh sách không thứ tự"><i class="fa-solid fa-list-ul"></i></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('insertOrderedList')" title="Danh sách có thứ tự"><i class="fa-solid fa-list-ol"></i></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('formatBlock', 'blockquote')" title="Trích dẫn"><i class="fa-solid fa-quote-left"></i></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('justifyLeft')" title="Căn trái"><i class="fa-solid fa-align-left"></i></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('justifyCenter')" title="Căn giữa"><i class="fa-solid fa-align-center"></i></button>
                    <button type="button" class="wp-btn-tool" onclick="execCmd('justifyRight')" title="Căn phải"><i class="fa-solid fa-align-right"></i></button>
                    <button type="button" class="wp-btn-tool" onclick="promptLink()" title="Chèn đường dẫn"><i class="fa-solid fa-link"></i></button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border" id="btnVisualMode" onclick="setEditorMode('visual')">Trực quan</button>
                        <button type="button" class="btn btn-sm btn-light border" id="btnTextMode" onclick="setEditorMode('text')">Văn bản</button>
                    </div>
                </div>

                <!-- Editable Content Area -->
                <div id="editorVisual" contenteditable="true" data-placeholder="Nhập nội dung chi tiết bài viết..." style="min-height: 300px; padding: 15px; outline: none; background:#fff; position:relative;"><?= $post['noi_dung'] ?></div>
                <textarea name="noi_dung" id="editorText" class="form-control border-0" placeholder="Nhập nội dung chi tiết bài viết..." style="min-height: 300px; display:none; font-family: monospace; border-radius:0;"><?= htmlspecialchars($post['noi_dung']) ?></textarea>
            </div>

            <!-- Tóm tắt bài viết -->
            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size: 14px;">Tóm tắt ngắn (Excerpt)</label>
                <textarea name="tom_tat" class="form-control" rows="3" placeholder="Mô tả ngắn gọn về nội dung bài viết..."><?= htmlspecialchars($post['tom_tat']) ?></textarea>
            </div>

            <!-- 7. Yoast SEO Box -->
            <div class="yoast-panel mb-4">
                <div class="yoast-header"><i class="fa-solid fa-search text-warning me-2"></i> Yoast SEO</div>
                <div class="yoast-tabs">
                    <div class="yoast-tab active" onclick="switchYoastTab('seo', this)"><i class="fa-solid fa-circle text-secondary me-1"></i> SEO</div>
                    <div class="yoast-tab" onclick="switchYoastTab('readability', this)"><i class="fa-solid fa-circle text-secondary me-1"></i> Tính dễ đọc</div>
                    <div class="yoast-tab" onclick="switchYoastTab('schema', this)"><i class="fa-solid fa-sitemap me-1"></i> Sơ đồ</div>
                    <div class="yoast-tab" onclick="switchYoastTab('social', this)"><i class="fa-solid fa-share-nodes me-1"></i> Mạng xã hội</div>
                </div>

                <!-- TAB 1: SEO -->
                <div id="yoastTabSEO" class="p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cụm từ khóa chính</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="tu_khoa_seo" id="tuKhoaSeo" class="form-control" placeholder="Nhập từ khóa SEO chính..." value="<?= htmlspecialchars($post['tu_khoa_seo']) ?>" oninput="updateGooglePreview()">
                            <button type="button" class="btn btn-outline-secondary">Nhận các cụm từ khóa liên quan</button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Google xem trước:</label>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="previewMode" id="prevMobile" checked onchange="updateGooglePreview()">
                                <label class="form-check-label small" for="prevMobile">Kết quả trên di động</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="previewMode" id="prevDesktop" onchange="updateGooglePreview()">
                                <label class="form-check-label small" for="prevDesktop">Kết quả trên máy tính</label>
                            </div>
                        </div>
                        <div class="google-preview-box">
                            <div class="google-preview-url" id="prevUrlDisplay">vnpt.vn › tin-tuc › <?= htmlspecialchars(!empty($post['slug']) ? $post['slug'] : 'bai-viet-moi') ?></div>
                            <a href="../frontend/index.php#page=<?= urlencode(!empty($post['slug']) ? $post['slug'] : 'bai-viet-moi') ?>" target="_blank" class="google-preview-title d-block" id="prevTitleDisplay" style="text-decoration: underline; cursor: pointer;">
                                <?= htmlspecialchars(!empty($post['tieu_de']) ? $post['tieu_de'] . ' - VNPT Nền tảng dịch vụ số' : 'Tiêu đề bài viết - VNPT Nền tảng dịch vụ số') ?>
                            </a>
                            <div class="google-preview-desc" id="prevDescDisplay"><?= htmlspecialchars(!empty($post['tom_tat']) ? $post['tom_tat'] : 'Cung cấp thông tin chi tiết về các dịch vụ số, hạ tầng CNTT, giải pháp chuyển đổi số toàn diện cho doanh nghiệp.') ?></div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: TÍNH DỄ ĐỌC -->
                <div id="yoastTabReadability" class="p-3" style="display:none;">
                    <h6 class="fw-bold small text-muted mb-3"><i class="fa-solid fa-glasses me-1"></i> Phân tích tính dễ đọc (Readability Analysis)</h6>
                    <div style="font-size:13px; display:flex; flex-direction:column; gap:10px;">
                        <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-circle text-success" style="font-size:10px;"></i> <span><strong>Độ dài câu:</strong> Bài viết có câu từ mạch lạc, câu dài và ngắn xen kẽ thích hợp.</span></div>
                        <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-circle text-success" style="font-size:10px;"></i> <span><strong>Đoạn văn:</strong> Mỗi đoạn văn ngắn gọn, dễ tiếp thu khi xem trên màn hình di động.</span></div>
                        <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-circle text-success" style="font-size:10px;"></i> <span><strong>Từ chuyển tiếp:</strong> Sử dụng tốt các từ nối giúp người đọc nắm bắt ý chính tốt hơn.</span></div>
                        <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-circle text-success" style="font-size:10px;"></i> <span><strong>Thể chủ động:</strong> Sử dụng nhiều câu chủ động tạo cảm giác trực diện.</span></div>
                    </div>
                </div>

                <!-- TAB 3: SƠ ĐỒ (SCHEMA) -->
                <div id="yoastTabSchema" class="p-3" style="display:none;">
                    <h6 class="fw-bold small text-muted mb-3"><i class="fa-solid fa-code me-1"></i> Cấu hình Schema.org (Structured Data)</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Loại trang (Page Type)</label>
                        <select class="form-select form-select-sm">
                            <option value="ItemPage">Trang nội dung (Item Page)</option>
                            <option value="AboutPage">Trang giới thiệu (About Page)</option>
                            <option value="ContactPage">Trang liên hệ (Contact Page)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Loại bài viết (Article Type)</label>
                        <select class="form-select form-select-sm">
                            <option value="BlogPosting" selected>Bài viết Blog (Blog Posting)</option>
                            <option value="NewsArticle">Thông cáo báo chí (News Article)</option>
                            <option value="TechArticle">Bài viết hướng dẫn kỹ thuật (Tech Article)</option>
                        </select>
                    </div>
                </div>

                <!-- TAB 4: MẠNG XÃ HỘI (SOCIAL) -->
                <div id="yoastTabSocial" class="p-3" style="display:none;">
                    <h6 class="fw-bold small text-muted mb-3"><i class="fa-solid fa-share-nodes me-1"></i> Xem trước chia sẻ Mạng xã hội (Facebook & X)</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tiêu đề chia sẻ (Social Title)</label>
                        <input type="text" class="form-control form-control-sm" id="socialTitleInput" placeholder="Mặc định lấy từ tiêu đề bài viết...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mô tả chia sẻ (Social Description)</label>
                        <textarea class="form-control form-control-sm" rows="2" id="socialDescInput" placeholder="Mặc định lấy từ tóm tắt bài viết..."></textarea>
                    </div>
                    <div class="border rounded p-3 bg-light">
                        <small class="text-muted fw-bold d-block mb-1">XEM TRƯỚC FACEBOOK CARD:</small>
                        <div class="border rounded bg-white overflow-hidden shadow-sm">
                            <div class="p-2 bg-secondary text-white text-center small"><i class="fa-solid fa-image me-1"></i> [Ảnh đại diện bài viết]</div>
                            <div class="p-2">
                                <div class="text-uppercase text-muted" style="font-size:11px;">VNPT.VN</div>
                                <div class="fw-bold text-dark small" id="fbPreviewTitle">Tiêu đề bài viết</div>
                                <div class="text-muted small text-truncate" id="fbPreviewDesc">Tóm tắt mô tả bài viết...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI (SIDEBAR): Khung Đăng, Chuyên mục, Thẻ, Ảnh đại diện -->
        <div class="col-lg-4">
            <!-- 8. Khung ĐĂNG (Publish Box) -->
            <div class="wp-sidebar-box">
                <div class="wp-sidebar-header" style="cursor:pointer;" onclick="toggleSidebarBox(this, event)">
                    <span>Đăng</span>
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                <div class="wp-sidebar-body">
                    <div class="d-flex justify-content-between mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="saveDraft()">Lưu nháp</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="previewPost()">Xem thử</button>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-location-pin me-2 text-muted"></i>
                        <span>Trạng thái:</span>
                        <select name="trang_thai" class="form-select form-select-sm d-inline-block w-auto ms-1">
                            <option value="nhap" <?= $post['trang_thai']==='nhap'?'selected':'' ?>>Bản nháp</option>
                            <option value="da_dang" <?= $post['trang_thai']==='da_dang'?'selected':'' ?>>Đã đăng</option>
                            <option value="an" <?= $post['trang_thai']==='an'?'selected':'' ?>>Đã ẩn</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-eye me-2 text-muted"></i>
                        <span>Hiển thị:</span> <strong>Công khai</strong> <a href="#" class="small text-decoration-none ms-1">Chỉnh sửa</a>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-calendar-days me-2 text-muted"></i>
                        <span>Đăng ngay lập tức</span> <a href="#" class="small text-decoration-none ms-1">Chỉnh sửa</a>
                    </div>
                    <div class="mb-2 text-muted" style="font-size:12px;">
                        <div><i class="fa-solid fa-pen-nib me-1"></i> Tính dễ đọc: <strong>Không có sẵn</strong></div>
                        <div><i class="fa-solid fa-chart-line me-1"></i> SEO: <strong>Chưa phân tích</strong></div>
                    </div>
                    <div class="pt-3 border-top mt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-paper-plane me-1"></i> Đăng
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Khung CHUYÊN MỤC (Categories) -->
            <div class="wp-sidebar-box">
                <div class="wp-sidebar-header" style="cursor:pointer;" onclick="toggleSidebarBox(this, event)">
                    <span>Chuyên mục</span>
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                <div class="wp-sidebar-body">
                    <ul class="nav nav-tabs nav-fill mb-2" style="font-size:12px;">
                        <li class="nav-item">
                            <a class="nav-link active py-1" id="tabCatAll" href="javascript:void(0)" onclick="switchCatTab('all', this)" style="cursor:pointer;">Tất cả chuyên mục</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 text-muted" id="tabCatPopular" href="javascript:void(0)" onclick="switchCatTab('popular', this)" style="cursor:pointer;">Dùng nhiều nhất</a>
                        </li>
                    </ul>
                    
                    <!-- Danh sách tất cả chuyên mục -->
                    <div class="wp-cat-list" id="catListAll">
                        <?php foreach ($categories as $cat): ?>
                        <div class="wp-cat-item">
                            <input class="form-check-input mt-0" type="radio" name="danh_muc_bai_viet_id" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>" <?= $post['danh_muc_bai_viet_id'] == $cat['id'] ? 'checked' : '' ?> onchange="syncCategorySelection(this.value)">
                            <label for="cat_<?= $cat['id'] ?>" class="form-check-label text-truncate"><?= htmlspecialchars($cat['ten']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Danh sách chuyên mục dùng nhiều nhất -->
                    <div class="wp-cat-list" id="catListPopular" style="display:none;">
                        <?php 
                        $popularCats = array_slice($categories, 0, 4);
                        foreach ($popularCats as $cat): 
                        ?>
                        <div class="wp-cat-item">
                            <input class="form-check-input mt-0" type="radio" name="danh_muc_bai_viet_id_pop" value="<?= $cat['id'] ?>" id="cat_pop_<?= $cat['id'] ?>" <?= $post['danh_muc_bai_viet_id'] == $cat['id'] ? 'checked' : '' ?> onchange="syncCategorySelection(this.value)">
                            <label for="cat_pop_<?= $cat['id'] ?>" class="form-check-label text-truncate"><?= htmlspecialchars($cat['ten']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <a href="javascript:void(0)" onclick="addNewCategory()" class="d-inline-block mt-2 small text-decoration-none">+ Thêm chuyên mục</a>
                </div>
            </div>

            <!-- 3. Khung THẺ (Tags) -->
            <div class="wp-sidebar-box">
                <div class="wp-sidebar-header" style="cursor:pointer;" onclick="toggleSidebarBox(this, event)">
                    <span>Thẻ (Tags)</span>
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                <div class="wp-sidebar-body">
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" id="tagInput" class="form-control" name="the_tags" placeholder="Nhập thẻ phân cách bởi dấu phẩy" value="<?= htmlspecialchars($post['the_tags']) ?>">
                        <button class="btn btn-outline-secondary" type="button" onclick="addTagFromInput()">Thêm</button>
                    </div>
                    <small class="text-muted d-block" style="font-size:11px;">Phân cách các thẻ bằng dấu phẩy (,)</small>
                </div>
            </div>

            <!-- 4. Khung Ảnh ĐẠI DIỆN (Featured Image) -->
            <div class="wp-sidebar-box">
                <div class="wp-sidebar-header" style="cursor:pointer;" onclick="toggleSidebarBox(this, event)">
                    <span>Ảnh đại diện</span>
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                <div class="wp-sidebar-body text-center">
                    <div id="imgPreviewWrap" class="mb-2" style="<?= empty($post['anh_bia']) ? 'display:none;' : '' ?>">
                        <img id="imgPreview" src="<?= htmlspecialchars($post['anh_bia']) ?>" alt="Preview" style="max-width: 100%; max-height: 180px; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                    <input type="hidden" name="anh_bia" id="anhBiaInput" value="<?= htmlspecialchars($post['anh_bia']) ?>">
                    <a href="javascript:void(0)" onclick="openMediaModal('featured')" class="small text-decoration-none fw-semibold">
                        <i class="fa-solid fa-image me-1"></i> Đặt ảnh đại diện
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Thêm Media / Chọn Ảnh từ máy tính & URL -->
<div class="modal fade" id="modalMedia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-photo-film text-primary me-2"></i>Thêm Media / Chọn Ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- 1. Tải ảnh từ máy tính -->
                <div class="mb-3 p-3 bg-light border rounded">
                    <label class="form-label fw-semibold text-dark mb-1"><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Tải ảnh lên từ máy tính của bạn</label>
                    <input type="file" id="mediaFileInput" class="form-control" accept="image/*" onchange="uploadFileFromMachine(this)">
                    <div id="uploadStatusText" class="small mt-1 text-muted">Chấp nhận định dạng: JPG, PNG, WEBP, GIF, SVG.</div>
                </div>

                <div class="text-center text-muted small my-2 fw-semibold">— HOẶC NHẬP ĐƯỜNG DẪN URL —</div>

                <!-- 2. Nhập URL -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Đường dẫn ảnh (URL)</label>
                    <input type="text" id="mediaUrlInput" class="form-control" placeholder="https://domain.com/image.jpg hoặc assets/images/..." oninput="previewModalImage(this.value)">
                </div>

                <!-- Preview ảnh -->
                <div id="modalImagePreviewWrap" class="text-center p-2 border rounded bg-white" style="display:none;">
                    <div class="small text-muted mb-1">Xem trước ảnh đã chọn:</div>
                    <img id="modalImagePreview" src="" alt="Selected Preview" style="max-height: 160px; max-width: 100%; border-radius: 4px; border:1px solid #e2e8f0;">
                </div>

                <div class="mt-3">
                    <div class="text-muted small mb-1">Hoặc chọn nhanh ảnh mẫu sẵn có:</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectQuickSample('assets/images/img09.jpg')">img09.jpg</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectQuickSample('assets/images/img10.jpg')">img10.jpg</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectQuickSample('assets/images/img05.png')">img05.png</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="confirmSelectMedia()"><i class="fa-solid fa-check me-1"></i> Sử dụng ảnh này</button>
            </div>
        </div>
    </div>
</div>

<script>
function execCmd(cmd, value = null) {
    document.execCommand(cmd, false, value);
    document.getElementById('editorVisual').focus();
    syncEditorContent();
}

function promptLink() {
    const url = prompt('Nhập URL đường dẫn:');
    if (url) execCmd('createLink', url);
}

function setEditorMode(mode) {
    const vis = document.getElementById('editorVisual');
    const txt = document.getElementById('editorText');
    if (mode === 'text') {
        txt.value = vis.innerHTML;
        vis.style.display = 'none';
        txt.style.display = 'block';
    } else {
        vis.innerHTML = txt.value;
        txt.style.display = 'none';
        vis.style.display = 'block';
    }
}

function syncEditorContent() {
    const vis = document.getElementById('editorVisual');
    const txt = document.getElementById('editorText');
    if (vis.style.display !== 'none') {
        txt.value = vis.innerHTML;
    }
}

function previewPost() {
    syncEditorContent();
    const slugInput = document.querySelector('input[name="slug"]');
    const slug = slugInput ? slugInput.value.trim() : '';
    if (slug) {
        window.open('../frontend/index.php#page=' + encodeURIComponent(slug), '_blank');
    } else {
        alert('Vui lòng nhập tiêu đề bài viết trước khi xem thử!');
    }
}

function saveDraft() {
    const statusSelect = document.querySelector('select[name="trang_thai"]');
    if (statusSelect) statusSelect.value = 'nhap';
    document.getElementById('postForm').submit();
}

document.getElementById('postForm').addEventListener('submit', function() {
    syncEditorContent();
});

let currentMediaTarget = 'editor';

function openMediaModal(target = 'editor') {
    currentMediaTarget = target;
    document.getElementById('mediaFileInput').value = '';
    document.getElementById('uploadStatusText').innerHTML = 'Chấp nhận định dạng: JPG, PNG, WEBP, GIF, SVG.';
    document.getElementById('uploadStatusText').className = 'small mt-1 text-muted';
    new bootstrap.Modal(document.getElementById('modalMedia')).show();
}

function selectQuickSample(url) {
    document.getElementById('mediaUrlInput').value = url;
    previewModalImage(url);
}

function previewModalImage(url) {
    const wrap = document.getElementById('modalImagePreviewWrap');
    const img = document.getElementById('modalImagePreview');
    if (url && url.trim() !== '') {
        img.src = url;
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

async function uploadFileFromMachine(input) {
    if (!input.files || !input.files[0]) return;

    const statusEl = document.getElementById('uploadStatusText');
    statusEl.className = 'small mt-1 text-primary fw-semibold';
    statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải file lên máy chủ...';

    const formData = new FormData();
    formData.append('file', input.files[0]);

    try {
        const response = await fetch('upload_media.php', {
            method: 'POST',
            body: formData
        });
        const res = await response.json();

        if (res.status === 'success') {
            statusEl.className = 'small mt-1 text-success fw-semibold';
            statusEl.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Đã tải ảnh lên thành công!';
            document.getElementById('mediaUrlInput').value = res.url;
            previewModalImage(res.url);
        } else {
            statusEl.className = 'small mt-1 text-danger fw-semibold';
            statusEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Lỗi: ' + (res.message || 'Không thể tải ảnh');
        }
    } catch (err) {
        statusEl.className = 'small mt-1 text-danger fw-semibold';
        statusEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Lỗi kết nối máy chủ';
    }
}

function confirmSelectMedia() {
    const url = document.getElementById('mediaUrlInput').value.trim();
    if (!url) {
        alert('Vui lòng chọn file từ máy hoặc nhập đường dẫn ảnh!');
        return;
    }

    if (currentMediaTarget === 'featured') {
        document.getElementById('anhBiaInput').value = url;
        document.getElementById('imgPreview').src = url;
        document.getElementById('imgPreviewWrap').style.display = 'block';
    } else {
        // Chèn vào WYSIWYG Editor
        const imgHtml = `<p><img src="${url}" alt="Hình ảnh bài viết" style="max-width:100%; height:auto; border-radius:6px; margin: 12px 0; display:block;"></p><p><br></p>`;
        document.execCommand('insertHTML', false, imgHtml);
        const vis = document.getElementById('editorVisual');
        if (vis) vis.focus();
        syncEditorContent();
        if (typeof updateEditorPlaceholder === 'function') updateEditorPlaceholder();
    }

    const modalEl = document.getElementById('modalMedia');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();
}

function createSlugJS(str) {
    if (!str) return '';
    str = str.toLowerCase();
    str = str.replace(/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/g, 'a');
    str = str.replace(/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/g, 'e');
    str = str.replace(/(ì|í|ị|ỉ|ĩ)/g, 'i');
    str = str.replace(/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/g, 'o');
    str = str.replace(/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/g, 'u');
    str = str.replace(/(ỳ|ý|ỵ|ỷ|ỹ)/g, 'y');
    str = str.replace(/(đ)/g, 'd');
    str = str.replace(/[^a-z0-9\s-]/g, '');
    str = str.replace(/\s+/g, '-');
    str = str.replace(/-+/g, '-');
    return str.trim();
}

function switchYoastTab(tab, el) {
    document.querySelectorAll('.yoast-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    const tSeo = document.getElementById('yoastTabSEO');
    const tRead = document.getElementById('yoastTabReadability');
    const tSchema = document.getElementById('yoastTabSchema');
    const tSocial = document.getElementById('yoastTabSocial');

    if (tSeo) tSeo.style.display = (tab === 'seo') ? 'block' : 'none';
    if (tRead) tRead.style.display = (tab === 'readability') ? 'block' : 'none';
    if (tSchema) tSchema.style.display = (tab === 'schema') ? 'block' : 'none';
    if (tSocial) tSocial.style.display = (tab === 'social') ? 'block' : 'none';
}

function updateGooglePreview() {
    const titleInput = document.querySelector('input[name="tieu_de"]');
    const descInput = document.querySelector('textarea[name="tom_tat"]');
    const slugInput = document.querySelector('input[name="slug"]');

    const title = titleInput ? titleInput.value.trim() : '';
    const desc = descInput ? descInput.value.trim() : '';
    let slug = slugInput ? slugInput.value.trim() : '';

    if (title && !slug) {
        slug = createSlugJS(title);
        if (slugInput) slugInput.value = slug;
    }

    const activeSlug = slug || 'internet-can-nhanh-o-moi-goc-nha';

    const titleEl = document.getElementById('prevTitleDisplay');
    const descEl = document.getElementById('prevDescDisplay');
    const urlEl = document.getElementById('prevUrlDisplay');

    if (titleEl) {
        titleEl.innerText = title ? (title + ' - VNPT Nền tảng dịch vụ số') : 'Tiêu đề bài viết - VNPT Nền tảng dịch vụ số';
        titleEl.href = '../frontend/index.php#page=' + encodeURIComponent(activeSlug);
    }
    if (descEl) {
        descEl.innerText = desc || 'Cung cấp thông tin chi tiết về các dịch vụ số, hạ tầng CNTT, giải pháp chuyển đổi số toàn diện cho doanh nghiệp.';
    }
    if (urlEl) {
        urlEl.innerText = 'vnpt.vn › tin-tuc › ' + activeSlug;
    }
}

function onTitleLeave() {
    const titleInput = document.querySelector('input[name="tieu_de"]');
    const slugInput = document.querySelector('input[name="slug"]');
    const title = titleInput ? titleInput.value.trim() : '';
    
    if (title) {
        const autoSlug = createSlugJS(title);
        if (slugInput) {
            slugInput.value = autoSlug;
        }
    }
    updateGooglePreview();
}
function toggleSidebarBox(headerEl, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    if (window.getSelection) {
        window.getSelection().removeAllRanges();
    }
    const box = headerEl.closest('.wp-sidebar-box');
    if (!box) return;
    const body = box.querySelector('.wp-sidebar-body');
    const icon = headerEl.querySelector('i.fa-solid');
    
    if (body) {
        if (body.style.display === 'none') {
            body.style.display = 'block';
            if (icon) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        } else {
            body.style.display = 'none';
            if (icon) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }
}

function switchCatTab(tab, el) {
    const tabAll = document.getElementById('tabCatAll');
    const tabPop = document.getElementById('tabCatPopular');
    const listAll = document.getElementById('catListAll');
    const listPop = document.getElementById('catListPopular');

    if (tabAll) {
        tabAll.classList.remove('active', 'text-muted');
        tabAll.classList.add(tab === 'all' ? 'active' : 'text-muted');
    }
    if (tabPop) {
        tabPop.classList.remove('active', 'text-muted');
        tabPop.classList.add(tab === 'popular' ? 'active' : 'text-muted');
    }

    if (listAll) listAll.style.display = (tab === 'all') ? 'block' : 'none';
    if (listPop) listPop.style.display = (tab === 'popular') ? 'block' : 'none';
}

function syncCategorySelection(catId) {
    document.querySelectorAll('input[name="danh_muc_bai_viet_id"]').forEach(r => {
        r.checked = (r.value == catId);
    });
    document.querySelectorAll('input[name="danh_muc_bai_viet_id_pop"]').forEach(r => {
        r.checked = (r.value == catId);
    });
}

function addTagFromInput() {
    const tagInput = document.getElementById('tagInput');
    if (tagInput && tagInput.value.trim() !== '') {
        alert('Đã thêm thẻ: ' + tagInput.value);
    } else {
        alert('Vui lòng nhập tên thẻ!');
    }
}

function addNewCategory() {
    const name = prompt('Nhập tên chuyên mục mới muốn thêm vào hệ thống:');
    if (name && name.trim()) {
        const formData = new FormData();
        formData.append('name', name.trim());
        
        fetch('../backend/api/add_category.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('✅ ' + data.message);
                const select = document.querySelector('select[name="danh_muc_bai_viet_id"]');
                if (select) {
                    const opt = document.createElement('option');
                    opt.value = data.data.id;
                    opt.textContent = data.data.ten;
                    opt.selected = true;
                    select.appendChild(opt);
                }
                const catBox = document.getElementById('categoriesBox');
                if (catBox) {
                    const div = document.createElement('div');
                    div.className = 'form-check mb-1';
                    div.innerHTML = `<input class="form-check-input" type="radio" name="danh_muc_bai_viet_id" value="${data.data.id}" id="cat_${data.data.id}" checked><label class="form-check-label" for="cat_${data.data.id}">${data.data.ten}</label>`;
                    catBox.appendChild(div);
                }
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(err => {
            alert('Lỗi kết nối máy chủ!');
        });
    }
}

function updateEditorPlaceholder() {
    const vis = document.getElementById('editorVisual');
    if (!vis) return;
    const txt = vis.innerText.replace(/\s+/g, '');
    if (txt === '' && !vis.querySelector('img')) {
        vis.setAttribute('data-empty', 'true');
    } else {
        vis.removeAttribute('data-empty');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const vis = document.getElementById('editorVisual');
    if (vis) {
        vis.addEventListener('input', updateEditorPlaceholder);
        vis.addEventListener('blur', updateEditorPlaceholder);
        vis.addEventListener('focus', updateEditorPlaceholder);
        vis.addEventListener('keyup', updateEditorPlaceholder);
        updateEditorPlaceholder();
    }

    const titleInput = document.querySelector('input[name="tieu_de"]');
    if (titleInput) {
        ['blur', 'focusout', 'change', 'mouseleave'].forEach(evt => {
            titleInput.addEventListener(evt, onTitleLeave);
        });
    }

    const slugInput = document.querySelector('input[name="slug"]');
    if (slugInput) {
        ['blur', 'change', 'keyup'].forEach(evt => {
            slugInput.addEventListener(evt, updateGooglePreview);
        });
    }

    const descInput = document.querySelector('textarea[name="tom_tat"]');
    if (descInput) {
        ['blur', 'change', 'keyup'].forEach(evt => {
            descInput.addEventListener(evt, updateGooglePreview);
        });
    }

    updateGooglePreview();
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
