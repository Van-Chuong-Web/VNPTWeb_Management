/**
 * init-db.js — Tiện ích khởi tạo cơ sở dữ liệu tự động.
 *
 *   npm run db:init
 *
 * Việc nó làm:
 *   1) Kết nối MySQL (KHÔNG chọn sẵn database) bằng cấu hình .env.
 *   2) Import backend/db/website_vnpt.sql (tạo database + toàn bộ bảng + seed data mới).
 *   3) Đảm bảo tài khoản admin demo với mật khẩu băm bcrypt an toàn trong bảng tai_khoan + nhan_vien.
 */
require('dotenv').config();
const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
const bcrypt = require('bcryptjs');

const DB_NAME = process.env.DB_NAME || 'website_vnpt';
const ADMIN_EMAIL = 'admin@vnpt.vn';
const ADMIN_PASSWORD = 'admin123';

async function run() {
  const conn = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true,
    charset: 'utf8mb4',
  });

  try {
    console.log('→ Đang import backend/db/website_vnpt.sql ...');
    const sqlFile = path.join(__dirname, 'website_vnpt.sql');
    if (!fs.existsSync(sqlFile)) {
      throw new Error(`Khổng tìm thấy file SQL tại: ${sqlFile}`);
    }
    const sqlContent = fs.readFileSync(sqlFile, 'utf8');
    await conn.query(sqlContent);

    console.log('→ Đang đồng bộ tài khoản admin demo (băm mật khẩu tại chỗ)...');
    const hash = await bcrypt.hash(ADMIN_PASSWORD, 10);
    await conn.query(`USE \`${DB_NAME}\`;`);

    // Kiểm tra xem tai_khoan admin@vnpt.vn đã có chưa
    const [existing] = await conn.query('SELECT id FROM tai_khoan WHERE email = ? LIMIT 1', [ADMIN_EMAIL]);
    let tkId;
    if (existing.length) {
      tkId = existing[0].id;
      await conn.query(
        `UPDATE tai_khoan SET mat_khau_hash = ?, trang_thai = 'hoat_dong', vai_tro_id = 1 WHERE id = ?`,
        [hash, tkId]
      );
    } else {
      const [res] = await conn.query(
        `INSERT INTO tai_khoan (email, mat_khau_hash, loai_tai_khoan, vai_tro_id, trang_thai, da_xac_thuc_email)
         VALUES (?, ?, 'nhan_vien', 1, 'hoat_dong', 1)`,
        [ADMIN_EMAIL, hash]
      );
      tkId = res.insertId;
    }

    // Đảm bảo có tương ứng trong nhan_vien
    const [nvRows] = await conn.query('SELECT id FROM nhan_vien WHERE tai_khoan_id = ? LIMIT 1', [tkId]);
    if (!nvRows.length) {
      await conn.query('INSERT INTO nhan_vien (tai_khoan_id, ho_ten) VALUES (?, ?)', [tkId, 'Quản trị viên']);
    }

    console.log('\n✅ Khởi tạo CSDL hoàn tất.');
    console.log(`   Database : ${DB_NAME}`);
    console.log(`   Admin    : ${ADMIN_EMAIL} / ${ADMIN_PASSWORD}`);
  } catch (err) {
    console.error('\n❌ Lỗi khởi tạo CSDL:', err.message);
    process.exitCode = 1;
  } finally {
    await conn.end();
  }
}

run();
