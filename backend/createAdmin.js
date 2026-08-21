const { pool } = require('./db/db.js');
const { hashPassword } = require('./api/auth-mw.js');

async function createAdmin() {
  const conn = await pool.getConnection();
  try {
    console.log('⏳ Đang tạo tài khoản Admin...');
    
    const email = 'admin_chuan@vnpt.vn';
    const password = 'admin_chuan123';
    const hoTen = 'Quản Trị Viên Hệ Thống';
    
    const hash = await hashPassword(password);

    await conn.beginTransaction();

    // Check if email exists in tai_khoan
    const [dup] = await conn.query('SELECT id FROM tai_khoan WHERE email = ? LIMIT 1', [email]);
    if (dup.length) {
      console.log('⚠️ Email Admin này đã tồn tại trong Database!');
      await conn.rollback();
      process.exit(0);
    }

    // 1) Insert into tai_khoan (vai_tro_id = 1 -> quan_tri_vien)
    const [tkRes] = await conn.query(
      `INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email)
       VALUES (?, ?, 'nhan_vien', 1, 'hoat_dong', 1)`,
      [email, hash]
    );

    // 2) Insert into nhan_vien
    await conn.query(
      `INSERT INTO nhan_vien (tai_khoan_id, ho_ten) VALUES (?, ?)`,
      [tkRes.insertId, hoTen]
    );

    await conn.commit();

    console.log('✅ Đã tạo tài khoản Admin thành công!');
    console.log(`✉️  Email: ${email}`);
    console.log(`🔑 Mật khẩu: ${password}`);
    process.exit(0);
  } catch (err) {
    await conn.rollback();
    console.error('❌ Lỗi hệ thống:', err);
    process.exit(1);
  } finally {
    conn.release();
  }
}

createAdmin();