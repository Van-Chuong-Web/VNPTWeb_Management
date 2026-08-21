-- MySQL dump for website_vnpt
-- Generated on 2026-08-11 09:09:57

CREATE DATABASE IF NOT EXISTS `website_vnpt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `website_vnpt`;

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ap_dung_khuyen_mai`;
CREATE TABLE `ap_dung_khuyen_mai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `khuyen_mai_id` bigint unsigned NOT NULL,
  `san_pham_id` bigint unsigned DEFAULT NULL,
  `danh_muc_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `khuyen_mai_id` (`khuyen_mai_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `danh_muc_id` (`danh_muc_id`),
  CONSTRAINT `ap_dung_khuyen_mai_ibfk_1` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ap_dung_khuyen_mai_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ap_dung_khuyen_mai_ibfk_3` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc_san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `bai_viet`;
CREATE TABLE `bai_viet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `danh_muc_bai_viet_id` bigint unsigned DEFAULT NULL,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tom_tat` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `noi_dung` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `anh_bia` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tac_gia_id` bigint unsigned DEFAULT NULL,
  `trang_thai` enum('nhap','da_dang','an') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'nhap',
  `ngay_xuat_ban` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `danh_muc_bai_viet_id` (`danh_muc_bai_viet_id`),
  KEY `tac_gia_id` (`tac_gia_id`),
  CONSTRAINT `bai_viet_ibfk_1` FOREIGN KEY (`danh_muc_bai_viet_id`) REFERENCES `danh_muc_bai_viet` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bai_viet_ibfk_2` FOREIGN KEY (`tac_gia_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bai_viet` (`id`, `danh_muc_bai_viet_id`, `tieu_de`, `slug`, `tom_tat`, `noi_dung`, `anh_bia`, `tac_gia_id`, `trang_thai`, `ngay_xuat_ban`) VALUES
('1', '3', 'VNPT Ra Mắt Giải Pháp VNPT Smart Cloud Đạt Tiêu Chuẩn Quốc Tế SLA 99.99%', 'vnpt-ra-mat-giai-phap-vnpt-smart-cloud-dat-tieu-chuan-quoc-te-sla-99-99', 'VNPT giới thiệu hạ tầng máy chủ đám mây thế hệ mới với ổ cứng NVMe Enterprise, cam kết độ sẵn sàng 99.99% và tự động sao lưu dữ liệu cho doanh nghiệp.', '<p>VNPT chính thức ra mắt dòng giải pháp điện toán đám mây thế hệ mới <strong>VNPT Smart Cloud</strong>. Được thiết kế tối ưu trên hạ tầng phần cứng dòng máy chủ cao cấp cùng ổ cứng lưu trữ NVMe Enterprise tốc độ cao, hệ thống đem lại hiệu năng xử lý gấp 3 lần so với các máy chủ truyền thống.</p><h4>Đặc điểm nổi bật của VNPT Smart Cloud:</h4><ul><li>Cam kết mức độ sẵn sàng dịch vụ SLA Uptime 99.99%.</li><li>Tích hợp cơ chế tự động tạo bản sao lưu Snapshot hàng ngày.</li><li>Bảo vệ toàn diện bởi Tường lửa VPS và hệ thống chống tấn công DDoS 100Gbps.</li></ul><p>Giải pháp giúp các khối doanh nghiệp vừa và lớn tiết kiệm đến 40% chi phí đầu tư hạ tầng ban đầu mà vẫn sở hữu hệ thống vận hành mượt mà, liên tục 24/7.</p>', 'frontend/assets/images/slider/banner1.webp', NULL, 'da_dang', '2026-08-07 04:03:04'),
('2', '1', 'Ứng Dụng Trí Tuệ Nhân Tạo AI OCR Giúp Tự Động Hóa 70% Quy Trình Xử Lý Chứng Từ', 'ung-dung-tri-tue-nhan-tao-ai-ocr-giup-tu-dong-hoa-70-quy-trinh-xu-ly-chung-tu', 'Trợ lý AI OCR số hóa thành công hàng ngàn loại giấy tờ định danh và hóa đơn GTGT với độ chính xác 99.8%, giảm thời gian xử lý thủ công từ 30 phút xuống 3 giây.', '<p>Trong kỷ nguyên chuyển đổi số, việc nhập liệu thủ công tài liệu giấy tờ gây tốn rất nhiều thời gian và dễ xảy ra sai sót. Giải pháp <strong>VNPT AI OCR</strong> ra đời nhằm số hóa toàn bộ quy trình xử lý thông tin nghiệp vụ.</p><p>Nhờ áp dụng mô hình Deep Learning tiên tiến, VNPT AI OCR có khả năng bóc tách dữ liệu từ Căn cước công dân (CCCD), Đăng ký xe, Hóa đơn GTGT, Giấy phép kinh doanh và Hợp đồng với độ chính xác vượt trội 99.8% trong thời gian chưa tới 0.5 giây.</p>', 'frontend/assets/images/slider/banner2.webp', NULL, 'da_dang', '2026-08-06 04:03:04'),
('3', '2', 'Chữ Ký Số Từ Xa VNPT SmartCA Bứt Phá Tiêu Chuẩn Ký Hợp Đồng Điện Tử Cho SME', 'chu-ky-so-tu-xa-vnpt-smartca-but-pha-tieu-chuan-ky-hop-dong-dien-tu-cho-sme', 'Giải pháp ký số từ xa an toàn không cần USB Token giúp các khối doanh nghiệp vừa và nhỏ phê duyệt chứng từ hợp đồng từ xa nhanh chóng.', '<p>Dịch vụ chữ ký số từ xa <strong>VNPT SmartCA</strong> đạt tiêu chuẩn an toàn bảo mật Châu Âu eIDAS và FIPS 140-2 Level 3. Khách hàng không cần phải mang theo thiết bị phần cứng USB Token cồng kềnh mà có thể thực hiện ký số trực tiếp trên Smartphone hoặc máy tính bảng mọi lúc mọi nơi.</p><p>Hệ thống hỗ trợ tích hợp trực tiếp vào phần mềm kế toán, hóa đơn điện tử VNPT Invoice và các cổng dịch vụ công quốc gia.</p>', 'frontend/assets/images/slider/banner3.webp', NULL, 'da_dang', '2026-08-05 04:03:04'),
('4', '5', 'VNPT Cyber Security Cảnh Báo Nguy Cơ Tấn Công DDoS & Giải Pháp Bảo Vệ SOC 24/7', 'vnpt-cyber-security-canh-bao-nguy-co-tan-cong-ddos-giai-phap-bao-ve-soc-247', 'Trung tâm an ninh mạng VNPT cảnh báo các đợt tấn công DDoS tăng mạnh trong quý 3 và giải pháp giám sát SOC 24/7 giúp loại bỏ 100% mã độc.', '<p>Các cuộc tấn công từ chối dịch vụ (DDoS) và mã độc mã hóa dữ liệu (Ransomware) đang ngày càng tinh vi và đe dọa trực tiếp tới sự sống còn của doanh nghiệp. Trung tâm điều hành an toàn thông tin <strong>VNPT SOC 24/7</strong> được trang bị công nghệ giám sát sự cố tự động kết hợp với đội ngũ chuyên gia an ninh mạng hàng đầu Việt Nam.</p><p>Hệ thống tự động phát hiện và ngăn chặn các mối đe dọa ngay tại tầng mạng trước khi chúng kịp gây hại cho hệ thống cơ sở dữ liệu của doanh nghiệp.</p>', 'frontend/assets/images/slider/banner1.webp', NULL, 'da_dang', '2026-08-04 04:03:04'),
('5', '4', 'Thông Cáo Báo Chí: VNPT Ký Kết Hợp Tác Chiến Lược Chuyển Đổi Số Với Top 500 Doanh Nghiệp', 'thong-cao-bao-chi-vnpt-ky-ket-hop-tac-chien-luoc-chuyen-doi-so-voi-top-500-doanh-nghiep', 'VNPT chính thức công bố thỏa thuận hợp tác cung cấp hạ tầng Cloud Server và AI tự động hóa cho chuỗi doanh nghiệp bán lẻ hàng đầu Việt Nam.', '<p>Ngày 05/08/2026, tại Hà Nội, VNPT đã chính thức lễ ký kết hợp tác chiến lược chuyển đổi số cùng các tập đoàn bán lẻ thuộc Top 500 doanh nghiệp hàng đầu Việt Nam. Theo thỏa thuận, VNPT sẽ cung cấp toàn bộ hạ tầng Multi-Cloud dùng riêng, giải pháp ký số SmartCA và hệ thống chăm sóc khách hàng tự động AI Chatbot.</p>', 'frontend/assets/images/uploads/img_20260807_044833_1924.jpg', NULL, 'da_dang', '2026-08-07 11:48:36');

DROP TABLE IF EXISTS `bien_the_san_pham`;
CREATE TABLE `bien_the_san_pham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned NOT NULL,
  `ten_bien_the` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_chenh_lech` decimal(15,2) DEFAULT '0.00',
  `ma_vach` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`),
  CONSTRAINT `bien_the_san_pham_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cau_hoi_thuong_gap`;
CREATE TABLE `cau_hoi_thuong_gap` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `danh_muc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cau_hoi` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cau_tra_loi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thu_tu` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cau_hoi_thuong_gap` (`id`, `danh_muc`, `cau_hoi`, `cau_tra_loi`, `thu_tu`) VALUES
('1', 'Tổng quan', 'VNPT cung cấp những dịch vụ gì?', 'VNPT cung cấp đầy đủ các giải pháp chuyển đổi số: Cloud Computing, Bảo mật số, AI & Tự động hóa, Hạ tầng 5G, Quản trị doanh nghiệp số và nhiều dịch vụ khác.', '1'),
('2', 'Đăng ký', 'Làm thế nào để đăng ký dịch vụ?', 'Bạn có thể đăng ký trực tiếp trên website, gọi hotline 1800 1260, hoặc để lại thông tin tư vấn để đội ngũ VNPT liên hệ trong vòng 24h.', '2'),
('3', 'Hỗ trợ', 'Có hỗ trợ kỹ thuật 24/7 không?', 'Có, VNPT cung cấp hỗ trợ kỹ thuật 24/7 cho tất cả các gói dịch vụ. Gói Cao cấp có SLA đảm bảo 99.9% uptime.', '3');

DROP TABLE IF EXISTS `dang_ky_dich_vu`;
CREATE TABLE `dang_ky_dich_vu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `khach_hang_id` bigint unsigned NOT NULL,
  `san_pham_id` bigint unsigned NOT NULL,
  `so_dien_thoai_dang_ky` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi_lap_dat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_dang_ky` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_kich_hoat_du_kien` date DEFAULT NULL,
  `trang_thai` enum('cho_xu_ly','dang_lap_dat','da_kich_hoat','tu_choi','huy') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cho_xu_ly',
  `nhan_vien_xu_ly_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `khach_hang_id` (`khach_hang_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `nhan_vien_xu_ly_id` (`nhan_vien_xu_ly_id`),
  CONSTRAINT `dang_ky_dich_vu_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`),
  CONSTRAINT `dang_ky_dich_vu_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`),
  CONSTRAINT `dang_ky_dich_vu_ibfk_3` FOREIGN KEY (`nhan_vien_xu_ly_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `danh_gia_san_pham`;
CREATE TABLE `danh_gia_san_pham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned DEFAULT NULL,
  `khach_hang_id` bigint unsigned DEFAULT NULL,
  `ho_ten_nguoi_danh_gia` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chuc_vu_cong_ty` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ten_dich_vu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `don_hang_chi_tiet_id` bigint unsigned DEFAULT NULL,
  `so_sao` tinyint NOT NULL,
  `tieu_de` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phan_hoi_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngay_phan_hoi_admin` datetime DEFAULT NULL,
  `hinh_anh_dinh_kem` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `trang_thai_duyet` enum('cho_duyet','da_duyet','tu_choi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cho_duyet',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `khach_hang_id` (`khach_hang_id`),
  KEY `don_hang_chi_tiet_id` (`don_hang_chi_tiet_id`),
  KEY `idx_danhgia_sanpham_duyet` (`san_pham_id`,`trang_thai_duyet`),
  CONSTRAINT `danh_gia_san_pham_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  CONSTRAINT `danh_gia_san_pham_ibfk_2` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `danh_gia_san_pham_ibfk_3` FOREIGN KEY (`don_hang_chi_tiet_id`) REFERENCES `don_hang_chi_tiet` (`id`) ON DELETE SET NULL,
  CONSTRAINT `danh_gia_san_pham_chk_1` CHECK ((`so_sao` between 1 and 5)),
  CONSTRAINT `danh_gia_san_pham_chk_2` CHECK (json_valid(`hinh_anh_dinh_kem`))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `danh_gia_san_pham` (`id`, `san_pham_id`, `khach_hang_id`, `ho_ten_nguoi_danh_gia`, `chuc_vu_cong_ty`, `ten_dich_vu`, `don_hang_chi_tiet_id`, `so_sao`, `tieu_de`, `noi_dung`, `phan_hoi_admin`, `ngay_phan_hoi_admin`, `hinh_anh_dinh_kem`, `trang_thai_duyet`, `created_at`) VALUES
('2', NULL, NULL, 'Thiên Long', 'CEO Vinafone', 'Bảo Mật & SmartCA', NULL, '5', 'Dịch vụ bảo mật', 'tốt', 'Cám ơn', '2026-08-07 13:24:20', NULL, 'da_duyet', '2026-08-06 09:26:38'),
('4', NULL, NULL, 'Trần Văn Bình', 'Vinfast', 'Bảo Mật & SmartCA', NULL, '5', 'Bảo mật', 'xuất sắc', 'Cám ơn quý khách', '2026-08-07 11:47:44', NULL, 'da_duyet', '2026-08-07 11:18:38'),
('5', NULL, '117', 'Đế Thiên', 'CEO Viettel', 'Combo SME', NULL, '5', 'Chất lượng dịch vụ', 'khá tốt', 'Cám ơn quý khách đã sử dụng dịch vụ', '2026-08-11 10:16:48', NULL, 'da_duyet', '2026-08-10 11:05:52'),
('7', NULL, NULL, 'Văn Chương', 'Nhân viên bán hàng', 'Bảo Mật & SmartCA', NULL, '5', 'Đánh giá chất lượng', 'Tạm', NULL, NULL, NULL, 'da_duyet', '2026-08-10 15:16:51'),
('8', NULL, NULL, 'Nguyễn Thị Lan', 'Mobifone', 'Cloud Enterprise', NULL, '5', 'Đánh giá dịch vụ', 'Tạm', 'cám ơn bạn', '2026-08-11 15:56:32', NULL, 'da_duyet', '2026-08-11 15:56:06');

DROP TABLE IF EXISTS `danh_muc_bai_viet`;
CREATE TABLE `danh_muc_bai_viet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `danh_muc_bai_viet` (`id`, `ten`, `slug`) VALUES
('1', 'Dịch vụ số', 'dich-vu-so'),
('2', 'Giải pháp doanh nghiệp', 'giai-phap-doanh-nghiep'),
('3', 'Hạ tầng Cloud & Server', 'ha-tang-cloud-server'),
('4', 'Thông cáo báo chí', 'thong-cao-bao-chi'),
('5', 'Tin tức công nghệ', 'tin-tuc-cong-nghe');

DROP TABLE IF EXISTS `danh_muc_san_pham`;
CREATE TABLE `danh_muc_san_pham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `danh_muc_cha_id` bigint unsigned DEFAULT NULL,
  `slug` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hinh_anh_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thu_tu_hien_thi` int DEFAULT '0',
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `danh_muc_cha_id` (`danh_muc_cha_id`),
  CONSTRAINT `danh_muc_san_pham_ibfk_1` FOREIGN KEY (`danh_muc_cha_id`) REFERENCES `danh_muc_san_pham` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `danh_muc_san_pham` (`id`, `ten_danh_muc`, `danh_muc_cha_id`, `slug`, `mo_ta`, `hinh_anh_url`, `thu_tu_hien_thi`, `trang_thai`) VALUES
('1', 'Hạ tầng & Cloud', NULL, 'ha-tang-cloud', 'Các dịch vụ Cloud, VPS, Server, Hạ tầng 5G', NULL, '1', '1'),
('2', 'Bảo mật an toàn', NULL, 'bao-mat-an-toan', 'Giải pháp an toàn thông tin, Firewall, SOC, Giám sát 24/7', NULL, '2', '1'),
('3', 'Ứng dụng doanh nghiệp', NULL, 'ung-dung-doanh-nghiep', 'Phần mềm quản trị, AI, Big Data, IoT, Chữ ký số, Hóa đơn điện tử', NULL, '3', '1'),
('4', 'Gói bảng giá Combo', NULL, 'goi-bang-gia-combo', 'Các gói tích hợp toàn diện theo quy mô doanh nghiệp', NULL, '4', '1'),
('5', 'Internet & Truyền hình', NULL, 'internet-truyen-hinh', 'Internet Cáp quang FiberVNN, Truyền hình MyTV, Data 4G/5G', NULL, '5', '1'),
('6', 'Thiết bị công nghệ', NULL, 'thiet-bi-cong-nghe', 'Thiết bị Router WiFi 6, Mesh, IP Camera, Switch', NULL, '6', '1');

DROP TABLE IF EXISTS `dia_chi_khach_hang`;
CREATE TABLE `dia_chi_khach_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `khach_hang_id` bigint unsigned NOT NULL,
  `ten_nguoi_nhan` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_dien_thoai` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tinh_thanh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quan_huyen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phuong_xa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dia_chi_chi_tiet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_dia_chi` enum('giao_hang','thanh_toan','ca_hai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ca_hai',
  `la_mac_dinh` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `khach_hang_id` (`khach_hang_id`),
  CONSTRAINT `dia_chi_khach_hang_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE `don_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_don_hang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `khach_hang_id` bigint unsigned NOT NULL,
  `dia_chi_giao_hang_id` bigint unsigned DEFAULT NULL,
  `tong_tien_hang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `phi_van_chuyen` decimal(15,2) DEFAULT '0.00',
  `giam_gia` decimal(15,2) DEFAULT '0.00',
  `tong_thanh_toan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ma_giam_gia_id` bigint unsigned DEFAULT NULL,
  `trang_thai_don_hang` enum('cho_xac_nhan','da_xac_nhan','dang_giao','hoan_thanh','da_huy') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cho_xac_nhan',
  `ghi_chu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_don_hang` (`ma_don_hang`),
  KEY `dia_chi_giao_hang_id` (`dia_chi_giao_hang_id`),
  KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
  KEY `idx_donhang_khachhang_trangthai` (`khach_hang_id`,`trang_thai_don_hang`),
  CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`),
  CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`dia_chi_giao_hang_id`) REFERENCES `dia_chi_khach_hang` (`id`) ON DELETE SET NULL,
  CONSTRAINT `don_hang_ibfk_3` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `don_hang` (`id`, `ma_don_hang`, `khach_hang_id`, `dia_chi_giao_hang_id`, `tong_tien_hang`, `phi_van_chuyen`, `giam_gia`, `tong_thanh_toan`, `ma_giam_gia_id`, `trang_thai_don_hang`, `ghi_chu`, `created_at`, `updated_at`) VALUES
('8', 'DH202608117372', '101', NULL, '2900000.00', '100000.00', '10000.00', '2990000.00', NULL, 'hoan_thanh', 'Thanh toán đơn hàng #DH306831', '2026-08-11 15:51:47', '2026-08-11 15:52:35'),
('9', 'DH202608119793', '101', NULL, '300000.00', '100000.00', '100000.00', '300000.00', NULL, 'hoan_thanh', 'Thanh toán đơn hàng #DH400306', '2026-08-11 15:53:21', '2026-08-11 16:07:54');

DROP TABLE IF EXISTS `don_hang_chi_tiet`;
CREATE TABLE `don_hang_chi_tiet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint unsigned NOT NULL,
  `san_pham_id` bigint unsigned NOT NULL,
  `bien_the_san_pham_id` bigint unsigned DEFAULT NULL,
  `ten_san_pham_snapshot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_luong` int NOT NULL,
  `don_gia` decimal(15,2) NOT NULL,
  `thanh_tien` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `bien_the_san_pham_id` (`bien_the_san_pham_id`),
  CONSTRAINT `don_hang_chi_tiet_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `don_hang_chi_tiet_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`),
  CONSTRAINT `don_hang_chi_tiet_ibfk_3` FOREIGN KEY (`bien_the_san_pham_id`) REFERENCES `bien_the_san_pham` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `don_hang_chi_tiet` (`id`, `don_hang_id`, `san_pham_id`, `bien_the_san_pham_id`, `ten_san_pham_snapshot`, `so_luong`, `don_gia`, `thanh_tien`) VALUES
('1', '1', '10', NULL, 'Gói Doanh nghiệp', '1', '2900000.00', '2900000.00'),
('2', '2', '9', NULL, 'Gói Cơ bản', '1', '990000.00', '990000.00'),
('3', '3', '20', NULL, 'Giải Pháp Tập Đoàn Multi-Cloud', '1', '15000000.00', '15000000.00'),
('4', '4', '21', NULL, 'Camera IP 360 độ 3MP EZVIZ H6c', '1', '690000.00', '690000.00'),
('5', '4', '19', NULL, 'Combo SME Chuyển Đổi Số', '1', '2900000.00', '2900000.00'),
('6', '4', '20', NULL, 'Giải Pháp Tập Đoàn Multi-Cloud', '1', '15000000.00', '15000000.00'),
('7', '5', '21', NULL, 'Camera IP 360 độ 3MP EZVIZ H6c', '1', '690000.00', '690000.00'),
('8', '6', '20', NULL, 'Giải Pháp Tập Đoàn Multi-Cloud', '1', '15000000.00', '15000000.00'),
('9', '7', '21', NULL, 'Camera IP 360 độ 3MP EZVIZ H6c', '1', '690000.00', '690000.00'),
('10', '7', '20', NULL, 'Giải Pháp Tập Đoàn Multi-Cloud', '1', '15000000.00', '15000000.00'),
('11', '8', '10', NULL, 'Gói Doanh nghiệp', '1', '2900000.00', '2900000.00'),
('12', '9', '70', NULL, 'Home 3 (2 Mesh)', '1', '300000.00', '300000.00');

DROP TABLE IF EXISTS `gia_tri_thuoc_tinh`;
CREATE TABLE `gia_tri_thuoc_tinh` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned NOT NULL,
  `thuoc_tinh_id` bigint unsigned NOT NULL,
  `gia_tri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `thuoc_tinh_id` (`thuoc_tinh_id`),
  CONSTRAINT `gia_tri_thuoc_tinh_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gia_tri_thuoc_tinh_ibfk_2` FOREIGN KEY (`thuoc_tinh_id`) REFERENCES `thuoc_tinh_san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `gio_hang`;
CREATE TABLE `gio_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `khach_hang_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `khach_hang_id` (`khach_hang_id`),
  CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gio_hang` (`id`, `khach_hang_id`, `session_id`, `created_at`) VALUES
('1', '101', NULL, '2026-07-20 09:18:47'),
('2', '111', NULL, '2026-08-06 09:13:04'),
('3', '102', NULL, '2026-08-07 11:17:45'),
('4', '113', NULL, '2026-08-07 16:04:46');

DROP TABLE IF EXISTS `gio_hang_chi_tiet`;
CREATE TABLE `gio_hang_chi_tiet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gio_hang_id` bigint unsigned NOT NULL,
  `san_pham_id` bigint unsigned NOT NULL,
  `bien_the_san_pham_id` bigint unsigned DEFAULT NULL,
  `so_luong` int NOT NULL DEFAULT '1',
  `don_gia_tai_thoi_diem` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gio_hang_id` (`gio_hang_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `bien_the_san_pham_id` (`bien_the_san_pham_id`),
  CONSTRAINT `gio_hang_chi_tiet_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gio_hang_chi_tiet_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`),
  CONSTRAINT `gio_hang_chi_tiet_ibfk_3` FOREIGN KEY (`bien_the_san_pham_id`) REFERENCES `bien_the_san_pham` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gio_hang_chi_tiet` (`id`, `gio_hang_id`, `san_pham_id`, `bien_the_san_pham_id`, `so_luong`, `don_gia_tai_thoi_diem`) VALUES
('7', '3', '11', NULL, '1', '7500000.00'),
('16', '4', '62', NULL, '1', '1500000.00'),
('17', '4', '63', NULL, '1', '1500000.00'),
('18', '4', '64', NULL, '1', '1500000.00'),
('19', '4', '13', NULL, '1', '3500000.00'),
('20', '4', '12', NULL, '1', '1800000.00'),
('21', '4', '16', NULL, '1', '3200000.00');

DROP TABLE IF EXISTS `goi_cuoc_di_dong`;
CREATE TABLE `goi_cuoc_di_dong` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned NOT NULL,
  `loai_thue_bao` enum('tra_truoc','tra_sau') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dung_luong_data_mb` int DEFAULT NULL,
  `so_phut_goi_noi_mang` int DEFAULT NULL,
  `so_phut_goi_ngoai_mang` int DEFAULT NULL,
  `so_sms` int DEFAULT NULL,
  `chu_ky_ngay` int DEFAULT '30',
  `gia_han_tu_dong` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `san_pham_id` (`san_pham_id`),
  CONSTRAINT `goi_cuoc_di_dong_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `goi_internet_truyen_hinh`;
CREATE TABLE `goi_internet_truyen_hinh` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned NOT NULL,
  `cong_nghe` enum('FTTH','ADSL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'FTTH',
  `toc_do_download_mbps` int DEFAULT NULL,
  `toc_do_upload_mbps` int DEFAULT NULL,
  `co_truyen_hinh_mytv` tinyint(1) DEFAULT '0',
  `so_kenh_truyen_hinh` int DEFAULT NULL,
  `doi_tuong` enum('ho_gia_dinh','doanh_nghiep') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ho_gia_dinh',
  PRIMARY KEY (`id`),
  UNIQUE KEY `san_pham_id` (`san_pham_id`),
  CONSTRAINT `goi_internet_truyen_hinh_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hinh_anh_san_pham`;
CREATE TABLE `hinh_anh_san_pham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `la_anh_dai_dien` tinyint(1) DEFAULT '0',
  `thu_tu` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`),
  CONSTRAINT `hinh_anh_san_pham_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `khach_hang`;
CREATE TABLE `khach_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tai_khoan_id` bigint unsigned NOT NULL,
  `ho_ten` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_dien_thoai` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('nam','nu','khac') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `anh_dai_dien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tai_khoan_id` (`tai_khoan_id`),
  UNIQUE KEY `so_dien_thoai` (`so_dien_thoai`),
  CONSTRAINT `khach_hang_ibfk_1` FOREIGN KEY (`tai_khoan_id`) REFERENCES `tai_khoan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `khach_hang` (`id`, `tai_khoan_id`, `ho_ten`, `so_dien_thoai`, `ngay_sinh`, `gioi_tinh`, `avatar_url`, `created_at`, `updated_at`, `anh_dai_dien`) VALUES
('101', '103', 'Nguyễn Thị Lan', '0901234568', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-11 16:06:20', NULL),
('102', '104', 'Trần Văn Bình', '0912345678', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('103', '105', 'Lê Thị Hương', '0923456789', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('104', '106', 'Phạm Minh Tuấn', '0934567890', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('105', '107', 'Hoàng Thị Mai', '0945678901', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('106', '108', 'Đặng Văn Hùng', '0956789012', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('107', '109', 'Bùi Thị Ngọc', '0967890123', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('108', '110', 'Vũ Minh Khoa', '0978901234', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('109', '111', 'Ngô Thị Thu', '0989012345', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('110', '112', 'Đinh Văn Phúc', '0990123456', NULL, NULL, NULL, '2026-07-23 00:00:00', '2026-08-07 11:07:16', NULL),
('111', '113', 'Thiên Đạt', '0325086642', NULL, NULL, NULL, '2026-08-06 09:12:51', '2026-08-07 11:07:16', NULL),
('113', '115', 'Văn Chương', '0325086645', NULL, NULL, NULL, '2026-08-07 16:04:46', '2026-08-07 16:04:46', NULL),
('116', '118', 'Đế Thiên', NULL, NULL, NULL, NULL, '2026-08-10 10:19:23', '2026-08-10 10:19:23', NULL),
('117', '119', 'Đế Thiên', NULL, NULL, NULL, NULL, '2026-08-10 10:33:12', '2026-08-10 10:33:12', NULL);

DROP TABLE IF EXISTS `kho_hang`;
CREATE TABLE `kho_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_kho` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tinh_thanh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `khu_vuc_phu_song`;
CREATE TABLE `khu_vuc_phu_song` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `san_pham_id` bigint unsigned NOT NULL,
  `tinh_thanh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quan_huyen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`),
  CONSTRAINT `khu_vuc_phu_song_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `khuyen_mai`;
CREATE TABLE `khuyen_mai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_chuong_trinh` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `loai_giam` enum('phan_tram','tien_mat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri_giam` decimal(15,2) NOT NULL,
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `lich_su_ap_dung_khuyen_mai`;
CREATE TABLE `lich_su_ap_dung_khuyen_mai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint unsigned NOT NULL,
  `khuyen_mai_id` bigint unsigned NOT NULL,
  `ngay_ap_dung` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `khuyen_mai_id` (`khuyen_mai_id`),
  CONSTRAINT `lich_su_ap_dung_khuyen_mai_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lich_su_ap_dung_khuyen_mai_ibfk_2` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `lich_su_nhan_vien`;
CREATE TABLE `lich_su_nhan_vien` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nhan_vien_id` bigint unsigned NOT NULL,
  `hanh_dong` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thoi_gian` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nhan_vien_id` (`nhan_vien_id`),
  CONSTRAINT `lich_su_nhan_vien_ibfk_1` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_vien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lich_su_nhan_vien` (`id`, `nhan_vien_id`, `hanh_dong`, `thoi_gian`) VALUES
('34', '1', 'Đã gửi thông báo \'Khuyến mãi tháng 8\' tới 12 khách hàng', '2026-08-10 09:09:49'),
('35', '1', 'Đã tạo sản phẩm mới ID #70 (\'Home 3 (2 Mesh)\')', '2026-08-10 11:32:06'),
('36', '1', 'Đã gửi phản hồi nhận xét ID #5', '2026-08-11 10:16:48'),
('37', '1', 'Đã gửi phản hồi cho tin nhắn #4', '2026-08-11 15:48:55'),
('38', '1', 'Đã điều chỉnh hóa đơn ID 8 (trạng thái: hoan_thanh)', '2026-08-11 15:52:35'),
('39', '1', 'Đã điều chỉnh hóa đơn ID 8 (trạng thái: hoan_thanh)', '2026-08-11 15:53:25'),
('40', '1', 'Đã gửi phản hồi nhận xét ID #8', '2026-08-11 15:56:32'),
('41', '1', 'Đã gửi thông báo \'Khuyến mãi tháng 8\' tới 1 khách hàng', '2026-08-11 15:58:21'),
('42', '1', 'Đã gửi thông báo \'Khuyến mãi tháng 8\' tới 1 khách hàng', '2026-08-11 15:58:41'),
('43', '1', 'Cập nhật thông tin hồ sơ & ảnh đại diện', '2026-08-11 16:03:50'),
('44', '1', 'Đã điều chỉnh hóa đơn ID 9 (trạng thái: hoan_thanh)', '2026-08-11 16:07:54');

DROP TABLE IF EXISTS `lich_su_trang_thai_don_hang`;
CREATE TABLE `lich_su_trang_thai_don_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint unsigned NOT NULL,
  `trang_thai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ghi_chu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nhan_vien_id` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `nhan_vien_id` (`nhan_vien_id`),
  CONSTRAINT `lich_su_trang_thai_don_hang_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lich_su_trang_thai_don_hang_ibfk_2` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lich_su_trang_thai_don_hang` (`id`, `don_hang_id`, `trang_thai`, `ghi_chu`, `nhan_vien_id`, `created_at`) VALUES
('13', '8', 'cho_xac_nhan', 'Khách hàng đặt hàng', NULL, '2026-08-11 15:51:47'),
('14', '8', 'hoan_thanh', 'Cập nhật bởi quản trị viên', '1', '2026-08-11 15:52:35'),
('15', '9', 'cho_xac_nhan', 'Khách hàng đặt hàng', NULL, '2026-08-11 15:53:21'),
('16', '9', 'hoan_thanh', 'Cập nhật bởi quản trị viên', '1', '2026-08-11 16:07:54');

DROP TABLE IF EXISTS `ma_giam_gia`;
CREATE TABLE `ma_giam_gia` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_giam` enum('phan_tram','tien_mat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri_giam` decimal(15,2) NOT NULL,
  `gia_tri_don_toi_thieu` decimal(15,2) NOT NULL,
  `so_luong_gioi_han` int NOT NULL,
  `so_luong_da_dung` int DEFAULT '0',
  `ngay_het_han` datetime DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_code` (`ma_code`),
  CONSTRAINT `ma_giam_gia_chk_1` CHECK ((`gia_tri_giam` >= 0)),
  CONSTRAINT `ma_giam_gia_chk_2` CHECK ((`gia_tri_don_toi_thieu` >= 0)),
  CONSTRAINT `ma_giam_gia_chk_3` CHECK ((`so_luong_gioi_han` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ma_giam_gia` (`id`, `ma_code`, `loai_giam`, `gia_tri_giam`, `gia_tri_don_toi_thieu`, `so_luong_gioi_han`, `so_luong_da_dung`, `ngay_het_han`, `trang_thai`) VALUES
('1', 'VNPT10', 'phan_tram', '10.00', '1000000.00', '100', '0', '2026-12-31 23:59:59', '1'),
('2', 'VNPT20', 'phan_tram', '20.00', '2000000.00', '50', '0', '2026-12-31 23:59:59', '1'),
('3', 'GIAM500K', 'tien_mat', '500000.00', '5000000.00', '20', '0', '2026-12-31 23:59:59', '1');

DROP TABLE IF EXISTS `ma_xac_nhan_otp`;
CREATE TABLE `ma_xac_nhan_otp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ma_otp` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('frontend','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'frontend',
  `het_han_luc` datetime NOT NULL,
  `da_su_dung` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `ma_otp` (`ma_otp`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ma_xac_nhan_otp` (`id`, `email`, `ma_otp`, `loai`, `het_han_luc`, `da_su_dung`, `created_at`) VALUES
('12', 'skyvalkyrie08@gmail.com', '586783', 'frontend', '2026-08-10 03:44:10', '0', '2026-08-10 10:34:10');

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_menu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `menu_cha_id` bigint unsigned DEFAULT NULL,
  `thu_tu` int DEFAULT '0',
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `menu_cha_id` (`menu_cha_id`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`menu_cha_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menu` (`id`, `ten_menu`, `slug`, `link`, `menu_cha_id`, `thu_tu`, `trang_thai`) VALUES
('1', 'Về chúng tôi', 'gioi-thieu', '#', NULL, '1', '1'),
('2', 'Dịch vụ', 'cloud-computing', '#', NULL, '2', '1'),
('3', 'Giải pháp', 'gp-sme', '#', NULL, '3', '1'),
('4', 'Bảng giá', '', '#pricing', NULL, '4', '1'),
('5', 'Hệ sinh thái', 'he-sinh-thai', '#', NULL, '5', '1'),
('6', 'Tin tức', 'thong-cao-bao-chi', '#', NULL, '6', '1'),
('7', 'Đối tác', 'doi-tac', '#', NULL, '7', '1'),
('8', 'Liên hệ', '', '#contact', NULL, '8', '1'),
('9', 'Giới thiệu', 'gioi-thieu', '#', '1', '1', '1'),
('10', 'Tầm nhìn & Sứ mệnh', 'tam-nhin-su-menh', '#', '1', '2', '1'),
('11', 'Đội ngũ lãnh đạo', 'doi-ngu-lanh-dao', '#', '1', '3', '1'),
('12', 'Thành tựu', 'thanh-tuu', '#', '1', '4', '1'),
('13', 'Hạ tầng số', 'ha-tang-so', '#', '2', '1', '1'),
('14', 'Bảo mật & An toàn', 'bao-mat-an-toan', '#', '2', '2', '1'),
('15', 'Cloud Computing', 'cloud-computing', '#', '2', '3', '1'),
('16', 'AI & Tự động hóa', 'ai-tu-dong-hoa', '#', '2', '4', '1'),
('17', 'Doanh nghiệp vừa & nhỏ', 'gp-sme', '#', '3', '1', '1'),
('18', 'Tập đoàn lớn', 'gp-enterprise', '#', '3', '2', '1'),
('19', 'Chính phủ số', 'gp-chinh-phu', '#', '3', '3', '1'),
('20', 'Y tế & Giáo dục', 'gp-yte-giaoduc', '#', '3', '4', '1'),
('21', 'Thông cáo báo chí', 'thong-cao-bao-chi', '#', '6', '1', '1'),
('22', 'Blog công nghệ', 'blog-cong-nghe', '#', '6', '2', '1'),
('23', 'Sự kiện', 'su-kien', '#', '6', '3', '1');

DROP TABLE IF EXISTS `nha_cung_cap`;
CREATE TABLE `nha_cung_cap` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ma_so_thue` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt_lien_he` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_so_thue` (`ma_so_thue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `nhan_vien`;
CREATE TABLE `nhan_vien` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tai_khoan_id` bigint unsigned NOT NULL,
  `ho_ten` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tai_khoan_id` (`tai_khoan_id`),
  CONSTRAINT `nhan_vien_ibfk_1` FOREIGN KEY (`tai_khoan_id`) REFERENCES `tai_khoan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nhan_vien` (`id`, `tai_khoan_id`, `ho_ten`, `created_at`, `updated_at`) VALUES
('1', '1', 'Quản trị viên', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('2', '2', 'Trần Thị Bích Ngọc', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('3', '3', 'Lê Minh Huy', '2026-07-08 08:11:23', '2026-08-07 11:07:16');

DROP TABLE IF EXISTS `phan_hoi_ho_tro`;
CREATE TABLE `phan_hoi_ho_tro` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `yeu_cau_ho_tro_id` bigint unsigned NOT NULL,
  `tai_khoan_id` bigint unsigned NOT NULL COMMENT 'Người gửi phản hồi - loại (nhân viên/khách hàng) tra qua tai_khoan.loai_tai_khoan',
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `yeu_cau_ho_tro_id` (`yeu_cau_ho_tro_id`),
  KEY `tai_khoan_id` (`tai_khoan_id`),
  CONSTRAINT `phan_hoi_ho_tro_ibfk_1` FOREIGN KEY (`yeu_cau_ho_tro_id`) REFERENCES `yeu_cau_ho_tro` (`id`) ON DELETE CASCADE,
  CONSTRAINT `phan_hoi_ho_tro_ibfk_2` FOREIGN KEY (`tai_khoan_id`) REFERENCES `tai_khoan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phan_hoi_ho_tro` (`id`, `yeu_cau_ho_tro_id`, `tai_khoan_id`, `noi_dung`, `created_at`) VALUES
('1', '3', '1', 'tốt', '2026-08-07 11:46:41'),
('2', '3', '2', 'Chào anh Văn Chương, tư vấn viên VNPT (Biên tập viên Trần Thị Bích Ngọc) đã tiếp nhận yêu cầu giải pháp AI & Bảo mật của anh và sẽ liên hệ hỗ trợ ngay!', '2026-08-07 12:26:44'),
('3', '4', '1', 'cám ơn quý khách', '2026-08-11 15:48:55');

DROP TABLE IF EXISTS `phuong_thuc_thanh_toan`;
CREATE TABLE `phuong_thuc_thanh_toan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phuong_thuc_thanh_toan` (`id`, `ten`, `trang_thai`) VALUES
('1', 'Thanh toán khi nhận hàng (COD)', '1'),
('2', 'Chuyển khoản ngân hàng', '1'),
('3', 'Ví điện tử / VNPAY', '1');

DROP TABLE IF EXISTS `phuong_thuc_van_chuyen`;
CREATE TABLE `phuong_thuc_van_chuyen` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phi_co_ban` decimal(15,2) DEFAULT '0.00',
  `thoi_gian_du_kien` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phuong_thuc_van_chuyen` (`id`, `ten`, `phi_co_ban`, `thoi_gian_du_kien`) VALUES
('1', 'Kích hoạt trực tuyến', '0.00', 'Trong 24h'),
('2', 'Nhân viên tư vấn liên hệ', '0.00', '1-2 ngày làm việc');

DROP TABLE IF EXISTS `quyen_han`;
CREATE TABLE `quyen_han` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_quyen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_quyen` (`ma_quyen`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `quyen_han` (`id`, `ma_quyen`, `mo_ta`) VALUES
('1', 'quan_ly_san_pham', 'Thêm/sửa/xóa sản phẩm, danh mục'),
('2', 'quan_ly_don_hang', 'Xử lý và cập nhật trạng thái đơn hàng'),
('3', 'quan_ly_noi_dung', 'Biên tập bài viết, trang tĩnh, menu'),
('4', 'quan_ly_nguoi_dung', 'Quản lý tài khoản nhân viên/khách hàng'),
('5', 'mua_hang', 'Đặt hàng, thanh toán trên website'),
('6', 'danh_gia_san_pham', 'Viết đánh giá sản phẩm đã mua');

DROP TABLE IF EXISTS `san_pham`;
CREATE TABLE `san_pham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_san_pham` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ten_san_pham` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `danh_muc_id` bigint unsigned NOT NULL,
  `loai_san_pham` enum('goi_cuoc_di_dong','goi_internet_truyen_hinh','thiet_bi','dich_vu_so','combo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thuong_hieu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nha_cung_cap_id` bigint unsigned DEFAULT NULL,
  `mo_ta_ngan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mo_ta_chi_tiet` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thong_so_ky_thuat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gia_niem_yet` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gia_khuyen_mai` decimal(15,2) DEFAULT NULL,
  `don_vi_tinh` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sÃŸâ•‘Ãºn phÃŸâ•‘âŒm',
  `hinh_anh_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `co_quan_ly_ton_kho` tinyint(1) DEFAULT '0',
  `trang_thai` enum('dang_ban','ngung_ban','sap_ra_mat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'dang_ban',
  `luot_xem` int DEFAULT '0',
  `luot_ban` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_san_pham` (`ma_san_pham`),
  UNIQUE KEY `slug` (`slug`),
  KEY `nha_cung_cap_id` (`nha_cung_cap_id`),
  KEY `idx_san_pham_danhmuc_trangthai` (`danh_muc_id`,`trang_thai`),
  KEY `idx_san_pham_loai` (`loai_san_pham`),
  FULLTEXT KEY `ft_ten_san_pham` (`ten_san_pham`),
  CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc_san_pham` (`id`),
  CONSTRAINT `san_pham_ibfk_2` FOREIGN KEY (`nha_cung_cap_id`) REFERENCES `nha_cung_cap` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `san_pham` (`id`, `ma_san_pham`, `ten_san_pham`, `slug`, `danh_muc_id`, `loai_san_pham`, `thuong_hieu`, `nha_cung_cap_id`, `mo_ta_ngan`, `mo_ta_chi_tiet`, `thong_so_ky_thuat`, `gia_niem_yet`, `gia_khuyen_mai`, `don_vi_tinh`, `hinh_anh_url`, `co_quan_ly_ton_kho`, `trang_thai`, `luot_xem`, `luot_ban`, `created_at`, `updated_at`) VALUES
('1', 'svc-001', 'Cloud Computing', 'cloud-computing', '1', 'dich_vu_so', 'VNPT', NULL, 'Hạ tầng điện toán đám mây linh hoạt, mở rộng theo nhu cầu.', NULL, NULL, '2500000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('2', 'svc-002', 'Bảo mật & An toàn số', 'bao-mat-an-toan-so', '1', 'dich_vu_so', 'VNPT', NULL, 'Giải pháp bảo mật toàn diện, giám sát 24/7.', NULL, NULL, '1800000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('3', 'svc-003', 'AI & Tự động hóa', 'ai-tu-dong-hoa', '1', 'dich_vu_so', 'VNPT', NULL, 'Ứng dụng trí tuệ nhân tạo tự động hóa quy trình nghiệp vụ.', NULL, NULL, '3200000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('4', 'svc-004', 'Hạ tầng mạng 5G', 'ha-tang-mang-5g', '1', 'dich_vu_so', 'VNPT', NULL, 'Kết nối 5G/SD-WAN tốc độ cao, độ trễ thấp cho doanh nghiệp.', NULL, NULL, '4500000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('5', 'svc-005', 'Quản trị doanh nghiệp số', 'quan-tri-doanh-nghiep-so', '1', 'dich_vu_so', 'VNPT', NULL, 'Nền tảng quản trị, điều hành doanh nghiệp trên môi trường số.', NULL, NULL, '990000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('6', 'svc-006', 'Giao tiếp & Cộng tác', 'giao-tiep-cong-tac', '1', 'dich_vu_so', 'VNPT', NULL, 'Hội họp trực tuyến, cộng tác nhóm bảo mật.', NULL, NULL, '750000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('7', 'svc-007', 'Big Data & Phân tích', 'big-data-phan-tich', '1', 'dich_vu_so', 'VNPT', NULL, 'Thu thập, phân tích dữ liệu lớn hỗ trợ ra quyết định.', NULL, NULL, '2100000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('8', 'svc-008', 'IoT & Smart City', 'iot-smart-city', '1', 'dich_vu_so', 'VNPT', NULL, 'Kết nối vạn vật, giải pháp đô thị thông minh.', NULL, NULL, '1500000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('9', 'pkg-basic', 'Gói Cơ bản', 'goi-co-ban', '2', 'combo', 'VNPT', NULL, 'Gói khởi đầu cho doanh nghiệp nhỏ.', NULL, NULL, '990000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('10', 'pkg-business', 'Gói Doanh nghiệp', 'goi-doanh-nghiep', '2', 'combo', 'VNPT', NULL, 'Gói phổ biến cho doanh nghiệp vừa.', NULL, NULL, '2900000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('11', 'pkg-premium', 'Gói Cao cấp', 'goi-cao-cap', '2', 'combo', 'VNPT', NULL, 'Giải pháp toàn diện cho tập đoàn lớn.', NULL, NULL, '7500000.00', NULL, 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-07-08 08:11:23', '2026-08-07 11:07:16'),
('12', 'cloud-sme', 'Cloud Doanh Nghiệp (SME)', 'cloud-doanh-nghiep-sme', '1', 'dich_vu_so', 'VNPT Cloud', NULL, 'Phù hợp cho Website doanh nghiệp & CSDL vừa. 2 vCPU, 4GB RAM, 80GB SSD NVMe.', 'Dịch vụ máy chủ ảo VNPT Cloud Doanh Nghiệp (SME) cung cấp hạ tầng điện toán đám mây tốc độ cao, độ an toàn bảo mật tuyệt đối với ổ cứng SSD NVMe Enterprise. Phù hợp cho website tin tức, thương mại điện tử và hệ thống cơ sở dữ liệu vừa và nhỏ.', NULL, '1800000.00', '1800000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-10 10:26:23'),
('13', 'cloud-enterprise', 'Cloud Enterprise', 'cloud-enterprise', '1', 'dich_vu_so', 'VNPT Cloud', NULL, 'Tối ưu cho hệ thống ERP & Ứng dụng tải cao. 4 vCPU, 8GB RAM, 160GB SSD NVMe.', 'Gói Cloud Enterprise được thiết kế cho các ứng dụng ERP, CRM và các sàn thương mại điện tử tải cao. Tích hợp sẵn cơ chế Auto Snapshot sao lưu dữ liệu tự động hằng ngày và tường lửa VPS phòng chống xâm nhập.', NULL, '2500000.00', '3500000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-10 10:26:23'),
('14', 'cloud-high-spec', 'Cloud High-Spec (Y tế/Giáo dục)', 'cloud-high-spec', '1', 'dich_vu_so', 'VNPT Cloud', NULL, 'Cấu hình cực mạnh cho bệnh viện & trường học. 16 vCPU, 32GB RAM, 500GB SSD NVMe.', 'Gói Cloud High-Spec đáp ứng các nhu cầu xử lý dữ liệu y tế, bệnh viện thông minh (HIS/LIS/PACS), trường học trực tuyến và hệ thống tài chính lớn với hạ tầng Multi-Region chịu lỗi tuyệt đối.', NULL, '7500000.00', '7500000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-10 10:26:23'),
('15', 'vnpt-5g-private', 'Hạ tầng Mạng 5G Private', 'ha-tang-mang-5g-private', '1', 'dich_vu_so', 'VNPT 5G', NULL, 'Mạng 5G riêng biệt chuyên dụng cho nhà máy thông minh, khu công nghiệp và cảng biển. Độ trễ siêu thấp <1ms, băng thông cực đại 10Gbps.', 'VNPT 5G Private Network cung cấp hạ tầng kết nối không dây tốc độ cao chuyên dụng cho các tổ chức, nhà máy thông minh Smart Factory, tự động hóa robot và giám sát cảm biến IoT siêu tốc.', NULL, '5000000.00', '4500000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-07 11:11:03'),
('16', 'vnpt-ai-ocr', 'VNPT AI OCR & Tự động hóa', 'vnpt-ai-ocr', '3', 'dich_vu_so', 'VNPT AI', NULL, 'Bóc tách dữ liệu giấy tờ chính xác 99.8%. Tự động hóa quy trình nghiệp vụ.', 'Công nghệ VNPT AI OCR ứng dụng trí tuệ nhân tạo tiên tiến nhất để số hóa toàn bộ tài liệu, giấy tờ định danh và hóa đơn chứng từ. Tốc độ nhận diện < 0.5 giây/trang với độ chính xác đạt 99.8%.', NULL, '3200000.00', '3200000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-10 10:26:23'),
('17', 'vnpt-soc-247', 'VNPT Bảo mật & An ninh mạng SOC 24/7', 'vnpt-bao-mat-soc-247', '2', 'dich_vu_so', 'VNPT Cyber Security', NULL, 'Trung tâm điều hành an ninh mạng SOC chuyên nghiệp. Chống tấn công WAF, phòng chống DDoS 100Gbps, giám sát độc hại và cảnh báo sự cố 24/7.', 'Dịch vụ giám sát an toàn thông tin VNPT SOC 24/7 bảo vệ tối đa các ứng dụng web và hệ thống công nghệ thông tin trước nguy cơ tấn công từ chối dịch vụ DDoS, mã độc tống tiền Ransomware và lỗ hổng bảo mật khẩn cấp.', NULL, '2200000.00', '1800000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-07 11:11:03'),
('18', 'vnpt-smartca', 'VNPT SmartCA Ký Số Từ Xa', 'vnpt-smartca-ky-so-tu-xa', '2', 'dich_vu_so', 'VNPT CA', NULL, 'Giải pháp chữ ký số từ xa không cần USB Token. Đạt tiêu chuẩn FIPS 140-2 Level 3, tích hợp ký hợp đồng trực tuyến và khai thuế điện tử.', 'VNPT SmartCA cho phép cá nhân và doanh nghiệp ký số điện tử trên các thiết bị di động, smartphone mọi lúc mọi nơi mà không cần thiết bị phần cứng USB Token. Đạt chứng nhận an toàn FIPS 140-2 Level 3 khắt khe.', NULL, '1200000.00', '990000.00', 'năm', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-07 11:11:03'),
('19', 'combo-sme', 'Combo SME Chuyển Đổi Số', 'combo-sme-chuyen-doi-so', '4', 'combo', 'VNPT SME', NULL, 'Gói tích hợp toàn diện cho Doanh nghiệp vừa & nhỏ: Cloud Server + SmartCA Ký số + Hóa đơn điện tử VNPT Invoice + Phần mềm Kế toán.', 'Gói Combo SME là bộ giải pháp số hóa đồng bộ dành cho các doanh nghiệp vừa và nhỏ, giúp tối ưu chi phí vận hành lên tới 40% so với mua lẻ từng dịch vụ.', NULL, '3500000.00', '2900000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-07 11:11:03'),
('20', 'giai-phap-tap-doan', 'Giải Pháp Tập Đoàn Multi-Cloud', 'giai-phap-tap-doan-multi-cloud', '4', 'combo', 'VNPT Enterprise', NULL, 'Hệ sinh thái hạ tầng đám mây dùng riêng Private Cloud, kết nối Multi-Region, tư vấn kiến trúc giải pháp và hỗ trợ hạ tầng Dedicated 24/7.', 'Giải pháp điện toán đám mây thiết kế riêng cho các tập đoàn tổng công ty lớn, ngân hàng và tổ chức tài chính với cam kết SLA 99.999% và đội ngũ chuyên gia hạ tầng đồng hành 24/7.', NULL, '18000000.00', '15000000.00', 'tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-07 10:28:39', '2026-08-07 11:11:03'),
('21', 'cam-ezviz-h6c', 'Camera IP 360 độ 3MP EZVIZ H6c', 'camera-ip-360-do-3mp-ezviz-h6c', '6', 'thiet_bi', 'EZVIZ', NULL, 'Camera thông minh xoay 360 độ độ phân giải 3MP', 'Camera wifi thông minh EZVIZ H6c 3MP độ phân giải 2K sắc nét', '<div class=\"specs-group-block\">\r\n  <h5 class=\"specs-group-header\"><i class=\"fa-solid fa-layer-group\"></i> Thông số &amp; Đặc tính kỹ thuật</h5>\r\n  <table class=\"specs-detail-table\">\r\n    <tr><th>Màn hình:</th><td>3MP (2K)</td></tr>\r\n    <tr><th>Kết nối:</th><td>Wi-Fi 2.4GHz</td></tr>\r\n    <tr><th>Lưu trữ:</th><td>Thẻ nhớ MicroSD 256GB</td></tr>\r\n  </table>\r\n</div>', '850000.00', '690000.00', 'thiết bị', 'assets/images/uploads/products/prod_1786078941_6896.jpg', '0', 'dang_ban', '0', '0', '2026-08-07 11:57:51', '2026-08-07 12:02:21'),
('23', 'cloud-doanh-nghip-sme', 'Cloud Doanh Nghiệp (SME)', 'cloud-doanh-nghip-sme', '1', 'dich_vu_so', NULL, NULL, 'Phù hợp cho Website & App doanh nghiệp dưới 5.000 user/ngày', '2 vCPU High Performance, 4 GB RAM ECC, 80 GB SSD NVMe Enterprise, IP Tĩnh được miễn phí, Cam kết Uptime SLA SLA 99.99%.', NULL, '1800000.00', '1800000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('27', 'vnpt-ai-ocr--t-ng-ha', 'VNPT AI OCR & Tự động hóa', 'vnpt-ai-ocr--t-ng-ha', '3', 'dich_vu_so', NULL, NULL, 'Bóc tách dữ liệu giấy tờ chính xác 99.9%', 'Nhận dạng CCCD, Hóa đơn, Đăng ký kinh doanh, Bằng lái xe tự động trong 0.5s.', NULL, '3200000.00', '3200000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('28', 'vnpt-soc-security', 'VNPT Bảo mật & An ninh mạng SOC 24/7', 'vnpt-soc-security', '3', 'dich_vu_so', NULL, NULL, 'Chống tấn công WAF & Anti-DDoS 100Gbps. Giám sát an toàn thông tin 24/7.', 'Giám sát an toàn thông tin 24/7/365, ngăn chặn tấn công DDoS, Ransomware và ứng cứu sự cố tức thì.', NULL, '1800000.00', '1800000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 10:26:23'),
('29', 'vnpt-bo-mt--an-ninh-mng-soc-247', 'VNPT Bảo mật & An ninh mạng SOC 24/7', 'vnpt-bo-mt--an-ninh-mng-soc-247', '3', 'dich_vu_so', NULL, NULL, 'Trung tâm vận hành An ninh mạng SOC 24/7', 'Giám sát an toàn thông tin 24/7/365, ngăn chặn tấn công DDoS, Ransomware và ứng cứu sự cố tức thì.', NULL, '1800000.00', '1800000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('30', 'smartca-doanh-nghiep', 'Chữ ký số VNPT SmartCA Doanh Nghiệp', 'smartca-doanh-nghiep', '2', 'dich_vu_so', NULL, NULL, 'Ký số từ xa không cần thiết bị USB Token', 'Hỗ trợ ký số hợp đồng, kê khai thuế, bảo hiểm xã hội trên Smartphone/Tablet/PC.', NULL, '1200000.00', '1200000.00', 'Năm', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('31', 'ch-k-s-vnpt-smartca-doanh-nghip', 'Chữ ký số VNPT SmartCA Doanh Nghiệp', 'ch-k-s-vnpt-smartca-doanh-nghip', '2', 'dich_vu_so', NULL, NULL, 'Ký số từ xa không cần thiết bị USB Token', 'Hỗ trợ ký số hợp đồng, kê khai thuế, bảo hiểm xã hội trên Smartphone/Tablet/PC.', NULL, '1200000.00', '1200000.00', 'Năm', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('32', 'einvoice-startup', 'Hóa đơn điện tử VNPT iInvoice (Khởi nghiệp)', 'einvoice-startup', '2', 'dich_vu_so', NULL, NULL, 'Gói 500 hóa đơn điện tử cho doanh nghiệp nhỏ', 'Tích hợp sẵn Tổng cục Thuế, miễn phí nộp tờ khai và chữ ký số ký hóa đơn.', NULL, '850000.00', '850000.00', 'Gói', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('33', 'ha-n-in-t-vnpt-iinvoice-khi-nghip', 'Hóa đơn điện tử VNPT iInvoice (Khởi nghiệp)', 'ha-n-in-t-vnpt-iinvoice-khi-nghip', '2', 'dich_vu_so', NULL, NULL, 'Gói 500 hóa đơn điện tử cho doanh nghiệp nhỏ', 'Tích hợp sẵn Tổng cục Thuế, miễn phí nộp tờ khai và chữ ký số ký hóa đơn.', NULL, '850000.00', '850000.00', 'Gói', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('34', 'einvoice-pro', 'Hóa đơn điện tử VNPT iInvoice (Chuyên nghiệp)', 'einvoice-pro', '2', 'dich_vu_so', NULL, NULL, 'Gói 2.000 hóa đơn điện tử kèm mẫu hóa đơn riêng', 'Hỗ trợ kết nối API với phần mềm kế toán MISA, Bravo, FAST.', NULL, '2200000.00', '2200000.00', 'Gói', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('35', 'ha-n-in-t-vnpt-iinvoice-chuyn-nghip', 'Hóa đơn điện tử VNPT iInvoice (Chuyên nghiệp)', 'ha-n-in-t-vnpt-iinvoice-chuyn-nghip', '2', 'dich_vu_so', NULL, NULL, 'Gói 2.000 hóa đơn điện tử kèm mẫu hóa đơn riêng', 'Hỗ trợ kết nối API với phần mềm kế toán MISA, Bravo, FAST.', NULL, '2200000.00', '2200000.00', 'Gói', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('36', 'fiber-vnn-business', 'Cáp quang FiberVNN Doanh Nghiệp 500Mbps', 'fiber-vnn-business', '4', 'goi_internet_truyen_hinh', NULL, NULL, 'Tốc độ 500Mbps kèm 1 IP Tĩnh miễn phí', 'Băng thông quốc tế tối thiểu 10.8Mbps, trang bị Modem Wi-Fi 6 thế hệ mới.', NULL, '990000.00', '990000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('37', 'cp-quang-fibervnn-doanh-nghip-500mbps', 'Cáp quang FiberVNN Doanh Nghiệp 500Mbps', 'cp-quang-fibervnn-doanh-nghip-500mbps', '4', 'goi_internet_truyen_hinh', NULL, NULL, 'Tốc độ 500Mbps kèm 1 IP Tĩnh miễn phí', 'Băng thông quốc tế tối thiểu 10.8Mbps, trang bị Modem Wi-Fi 6 thế hệ mới.', NULL, '990000.00', '990000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('38', 'metronet-private-link', 'Mạng truyền số liệu chuyên dùng Metronet', 'metronet-private-link', '4', 'goi_internet_truyen_hinh', NULL, NULL, 'Kênh thuê riêng bảo mật tuyệt đối', 'Kết nối mạng diện rộng WAN giữa trụ sở và các chi nhánh ngân hàng, tổ chức tài chính.', NULL, '5500000.00', '5500000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('39', 'mng-truyn-s-liu-chuyn-dng-metronet', 'Mạng truyền số liệu chuyên dùng Metronet', 'mng-truyn-s-liu-chuyn-dng-metronet', '4', 'goi_internet_truyen_hinh', NULL, NULL, 'Kênh thuê riêng bảo mật tuyệt đối', 'Kết nối mạng diện rộng WAN giữa trụ sở và các chi nhánh ngân hàng, tổ chức tài chính.', NULL, '5500000.00', '5500000.00', 'Tháng', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('40', 'dich-vu-so-vnpt', 'Dịch vụ số VNPT', 'dich-vu-so-vnpt', '1', 'dich_vu_so', NULL, NULL, 'Gói tổng thể Chuyển đổi số doanh nghiệp VNPT', 'Giải pháp chuyển đổi số toàn diện cho doanh nghiệp nhỏ và vừa.', NULL, '1500000.00', '1500000.00', 'Gói', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('41', 'dch-v-s-vnpt', 'Dịch vụ số VNPT', 'dch-v-s-vnpt', '1', 'dich_vu_so', NULL, NULL, 'Gói tổng thể Chuyển đổi số doanh nghiệp VNPT', 'Giải pháp chuyển đổi số toàn diện cho doanh nghiệp nhỏ và vừa.', NULL, '1500000.00', '1500000.00', 'Gói', NULL, '0', 'dang_ban', '0', '0', '2026-08-10 08:54:03', '2026-08-10 08:54:03'),
('70', 'svc-20260810043206', 'Home 3 (2 Mesh)', 'home-3-2-mesh-', '5', 'goi_internet_truyen_hinh', '', NULL, '- Tốc độ ~1Gbps (từ 500Mbps đến 1Gbps tùy thuộc vào chủng loại thiết bị và khoảng cách đến các thiết bị). Hỗ trợ nâng cấp XGSPON theo khu vực.\r\n\r\n- Tích hợp bảo mật: GreenNet hoặc Family Safe.\r\n\r\n- Trang bị miễn phí 02 Wifi Mesh 6 và thiết bị ONT 2 băng tần trong suốt thời gian sử dụng.\r\n\r\n- Lắp đặt nhanh chóng, chăm sóc và hỗ trợ khách hàng 24/7.', '- Cước hòa mạng áp dụng cho khách hàng cá nhân, hộ gia đình đăng ký mới dịch vụ: 300.000đ/thuê bao (đã bao gồm VAT).', '', '300000.00', NULL, 'tháng', '', '0', 'dang_ban', '0', '0', '2026-08-10 11:32:06', '2026-08-10 11:32:06');

DROP TABLE IF EXISTS `san_pham_yeu_thich`;
CREATE TABLE `san_pham_yeu_thich` (
  `khach_hang_id` bigint unsigned NOT NULL,
  `san_pham_id` bigint unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`khach_hang_id`,`san_pham_id`),
  KEY `san_pham_id` (`san_pham_id`),
  CONSTRAINT `san_pham_yeu_thich_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `san_pham_yeu_thich_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tai_khoan`;
CREATE TABLE `tai_khoan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinh_anh_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mat_khau_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_tai_khoan` enum('nhan_vien','khach_hang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vai_tro_id` bigint unsigned NOT NULL,
  `trang_thai` enum('hoat_dong','khoa','cho_xac_thuc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cho_xac_thuc',
  `da_xac_thuc_email` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `vai_tro_id` (`vai_tro_id`),
  CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`vai_tro_id`) REFERENCES `vai_tro` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tai_khoan` (`id`, `email`, `hinh_anh_url`, `mat_khau_hash`, `loai_tai_khoan`, `vai_tro_id`, `trang_thai`, `da_xac_thuc_email`, `created_at`, `updated_at`) VALUES
('1', 'admin@vnpt.vn', 'uploads/avatars/avatar_admin_1_1786439030.jpg', '$2y$10$E5MfnZAMfKoyFKQ/Y1FD1uIn3r1uSgI46PCRJ3WCTZ7fDIRreX9Se', 'nhan_vien', '1', 'hoat_dong', '1', '2026-07-08 08:11:23', '2026-08-11 16:03:50'),
('2', 'editor@vnpt.vn', NULL, '$2a$10$/mYxuPax4fCL5SljgXYA2.f1Kip/0hIpzHDiqdVRdRzAIyRCMJKF6', 'nhan_vien', '3', 'hoat_dong', '1', '2026-07-08 08:11:23', '2026-08-07 12:24:26'),
('3', 'manager@vnpt.vn', '', '$2a$10$rFoYWMCKSt.5GAyo32ZN1.QmfhBPl/3/Uyvliniy3Rqc5J.NyCsoi', 'nhan_vien', '2', 'hoat_dong', '1', '2026-07-08 08:11:23', '2026-08-07 11:57:14'),
('103', 'lannguyen@gmail.com', 'uploads/avatars/avatar_1786439175_5786.jpg', '$2a$10$8qEduq/SjwT8baVDxXq1PORf4JPc7hYOGr8Z394lMEgxT9azAXMeW', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-11 16:06:15'),
('104', 'binhtran@gmail.com', 'uploads/avatars/avatar_1786076355_8124.jpg', '$2a$10$bk3kkizAFGYUqRVBbfwVmOlg2HJikxGIiJSu3fWH8QnCZVSPRvmgW', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-07 11:45:13'),
('105', 'huongle@yahoo.com', NULL, 'huongle123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-07 11:45:13'),
('106', 'tuanpham@outlook.com', NULL, 'tuanpham123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-06 15:50:35'),
('107', 'maihoang@gmail.com', NULL, 'maihoang123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-06 09:18:53'),
('108', 'hungdang@gmail.com', NULL, 'hungdang123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-06 15:48:28'),
('109', 'ngocbui@gmail.com', NULL, 'ngocbui123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-07 11:45:13'),
('110', 'khoavu@gmail.com', NULL, 'khoavu123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-07 11:45:13'),
('111', 'thungo@gmail.com', NULL, 'thungo123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-07 11:45:13'),
('112', 'phucdinh@gmail.com', NULL, 'phucdinh123', 'khach_hang', '4', 'hoat_dong', '0', '2026-07-23 00:00:00', '2026-08-06 16:01:06'),
('113', 'superfire098@gmail.com', 'uploads/avatars/avatar_1785982396_7366.jpg', 'King020858', 'khach_hang', '4', 'hoat_dong', '1', '2026-08-06 09:12:51', '2026-08-06 09:13:16'),
('115', 'skyvalkyrie08@gmail.com', 'uploads/avatars/avatar_1786093525_4275.jpg', '$2y$10$7qt2UdTJsAUBy/OIgd4gHO/fVR2nhTLj/Gjlw3FwjBytGTCRewnfG', 'khach_hang', '4', 'hoat_dong', '1', '2026-08-07 16:04:46', '2026-08-10 08:50:19'),
('119', 'supersfire098@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocJPJhSlcdotunUKatgdexgGaz4pJ9MiHvGl8jMHsyXnyapWmVQh=s96-c', '$2y$10$hwCQsj2aXS16ATmv4CxbYOxBoDrOcKcjvQF82owOGSNjAXMKTjzOC', 'khach_hang', '4', 'hoat_dong', '1', '2026-08-10 10:33:12', '2026-08-10 10:33:12');

DROP TABLE IF EXISTS `thanh_toan`;
CREATE TABLE `thanh_toan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint unsigned NOT NULL,
  `phuong_thuc_thanh_toan_id` bigint unsigned NOT NULL,
  `so_tien` decimal(15,2) NOT NULL,
  `ma_giao_dich` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai` enum('cho_thanh_toan','thanh_cong','that_bai','hoan_tien') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cho_thanh_toan',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `phuong_thuc_thanh_toan_id` (`phuong_thuc_thanh_toan_id`),
  CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`phuong_thuc_thanh_toan_id`) REFERENCES `phuong_thuc_thanh_toan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `thong_bao`;
CREATE TABLE `thong_bao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `khach_hang_id` bigint unsigned NOT NULL,
  `tieu_de` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `loai` enum('don_hang','khuyen_mai','he_thong') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'he_thong',
  `da_doc` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `khach_hang_id` (`khach_hang_id`),
  CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `thong_bao` (`id`, `khach_hang_id`, `tieu_de`, `noi_dung`, `loai`, `da_doc`, `created_at`) VALUES
('15', '101', 'Khuyến mãi tháng 8', 'Giảm 10% tất cả dịch vụ', 'khuyen_mai', '1', '2026-08-11 15:58:21');

DROP TABLE IF EXISTS `thong_bao_nhan_vien`;
CREATE TABLE `thong_bao_nhan_vien` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nhan_vien_id` bigint unsigned NOT NULL,
  `nguoi_gui_id` bigint unsigned DEFAULT NULL COMMENT 'ID Nhân viên người gửi',
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `da_doc` tinyint(1) DEFAULT '0',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nhan_vien_id` (`nhan_vien_id`),
  CONSTRAINT `thong_bao_nhan_vien_ibfk_1` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_vien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `thuoc_tinh_san_pham`;
CREATE TABLE `thuoc_tinh_san_pham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_thuoc_tinh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `trang_tinh`;
CREATE TABLE `trang_tinh` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_san_pham` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `trang_tinh` (`id`, `slug`, `tieu_de`, `mo_ta`, `icon`, `ma_san_pham`) VALUES
('1', 'gioi-thieu', 'Giới thiệu về VNPT', 'Nguồn trung tâm dịch vụ số toàn diện, nâng tầm hệ sinh thái công nghệ cho doanh nghiệp.', 'info', NULL),
('2', 'tam-nhin-su-menh', 'Tầm nhìn & Sứ mệnh', 'Kiến tạo hạ tầng số vững chắc, thúc đẩy doanh nghiệp Việt bứt phá trong kỷ nguyên số.', 'target', NULL),
('3', 'doi-ngu-lanh-dao', 'Đội ngũ lãnh đạo', 'Những chuyên gia hàng đầu giàu kinh nghiệm trong lĩnh vực Điện toán đám mây & Bảo mật.', 'users', NULL),
('4', 'thanh-tuu', 'Thành tựu & Cột mốc', 'Hơn 30 năm phát triển đồng hành cùng hàng triệu khách hàng doanh nghiệp trên toàn quốc.', 'award', NULL),
('5', 'ha-tang-so', 'Hạ tầng số', 'Hạ tầng máy chủ trung tâm dữ liệu đạt chuẩn quốc tế Tier III với băng thông kết nối lớn.', 'server', NULL),
('6', 'bao-mat-an-toan', 'Bảo mật & An toàn thông tin', 'Hệ thống bảo vệ đa lớp, phát hiện và ứng cứu sự cố an ninh mạng SOC 24/7.', 'shield-check', NULL),
('7', 'cloud-computing', 'Cloud Computing', 'Triển khai hạ tầng đám mây linh hoạt, giúp tối ưu chi phí vận hành và nâng cao hiệu suất.', 'cloud', 'cloud-enterprise'),
('8', 'ai-tu-dong-hoa', 'AI & Tự động hóa', 'Số hóa tài liệu, tự động hóa quy trình nghiệp vụ với trí tuệ nhân tạo VNPT AI OCR.', 'cpu', 'vnpt-ai-ocr'),
('9', 'gp-sme', 'Giải pháp cho Doanh nghiệp vừa & nhỏ', 'Hệ sinh thái ứng dụng quản trị số tối ưu chi phí dành riêng cho SME.', 'briefcase', 'combo-sme'),
('10', 'gp-enterprise', 'Giải pháp cho Tập đoàn & Doanh nghiệp lớn', 'Hạ tầng Multi-Cloud dùng riêng, đáp ứng tiêu chuẩn vận hành khắt khe nhất.', 'building', 'giai-phap-tap-doan'),
('11', 'gp-chinh-phu', 'Chính phủ số', 'Đồng hành xây dựng hệ thống hành chính công trực tuyến hiện đại, minh bạch.', 'landmark', NULL),
('12', 'gp-yte-giaoduc', 'Chuyên ngành Y tế & Giáo dục', 'Cung cấp hạ tầng số hóa bệnh viện thông minh HIS/PACS và trường học trực tuyến.', 'heart-handshake', 'cloud-high-spec'),
('13', 'he-sinh-thai', 'Hệ sinh thái VNPT', 'Hệ sinh thái phần mềm tích hợp toàn diện cho hoạt động quản trị doanh nghiệp.', 'layers', NULL),
('14', 'doi-tac', 'Đối tác chiến lược', 'Đồng hành cùng các tập đoàn công nghệ hàng đầu thế giới.', 'handshake', NULL),
('15', 'thong-cao-bao-chi', 'Thông cáo báo chí', 'Thông tin chính thức từ Ban quản trị VNPT.', 'megaphone', NULL),
('16', 'blog-cong-nghe', 'Blog công nghệ', 'Kiến thức, xu hướng và góc nhìn chuyên sâu về chuyển đổi số.', 'rss', NULL),
('19', 'su-kien', 'Sự kiện', 'Các sự kiện, hội thảo và diễn đàn công nghệ do VNPT tổ chức.', 'calendar', NULL);

DROP TABLE IF EXISTS `vai_tro`;
CREATE TABLE `vai_tro` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_vai_tro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nhom_vai_tro` enum('nhan_vien','khach_hang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Xác định vai trò này dùng cho nhân viên hay khách hàng',
  `mo_ta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_vai_tro` (`ten_vai_tro`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vai_tro` (`id`, `ten_vai_tro`, `nhom_vai_tro`, `mo_ta`) VALUES
('1', 'quan_tri_vien', 'nhan_vien', 'Quản trị hệ thống, toàn quyền'),
('2', 'nhan_vien_ban_hang', 'nhan_vien', 'Nhân viên xử lý đơn hàng, hỗ trợ khách hàng'),
('3', 'bien_tap_vien', 'nhan_vien', 'Biên tập nội dung bài viết, trang tĩnh'),
('4', 'khach_hang', 'khach_hang', 'Vai trò mặc định của tài khoản khách hàng mua sắm trên website');

DROP TABLE IF EXISTS `vai_tro_quyen`;
CREATE TABLE `vai_tro_quyen` (
  `vai_tro_id` bigint unsigned NOT NULL,
  `quyen_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`vai_tro_id`,`quyen_id`),
  KEY `quyen_id` (`quyen_id`),
  CONSTRAINT `vai_tro_quyen_ibfk_1` FOREIGN KEY (`vai_tro_id`) REFERENCES `vai_tro` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vai_tro_quyen_ibfk_2` FOREIGN KEY (`quyen_id`) REFERENCES `quyen_han` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vai_tro_quyen` (`vai_tro_id`, `quyen_id`) VALUES
('1', '1'),
('1', '2'),
('2', '2'),
('1', '3'),
('3', '3'),
('1', '4'),
('4', '5'),
('4', '6');

DROP TABLE IF EXISTS `van_chuyen_don_hang`;
CREATE TABLE `van_chuyen_don_hang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint unsigned NOT NULL,
  `phuong_thuc_van_chuyen_id` bigint unsigned NOT NULL,
  `ma_van_don` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai_giao_hang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cho_lay_hang',
  `ngay_giao_du_kien` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `don_hang_id` (`don_hang_id`),
  KEY `phuong_thuc_van_chuyen_id` (`phuong_thuc_van_chuyen_id`),
  CONSTRAINT `van_chuyen_don_hang_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `van_chuyen_don_hang_ibfk_2` FOREIGN KEY (`phuong_thuc_van_chuyen_id`) REFERENCES `phuong_thuc_van_chuyen` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `yeu_cau_ho_tro`;
CREATE TABLE `yeu_cau_ho_tro` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `khach_hang_id` bigint unsigned DEFAULT NULL,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_yeu_cau` enum('ky_thuat','thanh_toan','khieu_nai','khac') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'khac',
  `trang_thai` enum('moi','dang_xu_ly','da_giai_quyet') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'moi',
  `nhan_vien_xu_ly_id` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `khach_hang_id` (`khach_hang_id`),
  KEY `nhan_vien_xu_ly_id` (`nhan_vien_xu_ly_id`),
  CONSTRAINT `yeu_cau_ho_tro_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `yeu_cau_ho_tro_ibfk_2` FOREIGN KEY (`nhan_vien_xu_ly_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `yeu_cau_ho_tro` (`id`, `khach_hang_id`, `tieu_de`, `noi_dung`, `loai_yeu_cau`, `trang_thai`, `nhan_vien_xu_ly_id`, `created_at`, `updated_at`) VALUES
('3', '111', 'Yêu cầu tư vấn Chuyển đổi số (AI_SECURITY)', 'KHÁCH HÀNG ĐĂNG KÝ TƯ VẤN BẮT ĐẦU NGAY\n- Tên/Doanh nghiệp: Văn Chương\n- Số điện thoại: 0325086642\n- Dịch vụ quan tâm: ai_security\n- Nội dung cần tư vấn: Ai mạnh\n- Thời gian đăng ký: 07/08/2026 04:44:55', 'khac', 'da_giai_quyet', '2', '2026-08-07 11:44:55', '2026-08-07 12:26:44'),
('4', NULL, 'Yêu cầu tư vấn Chuyển đổi số (AI)', 'KHÁCH HÀNG ĐĂNG KÝ TƯ VẤN BẮT ĐẦU NGAY\n- Tên/Doanh nghiệp: Viettel\n- Số điện thoại: 0985553192\n- Dịch vụ quan tâm: ai\n- Thời gian đăng ký: 11/08/2026 08:47:35', 'khac', 'da_giai_quyet', '1', '2026-08-11 15:47:35', '2026-08-11 15:48:55');

SET FOREIGN_KEY_CHECKS=1;
