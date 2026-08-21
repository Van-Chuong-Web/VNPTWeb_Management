/**
 * VNPT — Page Router Module (SPA) v5
 * Cung cấp các trang tĩnh và hỗ trợ hiển thị bài viết động từ CSDL MySQL.
 */
(function () {
  'use strict';

  /* ============================================================
   * 1. DỮ LIỆU CÁC TRANG TĨNH (STATIC PAGES)
   * ============================================================ */
  const PAGES = {
    'gioi-thieu': {
      breadcrumb: ['Về chúng tôi', 'Giới thiệu chung'],
      title: 'Giới Thiệu Tổng Quan VNPT',
      subtitle: 'Nền tảng dịch vụ số & hạ tầng viễn thông hàng đầu Việt Nam',
      icon: 'building-2',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.6rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Chúng tôi là ai?</h2>
          <p style="font-size: 1.05rem; margin-bottom: 24px;">VNPT là đơn vị tiên phong trong hệ sinh thái giải pháp số và hạ tầng viễn thông tại Việt Nam với hơn <strong>30 năm kinh nghiệm</strong> đồng hành cùng hàng trăm nghìn doanh nghiệp. Chúng tôi cung cấp các giải pháp công nghệ toàn diện bao gồm Điện toán đám mây (Cloud), Mạng viễn thông 5G, Chữ ký số SmartCA, Hóa đơn điện tử, AI OCR và An toàn thông tin đạt tiêu chuẩn quốc tế ISO/IEC 27001.</p>
          
          <!-- Thống kê ấn tượng -->
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 32px 0;">
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px 18px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #00AAFF;">100.000+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Doanh nghiệp tin dùng</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px 18px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #22C55E;">99.99%</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Cam kết Uptime SLA</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px 18px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #EAB308;">63/63</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Tỉnh thành phủ sóng</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px 18px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #A855F7;">5.000+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Kỹ sư CNTT &amp; Viễn thông</div>
            </div>
          </div>

          <h3 style="font-size: 1.3rem; font-weight: 800; color: #0F172A; margin: 32px 0 16px;">3 Trụ cột chiến lược của VNPT</h3>
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="cpu"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Tiên phong Công nghệ</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0; line-height: 1.6;">Ứng dụng Trí tuệ nhân tạo (AI), Điện toán đám mây Cloud Native và dữ liệu lớn Big Data vào tối ưu hóa vận hành.</p>
            </div>
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="shield-check"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Bảo mật Tuyệt đối</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0; line-height: 1.6;">Trung tâm vận hành An ninh mạng SOC hoạt động 24/7/365 ngăn chặn mọi mối đe dọa không gian mạng.</p>
            </div>
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="heart-handshake"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Đồng hành Tận tâm</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0; line-height: 1.6;">Tổng đài hỗ trợ 1800 1260 miễn phí và đội ngũ kỹ thuật phản ứng nhanh xử lý sự cố trong vòng 15 phút.</p>
            </div>
          </div>
        </div>
      `
    },
    'tam-nhin-su-menh': {
      breadcrumb: ['Về chúng tôi', 'Tầm nhìn & Sứ mệnh'],
      title: 'Tầm Nhìn & Sứ Mệnh Định Hướng 2030',
      subtitle: 'Kiến tạo nền tảng số tin cậy cho sự bứt phá của doanh nghiệp Việt',
      icon: 'compass',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <div style="background: linear-gradient(135deg, #0F172A, #0055BB); padding: 30px; border-radius: 20px; color: white;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <i data-lucide="eye" style="width: 32px; height: 32px; color: #00AAFF;"></i>
                <h3 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: white;">Tầm Nhìn Đến Năm 2030</h3>
              </div>
              <p style="font-size: 0.95rem; color: rgba(255,255,255,0.9); line-height: 1.7; margin: 0;">Trở thành Tập đoàn công nghệ cung cấp Giải pháp số và Hạ tầng Đám mây thuộc <strong>Top 3 khu vực Đông Nam Á</strong>, làm bệ phóng vững chắc cho công cuộc chuyển đổi số quốc gia.</p>
            </div>

            <div style="background: linear-gradient(135deg, #0055BB, #00AAFF); padding: 30px; border-radius: 20px; color: white;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <i data-lucide="target" style="width: 32px; height: 32px; color: #FFB347;"></i>
                <h3 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: white;">Sứ Mệnh Tiên Phong</h3>
              </div>
              <p style="font-size: 0.95rem; color: rgba(255,255,255,0.9); line-height: 1.7; margin: 0;">Đồng hành cùng mọi doanh nghiệp Việt Nam trên hành trình tối ưu hóa vận hành, bảo vệ dữ liệu và nâng cao năng lực cạnh tranh bình đẳng với các tập đoàn toàn cầu.</p>
            </div>
          </div>

          <h3 style="font-size: 1.3rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">5 Giá trị Cốt lõi (CORE VALUES)</h3>
          <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; text-align: center;">
            <div style="background: white; border: 1px solid #E2E8F0; padding: 18px 10px; border-radius: 14px;">
              <strong style="color: #0066CC; font-size: 1.1rem; display: block;">SÁNG TẠO</strong>
              <span style="font-size: 0.78rem; color: #64748B;">Đổi mới liên tục</span>
            </div>
            <div style="background: white; border: 1px solid #E2E8F0; padding: 18px 10px; border-radius: 14px;">
              <strong style="color: #0066CC; font-size: 1.1rem; display: block;">TÍN NHIỆM</strong>
              <span style="font-size: 0.78rem; color: #64748B;">Cam kết chất lượng</span>
            </div>
            <div style="background: white; border: 1px solid #E2E8F0; padding: 18px 10px; border-radius: 14px;">
              <strong style="color: #0066CC; font-size: 1.1rem; display: block;">TỐC ĐỘ</strong>
              <span style="font-size: 0.78rem; color: #64748B;">Phản ứng linh hoạt</span>
            </div>
            <div style="background: white; border: 1px solid #E2E8F0; padding: 18px 10px; border-radius: 14px;">
              <strong style="color: #0066CC; font-size: 1.1rem; display: block;">BẢO MẬT</strong>
              <span style="font-size: 0.78rem; color: #64748B;">An toàn tuyệt đối</span>
            </div>
            <div style="background: white; border: 1px solid #E2E8F0; padding: 18px 10px; border-radius: 14px;">
              <strong style="color: #0066CC; font-size: 1.1rem; display: block;">TẬN TÂM</strong>
              <span style="font-size: 0.78rem; color: #64748B;">Khách hàng làm trung tâm</span>
            </div>
          </div>
        </div>
      `
    },
    'doi-ngu-lanh-dao': {
      breadcrumb: ['Về chúng tôi', 'Đội ngũ lãnh đạo'],
      title: 'Ban Lãnh Đạo & Chuyên Gia VNPT',
      subtitle: 'Đội ngũ tâm huyết với tầm nhìn chiến lược dẫn dắt làn sóng chuyển đổi số',
      icon: 'users',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 20px;">Hội Đồng Quản Trị & Ban Điều Hành</h2>
          
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 900; margin: 0 auto 16px; border: 3px solid #00AAFF;">NVT</div>
              <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Nguyễn Văn Thành</h3>
              <span style="font-size: 0.85rem; font-weight: 700; color: #0066CC; display: block; margin-bottom: 12px;">Chủ tịch Hội đồng Quản trị</span>
              <p style="font-size: 0.85rem; color: #64748B; margin: 0; line-height: 1.5;">25+ năm kinh nghiệm quản trị các dự án viễn thông quốc gia và hạ tầng vệ tinh.</p>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #0055BB, #00AAFF); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 900; margin: 0 auto 16px; border: 3px solid #00E5FF;">HTM</div>
              <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Hoàng Thị Mai</h3>
              <span style="font-size: 0.85rem; font-weight: 700; color: #0066CC; display: block; margin-bottom: 12px;">Tổng Giám đốc Điều hành</span>
              <p style="font-size: 0.85rem; color: #64748B; margin: 0; line-height: 1.5;">Tiến sĩ CNTT từ ĐH Stanford, chuyên gia tư vấn chiến lược chuyển đổi số doanh nghiệp.</p>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #00AA55, #00FF88); color: #003311; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 900; margin: 0 auto 16px; border: 3px solid #00AA55;">TAT</div>
              <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Trần Anh Tuấn</h3>
              <span style="font-size: 0.85rem; font-weight: 700; color: #00AA55; display: block; margin-bottom: 12px;">Giám đốc Công nghệ (CTO)</span>
              <p style="font-size: 0.85rem; color: #64748B; margin: 0; line-height: 1.5;">Kiến trúc sư trưởng hệ sinh thái VNPT Cloud, AI OCR và Trung tâm An ninh mạng SOC.</p>
            </div>
          </div>
        </div>
      `
    },
    'thanh-tuu': {
      breadcrumb: ['Về chúng tôi', 'Thành tựu & Giải thưởng'],
      title: 'Thành Tựu & Giải Thưởng Quốc Gia',
      subtitle: 'Khẳng định vị thế thương hiệu quốc gia tiêu biểu trong kỷ nguyên số',
      icon: 'award',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 32px;">
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 16px; display: flex; gap: 16px; align-items: flex-start;">
              <div style="width: 48px; height: 48px; border-radius: 14px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;"><i data-lucide="trophy"></i></div>
              <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Top 10 Doanh Nghiệp ICT Việt Nam</h4>
                <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Vinh danh nhà cung cấp dịch vụ Điện toán đám mây &amp; An toàn thông tin xuất sắc nhất.</p>
              </div>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 16px; display: flex; gap: 16px; align-items: flex-start;">
              <div style="width: 48px; height: 48px; border-radius: 14px; background: #EFF6FF; color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;"><i data-lucide="shield-check"></i></div>
              <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Chứng Nhận Bảo Mật ISO/IEC 27001</h4>
                <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Tiêu chuẩn quốc tế nghiêm ngặt nhất về Hệ thống quản lý an toàn thông tin doanh nghiệp.</p>
              </div>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 16px; display: flex; gap: 16px; align-items: flex-start;">
              <div style="width: 48px; height: 48px; border-radius: 14px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;"><i data-lucide="award"></i></div>
              <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Giải Thưởng Vietnam Digital Awards</h4>
                <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Hệ thống Chữ ký số SmartCA đạt danh hiệu Sản phẩm số Xuất sắc phục vụ Chính quyền số.</p>
              </div>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 16px; display: flex; gap: 16px; align-items: flex-start;">
              <div style="width: 48px; height: 48px; border-radius: 14px; background: #FDF4FF; color: #A855F7; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;"><i data-lucide="star"></i></div>
              <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Thương Hiệu Quốc Gia Vietnam Value</h4>
                <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Biểu tượng uy tín cho các thương hiệu hàng đầu đại diện hình ảnh công nghệ Việt Nam.</p>
              </div>
            </div>
          </div>
        </div>
      `
    },
    'ha-tang-so': {
      breadcrumb: ['Dịch vụ', 'Hạ tầng số'],
      title: 'Hạ Tầng Số & Data Center Tier III',
      subtitle: 'Nền tảng hạ tầng đám mây & viễn thông hiện đại hàng đầu',
      icon: 'server',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Trung Tâm Dữ Liệu Standard Tier III</h2>
          <p style="font-size: 1rem; margin-bottom: 24px;">VNPT vận hành hệ thống Data Center trải dài 3 miền Bắc - Trung - Nam với tổng diện tích sàn hơn 15.000m², được trang bị hệ thống nguồn điện N+1, hệ thống làm mát chính xác và các lớp bảo mật sinh trắc học 24/7.</p>
          
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 24px; border-radius: 16px; margin-bottom: 24px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0066CC; margin: 0 0 12px;">Thông Số Kỹ Thuật Nổi Bật:</h4>
            <ul style="margin: 0; padding-left: 20px; font-size: 0.92rem; color: #334155; line-height: 1.8;">
              <li>Băng thông kết nối nội địa 10Gbps, kết nối cáp quang biển quốc tế qua các tuyến AAG, APG, IA.</li>
              <li>Hệ thống máy chủ VNPT Cloud trang bị chip xử lý Intel Xeon Scalable thế hệ mới và ổ cứng SSD NVMe Enterprise.</li>
              <li>Cam kết mức độ sẵn sàng liên tục Uptime SLA lên tới 99.99%.</li>
              <li>Hệ thống sao lưu tự động (Auto Backup) theo cơ chế Multi-AZ dự phòng rủi ro sự cố thiên tai.</li>
            </ul>
          </div>
        </div>
      `
    },
    'dieu-khoan-dich-vu': {
      breadcrumb: ['Pháp lý', 'Điều khoản dịch vụ'],
      title: 'Điều Khoản Dịch Vụ VNPT',
      subtitle: 'Quy định pháp lý và cam kết sử dụng dịch vụ số an toàn',
      icon: 'file-text',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 14px;">1. Quy định chung</h2>
          <p style="margin-bottom: 20px;">Điều khoản này áp dụng cho toàn bộ khách hàng cá nhân và doanh nghiệp khi đăng ký sử dụng các sản phẩm dịch vụ thuộc hệ sinh thái VNPT. Việc khách hàng nhấn đăng ký mặc định được hiểu là đã đồng ý với toàn bộ quy định này.</p>

          <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 14px;">2. Cam kết chất lượng dịch vụ (SLA)</h2>
          <p style="margin-bottom: 20px;">VNPT cam kết duy trì độ sẵn sàng của hệ thống ở mức 99.99%. Trong trường hợp xảy ra sự cố gián đoạn ngoài kế hoạch bảo trì, VNPT sẽ hoàn trả cước phí theo đúng chính sách bồi thường ghi trong hợp đồng kinh tế.</p>

          <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 14px;">3. Trách nhiệm người dùng</h2>
          <p style="margin-bottom: 20px;">Khách hàng có trách nhiệm bảo mật thông tin tài khoản đăng nhập, không khai thác dịch vụ cho các mục đích vi phạm pháp luật (như tấn công mạng, phát tán mã độc, giả mạo thương hiệu).</p>
        </div>
      `
    },
    'chinh-sach-bao-mat': {
      breadcrumb: ['Pháp lý', 'Chính sách bảo mật'],
      title: 'Chính Sách Bảo Mật Quyền Riêng Tư',
      subtitle: 'Cam kết bảo vệ dữ liệu khách hàng theo Nghị định 13/2023/NĐ-CP',
      icon: 'shield',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 14px;">1. Thu thập dữ liệu cá nhân</h2>
          <p style="margin-bottom: 20px;">Chúng tôi chỉ thu thập các thông tin cần thiết phục vụ cho việc khởi tạo tài khoản, xác thực hợp đồng điện tử và hỗ trợ kỹ thuật bao gồm: Họ tên, Email, Số điện thoại, Tên công ty và Mã số thuế.</p>

          <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 14px;">2. Cam kết an toàn dữ liệu</h2>
          <p style="margin-bottom: 20px;">Mọi dữ liệu giao dịch và lưu trữ trên hệ thống VNPT đều được mã hóa bằng chuẩn AES-256 bits và SSL/TLS. Chúng tôi tuyệt đối không bán hoặc tiết lộ dữ liệu khách hàng cho bên thứ ba vì mục đích thương mại.</p>

          <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 14px;">3. Quyền của chủ thể dữ liệu</h2>
          <p style="margin-bottom: 20px;">Khách hàng có quyền truy cập, chỉnh sửa hoặc yêu cầu hủy bỏ thông tin cá nhân của mình bất kỳ lúc nào bằng cách liên hệ với Trung tâm CSKH qua hotline 1800 1260 hoặc email contact@vnpt.vn.</p>
        </div>
      `
    },
    /* ============================================================
     * DỊCH VỤ & GIẢI PHÁP SỐ SINH ĐỘNG
     * ============================================================ */
    'ha-tang-so': {
      breadcrumb: ['Dịch vụ', 'Hạ tầng số'],
      title: 'Hạ Tầng Số & Data Center Tier III',
      subtitle: 'Nền tảng hạ tầng đám mây & viễn thông hiện đại hàng đầu',
      icon: 'server',
      ma_san_pham: 'SP001',
      gia_niem_yet: 2500000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Trung Tâm Dữ Liệu Standard Tier III</h2>
          <p style="font-size: 1rem; margin-bottom: 24px;">VNPT vận hành hệ thống Data Center trải dài 3 miền Bắc - Trung - Nam với tổng diện tích sàn hơn 15.000m², được trang bị hệ thống nguồn điện N+1, hệ thống làm mát chính xác và các lớp bảo mật sinh trắc học 24/7.</p>
          
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 24px; border-radius: 16px; margin-bottom: 24px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0066CC; margin: 0 0 12px;">Thông Số Kỹ Thuật Nổi Bật:</h4>
            <ul style="margin: 0; padding-left: 20px; font-size: 0.92rem; color: #334155; line-height: 1.8;">
              <li>Băng thông kết nối nội địa 10Gbps, kết nối cáp quang biển quốc tế qua các tuyến AAG, APG, IA.</li>
              <li>Hệ thống máy chủ VNPT Cloud trang bị chip xử lý Intel Xeon Scalable thế hệ mới và ổ cứng SSD NVMe Enterprise.</li>
              <li>Cam kết mức độ sẵn sàng liên tục Uptime SLA lên tới 99.99%.</li>
              <li>Hệ thống sao lưu tự động (Auto Backup) theo cơ chế Multi-AZ dự phòng rủi ro sự cố thiên tai.</li>
            </ul>
          </div>
        </div>
      `
    },
    'cloud-computing': {
      breadcrumb: ['Dịch vụ', 'Cloud Computing & Data Center'],
      title: 'Điện Toán Đám Mây VNPT Cloud Enterprise',
      subtitle: 'Máy chủ ảo SSD NVMe siêu tốc, hạ tầng Uptime Tier III SLA 99.99%',
      icon: 'cloud',
      ma_san_pham: 'SP001',
      gia_niem_yet: 2500000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Hạ Tầng Điện Toán Đám Mây Thế Hệ Mới</h2>
          <p style="font-size: 1.02rem; margin-bottom: 24px;">VNPT Cloud được xây dựng trên nền tảng ảo hóa KVM hiện đại, hỗ trợ mở rộng tài nguyên vCPU, RAM và dung lượng SSD linh hoạt theo thời gian thực mà không làm gián đoạn hệ thống.</p>
          
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="zap"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">SSD NVMe Enterprise</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Tốc độ truy xuất dữ liệu IOPS cực cao, phản hồi truy vấn CSDL mượt mà.</p>
            </div>
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="refresh-cw"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Auto Snapshot &amp; Backup</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Tự động tạo bản sao lưu dữ liệu hàng ngày, khôi phục thảm họa Multi-Region.</p>
            </div>
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="lock"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Bảo Mật Cách Ly VPS</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Tường lửa hạ tầng riêng biệt ngăn ngừa lây nhiễm mã độc ngang cấp.</p>
            </div>
          </div>
        </div>
      `
    },
    'ai-tu-dong-hoa': {
      breadcrumb: ['Dịch vụ', 'AI & Tự động hóa'],
      title: 'Trí Tuệ Nhân Tạo & Tự Động Hóa AI OCR',
      subtitle: 'Số hóa tài liệu tự động, bóc tách dữ liệu 99.8% & Chatbot AI 24/7',
      icon: 'cpu',
      ma_san_pham: 'SP003',
      gia_niem_yet: 3200000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Giải Pháp Đột Phá Năng Suất Doanh Nghiệp</h2>
          <p style="font-size: 1.02rem; margin-bottom: 24px;">VNPT AI OCR ứng dụng mô hình Deep Learning tiên tiến nhất để tự động nhận diện và trích xuất dữ liệu từ Giấy tờ định danh, Đăng ký xe, Hóa đơn VAT và Hợp đồng kinh tế chỉ trong 0.5 giây.</p>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 24px; border-radius: 16px;">
              <h4 style="font-size: 1.1rem; font-weight: 800; color: #0066CC; margin: 0 0 10px;">🤖 VNPT AI OCR Smart</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0 0 12px;">Trích xuất thông tin tự động từ CCCD, Hộ chiếu, Giấy phép lái xe với độ chính xác tuyệt đối 99.8%.</p>
              <span style="font-size: 0.82rem; font-weight: 700; color: #16A34A; background: #DCFCE7; padding: 4px 10px; border-radius: 12px;">Giảm 80% thời gian xử lý thủ công</span>
            </div>
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 24px; border-radius: 16px;">
              <h4 style="font-size: 1.1rem; font-weight: 800; color: #0066CC; margin: 0 0 10px;">💬 AI Chatbot &amp; Virtual Agent</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0 0 12px;">Trợ lý ảo tư vấn tự động 24/7 tích hợp Gemini AI trả lời thắc mắc khách hàng trên mọi nền tảng.</p>
              <span style="font-size: 0.82rem; font-weight: 700; color: #2563EB; background: #DBEAFE; padding: 4px 10px; border-radius: 12px;">Phản hồi tức thì trong 1 giây</span>
            </div>
          </div>
        </div>
      `
    },
    'bao-mat-an-toan': {
      breadcrumb: ['Dịch vụ', 'Bảo mật & An toàn số'],
      title: 'Bảo Mật & An Toàn Thông Tin Cyber Security',
      subtitle: 'Giám sát an ninh mạng 24/7/365, chống tấn công DDoS & WAF toàn diện',
      icon: 'shield-alert',
      ma_san_pham: 'SP002',
      gia_niem_yet: 1800000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Lá Chắn An Ninh Số Cho Doanh Nghiệp</h2>
          <p style="font-size: 1.02rem; margin-bottom: 24px;">VNPT Cyber Security bảo vệ tài nguyên ứng dụng và cơ sở dữ liệu doanh nghiệp trước các đợt tấn công từ chối dịch vụ Anti-DDoS nguy hiểm và mã độc tống tiền Ransomware.</p>

          <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 28px; border-radius: 18px; color: white; margin-bottom: 24px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #00AAFF; margin: 0 0 12px;">Trung Tâm SOC Giám Sát An Ninh Mạng 24/7</h3>
            <p style="font-size: 0.92rem; color: #CBD5E1; margin: 0; line-height: 1.7;">Đội ngũ chuyên gia an ninh mạng WhiteHat giám sát lưu lượng liên tục 24/7/365, cảnh báo các lỗ hổng bảo mật Zero-Day và hỗ trợ ứng cứu sự cố tức thì trong 15 phút.</p>
          </div>
        </div>
      `
    },
    'gp-yte-giaoduc': {
      breadcrumb: ['Giải pháp', 'Y tế & Giáo dục số'],
      title: 'Chuyển Đổi Số Y Tế & Giáo Dục Thông Minh',
      subtitle: 'Nền tảng Bệnh viện số VNPT HIS, Hồ sơ EMR & Hệ sinh thái vnEdu',
      icon: 'heart-pulse',
      ma_san_pham: 'SP007',
      gia_niem_yet: 7500000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 20px;">Hệ Sinh Thái Giải Pháp Số Ngành Y Tế &amp; Giáo Dục</h2>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <!-- Y tế số -->
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 26px; border-radius: 18px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><i data-lucide="stethoscope"></i></div>
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #0F172A; margin: 0;">Y Tế Số (VNPT HIS / EMR)</h3>
              </div>
              <ul style="margin: 0; padding-left: 18px; font-size: 0.9rem; color: #475569; line-height: 1.8;">
                <li><strong>Bệnh án điện tử EMR</strong>: Thay thế bệnh án giấy, tích hợp Chữ ký số SmartCA bảo mật tuyệt đối.</li>
                <li><strong>Quản lý Bệnh viện VNPT HIS</strong>: Quản lý khám chữa bệnh, kho dược và thanh toán BHYT liên thông.</li>
                <li><strong>Thanh toán viện phí không tiền mặt</strong>: Quét mã VietQR tiện lợi ngay tại phòng khám.</li>
              </ul>
            </div>

            <!-- Giáo dục số -->
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 26px; border-radius: 18px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><i data-lucide="graduation-cap"></i></div>
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #0F172A; margin: 0;">Giáo Dục Số (vnEdu 4.0)</h3>
              </div>
              <ul style="margin: 0; padding-left: 18px; font-size: 0.9rem; color: #475569; line-height: 1.8;">
                <li><strong>Mạng giáo dục vnEdu</strong>: Sổ liên lạc điện tử kết nối nhà trường và phụ huynh thời gian thực.</li>
                <li><strong>Hệ thống LMS &amp; E-Learning</strong>: Quản lý giáo án điện tử, bài giảng video và tổ chức thi trực tuyến.</li>
                <li><strong>Tuyển sinh đầu cấp trực tuyến</strong>: Nộp hồ sơ và tra cứu kết quả xét tuyển nhanh chóng.</li>
              </ul>
            </div>
          </div>
        </div>
      `
    },
    'gp-doanh-nghiep-sme': {
      breadcrumb: ['Giải pháp', 'Doanh nghiệp vừa & nhỏ (SME)'],
      title: 'Giải Pháp Số Toàn Diện Cho Doanh Nghiệp SME',
      subtitle: 'Tối ưu chi phí vận hành, chuyển đổi số siêu tốc trọn gói chỉ trong 24 giờ',
      icon: 'briefcase',
      ma_san_pham: 'SP004',
      gia_niem_yet: 2900000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Combo Bộ Ba Chuyển Đổi Số Khởi Tạo Doanh Nghiệp</h2>
          <p style="font-size: 1.02rem; margin-bottom: 24px;">Giúp doanh nghiệp SME sẵn sàng vận hành pháp lý số ngay lập tức với chi phí tiết kiệm 35% so với đăng ký riêng lẻ.</p>

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 28px;">
            <div style="background: white; border: 1px solid #E2E8F0; padding: 20px; border-radius: 16px;">
              <strong style="color: #0066CC; font-size: 1.05rem; display: block; margin-bottom: 6px;">🔑 SmartCA Khởi Tạo</strong>
              <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Ký Hợp đồng &amp; Tờ khai Thuế trên Smartphone mọi lúc mọi nơi.</p>
            </div>
            <div style="background: white; border: 1px solid #E2E8F0; padding: 20px; border-radius: 16px;">
              <strong style="color: #0066CC; font-size: 1.05rem; display: block; margin-bottom: 6px;">📜 VNPT Invoice</strong>
              <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Khởi tạo 1.000 Hóa đơn điện tử chuẩn Thông tư 78 Tổng cục Thuế.</p>
            </div>
            <div style="background: white; border: 1px solid #E2E8F0; padding: 20px; border-radius: 16px;">
              <strong style="color: #0066CC; font-size: 1.05rem; display: block; margin-bottom: 6px;">⚡ FiberVNN Doanh Nghiệp</strong>
              <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Mạng Cáp quang siêu tốc 200Mbps tặng kèm IP Tĩnh miễn phí.</p>
            </div>
          </div>
        </div>
      `
    },
    'gp-tap-doan-lon': {
      breadcrumb: ['Giải pháp', 'Tập đoàn lớn'],
      title: 'Giải Pháp Hạ Tầng & Quản Trị Cho Tập Đoàn',
      subtitle: 'Hạ tầng Private Cloud riêng biệt, Kênh truyền SD-WAN & Trung tâm SOC',
      icon: 'building',
      ma_san_pham: 'SP005',
      gia_niem_yet: 15000000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Hạ Tầng Độc Quyền Dành Cho Tập Đoàn &amp; Tổng Công Ty</h2>
          <p style="font-size: 1.02rem; margin-bottom: 24px;">VNPT thiết kế các giải pháp hạ tầng may đo riêng biệt đảm bảo khả năng xử lý hàng triệu giao dịch mỗi giây, kết nối an toàn đa chi nhánh và cam kết SLA Uptime 99.99%.</p>

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="cloud"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Private Cloud Độc Quyền</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Khai thác toàn bộ tài nguyên phần cứng riêng biệt, mã hóa dữ liệu nhạy cảm cấp cao nhất.</p>
            </div>
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="network"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Kênh Truyền Riêng SD-WAN</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Kết nối đa chi nhánh trên toàn quốc với độ trễ siêu thấp và định tuyến thông minh.</p>
            </div>
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 16px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px;"><i data-lucide="user-check"></i></div>
              <h4 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Dedicated Support 1-1</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Đội ngũ kỹ sư CNTT cấp cao được chỉ định trực tiếp hỗ trợ vận hành 24/7/365.</p>
            </div>
          </div>
        </div>
      `
    },
    'gp-chinh-phu-so': {
      breadcrumb: ['Giải pháp', 'Chính phủ số'],
      title: 'Hệ Thống Nền Tảng Số Cho Chính Phủ & Đô Thị Thông Minh',
      subtitle: 'Trung tâm điều hành thông minh IOC & Cổng dịch vụ công trực tuyến',
      icon: 'landmark',
      ma_san_pham: 'SP006',
      gia_niem_yet: 25000000,
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 16px;">Giải Pháp Số Phục Vụ Chính Quyền &amp; Người Dân</h2>
          <p style="font-size: 1.02rem; margin-bottom: 24px;">Kiến tạo mô hình Đô thị thông minh Smart City với Trung tâm điều hành IOC giám sát chỉ số kinh tế xã hội và giao thông thời gian thực.</p>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 24px; border-radius: 16px;">
              <h4 style="font-size: 1.1rem; font-weight: 800; color: #0066CC; margin: 0 0 10px;">🏛️ Trung Tâm Điều Hành Thông Minh IOC</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Tổng hợp và phân tích dữ liệu chỉ đạo điều hành thời gian thực cho Lãnh đạo Tỉnh/Thành phố.</p>
            </div>
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 24px; border-radius: 16px;">
              <h4 style="font-size: 1.1rem; font-weight: 800; color: #0066CC; margin: 0 0 10px;">📑 Một Cửa Điện Tử &amp; Dịch Vụ Công</h4>
              <p style="font-size: 0.9rem; color: #64748B; margin: 0;">Tối ưu hóa quy trình tiếp nhận và giải quyết thủ tục hành chính công trực tuyến mức độ 4.</p>
            </div>
          </div>
        </div>
      `
    },
    /* ============================================================
     * TRANG BẢNG GIÁ & HỆ SINH THÁI SỐ CHUYÊN NGHIỆP
     * ============================================================ */
    'bang-gia': {
      breadcrumb: ['Bảng giá', 'Bảng giá & Gói cước tổng hợp'],
      title: 'Bảng Giá Chi Tiết Dịch Vụ Số VNPT',
      subtitle: 'Bảng giá minh bạch, tối ưu chi phí & cam kết SLA chất lượng hàng đầu',
      icon: 'tags',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          
          <!-- Banner Khuyến Mãi -->
          <div style="background: linear-gradient(135deg, #0F172A, #0055BB); padding: 24px 30px; border-radius: 20px; color: white; display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 36px; box-shadow: 0 10px 30px rgba(0,102,204,0.2);">
            <div>
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span style="background: #FF6B00; color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 800;">ƯU ĐÃI THÁNG 8</span>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: white; margin: 0;">Chiết khấu lên tới 25% khi Đăng ký Combo Chuyển Đổi Số SME</h3>
              </div>
              <p style="font-size: 0.9rem; color: #94A3B8; margin: 0;">Áp dụng cho khách hàng đăng ký trọn bộ SmartCA + VNPT Invoice + FiberVNN Internet.</p>
            </div>
            <button class="btn-goto-contact pg-goto-contact" style="background: linear-gradient(135deg, #00AAFF, #0066CC); color: white; border: none; padding: 12px 24px; border-radius: 30px; font-weight: 700; font-size: 0.9rem; cursor: pointer; white-space: nowrap; box-shadow: 0 4px 15px rgba(0,170,255,0.4);">
              <i data-lucide="phone"></i> Nhận tư vấn ngay
            </button>
          </div>

          <!-- Danh mục 1: Cloud & Server -->
          <div style="margin-bottom: 40px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #E2E8F0;">
              <div style="width: 36px; height: 36px; border-radius: 10px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;"><i data-lucide="cloud"></i></div>
              <h3 style="font-size: 1.35rem; font-weight: 800; color: #0F172A; margin: 0;">1. Điện Toán Đám Mây VNPT Cloud &amp; Máy Chủ Áo</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
              
              <!-- Thẻ Gói 1 -->
              <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; transition: 0.3s; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
                <div>
                  <h4 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 6px;">Cloud Doanh Nghiệp (SME)</h4>
                  <p style="font-size: 0.85rem; color: #64748B; margin: 0 0 16px;">Phù hợp cho Website doanh nghiệp &amp; CSDL vừa</p>
                  <div style="font-size: 1.6rem; font-weight: 900; color: #0066CC; margin-bottom: 16px;">1.800.000 ₫ <span style="font-size: 0.85rem; font-weight: 600; color: #94A3B8;">/tháng</span></div>
                  <ul style="padding-left: 18px; margin: 0 0 20px 0; font-size: 0.88rem; color: #475569; line-height: 1.8;">
                    <li><strong>2 vCPU</strong> High Performance</li>
                    <li><strong>4 GB</strong> RAM DDR4</li>
                    <li><strong>80 GB</strong> SSD NVMe Enterprise</li>
                    <li>1 IP Tĩnh Dedicated miễn phí</li>
                    <li>Cam kết SLA Uptime 99.99%</li>
                  </ul>
                </div>
                <button class="btn-add-cart" data-id="cloud-doanh-nghiep-sme" data-name="Cloud Doanh Nghiệp (SME)" data-price="1800000" data-icon="cloud" data-color="#0066CC" style="width: 100%; background: #0066CC; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: 0.2s;">
                  <i data-lucide="shopping-cart"></i> Đăng ký gói này
                </button>
              </div>

              <!-- Thẻ Gói 2: Nổi bật -->
              <div style="background: white; border: 2px solid #00AAFF; border-radius: 18px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; position: relative; box-shadow: 0 10px 25px rgba(0,170,255,0.15);">
                <span style="position: absolute; top: -14px; right: 20px; background: linear-gradient(135deg, #00AAFF, #0066CC); color: white; padding: 4px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;">PHỔ BIẾN NHẤT</span>
                <div>
                  <h4 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 6px;">Cloud Enterprise</h4>
                  <p style="font-size: 0.85rem; color: #64748B; margin: 0 0 16px;">Tối ưu cho hệ thống ERP &amp; Ứng dụng tải cao</p>
                  <div style="font-size: 1.6rem; font-weight: 900; color: #00AAFF; margin-bottom: 16px;">2.500.000 ₫ <span style="font-size: 0.85rem; font-weight: 600; color: #94A3B8;">/tháng</span></div>
                  <ul style="padding-left: 18px; margin: 0 0 20px 0; font-size: 0.88rem; color: #475569; line-height: 1.8;">
                    <li><strong>4 vCPU</strong> High Performance</li>
                    <li><strong>8 GB</strong> RAM DDR4</li>
                    <li><strong>160 GB</strong> SSD NVMe Enterprise</li>
                    <li>Tự động Auto Snapshot hàng ngày</li>
                    <li>Bảo mật cách ly Tường lửa VPS</li>
                  </ul>
                </div>
                <button class="btn-add-cart" data-id="cloud-enterprise" data-name="Cloud Enterprise" data-price="2500000" data-icon="cloud" data-color="#00AAFF" style="width: 100%; background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: 0.2s;">
                  <i data-lucide="zap"></i> Đăng ký ngay
                </button>
              </div>

              <!-- Thẻ Gói 3 -->
              <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
                <div>
                  <h4 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 6px;">Cloud High-Spec (Y tế/Giáo dục)</h4>
                  <p style="font-size: 0.85rem; color: #64748B; margin: 0 0 16px;">Cấu hình cực mạnh cho bệnh viện &amp; trường học</p>
                  <div style="font-size: 1.6rem; font-weight: 900; color: #0F172A; margin-bottom: 16px;">7.500.000 ₫ <span style="font-size: 0.85rem; font-weight: 600; color: #94A3B8;">/tháng</span></div>
                  <ul style="padding-left: 18px; margin: 0 0 20px 0; font-size: 0.88rem; color: #475569; line-height: 1.8;">
                    <li><strong>16 vCPU</strong> Full Capacity</li>
                    <li><strong>32 GB</strong> RAM DDR4</li>
                    <li><strong>500 GB</strong> SSD NVMe Ultra Speed</li>
                    <li>Khôi phục thảm họa Multi-Region</li>
                    <li>Hỗ trợ kỹ thuật 24/7/365 Dedicated</li>
                  </ul>
                </div>
                <button class="btn-add-cart" data-id="cloud-high-spec" data-name="Cloud High-Spec (Y tế/Giáo dục)" data-price="7500000" data-icon="server" data-color="#0F172A" style="width: 100%; background: #0F172A; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: 0.2s;">
                  <i data-lucide="shopping-cart"></i> Đăng ký gói này
                </button>
              </div>

            </div>
          </div>

          <!-- Danh mục 2: AI & Cyber Security -->
          <div style="margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #E2E8F0;">
              <div style="width: 36px; height: 36px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;"><i data-lucide="shield"></i></div>
              <h3 style="font-size: 1.35rem; font-weight: 800; color: #0F172A; margin: 0;">2. Trí Tuệ Nhân Tạo AI &amp; An Toàn Thông Tin</h3>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
              <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">VNPT AI OCR &amp; Tự động hóa</h4>
                  <p style="font-size: 0.85rem; color: #64748B; margin: 0 0 10px;">Bóc tách dữ liệu giấy tờ chính xác 99.8%</p>
                  <div style="font-size: 1.35rem; font-weight: 900; color: #0066CC;">3.200.000 ₫ <span style="font-size: 0.8rem; font-weight: 600; color: #94A3B8;">/tháng</span></div>
                </div>
                <button class="btn-add-cart" data-id="vnpt-ai-ocr" data-name="VNPT AI OCR &amp; Tự động hóa" data-price="3200000" data-icon="cpu" data-color="#0066CC" style="background: #0066CC; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                  <i data-lucide="shopping-cart"></i> Mua ngay
                </button>
              </div>

              <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 24px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">VNPT Cyber Security SOC 24/7</h4>
                  <p style="font-size: 0.85rem; color: #64748B; margin: 0 0 10px;">Chống tấn công WAF &amp; Anti-DDoS 100Gbps</p>
                  <div style="font-size: 1.35rem; font-weight: 900; color: #16A34A;">1.800.000 ₫ <span style="font-size: 0.8rem; font-weight: 600; color: #94A3B8;">/tháng</span></div>
                </div>
                <button class="btn-add-cart" data-id="vnpt-soc-security" data-name="VNPT Bảo mật &amp; An ninh mạng SOC 24/7" data-price="1800000" data-icon="shield-check" data-color="#16A34A" style="background: #16A34A; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                  <i data-lucide="shopping-cart"></i> Mua ngay
                </button>
              </div>
            </div>
          </div>

        </div>
      `
    },
    'he-sinh-thai': {
      breadcrumb: ['Hệ sinh thái', 'Hệ sinh thái số VNPT Digital'],
      title: 'Hệ Sinh Thái Dịch Vụ Số Toàn Diện VNPT',
      subtitle: 'Trục kết nối hạ tầng Đám mây, Bảo mật, Trí tuệ nhân tạo AI & Giải pháp quản trị 4.0',
      icon: 'layers',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          
          <!-- Thống kê hệ sinh thái -->
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 36px;">
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #00AAFF;">12+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Sản phẩm số chủ lực</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #22C55E;">100.000+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Doanh nghiệp tin dùng</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #EAB308;">Tier III</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Chuẩn Data Center Quốc tế</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #A855F7;">24/7/365</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Hỗ trợ chuyên gia 1800 1260</div>
            </div>
          </div>

          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 20px;">4 Tầng Kiến Trúc Hệ Sinh Thái Số VNPT</h2>

          <!-- 4 Tầng Hệ sinh thái -->
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 40px;">
            
            <!-- Tầng 1 -->
            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 26px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><i data-lucide="server"></i></div>
                <div>
                  <span style="font-size: 0.75rem; font-weight: 800; color: #0066CC; text-transform: uppercase;">Tầng 1</span>
                  <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">Hạ Tầng &amp; Kết Nối Băng Thống Rộng</h3>
                </div>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; line-height: 1.6; margin-bottom: 14px;">Hạ tầng Điện toán đám mây VNPT Cloud, Trung tâm dữ liệu Tier III Quốc tế, Mạng di động 5G siêu băng rộng và Cáp quang biển quốc tế FiberVNN.</p>
              <a href="#page=ha-tang-so" data-page="ha-tang-so" style="font-size: 0.85rem; font-weight: 700; color: #0066CC; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">Khám phá Hạ tầng số <i data-lucide="chevron-right" style="width:14px;height:14px;"></i></a>
            </div>

            <!-- Tầng 2 -->
            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 26px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><i data-lucide="shield-check"></i></div>
                <div>
                  <span style="font-size: 0.75rem; font-weight: 800; color: #16A34A; text-transform: uppercase;">Tầng 2</span>
                  <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">Bảo Mật &amp; Định Danh Pháp Lý Số</h3>
                </div>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; line-height: 1.6; margin-bottom: 14px;">Chữ ký số từ xa SmartCA, Hóa đơn điện tử VNPT Invoice, Hợp đồng số eContract và Trung tâm giám sát An ninh mạng SOC 24/7/365.</p>
              <a href="#page=bao-mat-an-toan" data-page="bao-mat-an-toan" style="font-size: 0.85rem; font-weight: 700; color: #16A34A; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">Khám phá Bảo mật số <i data-lucide="chevron-right" style="width:14px;height:14px;"></i></a>
            </div>

            <!-- Tầng 3 -->
            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 26px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><i data-lucide="cpu"></i></div>
                <div>
                  <span style="font-size: 0.75rem; font-weight: 800; color: #D97706; text-transform: uppercase;">Tầng 3</span>
                  <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">Trí Tuệ Nhân Tạo &amp; Dữ Liệu Big Data</h3>
                </div>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; line-height: 1.6; margin-bottom: 14px;">Giải pháp bóc tách dữ liệu giấy tờ VNPT AI OCR chính xác 99.8%, Trợ lý ảo AI Chatbot 24/7 và Nền tảng phân tích dữ liệu kinh doanh thông minh.</p>
              <a href="#page=ai-tu-dong-hoa" data-page="ai-tu-dong-hoa" style="font-size: 0.85rem; font-weight: 700; color: #D97706; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">Khám phá Trợ lý AI <i data-lucide="chevron-right" style="width:14px;height:14px;"></i></a>
            </div>

            <!-- Tầng 4 -->
            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 26px; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #FDF4FF; color: #A855F7; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><i data-lucide="building-2"></i></div>
                <div>
                  <span style="font-size: 0.75rem; font-weight: 800; color: #A855F7; text-transform: uppercase;">Tầng 4</span>
                  <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">Giải Pháp Chuyển Đổi Số Ngành</h3>
                </div>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; line-height: 1.6; margin-bottom: 14px;">Bệnh viện số VNPT HIS / EMR, Trường học thông minh vnEdu 4.0, Trung tâm điều hành IOC Chính phủ số và Combo Chuyển đổi số SME.</p>
              <a href="#page=gp-yte-giaoduc" data-page="gp-yte-giaoduc" style="font-size: 0.85rem; font-weight: 700; color: #A855F7; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">Khám phá Giải pháp Y tế &amp; Giáo dục <i data-lucide="chevron-right" style="width:14px;height:14px;"></i></a>
            </div>

          </div>

        </div>
      `
    },
    /* ============================================================
     * TRANG ĐỐI TÁC & TRUNG TÂM LIÊN HỆ 24/7 CHUYÊN NGHIỆP
     * ============================================================ */
    'doi-tac': {
      breadcrumb: ['Đối tác', 'Mạng lưới đối tác chiến lược'],
      title: 'Mạng Lưới Đối Tác Chiến Lược & Hệ Sinh Thái Toàn Cầu',
      subtitle: 'Đồng hành cùng 50+ Tập đoàn công nghệ hàng đầu thế giới kiến tạo giá trị số bền vững',
      icon: 'handshake',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          
          <!-- Thống kê mạng lưới đối tác -->
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 36px;">
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #00AAFF;">50+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Tập đoàn Công nghệ Toàn cầu</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #22C55E;">500+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Đại lý &amp; Đối tác Giải pháp</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #EAB308;">10.000+</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Dự án Chuyển đổi số thành công</div>
            </div>
            <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 16px; color: white; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
              <div style="font-size: 1.8rem; font-weight: 900; color: #A855F7;">99.5%</div>
              <div style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px; font-weight: 600;">Mức độ hài lòng của Đối tác</div>
            </div>
          </div>

          <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 20px;">4 Nhóm Đối Tác Tiên Phong</h2>

          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 36px;">
            
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 18px;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i data-lucide="cpu"></i></div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">1. Đối Tác Hạ Tầng &amp; Phần Cứng</h3>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; margin-bottom: 12px;">Hợp tác cùng <strong>Intel, Dell Technologies, Cisco, Juniper Networks, HP Enterprise</strong> cung cấp máy chủ Data Center Tier III &amp; thiết bị mạng lõi 5G.</p>
              <span style="font-size: 0.8rem; font-weight: 700; color: #0066CC; background: #EFF6FF; padding: 4px 10px; border-radius: 12px;">Hạ tầng Uptime Tier III</span>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 18px;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i data-lucide="cloud"></i></div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">2. Đối Tác Đám Mây &amp; Phần Mềm</h3>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; margin-bottom: 12px;">Hợp tác chiến lược cùng <strong>Microsoft, VMware, RedHat, Oracle, SAP</strong> tích hợp hệ sinh thái Cloud Native và Hệ thống quản trị doanh nghiệp ERP.</p>
              <span style="font-size: 0.8rem; font-weight: 700; color: #16A34A; background: #DCFCE7; padding: 4px 10px; border-radius: 12px;">Tích hợp Cloud Native 4.0</span>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 18px;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i data-lucide="shield-check"></i></div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">3. Đối Tác An Ninh Mạng Cyber Security</h3>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; margin-bottom: 12px;">Đồng hành cùng <strong>Palo Alto Networks, Fortinet, CheckPoint, Kaspersky</strong> vận hành Trung tâm giám sát An ninh mạng SOC 24/7.</p>
              <span style="font-size: 0.8rem; font-weight: 700; color: #D97706; background: #FEF3C7; padding: 4px 10px; border-radius: 12px;">Chuẩn ISO/IEC 27001</span>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 24px; border-radius: 18px;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #FDF4FF; color: #A855F7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i data-lucide="credit-card"></i></div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">4. Đối Tác Tài Chính &amp; Thanh Toán</h3>
              </div>
              <p style="font-size: 0.9rem; color: #64748B; margin-bottom: 12px;">Kết nối liên thông cùng <strong>Vietcombank, BIDV, VietinBank, MoMo, VietQR</strong> khởi tạo hạ tầng thanh toán hóa đơn điện tử siêu tốc.</p>
              <span style="font-size: 0.8rem; font-weight: 700; color: #A855F7; background: #FDF4FF; padding: 4px 10px; border-radius: 12px;">Thanh toán VietQR tức thì</span>
            </div>

          </div>

          <!-- Khối Đăng ký trở thành Đối tác -->
          <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; padding: 30px; border-radius: 20px; text-align: center;">
            <h3 style="font-size: 1.3rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Trở Thành Đối Tác Hợp Tác Cùng VNPT</h3>
            <p style="font-size: 0.92rem; color: #64748B; margin: 0 0 20px;">Gia nhập mạng lưới phát triển đại lý &amp; đối tác công nghệ để nhận chính sách chiết khấu tới 30% và hỗ trợ truyền thông 1-1.</p>
            <button class="pg-goto-contact" style="background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; border: none; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 0.9rem; cursor: pointer; box-shadow: 0 4px 15px rgba(0,102,204,0.3);">
              <i data-lucide="handshake"></i> Gửi Yêu Cầu Hợp Tác Ngay
            </button>
          </div>

        </div>
      `
    },
    'lien-he': {
      breadcrumb: ['Liên hệ', 'Trung tâm hỗ trợ & Liên hệ 24/7'],
      title: 'Trung Tâm Liên Hệ & Hỗ Trợ Khách Hàng VNPT',
      subtitle: 'Đội ngũ chuyên gia CNTT & Viễn thông luôn sẵn sàng phục vụ 24/7/365',
      icon: 'phone-call',
      body: `
        <div class="pg-section" style="line-height: 1.8; color: #334155;">
          
          <!-- 4 Khung kênh liên hệ nhanh -->
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 36px;">
            
            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 16px; padding: 22px 18px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin: 0 auto 12px;"><i data-lucide="phone"></i></div>
              <h4 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Tổng Đài 24/7</h4>
              <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 8px;">Miễn cước toàn quốc</p>
              <strong style="font-size: 1.15rem; color: #0066CC; display: block;">1800 1260</strong>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 16px; padding: 22px 18px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 48px; height: 48px; border-radius: 50%; background: #F0FDF4; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin: 0 auto 12px;"><i data-lucide="mail"></i></div>
              <h4 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Email Tiếp Nhận</h4>
              <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 8px;">Phản hồi trong 2 giờ</p>
              <strong style="font-size: 0.95rem; color: #16A34A; display: block;">contact@vnpt.vn</strong>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 16px; padding: 22px 18px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 48px; height: 48px; border-radius: 50%; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin: 0 auto 12px;"><i data-lucide="map-pin"></i></div>
              <h4 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Trụ Sở Chính</h4>
              <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 8px;">Tòa nhà VNPT Tower</p>
              <strong style="font-size: 0.88rem; color: #D97706; display: block;">57 Huỳnh Thúc Kháng, Hà Nội</strong>
            </div>

            <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 16px; padding: 22px 18px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
              <div style="width: 48px; height: 48px; border-radius: 50%; background: #FDF4FF; color: #A855F7; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin: 0 auto 12px;"><i data-lucide="bot"></i></div>
              <h4 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px;">Trợ Lý AI Chatbot</h4>
              <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 8px;">Tư vấn giải đáp tức thì</p>
              <strong style="font-size: 0.95rem; color: #A855F7; display: block;">VNPT Smart AI</strong>
            </div>

          </div>

          </div>

          <!-- Biểu mẫu gửi yêu cầu tư vấn & Liên hệ trực tiếp -->
          <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 28px; margin-bottom: 32px; align-items: start;">
            
            <!-- Biểu mẫu Form -->
            <div style="background: white; border: 1.5px solid #E2E8F0; padding: 28px; border-radius: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #0066CC; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;"><i data-lucide="send"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0;">Gửi Yêu Cầu Tư Vấn &amp; Báo Giá</h3>
              </div>
              <p style="font-size: 0.88rem; color: #64748B; margin-bottom: 20px;">Vui lòng điền thông tin bên dưới, chuyên viên VNPT sẽ liên hệ hỗ trợ bạn trong vòng 15 phút.</p>

              <form id="contactFormPage" style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                  <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Họ và Tên <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="name" required placeholder="Nguyễn Văn A" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none;" />
                  </div>
                  <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Số Điện Thoại <span style="color: #EF4444;">*</span></label>
                    <input type="tel" name="phone" required placeholder="0912 345 678" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none;" />
                  </div>
                </div>

                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Dịch Vụ Quan Tâm</label>
                  <select name="service" style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none; background: white;">
                    <option value="cloud_computing">☁️ Điện toán đám mây Cloud Computing</option>
                    <option value="smartca">🔑 Chữ ký số SmartCA & Hóa đơn số</option>
                    <option value="fibervnn">⚡ Internet Cáp quang FiberVNN</option>
                    <option value="ai_security">🤖 AI OCR & Bảo mật Cyber Security</option>
                    <option value="sme_combo">🏢 Gói Combo Chuyển đổi số Doanh nghiệp SME</option>
                    <option value="enterprise_cloud">🏛️ Giải pháp May đo cho Tập đoàn / Government</option>
                  </select>
                </div>

                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Nội Dung Yêu Cầu / Thắc Mắc</label>
                  <textarea name="message" rows="3" placeholder="Nhập nhu cầu chi tiết của quý khách..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.9rem; outline: none; resize: vertical;"></textarea>
                </div>

                <button type="submit" id="btnSubmitContactForm" style="background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; border: none; padding: 12px 24px; border-radius: 30px; font-weight: 700; font-size: 0.92rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,102,204,0.3); transition: 0.2s;">
                  <i data-lucide="send"></i> 🚀 Gửi Yêu Cầu Tư Vấn Ngay
                </button>
              </form>
            </div>

            <!-- Văn phòng chi nhánh & Quy trình hỗ trợ -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
              
              <!-- Danh sách văn phòng -->
              <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; padding: 22px; border-radius: 18px;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 14px;">Mạng Lưới Phủ Khắp 3 Miền</h3>
                
                <div style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #E2E8F0;">
                  <strong style="color: #0066CC; font-size: 0.9rem; display: block; margin-bottom: 2px;">📍 Hà Nội (Miền Bắc)</strong>
                  <span style="font-size: 0.85rem; color: #475569;">Tòa nhà VNPT Tower, 57 Huỳnh Thúc Kháng, Đống Đa.</span>
                </div>

                <div style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #E2E8F0;">
                  <strong style="color: #0066CC; font-size: 0.9rem; display: block; margin-bottom: 2px;">📍 TP. Hồ Chí Minh (Miền Nam)</strong>
                  <span style="font-size: 0.85rem; color: #475569;">125 Hai Bà Trưng, Phường Bến Nghé, Quận 1.</span>
                </div>

                <div>
                  <strong style="color: #0066CC; font-size: 0.9rem; display: block; margin-bottom: 2px;">📍 Đà Nẵng (Miền Trung)</strong>
                  <span style="font-size: 0.85rem; color: #475569;">346 Đường 2/9, Phường Hòa Cường Bắc, Quận Hải Châu.</span>
                </div>
              </div>

              <!-- Cam kết hỗ trợ 4 bước -->
              <div style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 22px; border-radius: 18px; color: white;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #00AAFF; margin: 0 0 12px;">Quy Trình Tiếp Nhận 24/7</h3>
                
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: #CBD5E1;">
                  <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: #00AAFF; color: white; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0;">1</span>
                    <span><strong>Tiếp nhận yêu cầu</strong> qua Hotline 1800 1260 / Form.</span>
                  </div>
                  <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: #00AAFF; color: white; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0;">2</span>
                    <span><strong>Chuyển Kỹ sư chuyên trách</strong> hỗ trợ kỹ thuật.</span>
                  </div>
                  <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: #00AAFF; color: white; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0;">3</span>
                    <span><strong>Phản hồi &amp; Xử lý trong 15 phút</strong> cho hạ tầng.</span>
                  </div>
                  <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: #22C55E; color: white; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0;">4</span>
                    <span><strong>Xác nhận hoàn tất &amp; Hài lòng</strong> của khách hàng.</span>
                  </div>
                </div>
              </div>

            </div>

          </div>

        </div>
      `
    }
  };

  /* Alias mapping cho các route dạng viết tắt trong CSDL MySQL */
  PAGES['contact'] = PAGES['lien-he'];
  PAGES['pricing'] = PAGES['bang-gia'];
  PAGES['gp-sme'] = PAGES['gp-doanh-nghiep-sme'];
  PAGES['gp-enterprise'] = PAGES['gp-tap-doan-lon'];
  PAGES['gp-chinh-phu'] = PAGES['gp-chinh-phu-so'];
  PAGES['y-te-giao-duc'] = PAGES['gp-yte-giaoduc'];
  PAGES['gp-y-te-giao-duc'] = PAGES['gp-yte-giaoduc'];
  PAGES['doanh-nghiep-sme'] = PAGES['gp-doanh-nghiep-sme'];
  PAGES['tap-doan-lon'] = PAGES['gp-tap-doan-lon'];
  PAGES['chinh-phu-so'] = PAGES['gp-chinh-phu-so'];
  PAGES['bao-mat-so'] = PAGES['bao-mat-an-toan'];

  const PAGE_ICONS = {
    'gioi-thieu': 'building-2',
    'tam-nhin-su-menh': 'compass',
    'doi-ngu-lanh-dao': 'users',
    'thanh-tuu': 'award',
    'ha-tang-so': 'server',
    'dieu-khoan-dich-vu': 'file-text',
    'chinh-sach-bao-mat': 'shield',
  };

  /* ============================================================
   * 2. PAGE RENDERER LOGIC
   * ============================================================ */
  let pageContainer = null;

  function ensureContainer() {
    if (pageContainer) return pageContainer;
    pageContainer = document.getElementById('pageView');
    if (!pageContainer) {
      pageContainer = document.createElement('div');
      pageContainer.id = 'pageView';
      pageContainer.className = 'page-view';
      document.body.appendChild(pageContainer);
    }
    return pageContainer;
  }

  function renderPage(page) {
    if (!page) return false;
    const c = ensureContainer();

    const crumbs = (page.breadcrumb || []).map((b, i) =>
      i === page.breadcrumb.length - 1
        ? `<span class="pg-crumb-current">${b}</span>`
        : `<span>${b}</span><i data-lucide="chevron-right" class="pg-crumb-sep"></i>`
    ).join('');

    const isPurchasable = page.ma_san_pham && page.gia_niem_yet;
    let buyNowBlock = '';

    if (isPurchasable) {
      const priceFormatted = new Intl.NumberFormat('vi-VN').format(page.gia_niem_yet) + ' ₫';
      buyNowBlock = `
        <div class="pg-buy-action" style="margin: 40px 0 20px 0; padding: 30px; background: rgba(229, 62, 62, 0.05); border-radius: 12px; border: 1px dashed rgba(229, 62, 62, 0.3); text-align: center;">
          <h3 style="margin: 0; color: #333;">Sẵn sàng bứt phá cùng giải pháp này?</h3>
          <h4 style="margin: 10px 0; color: #E53E3E;">Phí dịch vụ: <strong>${priceFormatted}</strong></h4>
          <button class="btn-buy-now btn-add-cart" style="background: #E53E3E; color: white; border: none; border-radius: 8px; padding: 12px 30px; font-weight: 600; cursor: pointer;" data-id="${page.ma_san_pham}">
            <i data-lucide="zap"></i> Mua ngay
          </button>
        </div>
      `;
    }

    const categoryLabel = (page.breadcrumb && page.breadcrumb.length > 0) ? page.breadcrumb[0] : 'VNPT Telecom';

    c.innerHTML = `
      <div class="pg-hero">
        <div class="pg-hero-orbs"><span></span><span></span></div>
        <div class="container">
          <button class="pg-back" id="pgBack"><i data-lucide="arrow-left"></i> Về trang chủ</button>
          <div class="pg-breadcrumb"><a href="#home" data-page="home">Trang chủ</a><i data-lucide="chevron-right" class="pg-crumb-sep"></i>${crumbs}</div>
          <div class="pg-hero-title-row">
            <div class="pg-hero-icon"><i data-lucide="${page.icon || 'newspaper'}"></i></div>
            <div class="pg-hero-title-box">
              <div class="pg-hero-category-tag"><i data-lucide="${page.icon || 'newspaper'}"></i> ${categoryLabel}</div>
              <h1 class="pg-hero-h1">${page.title}</h1>
              ${page.subtitle ? `<p class="pg-hero-sub">${page.subtitle}</p>` : ''}
            </div>
          </div>
        </div>
      </div>
      <div class="pg-content container">
        ${page.body}
        ${buyNowBlock}
        <div class="pg-cta-box" style="margin-top:40px;">
          <div>
            <h3>Cần tư vấn thêm? Hãy để VNPT đồng hành cùng bạn</h3>
            <p>Đội ngũ chuyên gia của VNPT sẵn sàng hỗ trợ doanh nghiệp bạn 24/7.</p>
          </div>
          <button class="btn-primary pg-goto-contact"><i data-lucide="phone"></i> Liên hệ ngay</button>
        </div>
      </div>`;

    document.body.classList.add('page-open');
    c.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'auto' });
    if (window.lucide) lucide.createIcons();
    document.dispatchEvent(new CustomEvent('vnpt:pagechange'));

    c.querySelector('#pgBack')?.addEventListener('click', goHome);
    c.querySelectorAll('.pg-goto-contact').forEach(b =>
      b.addEventListener('click', () => { goHome(); setTimeout(scrollToContact, 250); })
    );
    c.querySelectorAll('[data-page]').forEach(el => {
      el.addEventListener('click', (e) => {
        const key = el.getAttribute('data-page');
        if (key) {
          e.preventDefault();
          navigateTo(key);
        }
      });
    });
    return true;
  }

  function scrollToContact() {
    const t = document.getElementById('contact');
    if (t) {
      const top = t.getBoundingClientRect().top + window.scrollY - 110;
      window.scrollTo({ top, behavior: 'smooth' });
    }
  }

  function goHome() {
    document.body.classList.remove('page-open');
    if (pageContainer) pageContainer.classList.remove('active');
    history.replaceState(null, '', '#home');
    document.dispatchEvent(new CustomEvent('vnpt:pagechange'));
  }

  /* ============================================================
   * 3. NAVIGATE TO FUNCTION (DỮ LIỆU ĐỘNG TỪ MYSQL)
   * ============================================================ */
  async function navigateTo(key) {
    if (!key) return;
    if (key === 'home') { goHome(); return; }
    if (key === 'contact') { goHome(); setTimeout(scrollToContact, 200); return; }

    const cleanKey = decodeURIComponent(key).trim();
    let pageInfo = PAGES[cleanKey] || PAGES[key];

    // Thử gọi API backend kiểm tra cả bài viết & trang động từ MySQL
    try {
      const response = await fetch(`backend/api/pages.php?slug=${encodeURIComponent(cleanKey)}`);
      if (response.ok) {
        const res = await response.json();
        if (res.status === 'success') {
          const data = res.data;
          if (res.type === 'category') {
            const posts = data.posts || [];
            let postsListHtml = '';

            if (posts.length === 0) {
              postsListHtml = `
                <div style="padding:60px 20px; text-align:center; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1; margin-top:10px;">
                  <i data-lucide="newspaper" style="width:44px; height:44px; color:#94a3b8; margin-bottom:12px;"></i>
                  <h4 style="margin:0 0 6px 0; color:#334155; font-size:1.1rem;">Chưa có bài viết nào</h4>
                  <p style="margin:0; color:#64748b; font-size:0.9rem;">Chuyên mục này hiện chưa có bài viết nào được xuất bản.</p>
                </div>
              `;
            } else {
              postsListHtml = posts.map(p => `
                <article class="vnpt-news-item-horizontal" style="display:flex; gap:24px; padding:24px 0; border-bottom:1px solid #e2e8f0; align-items:flex-start;">
                  ${p.anh_bia ? `
                    <a href="#page=${encodeURIComponent(p.slug)}" data-page="${p.slug}" style="display:block; flex-shrink:0; width:220px; height:140px; border-radius:8px; overflow:hidden; background:#f1f5f9;">
                      <img src="${p.anh_bia}" alt="${p.title}" style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;">
                    </a>
                  ` : ''}
                  <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                    <h3 style="font-size:1.18rem; font-weight:700; line-height:1.4; margin:0 0 8px 0;">
                      <a href="#page=${encodeURIComponent(p.slug)}" data-page="${p.slug}" style="color:#0f172a; text-decoration:none; transition:color 0.2s;">
                        ${p.title}
                      </a>
                    </h3>
                    <p style="color:#64748b; font-size:0.92rem; line-height:1.6; margin:0 0 10px 0; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                      ${p.tom_tat || 'Cung cấp thông tin chi tiết về giải pháp và dịch vụ số VNPT...'}
                    </p>
                    <div style="font-size:0.83rem; color:#94a3b8; margin-bottom:10px;">
                      ${p.ngay_xuat_ban}
                    </div>
                    <div>
                      <a href="#page=${encodeURIComponent(p.slug)}" data-page="${p.slug}" style="color:#0066CC; font-weight:600; font-size:0.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                        Chi tiết <i data-lucide="chevron-right" style="width:14px;height:14px;"></i>
                      </a>
                    </div>
                  </div>
                </article>
              `).join('');
            }

            const categoriesList = ((data.categories && data.categories.length > 0) ? data.categories : [
              { ten: 'Dịch vụ số', slug: 'dich-vu-so' },
              { ten: 'Giải pháp doanh nghiệp', slug: 'giai-phap-doanh-nghiep' },
              { ten: 'Hạ tầng Cloud & Server', slug: 'ha-tang-cloud-server' },
              { ten: 'Thông cáo báo chí', slug: 'thong-cao-bao-chi' },
              { ten: 'Tin tức công nghệ', slug: 'tin-tuc-cong-nghe' }
            ]).filter(c => c.slug !== 'chua-duoc-phan-loai' && !c.ten.includes('Chưa được phân loại'));

            const categoriesNavHtml = categoriesList.map(cat => `
              <li>
                <a href="#page=${encodeURIComponent(cat.slug)}" data-page="${cat.slug}" style="color:${cleanKey === cat.slug ? '#0066CC' : '#334155'}; font-weight:${cleanKey === cat.slug ? '700' : '600'}; text-decoration:none; display:flex; align-items:center; gap:8px; font-size:0.9rem; text-transform:uppercase;">
                  <span style="color:#0066CC; font-weight:bold;">•</span> ${cat.ten}
                </a>
              </li>
            `).join('');

            const sidebarHtml = `
              <aside class="vnpt-news-sidebar" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; box-shadow:0 4px 15px rgba(0,0,0,0.02); position:sticky; top:110px;">
                <h3 style="font-size:1.1rem; font-weight:700; color:#0f172a; margin:0 0 16px 0; padding-bottom:12px; border-bottom:2px solid #0066CC;">
                  Tin tức - <span style="color:#0066CC;">Chuyên Mục</span>
                </h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                  <li>
                    <a href="#page=tin-tuc" data-page="tin-tuc" style="color:${cleanKey === 'tin-tuc' ? '#0066CC' : '#334155'}; font-weight:${cleanKey === 'tin-tuc' ? '700' : '600'}; text-decoration:none; display:flex; align-items:center; gap:8px; font-size:0.9rem;">
                      <span style="color:#0066CC; font-weight:bold;">•</span> TẤT CẢ TIN TỨC
                    </a>
                  </li>
                  ${categoriesNavHtml}
                </ul>
              </aside>
            `;

            renderPage({
              breadcrumb: ['Tin tức', data.title],
              title: data.title,
              subtitle: data.subtitle || 'Kiến thức, sự kiện và thông tin mới nhất từ VNPT',
              icon: 'newspaper',
              body: `
                <div class="category-horizontal-wrapper" style="max-width: 1180px; margin: 0 auto; padding: 10px 0; display: grid; grid-template-columns: 1fr 300px; gap: 40px; align-items: start;">
                  <div class="vnpt-news-left-list">
                    ${postsListHtml}
                  </div>
                  ${sidebarHtml}
                </div>
              `
            });
            history.replaceState(null, '', '#page=' + encodeURIComponent(cleanKey));
            return;
          } else if (res.type === 'post') {
            const hasBodyImg = data.noi_dung && (data.noi_dung.includes('<img') || (data.anh_bia && data.noi_dung.includes(data.anh_bia)));
            const coverImg = (data.anh_bia && !hasBodyImg) ? `
              <div style="text-align:center; margin-bottom:28px; background:#f8fafc; border-radius:16px; padding:10px; border:1px solid #e2e8f0; box-shadow:0 8px 24px rgba(0,0,0,0.06);">
                <img src="${data.anh_bia}" alt="${data.title}" style="width:100%; max-height:480px; object-fit:cover; border-radius:12px; display:block; margin:0 auto;">
              </div>
            ` : '';

            const leadExcerpt = data.subtitle ? `
              <div style="font-size:1.06rem; font-weight:600; line-height:1.75; color:#1e293b; margin-bottom:28px; padding:18px 24px; background:linear-gradient(135deg, #EFF6FF, #F8FAFC); border-left:4.5px solid #0066CC; border-radius:0 14px 14px 0; box-shadow:0 3px 12px rgba(0,102,204,0.06);">
                ${data.subtitle}
              </div>
            ` : '';

            const metaBar = `
              <div style="display:flex; align-items:center; gap:16px; margin-bottom:22px; padding-bottom:16px; border-bottom:1px solid #e2e8f0; font-size:0.88rem; color:#64748b; flex-wrap:wrap;">
                <span style="display:inline-flex; align-items:center; gap:6px; background:#EFF6FF; color:#0066CC; padding:5px 14px; border-radius:20px; font-weight:700; font-size:0.82rem;">
                  <i class="fa-solid fa-building"></i> VNPT Telecom
                </span>
                <span style="display:inline-flex; align-items:center; gap:6px; color:#64748b; font-weight:600;">
                  <i class="fa-regular fa-clock" style="color:#0066CC;"></i> ${data.ngay_xuat_ban}
                </span>
                <span style="display:inline-flex; align-items:center; gap:6px; color:#10B981; font-weight:700; margin-left:auto; font-size:0.82rem;">
                  <i class="fa-solid fa-check-circle"></i> Đã xác minh chính thức
                </span>
              </div>
            `;

            // Tự động bọc table trong container scroll và làm sạch margin âm do MS Word tạo ra
            let rawContent = data.noi_dung || '<p>Nội dung bài viết đang được cập nhật...</p>';
            rawContent = rawContent.replace(/margin-left\s*:\s*-[^;"]+;?/gi, 'margin-left: 0;');
            if (rawContent.includes('<table') && !rawContent.includes('table-responsive-wrapper')) {
              rawContent = rawContent.replace(/<table/g, '<div class="table-responsive-wrapper" style="overflow-x:auto; margin:24px 0; border-radius:12px; border:1px solid #e2e8f0; max-width:100%;"><table').replace(/<\/table>/g, '</table></div>');
            }

            const relatedList = (data.related_posts || []).map(r => `
              <article class="vnpt-related-item" style="display:flex; gap:14px; padding-bottom:14px; margin-bottom:14px; border-bottom:1px solid #f1f5f9; align-items:flex-start;">
                ${r.anh_bia ? `
                  <a href="#page=${encodeURIComponent(r.slug)}" data-page="${r.slug}" style="display:block; flex-shrink:0; width:95px; height:65px; border-radius:8px; overflow:hidden; background:#f1f5f9; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                    <img src="${r.anh_bia}" alt="${r.title}" style="width:100%; height:100%; object-fit:cover;">
                  </a>
                ` : ''}
                <div style="flex:1; min-width:0;">
                  <h4 style="font-size:0.92rem; font-weight:700; line-height:1.35; margin:0 0 6px 0;">
                    <a href="#page=${encodeURIComponent(r.slug)}" data-page="${r.slug}" style="color:#1e293b; text-decoration:none; transition:color 0.2s;">
                      ${r.title}
                    </a>
                  </h4>
                  <div style="font-size:0.78rem; color:#94a3b8;">
                    ${r.ngay_xuat_ban}
                  </div>
                </div>
              </article>
            `).join('');

            const sidebarRelatedHtml = `
              <aside class="vnpt-related-sidebar" style="background:#ffffff; border-radius:16px; padding:24px; border:1.5px solid #e2e8f0; position:sticky; top:110px; box-shadow:0 4px 16px rgba(0,0,0,0.03);">
                <h3 style="font-size:1.15rem; font-weight:800; color:#0f172a; margin:0 0 16px 0; padding-bottom:10px; border-bottom:2.5px solid #0066CC;">
                  Tin liên quan
                </h3>
                <div class="vnpt-related-list">
                  ${relatedList || '<p style="color:#94a3b8; font-size:0.88rem;">Chưa có bài viết liên quan.</p>'}
                </div>
              </aside>
            `;

            renderPage({
              breadcrumb: ['VNPT', 'Tin tức', data.title],
              title: data.title,
              subtitle: 'Thông tin & Sự kiện chính thức từ VNPT Telecom',
              icon: 'newspaper',
              body: `
                <div class="single-article-vnpt-wrapper" style="max-width: 1180px; margin: 0 auto; padding: 10px 0; display: grid; grid-template-columns: 1fr 340px; gap: 40px; align-items: start;">
                  
                  <!-- Left Main Article Column -->
                  <div class="single-article-left-content" style="background:#ffffff; padding:10px 0;">
                    ${metaBar}
                    ${leadExcerpt}
                    ${coverImg}

                    <div class="pg-section post-content-body" style="font-size:1.05rem; line-height:1.85; color:#1e293b; padding:0;">
                      ${rawContent}
                    </div>

                    <div class="article-share-bar" style="margin-top:40px; padding-top:24px; border-top:1px solid #e2e8f0; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                      <span style="font-weight:600; color:#334155; font-size:0.95rem;">Chia sẻ ngay:</span>
                      <a href="javascript:void(0)" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(location.href), '_blank', 'width=600,height=400')" title="Chia sẻ qua Facebook" style="width:34px; height:34px; background:#3b5998; color:#ffffff; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:1.1rem; text-decoration:none; box-shadow:0 2px 6px rgba(59,89,152,0.3); transition:transform 0.2s;">f</a>
                      <a href="javascript:void(0)" onclick="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(location.href) + '&text=' + encodeURIComponent(document.title), '_blank', 'width=600,height=400')" title="Chia sẻ qua X (Twitter)" style="width:34px; height:34px; background:#14171a; color:#ffffff; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:1rem; text-decoration:none; box-shadow:0 2px 6px rgba(20,23,26,0.3); transition:transform 0.2s;">𝕏</a>
                      <a href="javascript:void(0)" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(location.href), '_blank', 'width=600,height=400')" title="Chia sẻ qua LinkedIn" style="width:34px; height:34px; background:#0077b5; color:#ffffff; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:0.95rem; text-decoration:none; box-shadow:0 2px 6px rgba(0,119,181,0.3); transition:transform 0.2s;">in</a>
                      <button onclick="navigator.clipboard.writeText(location.href).then(() => alert('Đã sao chép liên kết bài viết!'))" title="Sao chép liên kết" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; margin-left:6px;">
                        <i data-lucide="link" style="width:14px;height:14px;"></i> Sao chép liên kết
                      </button>
                    </div>
                  </div>

                  <!-- Right Related News Sidebar -->
                  ${sidebarRelatedHtml}

                </div>
              `
            });
            history.replaceState(null, '', '#page=' + encodeURIComponent(cleanKey));
            return;
          } else if (res.type === 'page') {
            if (pageInfo) {
              if (data.ma_san_pham) pageInfo.ma_san_pham = data.ma_san_pham;
              if (data.gia_niem_yet) pageInfo.gia_niem_yet = data.gia_niem_yet;
            } else {
              pageInfo = {
                breadcrumb: ['Thông tin', data.title],
                title: data.title,
                subtitle: data.subtitle,
                icon: data.icon || 'file-text',
                body: `<div class="pg-section"><p>${data.subtitle}</p></div>`,
                ma_san_pham: data.ma_san_pham,
                gia_niem_yet: data.gia_niem_yet
              };
            }
          }
        }
      }
    } catch (err) {
      console.error('Lỗi tải bài viết từ CSDL:', err);
    }

    if (!pageInfo) {
      console.warn('Không tìm thấy trang:', cleanKey);
      return;
    }

    const title = pageInfo.breadcrumb[pageInfo.breadcrumb.length - 1];
    renderPage({
      ...pageInfo,
      title: pageInfo.title || title,
      subtitle: pageInfo.subtitle || `Thông tin chi tiết về ${title.toLowerCase()}`,
      icon: pageInfo.icon || PAGE_ICONS[cleanKey] || 'file-text',
    });
    history.replaceState(null, '', '#page=' + encodeURIComponent(cleanKey));
  }

  window.VNPTRouter = { navigateTo, goHome, renderCustom: renderPage };

  /* ============================================================
   * 4. SỰ KIỆN KHỞI TẠO ROUTER, DELEGATION & HASH CHANGE
   * ============================================================ */
  document.addEventListener('DOMContentLoaded', () => {
    
    // Global click listener cho tất cả các nút Liên hệ & SPA links
    document.addEventListener('click', (e) => {
      const contactBtn = e.target.closest('.pg-goto-contact, a[href="#contact"], a[href="#lien-he"]');
      if (contactBtn) {
        e.preventDefault();
        navigateTo('lien-he');
        return;
      }

      const pageLink = e.target.closest('[data-page], a[href="#pricing"], a[href="#he-sinh-thai"], a[href="#bang-gia"], a[href="#doi-tac"]');
      if (pageLink && !pageLink.closest('#pageView') && pageLink.getAttribute('target') !== '_blank') {
        let key = pageLink.getAttribute('data-page');
        if (!key) {
          const href = pageLink.getAttribute('href');
          if (href === '#pricing' || href === '#bang-gia') key = 'bang-gia';
          if (href === '#he-sinh-thai') key = 'he-sinh-thai';
          if (href === '#doi-tac') key = 'doi-tac';
          if (href === '#contact' || href === '#lien-he') key = 'lien-he';
        }
        if (key) {
          e.preventDefault();
          navigateTo(key);
        }
      }
    });

    // Form submit handler cho biểu mẫu liên hệ
    document.addEventListener('submit', async (e) => {
      if (e.target && e.target.id === 'contactFormPage') {
        e.preventDefault();
        const form = e.target;
        const submitBtn = document.getElementById('btnSubmitContactForm');
        const origBtnHtml = submitBtn ? submitBtn.innerHTML : 'Gửi Yêu Cầu';

        const phoneVal = (form.querySelector('[name="phone"], #contactPhonePage')?.value || '').trim().replace(/\s+/g, '');
        const emailVal = (form.querySelector('[name="email"], #contactEmailPage')?.value || '').trim();

        if (phoneVal && !/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/.test(phoneVal)) {
          if (window.showToast) window.showToast('⚠️ Số điện thoại không hợp lệ! (10 chữ số, bắt đầu 03, 05, 07, 08, 09)', true);
          else alert('⚠️ Số điện thoại không hợp lệ! (10 chữ số, bắt đầu 03, 05, 07, 08, 09)');
          return;
        }

        if (emailVal && !/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(emailVal)) {
          if (window.showToast) window.showToast('⚠️ Địa chỉ Email không hợp lệ! (ví dụ: contact@company.vn)', true);
          else alert('⚠️ Địa chỉ Email không hợp lệ! (ví dụ: contact@company.vn)');
          return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';
        }

        try {
          const formData = new FormData(form);
          const response = await fetch('backend/api/consultation.php', {
            method: 'POST',
            body: formData
          });
          const result = await response.json();

          if (result.status === 'success') {
            if (window.showToast) window.showToast(result.message, 'success');
            else alert(result.message);
            form.reset();
          } else {
            if (window.showToast) window.showToast(result.message || 'Lỗi gửi thông tin!', 'error');
            else alert(result.message || 'Lỗi gửi thông tin!');
          }
        } catch (err) {
          console.error('Lỗi gửi form liên hệ:', err);
          if (window.showToast) window.showToast('Gửi yêu cầu thất bại. Vui lòng thử lại!', 'error');
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origBtnHtml;
          }
        }
      }
    });

    function checkHash() {
      const hash = location.hash;
      const m = hash.match(/#page=([^&]+)/);
      if (m) {
        navigateTo(decodeURIComponent(m[1]));
      } else if (hash === '#pricing' || hash === '#bang-gia') {
        navigateTo('bang-gia');
      } else if (hash === '#he-sinh-thai') {
        navigateTo('he-sinh-thai');
      } else if (hash === '#doi-tac') {
        navigateTo('doi-tac');
      } else if (hash === '#lien-he' || hash === '#contact') {
        navigateTo('lien-he');
      } else {
        goHome();
      }
    }

    window.addEventListener('hashchange', checkHash);
    checkHash();
  });
})();