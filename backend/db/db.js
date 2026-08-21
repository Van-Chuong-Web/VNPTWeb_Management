/**
 * backend/db/db.js — Smart Multi-Candidate Connection Pool
 * Kết nối CSDL MySQL local website_vnpt.
 */
require('dotenv').config();
const mysql = require('mysql2/promise');

const candidates = [
  {
    host: process.env.DB_HOST || 'localhost',
    port: Number(process.env.DB_PORT || 3306),
    user: (process.env.DB_USER && process.env.DB_USER.trim()) ? process.env.DB_USER.trim() : 'root',
    password: process.env.DB_PASSWORD !== undefined ? process.env.DB_PASSWORD : '',
    database: process.env.DB_NAME || 'website_vnpt',
  },
  {
    host: '127.0.0.1',
    port: 3306,
    user: 'root',
    password: '',
    database: 'website_vnpt',
  },
  {
    host: 'localhost',
    port: 3306,
    user: 'vnpt_user',
    password: 'MatKhauBaoMat123@#$',
    database: 'website_vnpt',
  },
  {
    host: '127.0.0.1',
    port: 3306,
    user: 'vnpt_user',
    password: 'MatKhauBaoMat123@#$',
    database: 'website_vnpt',
  }
];

let workingPool = null;

async function getWorkingPool() {
  if (workingPool) return workingPool;

  for (const config of candidates) {
    try {
      const pool = mysql.createPool({
        ...config,
        waitForConnections: true,
        connectionLimit: Number(process.env.DB_CONNECTION_LIMIT || 10),
        queueLimit: 0,
      });
      const conn = await pool.getConnection();
      await conn.ping();
      conn.release();
      console.log(`[db.js] ✅ Kết nối MySQL thành công với host ${config.host}:${config.port}/${config.database}`);
      workingPool = pool;
      return workingPool;
    } catch (err) {
      console.warn(`[db.js] ⚠️ Thử kết nối host ${config.host}:${config.port} không thành công: ${err.message}`);
    }
  }

  throw new Error('[db.js] ❌ Tất cả các cấu hình kết nối MySQL đều thất bại!');
}

async function query(sql, params) {
  const pool = await getWorkingPool();
  const [results] = await pool.execute(sql, params);
  return results;
}

module.exports = {
  getWorkingPool,
  query,
};
