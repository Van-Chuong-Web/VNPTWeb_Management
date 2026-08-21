/**
 * backend/api/shape.js
 * Chuyển đổi giữa bản ghi DB và "user object" mà frontend đang dùng
 * ({ id, firstName, lastName, email, phone, role }), cùng metadata
 * hiển thị (icon/color) cho sản phẩm để khớp giao diện cũ.
 */

// ho_ten (1 chuỗi) -> { firstName, lastName }
function splitName(hoTen) {
  const parts = String(hoTen || '').trim().split(/\s+/).filter(Boolean);
  if (parts.length === 0) return { firstName: '', lastName: '' };
  if (parts.length === 1) return { firstName: parts[0], lastName: '' };
  return { firstName: parts[0], lastName: parts.slice(1).join(' ') };
}
function joinName(firstName, lastName) {
  return `${String(firstName || '').trim()} ${String(lastName || '').trim()}`.trim();
}

// Map row từ tai_khoan + khach_hang -> user object
function shapeCustomer(row) {
  const { firstName, lastName } = splitName(row.ho_ten);
  return {
    id: row.khach_hang_id || row.id,
    taiKhoanId: row.tai_khoan_id || row.id,
    firstName,
    lastName,
    ho_ten: row.ho_ten || `${firstName} ${lastName}`.trim(),
    email: row.email || '',
    phone: row.so_dien_thoai || '',
    role: 'customer',
    hinh_anh_url: row.hinh_anh_url || row.avatar || '',
    avatar: row.hinh_anh_url || row.avatar || '',
  };
}

// Map row từ tai_khoan + nhan_vien -> user object (admin)
function shapeAdmin(row) {
  const { firstName, lastName } = splitName(row.ho_ten);
  const roleName = row.ten_vai_tro === 'quan_tri_vien' ? 'admin' : (row.ten_vai_tro || 'admin');
  return {
    id: row.nhan_vien_id || row.id,
    taiKhoanId: row.tai_khoan_id || row.id,
    firstName,
    lastName,
    ho_ten: row.ho_ten || `${firstName} ${lastName}`.trim(),
    email: row.email || '',
    phone: row.so_dien_thoai || '',
    role: roleName,
    hinh_anh_url: row.hinh_anh_url || row.avatar || '',
    avatar: row.hinh_anh_url || row.avatar || '',
  };
}

// Metadata trình bày (icon lucide + màu) cho từng sản phẩm theo ma_san_pham.
const PRODUCT_META = {
  'svc-001': { icon: 'cloud',        color: '#0066CC' },
  'svc-002': { icon: 'shield-check', color: '#FF6B00' },
  'svc-003': { icon: 'cpu',          color: '#00AA55' },
  'svc-004': { icon: 'wifi',         color: '#8800CC' },
  'svc-005': { icon: 'file-text',    color: '#CC3300' },
  'svc-006': { icon: 'video',        color: '#0099AA' },
  'svc-007': { icon: 'database',     color: '#0066CC' },
  'svc-008': { icon: 'radio',        color: '#00AA55' },
  'pkg-basic':    { icon: 'package',   color: '#0099AA' },
  'pkg-business': { icon: 'briefcase', color: '#0066CC' },
  'pkg-premium':  { icon: 'crown',     color: '#FF6B00' },
  'SP001': { icon: 'cloud',        color: '#00AAFF' },
  'SP002': { icon: 'shield-check', color: '#16A34A' },
  'SP003': { icon: 'bot',          color: '#0066CC' },
  'SP004': { icon: 'cloud',        color: '#0066CC' },
  'SP005': { icon: 'layers',       color: '#8800CC' },
  'SP006': { icon: 'building-2',   color: '#0F172A' },
  'SP007': { icon: 'server',       color: '#0F172A' },
  'cloud-doanh-nghiep-sme': { icon: 'cloud', color: '#0066CC' },
  'cloud-enterprise': { icon: 'cloud', color: '#00AAFF' },
  'cloud-high-spec': { icon: 'server', color: '#0F172A' },
  'vnpt-ai-ocr': { icon: 'cpu', color: '#0066CC' },
  'vnpt-soc-security': { icon: 'shield-check', color: '#16A34A' },
};

function shapeProduct(row) {
  const meta = PRODUCT_META[row.ma_san_pham] || { icon: 'box', color: '#0066CC' };
  const price = row.gia_khuyen_mai != null ? Number(row.gia_khuyen_mai) : Number(row.gia_niem_yet);
  return {
    id: row.ma_san_pham,
    dbId: row.id,
    name: row.ten_san_pham,
    price,
    listPrice: Number(row.gia_niem_yet),
    unit: row.don_vi_tinh || 'tháng',
    slug: row.slug,
    category: row.danh_muc_id,
    type: row.loai_san_pham,
    desc: row.mo_ta_ngan || '',
    status: row.trang_thai,
    icon: meta.icon,
    color: meta.color,
  };
}

module.exports = { splitName, joinName, shapeCustomer, shapeAdmin, shapeProduct, PRODUCT_META };
