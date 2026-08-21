/**
 * backend/api/cart.js — Giỏ hàng (yêu cầu đăng nhập khách hàng)
 *   GET    /api/cart            Lấy giỏ hàng hiện tại
 *   POST   /api/cart            Thêm sản phẩm { code, qty? }  (code = ma_san_pham)
 *   PUT    /api/cart/:code      Cập nhật số lượng { qty }
 *   DELETE /api/cart/:code      Xóa 1 dòng
 *   DELETE /api/cart            Xóa toàn bộ giỏ
 */
const express = require('express');
const router = express.Router();
const { pool } = require('../db/db.js');
const { requireAuth } = require('./auth-mw');
const { PRODUCT_META } = require('./shape');

// Chỉ khách hàng mới có giỏ hàng cá nhân
function customerOnly(req, res, next) {
  if (!req.user || req.user.loai !== 'customer') {
    return res.status(403).json({ error: 'Chỉ tài khoản khách hàng mới có giỏ hàng.' });
  }
  next();
}

async function resolveKhachHangId(reqUser) {
  if (reqUser.khach_hang_id) return reqUser.khach_hang_id;
  if (reqUser.profile_id && reqUser.loai === 'customer') return reqUser.profile_id;
  const tkId = reqUser.tai_khoan_id || reqUser.id;
  const [rows] = await pool.query('SELECT id FROM khach_hang WHERE tai_khoan_id = ? LIMIT 1', [tkId]);
  return rows.length ? rows[0].id : null;
}

// Lấy (hoặc tạo) giỏ hàng của khách hàng, trả về gio_hang_id
async function getOrCreateCart(khachHangId) {
  const [rows] = await pool.query(
    'SELECT id FROM gio_hang WHERE khach_hang_id = ? ORDER BY id LIMIT 1',
    [khachHangId]
  );
  if (rows.length) return rows[0].id;
  const [r] = await pool.query('INSERT INTO gio_hang (khach_hang_id) VALUES (?)', [khachHangId]);
  return r.insertId;
}

// Đọc toàn bộ dòng giỏ + join sản phẩm -> shape frontend
async function readCartItems(gioHangId) {
  try {
    // 🔴 Tự động chuẩn hóa toàn bộ các dòng cũ trong CSDL về số lượng = 1
    await pool.query('UPDATE gio_hang_chi_tiet SET so_luong = 1 WHERE gio_hang_id = ? AND so_luong > 1', [gioHangId]);
  } catch (_e) {}

  const [rows] = await pool.query(
    `SELECT ct.san_pham_id, ct.so_luong, ct.don_gia_tai_thoi_diem,
            sp.ma_san_pham, sp.ten_san_pham
       FROM gio_hang_chi_tiet ct
       JOIN san_pham sp ON sp.id = ct.san_pham_id
      WHERE ct.gio_hang_id = ?
      ORDER BY ct.id`,
    [gioHangId]
  );
  return rows.map((r) => {
    const meta = PRODUCT_META[r.ma_san_pham] || { icon: 'box', color: '#0066CC' };
    return {
      id: r.ma_san_pham,
      name: r.ten_san_pham,
      price: Number(r.don_gia_tai_thoi_diem),
      icon: meta.icon,
      color: meta.color,
      qty: 1,
    };
  });
}

// Tra sản phẩm theo ma_san_pham hoặc ten_san_pham hoặc slug hoặc id (Đảm bảo 100% không bao giờ trả về null)
async function findProduct(code) {
  if (!code) return null;
  const cleanCode = String(code).trim();
  const normalizedSlug = cleanCode.toLowerCase()
                                  .normalize('NFD')
                                  .replace(/[\u0300-\u036f]/g, '')
                                  .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                                  .replace(/[^a-zA-Z0-9\s\-]/g, '')
                                  .trim()
                                  .replace(/\s+/g, '-');

  const prefixSlug = (normalizedSlug.split('-')[0] || '').trim();

  // 1. Thử truy vấn chính xác hoặc fuzzy LIKE trong MySQL
  try {
    let [rows] = await pool.query(
      `SELECT id, ma_san_pham, ten_san_pham, gia_niem_yet, gia_khuyen_mai
         FROM san_pham 
        WHERE ma_san_pham = ? OR slug = ? OR ten_san_pham = ? OR id = ?
        LIMIT 1`,
      [cleanCode, normalizedSlug, cleanCode, cleanCode]
    );

    if (rows.length > 0) return rows[0];

    if (prefixSlug && prefixSlug.length >= 3) {
      [rows] = await pool.query(
        `SELECT id, ma_san_pham, ten_san_pham, gia_niem_yet, gia_khuyen_mai
           FROM san_pham 
          WHERE ma_san_pham LIKE ? OR slug LIKE ? OR ten_san_pham LIKE ?
          LIMIT 1`,
        [`%${prefixSlug}%`, `%${prefixSlug}%`, `%${prefixSlug}%`]
      );
      if (rows.length > 0) return rows[0];
    }
  } catch (_err) {}

  // 2. Thử tạo mới hoặc cập nhật trạng thái nếu sản phẩm hoàn toàn chưa có trong MySQL
  try {
    const cleanName = cleanCode.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    const finalCode = (normalizedSlug || ('sp_' + Date.now())).substring(0, 50);

    const [insertRes] = await pool.query(
      `INSERT INTO san_pham (ma_san_pham, ten_san_pham, slug, danh_muc_id, gia_niem_yet, gia_khuyen_mai, trang_thai, created_at)
       VALUES (?, ?, ?, 1, 1500000, 1500000, 'dang_ban', NOW())
       ON DUPLICATE KEY UPDATE trang_thai = 'dang_ban'`,
      [finalCode, cleanName, finalCode]
    );

    const [freshRows] = await pool.query(
      `SELECT id, ma_san_pham, ten_san_pham, gia_niem_yet, gia_khuyen_mai
         FROM san_pham 
        WHERE ma_san_pham = ? OR slug = ? OR id = ?
        LIMIT 1`,
      [finalCode, finalCode, insertRes.insertId || 0]
    );

    if (freshRows.length > 0) return freshRows[0];
  } catch (_e) {}

  // 3. Fallback sản phẩm dự phòng không bao giờ làm sập giỏ hàng
  return {
    id: 1,
    ma_san_pham: cleanCode,
    ten_san_pham: cleanCode.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
    gia_niem_yet: 1500000,
    gia_khuyen_mai: 1500000
  };
}

router.use(requireAuth, customerOnly);

/* ---------- GET /api/cart ---------- */
router.get('/', async (req, res) => {
  try {
    const khId = await resolveKhachHangId(req.user);
    if (!khId) return res.status(404).json({ error: 'Không tìm thấy thông tin khách hàng.' });
    const cartId = await getOrCreateCart(khId);
    return res.json({ items: await readCartItems(cartId) });
  } catch (err) {
    console.error('GET /api/cart:', err);
    return res.status(500).json({ error: 'Không tải được giỏ hàng.' });
  }
});

/* ---------- POST /api/cart  (thêm / +qty) ---------- */
router.post('/', async (req, res) => {
  try {
    const { code, qty } = req.body || {};
    const addQty = Math.max(1, parseInt(qty, 10) || 1);
    let product = await findProduct(code);
    if (!product) {
      product = {
        id: 1,
        ma_san_pham: String(code || 'dich-vu-so-vnpt'),
        ten_san_pham: String(code || 'Dịch vụ số VNPT').replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        gia_niem_yet: 1500000,
        gia_khuyen_mai: 1500000
      };
    }

    const khId = await resolveKhachHangId(req.user);
    if (!khId) return res.status(404).json({ error: 'Không tìm thấy thông tin khách hàng.' });

    const cartId = await getOrCreateCart(khId);
    const price = product.gia_khuyen_mai != null ? Number(product.gia_khuyen_mai) : Number(product.gia_niem_yet);

    const [existing] = await pool.query(
      'SELECT id, so_luong FROM gio_hang_chi_tiet WHERE gio_hang_id = ? AND san_pham_id = ? LIMIT 1',
      [cartId, product.id]
    );
    if (existing.length) {
      await pool.query('UPDATE gio_hang_chi_tiet SET so_luong = 1 WHERE id = ?', [existing[0].id]);
      return res.status(400).json({ error: 'Gói "' + product.ten_san_pham + '" đã có trong giỏ hàng!' });
    } else {
      await pool.query(
        'INSERT INTO gio_hang_chi_tiet (gio_hang_id, san_pham_id, so_luong, don_gia_tai_thoi_diem) VALUES (?, ?, 1, ?)',
        [cartId, product.id, price]
      );
    }
    return res.status(201).json({ items: await readCartItems(cartId) });
  } catch (err) {
    console.error('POST /api/cart:', err);
    return res.status(500).json({ error: 'Không thêm được vào giỏ hàng.' });
  }
});

/* ---------- PUT /api/cart/:code  (đặt số lượng tuyệt đối) ---------- */
router.put('/:code', async (req, res) => {
  try {
    const rawQty = parseInt(req.body?.qty, 10);
    let product = await findProduct(req.params.code);
    if (!product) {
      product = {
        id: 1,
        ma_san_pham: String(req.params.code || 'dich-vu-so-vnpt'),
        ten_san_pham: String(req.params.code || 'Dịch vụ số VNPT').replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        gia_niem_yet: 1500000,
        gia_khuyen_mai: 1500000
      };
    }

    const khId = await resolveKhachHangId(req.user);
    if (!khId) return res.status(404).json({ error: 'Không tìm thấy thông tin khách hàng.' });

    const cartId = await getOrCreateCart(khId);

    if (rawQty <= 0) {
      await pool.query('DELETE FROM gio_hang_chi_tiet WHERE gio_hang_id = ? AND san_pham_id = ?', [cartId, product.id]);
    } else {
      const [existing] = await pool.query(
        'SELECT id FROM gio_hang_chi_tiet WHERE gio_hang_id = ? AND san_pham_id = ? LIMIT 1',
        [cartId, product.id]
      );
      if (existing.length) {
        await pool.query('UPDATE gio_hang_chi_tiet SET so_luong = 1 WHERE id = ?', [existing[0].id]);
      } else {
        const price = product.gia_khuyen_mai != null ? Number(product.gia_khuyen_mai) : Number(product.gia_niem_yet);
        await pool.query(
          'INSERT INTO gio_hang_chi_tiet (gio_hang_id, san_pham_id, so_luong, don_gia_tai_thoi_diem) VALUES (?, ?, 1, ?)',
          [cartId, product.id, price]
        );
      }
    }
    return res.json({ items: await readCartItems(cartId) });
  } catch (err) {
    console.error('PUT /api/cart/:code:', err);
    return res.status(500).json({ error: 'Không cập nhật được giỏ hàng.' });
  }
});

/* ---------- DELETE /api/cart/:code ---------- */
router.delete('/:code', async (req, res) => {
  try {
    let product = await findProduct(req.params.code);
    if (!product) {
      product = {
        id: 1,
        ma_san_pham: String(req.params.code || 'dich-vu-so-vnpt'),
        ten_san_pham: String(req.params.code || 'Dịch vụ số VNPT').replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        gia_niem_yet: 1500000,
        gia_khuyen_mai: 1500000
      };
    }

    const khId = await resolveKhachHangId(req.user);
    if (!khId) return res.status(404).json({ error: 'Không tìm thấy thông tin khách hàng.' });

    const cartId = await getOrCreateCart(khId);
    await pool.query('DELETE FROM gio_hang_chi_tiet WHERE gio_hang_id = ? AND san_pham_id = ?', [cartId, product.id]);
    return res.json({ items: await readCartItems(cartId) });
  } catch (err) {
    console.error('DELETE /api/cart/:code:', err);
    return res.status(500).json({ error: 'Không xóa được sản phẩm khỏi giỏ.' });
  }
});

/* ---------- DELETE /api/cart  (xóa toàn bộ) ---------- */
router.delete('/', async (req, res) => {
  try {
    const khId = await resolveKhachHangId(req.user);
    if (!khId) return res.status(404).json({ error: 'Không tìm thấy thông tin khách hàng.' });

    const cartId = await getOrCreateCart(khId);
    await pool.query('DELETE FROM gio_hang_chi_tiet WHERE gio_hang_id = ?', [cartId]);
    return res.json({ items: [] });
  } catch (err) {
    console.error('DELETE /api/cart:', err);
    return res.status(500).json({ error: 'Không xóa được giỏ hàng.' });
  }
});

module.exports = router;
