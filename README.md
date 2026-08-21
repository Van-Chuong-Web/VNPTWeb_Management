# 🌐 Website VNPT v3.0

---

## 📁 Cấu trúc thư mục

```
website_vnpt/
├── backend/                    # Node.js/Express backend
│   ├── api/
│   │   ├── admin.js            # API quản trị (stats, users, products, orders)
│   │   ├── auth.js             # Đăng ký / Đăng nhập / Me (JWT)
│   │   ├── auth-mw.js          # Middleware xác thực JWT
│   │   ├── cart.js             # Giỏ hàng
│   │   ├── orders.js           # Đơn hàng / Checkout
│   │   ├── products.js         # Sản phẩm / Dịch vụ
│   │   ├── shape.js            # Helper format dữ liệu
│   │   ├── pages.php           # PHP API trang tĩnh
│   │   └── search.php          # PHP API tìm kiếm
│   ├── db/
│   │   ├── db.js               # MySQL connection pool (Node.js)
│   │   ├── database.php        # MySQL connection (PHP)
│   │   ├── schema.sql          # Schema gốc (webbt_fullstack2)
│   │   ├── seed.sql            # Seed data gốc
│   │   └── website_vnpt.sql    # Schema gốc (phpMyAdmin export)
│   ├── createAdmin.js          # Script tạo tài khoản admin
│   └── server.js               # Entry point Node.js server
│
├── frontend/                   # Website frontend (PHP + Vanilla JS)
│   ├── assets/
│   │   ├── images/             # Hình ảnh (img01.jpg ... img10.jpg)
│   │   └── style.css           # CSS toàn bộ website
│   ├── components/
│   │   ├── header.php          # Header + Navbar
│   │   ├── body.php            # Body content
│   │   └── footer.php          # Footer + Modals + Cart sidebar
│   ├── js/
│   │   ├── admin.js            # Module Admin Panel (JS)
│   │   ├── api.js              # API client (fetch wrapper)
│   │   ├── auth.js             # Đăng nhập / Đăng ký
│   │   ├── carousel.js         # Slider/Carousel
│   │   ├── cart.js             # Giỏ hàng
│   │   ├── chat.js             # Chatbot AI Gemini
│   │   ├── main.js             # Main entry point
│   │   ├── pages.js            # Trang chi tiết sản phẩm
│   │   └── search.js           # Tìm kiếm
│   └── index.php               # Trang chủ
│
├── admin_panel/                # PHP Admin Panel (VNPT_Management)
│   ├── index.php               # Dashboard: thống kê tổng quan
│   ├── admins.php              # Quản lý Quản trị viên (CRUD)
│   ├── customers.php           # Quản lý Khách hàng (CRUD)
│   ├── db.php                  # Kết nối PDO → website_vnpt (merged)
│   ├── header.php              # Layout header + sidebar
│   └── footer.php              # Layout footer
│
├── database/
│   └── merged_database.sql     # ⭐ DATABASE MERGED (dùng file này!)
│
├── .env.example                # Mẫu cấu hình môi trường
├── .gitignore
├── package.json                # Node.js dependencies
└── README.md                   # File này
```

---

## 🚀 Cài đặt & Chạy

### Yêu cầu hệ thống
- **Node.js** >= 18.0.0
- **MySQL** >= 8.0 (hoặc MariaDB >= 10.4)
- **PHP** >= 8.0 + Apache/Nginx (chỉ cần cho Admin Panel PHP)
- **XAMPP** / **Laragon** / **WAMP** (khuyến nghị cho local)

### Bước 1: Import Database

```bash
# Tạo database và import toàn bộ schema + seed data
mysql -u root -p < backend/db/website_vnpt.sql
```

Hoặc chạy lệnh tự động bằng Node.js:
```bash
npm run db:init
```

Hoặc dùng phpMyAdmin:
1. Tạo database `website_vnpt`
2. Import file `backend/db/website_vnpt.sql`

### Bước 2: Cấu hình môi trường

```bash
cp .env.example .env
```

Mở file `.env` và điền thông tin:
```env
DB_PASSWORD=your_mysql_password
JWT_SECRET=your_random_secret_key_here
GEMINI_API_KEY=your_gemini_key  # Tùy chọn
```

### Bước 3: Cài dependencies & Chạy Node.js server

```bash
npm install
npm start          # Production
# hoặc
npm run dev        # Development (auto-reload)
```

Server chạy tại: **http://localhost:3000**

### Bước 4: Truy cập PHP Admin Panel

Đặt thư mục `admin_panel/` vào web server PHP:
- **XAMPP**: Copy vào `C:/xampp/htdocs/vnpt_admin/`
- **Laragon**: Copy vào `C:/laragon/www/vnpt_admin/`

Truy cập: **http://localhost/vnpt_admin/**

Cập nhật `admin_panel/db.php` nếu cần:
```php
define('DB_PASS', 'your_mysql_password');
```

---

## 🔑 Tài khoản Demo

### Node.js Backend (JWT Authentication)
| Email | Mật khẩu | Vai trò |
|-------|----------|---------|
| admin@vnpt.vn | admin123 | Admin |

### PHP Admin Panel
| Email | Mật khẩu | Vai trò |
|-------|----------|---------|
| admin@vnpt.vn | password | Super Admin |
| editor@vnpt.vn | password | Editor |
| manager@vnpt.vn | password | Admin |

### Mã giảm giá Demo
| Mã | Loại | Giá trị |
|----|------|---------|
| VNPT10 | % | Giảm 10% |
| VNPT20 | % | Giảm 20% |
| GIAM500K | Số tiền | Giảm 500,000đ |

---

## 📡 API Endpoints (Node.js)

### Authentication
| Method | Endpoint | Mô tả |
|--------|----------|-------|
| POST | `/api/auth/register` | Đăng ký tài khoản |
| POST | `/api/auth/login` | Đăng nhập |
| GET | `/api/auth/me` | Thông tin user hiện tại |

### Products
| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/products` | Danh sách sản phẩm/dịch vụ |
| GET | `/api/products/:id` | Chi tiết sản phẩm |

### Cart
| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/cart` | Xem giỏ hàng |
| POST | `/api/cart` | Thêm vào giỏ |
| PUT | `/api/cart/:id` | Cập nhật số lượng |
| DELETE | `/api/cart/:id` | Xóa khỏi giỏ |

### Orders
| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/orders` | Đơn hàng của tôi |
| POST | `/api/orders` | Tạo đơn hàng (checkout) |
| GET | `/api/orders/:id` | Chi tiết đơn hàng |

### Admin (yêu cầu role=admin)
| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/admin/stats` | Thống kê tổng quan |
| GET | `/api/admin/users` | Danh sách khách hàng |
| GET | `/api/admin/products` | Danh sách sản phẩm |
| GET | `/api/admin/orders` | Danh sách đơn hàng |

### Chatbot AI
| Method | Endpoint | Mô tả |
|--------|----------|-------|
| POST | `/api/chat` | Chat với VNPT AI (Gemini) |

---

## 🗄️ Database Schema

Database `website_vnpt` gồm **43 bảng** (41 từ website_vnpt.sql + 2 từ admin_panel):

### Nhóm User & Phân quyền
- `khach_hang` — Khách hàng (đăng ký website)
- `nhan_vien` — Nhân viên/Admin (Node.js backend)
- `admins` — Quản trị viên (PHP Admin Panel) ⭐ Mới
- `customers` — Khách hàng (PHP Admin Panel) ⭐ Mới
- `vai_tro`, `quyen_han`, `vai_tro_quyen` — Phân quyền
- `dia_chi_khach_hang` — Địa chỉ giao hàng

### Nhóm Sản phẩm
- `san_pham`, `danh_muc_san_pham`, `nha_cung_cap`
- `bien_the_san_pham`, `hinh_anh_san_pham`
- `thuoc_tinh_san_pham`, `gia_tri_thuoc_tinh`
- `goi_cuoc_di_dong`, `goi_internet_truyen_hinh`
- `kho_hang`, `ton_kho`

### Nhóm Đơn hàng & Thanh toán
- `don_hang`, `don_hang_chi_tiet`
- `gio_hang`, `gio_hang_chi_tiet`
- `phuong_thuc_thanh_toan`, `thanh_toan`
- `phuong_thuc_van_chuyen`, `van_chuyen_don_hang`
- `lich_su_trang_thai_don_hang`
- `ma_giam_gia`, `khuyen_mai`, `ap_dung_khuyen_mai`

### Nhóm Nội dung & Marketing
- `bai_viet`, `danh_muc_bai_viet`
- `cau_hoi_thuong_gap`
- `trang_tinh` (static pages)
- `menu`
- `thong_bao`

### Nhóm Hỗ trợ & Dịch vụ
- `yeu_cau_ho_tro`, `phan_hoi_ho_tro`
- `dang_ky_dich_vu`
- `danh_gia_san_pham`, `san_pham_yeu_thich`
- `khu_vuc_phu_song`

---

## 🔧 Giải quyết Conflicts khi Merge

| Vấn đề | Giải pháp |
|--------|-----------|
| 2 backend server.js giống nhau | Dùng webbt_fullstack2 làm base (đầy đủ hơn, có `search.js`) |
| admin_panel dùng DB riêng `vnpt_admin` | Gộp bảng `admins` + `customers` vào `website_vnpt` |
| VNPT_Management thiếu `search.php` | Lấy từ webbt_fullstack2 |
| Import paths khác nhau (`./db/db` vs `../db`) | Giữ nguyên webbt_fullstack2 paths (đúng) |
| website_vnpt.sql có 41 bảng, schema.sql có 28 bảng | Dùng website_vnpt.sql làm base (đầy đủ hơn) |

---

## 📞 Liên hệ & Hỗ trợ

- **Hotline**: 1800 1260
- **Email**: support@vnpt.vn
- **Website**: https://vnpt.vn
