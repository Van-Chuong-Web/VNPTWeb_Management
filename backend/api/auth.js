/**
 * backend/api/auth.js — Xác thực & phân quyền
 *   POST /api/auth/register  Đăng ký khách hàng (tai_khoan + khach_hang)
 *   POST /api/auth/login     Đăng nhập (nhân viên & khách hàng qua tai_khoan)
 *   GET  /api/auth/me        Lấy thông tin người đang đăng nhập (theo JWT)
 */
const express = require('express');
const router = express.Router();
const { pool } = require('../db/db.js');
const { hashPassword, comparePassword, signToken, requireAuth } = require('./auth-mw');
const { shapeCustomer, shapeAdmin, joinName } = require('./shape');

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

/* ---------- POST /api/auth/register ---------- */
router.post('/register', async (req, res) => {
  const conn = await pool.getConnection();
  try {
    const { firstName, lastName, email, phone, password } = req.body || {};
    if (!firstName || !String(firstName).trim()) {
      return res.status(400).json({ error: 'Vui lòng nhập họ.' });
    }
    if (!email || !EMAIL_RE.test(email)) {
      return res.status(400).json({ error: 'Email không hợp lệ.' });
    }
    if (!password || String(password).length < 8) {
      return res.status(400).json({ error: 'Mật khẩu tối thiểu 8 ký tự.' });
    }

    // 1) Email đã tồn tại trong hệ thống?
    const [dupEmail] = await conn.query('SELECT id FROM tai_khoan WHERE email = ? LIMIT 1', [email]);
    if (dupEmail.length) {
      return res.status(409).json({ error: 'Email này đã được đăng ký. Vui lòng đăng nhập hoặc dùng email khác.' });
    }

    // 2) Số điện thoại đã tồn tại trong CSDL khách hàng?
    const cleanPhone = phone ? String(phone).trim() : '';
    if (cleanPhone) {
      const [dupPhone] = await conn.query('SELECT id FROM khach_hang WHERE so_dien_thoai = ? LIMIT 1', [cleanPhone]);
      if (dupPhone.length) {
        return res.status(409).json({ error: 'Số điện thoại này đã được đăng ký. Vui lòng kiểm tra lại hoặc dùng số khác.' });
      }
    }

    await conn.beginTransaction();

    const hash = await hashPassword(password);
    const hoTen = joinName(firstName, lastName);

    // 1) Thêm vào tai_khoan (vai_tro_id = 4 là khach_hang)
    const [tkRes] = await conn.query(
      `INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email)
       VALUES (?, ?, 'khach_hang', 4, 'hoat_dong', 1)`,
      [email, hash]
    );
    const taiKhoanId = tkRes.insertId;

    // 2) Thêm vào khach_hang
    const [khRes] = await conn.query(
      `INSERT INTO khach_hang (tai_khoan_id, ho_ten, so_dien_thoai) VALUES (?, ?, ?)`,
      [taiKhoanId, hoTen, phone || null]
    );
    const khachHangId = khRes.insertId;

    await conn.commit();

    const user = {
      id: khachHangId,
      taiKhoanId,
      firstName: String(firstName).trim(),
      lastName: String(lastName || '').trim(),
      email,
      phone: phone || '',
      role: 'customer',
    };
    const token = signToken({
      id: taiKhoanId,
      tai_khoan_id: taiKhoanId,
      profile_id: khachHangId,
      khach_hang_id: khachHangId,
      loai: 'customer',
      role: 'customer',
      email,
      ho_ten: hoTen
    });
    return res.status(201).json({ token, user });
  } catch (err) {
    await conn.rollback();
    if (err && err.code === 'ER_DUP_ENTRY') {
      return res.status(409).json({ error: 'Email hoặc số điện thoại đã tồn tại.' });
    }
    console.error('POST /api/auth/register:', err);
    return res.status(500).json({ error: 'Lỗi server khi đăng ký. Vui lòng thử lại.' });
  } finally {
    conn.release();
  }
});

/* ---------- POST /api/auth/social ---------- */
router.post('/social', async (req, res) => {
  const conn = await pool.getConnection();
  try {
    const { provider, email, firstName, lastName, providerId } = req.body || {};
    if (!email || !EMAIL_RE.test(email)) {
      return res.status(400).json({ error: 'Email không hợp lệ.' });
    }

    const fName = String(firstName || 'Social').trim();
    const lName = String(lastName || 'User').trim();
    const hoTen = joinName(fName, lName);

    const [existing] = await conn.query('SELECT id FROM tai_khoan WHERE email = ? LIMIT 1', [email]);
    let taiKhoanId;
    let khachHangId;

    if (existing.length) {
      taiKhoanId = existing[0].id;
      await conn.query("UPDATE tai_khoan SET da_xac_thuc_email = 1, trang_thai = 'hoat_dong' WHERE id = ?", [taiKhoanId]);
      
      const [khRows] = await conn.query('SELECT id FROM khach_hang WHERE tai_khoan_id = ? LIMIT 1', [taiKhoanId]);
      if (khRows.length) {
        khachHangId = khRows[0].id;
      } else {
        const [khRes] = await conn.query('INSERT INTO khach_hang (tai_khoan_id, ho_ten) VALUES (?, ?)', [taiKhoanId, hoTen]);
        khachHangId = khRes.insertId;
      }
    } else {
      await conn.beginTransaction();
      const fakeHash = await hashPassword('SOCIAL_' + (provider || 'social') + '_' + (providerId || Date.now()));
      
      const [tkRes] = await conn.query(
        `INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email)
         VALUES (?, ?, 'khach_hang', 4, 'hoat_dong', 1)`,
        [email, fakeHash]
      );
      taiKhoanId = tkRes.insertId;

      const [khRes] = await conn.query(
        `INSERT INTO khach_hang (tai_khoan_id, ho_ten) VALUES (?, ?)`,
        [taiKhoanId, hoTen]
      );
      khachHangId = khRes.insertId;
      await conn.commit();
    }

    const user = {
      id: khachHangId,
      taiKhoanId,
      firstName: fName,
      lastName: lName,
      email,
      role: 'customer',
      provider: provider || 'social'
    };

    const token = signToken({
      id: taiKhoanId,
      tai_khoan_id: taiKhoanId,
      profile_id: khachHangId,
      khach_hang_id: khachHangId,
      loai: 'customer',
      role: 'customer',
      email,
      ho_ten: hoTen
    });

    return res.status(200).json({ status: 'success', token, user });
  } catch (err) {
    await conn.rollback();
    console.error('POST /api/auth/social:', err);
    return res.status(500).json({ error: 'Lỗi server khi đăng nhập xã hội.' });
  } finally {
    conn.release();
  }
});

/* ---------- POST /api/auth/login ---------- */
router.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body || {};
    if (!email || !EMAIL_RE.test(email)) {
      return res.status(400).json({ error: 'Email không hợp lệ.' });
    }
    if (!password) {
      return res.status(400).json({ error: 'Vui lòng nhập mật khẩu.' });
    }

    const [rows] = await pool.query(
      `SELECT tk.id AS tai_khoan_id, tk.email, tk.mat_khau_hash, tk.loai_tai_khoan, tk.trang_thai, tk.vai_tro_id, tk.hinh_anh_url,
              vt.ten_vai_tro, vt.nhom_vai_tro,
              nv.id AS nhan_vien_id, nv.ho_ten AS nv_ho_ten,
              kh.id AS khach_hang_id, kh.ho_ten AS kh_ho_ten, kh.so_dien_thoai AS kh_sdt
         FROM tai_khoan tk
         JOIN vai_tro vt ON vt.id = tk.vai_tro_id
         LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
         LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
        WHERE tk.email = ? LIMIT 1`,
      [email]
    );

    if (!rows.length) {
      return res.status(401).json({ error: 'Email hoặc mật khẩu không đúng.' });
    }

    const row = rows[0];

    if (row.trang_thai === 'khoa') {
      return res.status(403).json({ error: 'Tài khoản đã bị khóa.' });
    }

    let ok = await comparePassword(password, row.mat_khau_hash);
    // Hỗ trợ trường hợp seed data có mật khẩu dạng text thuần
    if (!ok && row.mat_khau_hash === password) {
      ok = true;
      // Cập nhật lại băm mật khẩu bcrypt
      const newHash = await hashPassword(password);
      await pool.query('UPDATE tai_khoan SET mat_khau_hash = ? WHERE id = ?', [newHash, row.tai_khoan_id]);
    }

    if (!ok) {
      return res.status(401).json({ error: 'Email hoặc mật khẩu không đúng.' });
    }

    if (row.loai_tai_khoan === 'nhan_vien') {
      const user = shapeAdmin({
        id: row.nhan_vien_id,
        tai_khoan_id: row.tai_khoan_id,
        ho_ten: row.nv_ho_ten || 'Quản trị viên',
        email: row.email,
        ten_vai_tro: row.ten_vai_tro,
        hinh_anh_url: row.hinh_anh_url || '',
      });
      const token = signToken({
        id: row.tai_khoan_id,
        tai_khoan_id: row.tai_khoan_id,
        profile_id: row.nhan_vien_id,
        nhan_vien_id: row.nhan_vien_id,
        loai: 'admin',
        role: user.role,
        email: user.email,
        ho_ten: user.firstName + ' ' + user.lastName
      });
      return res.json({ token, user });
    } else {
      const user = shapeCustomer({
        id: row.khach_hang_id,
        tai_khoan_id: row.tai_khoan_id,
        ho_ten: row.kh_ho_ten || 'Khách hàng',
        email: row.email,
        so_dien_thoai: row.kh_sdt,
        hinh_anh_url: row.hinh_anh_url || '',
      });
      const token = signToken({
        id: row.tai_khoan_id,
        tai_khoan_id: row.tai_khoan_id,
        profile_id: row.khach_hang_id,
        khach_hang_id: row.khach_hang_id,
        loai: 'customer',
        role: 'customer',
        email: user.email,
        ho_ten: user.firstName + ' ' + user.lastName
      });
      return res.json({ token, user });
    }
  } catch (err) {
    console.error('POST /api/auth/login:', err);
    return res.status(500).json({ error: 'Lỗi server khi đăng nhập. Vui lòng thử lại.' });
  }
});

/* ---------- GET /api/auth/me ---------- */
router.get('/me', requireAuth, async (req, res) => {
  try {
    const tkId = req.user.tai_khoan_id || req.user.id;
    const [rows] = await pool.query(
      `SELECT tk.id AS tai_khoan_id, tk.email, tk.loai_tai_khoan, tk.trang_thai, tk.hinh_anh_url, vt.ten_vai_tro,
              nv.id AS nhan_vien_id, nv.ho_ten AS nv_ho_ten,
              kh.id AS khach_hang_id, kh.ho_ten AS kh_ho_ten, kh.so_dien_thoai AS kh_sdt
         FROM tai_khoan tk
         JOIN vai_tro vt ON vt.id = tk.vai_tro_id
         LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
         LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
        WHERE tk.id = ? LIMIT 1`,
      [tkId]
    );

    if (!rows.length) return res.status(404).json({ error: 'Không tìm thấy tài khoản.' });

    const row = rows[0];
    if (row.loai_tai_khoan === 'nhan_vien') {
      return res.json({
        user: shapeAdmin({
          id: row.nhan_vien_id,
          tai_khoan_id: row.tai_khoan_id,
          ho_ten: row.nv_ho_ten,
          email: row.email,
          ten_vai_tro: row.ten_vai_tro,
          hinh_anh_url: row.hinh_anh_url || '',
        })
      });
    }

    return res.json({
      user: shapeCustomer({
        id: row.khach_hang_id,
        tai_khoan_id: row.tai_khoan_id,
        ho_ten: row.kh_ho_ten,
        email: row.email,
        so_dien_thoai: row.kh_sdt,
        hinh_anh_url: row.hinh_anh_url || '',
      })
    });
  } catch (err) {
    console.error('GET /api/auth/me:', err);
    return res.status(500).json({ error: 'Lỗi server.' });
  }
});

/* ---------- PUT /api/auth/me — Cập nhật hồ sơ cá nhân ---------- */
router.put('/me', requireAuth, async (req, res) => {
  try {
    const tkId = req.user.tai_khoan_id || req.user.id;
    const { firstName, lastName, phone } = req.body || {};

    if (!firstName || !String(firstName).trim()) {
      return res.status(400).json({ error: 'Vui lòng nhập họ.' });
    }
    if (phone && !/^(0|\+84)[0-9]{8,10}$/.test(String(phone).replace(/\s/g, ''))) {
      return res.status(400).json({ error: 'Số điện thoại không hợp lệ.' });
    }

    const hoTen = joinName(firstName, lastName);

    const [rows] = await pool.query('SELECT loai_tai_khoan FROM tai_khoan WHERE id = ? LIMIT 1', [tkId]);
    if (!rows.length) return res.status(404).json({ error: 'Không tìm thấy tài khoản.' });

    if (rows[0].loai_tai_khoan === 'nhan_vien') {
      await pool.query('UPDATE nhan_vien SET ho_ten = ? WHERE tai_khoan_id = ?', [hoTen, tkId]);
    } else {
      await pool.query(
        'UPDATE khach_hang SET ho_ten = ?, so_dien_thoai = ? WHERE tai_khoan_id = ?',
        [hoTen, phone || null, tkId]
      );
    }

    const [after] = await pool.query(
      `SELECT tk.id AS tai_khoan_id, tk.email, tk.loai_tai_khoan, vt.ten_vai_tro,
              nv.id AS nhan_vien_id, nv.ho_ten AS nv_ho_ten,
              kh.id AS khach_hang_id, kh.ho_ten AS kh_ho_ten, kh.so_dien_thoai AS kh_sdt
         FROM tai_khoan tk
         JOIN vai_tro vt ON vt.id = tk.vai_tro_id
         LEFT JOIN nhan_vien nv ON nv.tai_khoan_id = tk.id
         LEFT JOIN khach_hang kh ON kh.tai_khoan_id = tk.id
        WHERE tk.id = ? LIMIT 1`,
      [tkId]
    );
    const row = after[0];
    const user = row.loai_tai_khoan === 'nhan_vien'
      ? shapeAdmin({ id: row.nhan_vien_id, tai_khoan_id: row.tai_khoan_id, ho_ten: row.nv_ho_ten, email: row.email, ten_vai_tro: row.ten_vai_tro })
      : shapeCustomer({ id: row.khach_hang_id, tai_khoan_id: row.tai_khoan_id, ho_ten: row.kh_ho_ten, email: row.email, so_dien_thoai: row.kh_sdt });

    return res.json({ user });
  } catch (err) {
    console.error('PUT /api/auth/me:', err);
    return res.status(500).json({ error: 'Lỗi server khi cập nhật hồ sơ.' });
  }
});

/* ---------- PUT /api/auth/password — Đổi mật khẩu ---------- */
router.put('/password', requireAuth, async (req, res) => {
  try {
    const tkId = req.user.tai_khoan_id || req.user.id;
    const { currentPassword, newPassword } = req.body || {};

    if (!newPassword || String(newPassword).length < 8) {
      return res.status(400).json({ error: 'Mật khẩu mới tối thiểu 8 ký tự.' });
    }

    const [rows] = await pool.query('SELECT mat_khau_hash FROM tai_khoan WHERE id = ? LIMIT 1', [tkId]);
    if (!rows.length) return res.status(404).json({ error: 'Không tìm thấy tài khoản.' });

    const ok = await comparePassword(currentPassword || '', rows[0].mat_khau_hash);
    if (!ok) {
      return res.status(401).json({ error: 'Mật khẩu hiện tại không chính xác.' });
    }

    const newHash = await hashPassword(newPassword);
    await pool.query('UPDATE tai_khoan SET mat_khau_hash = ? WHERE id = ?', [newHash, tkId]);

    return res.json({ ok: true });
  } catch (err) {
    console.error('PUT /api/auth/password:', err);
    return res.status(500).json({ error: 'Lỗi server khi đổi mật khẩu.' });
  }
});

/* ---------- POST /api/auth/social — Đăng nhập MXH (Google / Facebook) ---------- */
router.post('/social', async (req, res) => {
  try {
    const { email, firstName, lastName, provider, avatar } = req.body || {};
    if (!email || !EMAIL_RE.test(email)) {
      return res.status(400).json({ error: 'Email không hợp lệ.' });
    }
    const hoTen = String(`${firstName || ''} ${lastName || ''}`).trim() || 'Khách hàng';

    // 1. Tìm hoặc tạo tài khoản trong DB
    let [rows] = await pool.query('SELECT id, loai_tai_khoan, trang_thai FROM tai_khoan WHERE email = ? LIMIT 1', [email]);
    let taiKhoanId;
    let khachHangId;

    if (rows.length) {
      if (rows[0].trang_thai === 'khoa') {
        return res.status(403).json({ error: '🚫 Tài khoản đã bị khóa bởi Quản trị viên.' });
      }
      taiKhoanId = rows[0].id;
      const [khRows] = await pool.query('SELECT id FROM khach_hang WHERE tai_khoan_id = ? LIMIT 1', [taiKhoanId]);
      if (khRows.length) {
        khachHangId = khRows[0].id;
      } else {
        const [r] = await pool.query('INSERT INTO khach_hang (tai_khoan_id, ho_ten, created_at) VALUES (?, ?, NOW())', [taiKhoanId, hoTen]);
        khachHangId = r.insertId;
      }
      if (avatar) {
        await pool.query('UPDATE tai_khoan SET hinh_anh_url = ? WHERE id = ?', [avatar, taiKhoanId]);
      }
    } else {
      const dummyHash = await hashPassword('SocialPass_' + Date.now());
      const [rTk] = await pool.query(
        'INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email, hinh_anh_url, created_at) VALUES (?, ?, "khach_hang", 4, "hoat_dong", 1, ?, NOW())',
        [email, dummyHash, avatar || null]
      );
      taiKhoanId = rTk.insertId;
      const [rKh] = await pool.query('INSERT INTO khach_hang (tai_khoan_id, ho_ten, created_at) VALUES (?, ?, NOW())', [taiKhoanId, hoTen]);
      khachHangId = rKh.insertId;
    }

    const user = shapeCustomer({
      id: khachHangId,
      tai_khoan_id: taiKhoanId,
      ho_ten: hoTen,
      email: email,
      hinh_anh_url: avatar || '',
      provider: provider || 'google'
    });

    const token = signToken({
      id: taiKhoanId,
      tai_khoan_id: taiKhoanId,
      profile_id: khachHangId,
      khach_hang_id: khachHangId,
      loai: 'customer',
      role: 'customer',
      email: email,
      ho_ten: hoTen
    });

    return res.json({ status: 'success', success: true, token, user });
  } catch (err) {
    console.error('POST /api/auth/social:', err);
    return res.status(500).json({ error: 'Lỗi server khi đăng nhập mạng xã hội.' });
  }
});

module.exports = router;
