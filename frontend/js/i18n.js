/**
 * VNPT — Universal Multi-language Module (VI / EN) v5
 * Tự động quét và dịch toàn bộ 100% các trang con, menu dropdown, nút bấm, bài viết, breadcrumb, tiêu đề giữa Tiếng Việt và Tiếng Anh.
 * Sử dụng MutationObserver để tự động dịch tức thì bất kỳ phần tử DOM nào được nạp mới.
 */
(function () {
  'use strict';

  const PHRASE_MAP = [
    // Sub-Pages & Static Pages (Giới thiệu, Tầm nhìn, Hạ tầng, Điều khoản, Bảo mật)
    { vi: "Về trang chủ", en: "Back to Home" },
    { vi: "Trang chủ", en: "Home" },
    { vi: "Giới thiệu", en: "About Us" },
    { vi: "Thông tin chi tiết về giới thiệu", en: "Detailed information about our company" },
    { vi: "Thông tin chi tiết về tầm nhìn & sứ mệnh", en: "Detailed information about vision & mission" },
    { vi: "Thông tin chi tiết về hạ tầng số", en: "Detailed information about digital infrastructure" },
    { vi: "Thông tin chi tiết về điều khoản dịch vụ", en: "Detailed information about terms of service" },
    { vi: "Thông tin chi tiết về chính sách bảo mật", en: "Detailed information about privacy policy" },
    { vi: "Thông tin chi tiết về", en: "Detailed information about" },
    { vi: "Chúng tôi là ai?", en: "Who We Are" },
    { vi: "VNPT là nhà cung cấp dịch vụ số hàng đầu Việt Nam với hơn 30 năm kinh nghiệm trong lĩnh vực viễn thông và công nghệ thông tin. Chúng tôi cung cấp hệ sinh thái sản phẩm – dịch vụ số toàn diện: từ hạ tầng Cloud, mạng 5G, bảo mật, đến trí tuệ nhân tạo và các giải pháp quản trị doanh nghiệp.", en: "VNPT is a leading digital service provider in Vietnam with over 30 years of experience in telecommunications and IT. We deliver a comprehensive ecosystem of digital products & services: from Cloud infrastructure, 5G networks, security, to artificial intelligence and enterprise management solutions." },
    { vi: "VNPT là nhà cung cấp dịch vụ số hàng đầu Việt Nam với hơn", en: "VNPT is Vietnam's leading digital service provider with over" },
    { vi: "trong lĩnh vực viễn thông và công nghệ thông tin. Chúng tôi cung cấp hệ sinh thái sản phẩm – dịch vụ số toàn diện: từ hạ tầng Cloud, mạng 5G, bảo mật, đến trí tuệ nhân tạo và các giải pháp quản trị doanh nghiệp.", en: "in telecommunications and IT. We deliver a comprehensive ecosystem of digital products & services: from Cloud infrastructure, 5G networks, security, to AI and enterprise management solutions." },
    { vi: "Tầm nhìn & Sứ mệnh", en: "Vision & Mission" },
    { vi: "Trở thành tập đoàn công nghệ số hàng đầu khu vực Đông Nam Á.", en: "To become a leading digital technology group in Southeast Asia." },
    { vi: "Hạ tầng số", en: "Digital Infrastructure" },
    { vi: "Data Center chuẩn Tier III, kết nối băng thông rộng phủ sóng toàn quốc.", en: "Tier III Standard Data Center, nationwide high-speed broadband coverage." },
    { vi: "Pháp lý", en: "Legal" },
    { vi: "Các quy định áp dụng khi sử dụng dịch vụ VNPT.", en: "Regulations and terms governing the use of VNPT services." },
    { vi: "Cam kết bảo vệ dữ liệu cá nhân theo tiêu chuẩn quốc tế.", en: "Commitment to personal data protection according to international standards." },
    { vi: "30 năm kinh nghiệm", en: "30 years of experience" },

    // Dropdown items
    { vi: "Doanh nghiệp vừa & nhỏ", en: "SMEs & Small Businesses" },
    { vi: "Doanh nghiệp SME", en: "SME Businesses" },
    { vi: "Tập đoàn lớn", en: "Large Corporations" },
    { vi: "Chính phủ số", en: "Digital Government" },
    { vi: "Y tế & Giáo dục", en: "Healthcare & Education" },
    { vi: "Y tế số", en: "Digital Healthcare" },
    { vi: "Giáo dục số", en: "Digital Education" },

    // Header & Navigation
    { vi: "Chuyển đổi số – Vươn tầm thế giới", en: "Digital Transformation – Reaching Global Heights" },
    { vi: "Chuyển đổi số", en: "Digital Transformation" },
    { vi: "Toàn diện & Bền vững", en: "Comprehensive & Sustainable" },
    { vi: "Nền tảng dịch vụ số hàng đầu Việt Nam", en: "Vietnam's Leading Digital Services Platform" },
    { vi: "Hệ sinh thái dịch vụ số tích hợp — từ hạ tầng đến ứng dụng — AI thông minh — giúp doanh nghiệp bứt phá trong kỷ nguyên số.", en: "Integrated digital service ecosystem — from infrastructure to applications — smart AI — driving enterprise growth in the digital era." },
    { vi: "Khám phá dịch vụ", en: "Explore Services" },
    { vi: "Xem demo", en: "Watch Demo" },
    { vi: "Khách hàng", en: "Clients" },
    { vi: "Năm kinh nghiệm", en: "Years Experience" },
    { vi: "Uptime SLA", en: "Uptime SLA" },

    // Nav
    { vi: "Về chúng tôi", en: "About Us" },
    { vi: "Dịch vụ", en: "Services" },
    { vi: "Giải pháp", en: "Solutions" },
    { vi: "Bảng giá", en: "Pricing" },
    { vi: "Hệ sinh thái", en: "Ecosystem" },
    { vi: "Tin tức", en: "News" },
    { vi: "Đối tác", en: "Partners" },
    { vi: "Liên hệ ngay", en: "Contact Us Now" },
    { vi: "Liên hệ", en: "Contact Us" },

    // Search Popup
    { vi: "Nhập tên dịch vụ, gói cước...", en: "Search services, packages..." },
    { vi: "Gõ từ khóa để bắt đầu tìm kiếm", en: "Type keywords to start searching" },
    { vi: "Tìm kiếm", en: "Search" },

    // Category Filter Bar
    { vi: "Tất cả dịch vụ", en: "All Services" },
    { vi: "Hạ tầng Cloud", en: "Cloud Infrastructure" },
    { vi: "Bảo mật & An ninh mạng", en: "Cyber Security" },
    { vi: "Trí tuệ nhân tạo (AI)", en: "Artificial Intelligence (AI)" },
    { vi: "Viễn thông & 5G", en: "Telecom & 5G" },
    { vi: "Quản trị doanh nghiệp", en: "Enterprise ERP" },

    // Section Titles & Badges
    { vi: "Dịch vụ nổi bật", en: "Featured Services" },
    { vi: "Giải pháp số toàn diện cho mọi quy mô doanh nghiệp", en: "Comprehensive Digital Solutions for Every Business Scale" },
    { vi: "Giải pháp chuyển đổi số toàn diện cho mọi quy mô doanh nghiệp", en: "Comprehensive Digital Solutions for Every Business Scale" },
    { vi: "Bảng giá dịch vụ", en: "Service Pricing Plans" },
    { vi: "Gói cước linh hoạt", en: "Flexible Pricing" },
    { vi: "Cập nhật mới nhất", en: "Latest Updates" },
    { vi: "Tin tức & Sự kiện", en: "News & Events" },
    { vi: "Đối tác chiến lược", en: "Strategic Partners" },
    { vi: "NỔI BẬT", en: "FEATURED" },
    { vi: "BỔ SUNG VÀO GIỎ HÀNG", en: "ADD TO CART" },

    // Service Cards
    { vi: "Cloud Server", en: "Cloud Server" },
    { vi: "Hệ thống máy chủ ảo hiệu năng cao, mở rộng linh hoạt theo nhu cầu.", en: "High-performance virtual servers with flexible on-demand scalability." },
    { vi: "Bảo mật số", en: "Digital Security" },
    { vi: "Giải pháp an toàn thông tin toàn diện, bảo vệ dữ liệu doanh nghiệp 24/7.", en: "Comprehensive information security solutions, protecting enterprise data 24/7." },
    { vi: "AI & Automation", en: "AI & Automation" },
    { vi: "Tự động hóa quy trình nghiệp vụ với trí tuệ nhân tạo thế hệ mới.", en: "Automate business workflows with next-generation artificial intelligence." },
    { vi: "Hạ tầng 5G & IoT", en: "5G & IoT Infrastructure" },
    { vi: "Tốc độ cao, độ trễ thấp, kết nối hàng triệu thiết bị thông minh.", en: "High speed, ultra-low latency, connecting millions of smart devices." },
    { vi: "Quản trị DN", en: "Enterprise ERP" },
    { vi: "Phần mềm quản lý tổng thể doanh nghiệp ERP chuẩn quốc tế.", en: "International standard ERP enterprise management software." },
    { vi: "Big Data & Phân tích", en: "Big Data & Analytics" },
    { vi: "Thu thập, phân tích dữ liệu lớn hỗ trợ ra quyết định.", en: "Collect and analyze big data to drive smart decision making." },
    { vi: "IoT & Smart City", en: "IoT & Smart City" },
    { vi: "Kết nối vạn vật, giải pháp đô thị thông minh.", en: "Internet of Things and smart city solutions." },

    // Feature Bullets
    { vi: "Tự động sao lưu", en: "Auto Backup" },
    { vi: "Băng thông không giới hạn", en: "Unlimited Bandwidth" },
    { vi: "Hỗ trợ 24/7", en: "24/7 Support" },
    { vi: "Tường lửa đa lớp", en: "Multi-layer Firewall" },
    { vi: "Phát hiện mối đe dọa AI", en: "AI Threat Detection" },
    { vi: "Tuân thủ ISO 27001", en: "ISO 27001 Compliance" },
    { vi: "Chatbot thông minh", en: "Smart Chatbot" },
    { vi: "Xử lý ngôn ngữ tự nhiên", en: "Natural Language Processing" },
    { vi: "Tự động hóa RPA", en: "RPA Automation" },
    { vi: "Độ trễ siêu thấp (<1ms)", en: "Ultra-low latency (<1ms)" },
    { vi: "Phủ sóng 99.9%", en: "99.9% Coverage" },
    { vi: "Bảo mật phần cứng", en: "Hardware Security" },
    { vi: "Quản lý tài chính", en: "Financial Management" },
    { vi: "Quản lý nhân sự", en: "HR Management" },
    { vi: "Báo cáo thời gian thực", en: "Real-time Reports" },

    // Pricing Cards
    { vi: "Gói Cơ bản", en: "Basic Plan" },
    { vi: "Gói Doanh nghiệp", en: "Business Plan" },
    { vi: "Gói Cao cấp", en: "Premium Plan" },
    { vi: "Dành cho cá nhân & Startup nhỏ", en: "For Individuals & Small Startups" },
    { vi: "Dành cho doanh nghiệp vừa và nhỏ", en: "For Small & Medium Enterprises" },
    { vi: "Dành cho tập đoàn & tổ chức lớn", en: "For Corporations & Large Enterprises" },
    { vi: "Đăng ký gói", en: "Subscribe Plan" },
    { vi: "Tất cả tính năng Cơ bản", en: "All Basic Features" },
    { vi: "Tất cả tính năng Doanh nghiệp", en: "All Business Features" },
    { vi: "Hỗ trợ ưu tiên 24/7", en: "24/7 Priority Support" },
    { vi: "Hạ tầng riêng biệt (Private Cloud)", en: "Dedicated Private Cloud" },
    { vi: "Cam kết SLA 99.99%", en: "SLA 99.99% Commitment" },

    // Implementation Steps
    { vi: "Tư vấn & Khảo sát", en: "Consultation & Survey" },
    { vi: "Đánh giá nhu cầu thực tế và đề xuất giải pháp tối ưu.", en: "Assess actual needs and propose optimal solutions." },
    { vi: "Triển khai & Tích hợp", en: "Deployment & Integration" },
    { vi: "Cấu hình hệ thống, tích hợp hạ tầng mượt mà, không gián đoạn.", en: "Configure systems and integrate infrastructure smoothly with zero downtime." },
    { vi: "Vận hành & Hỗ trợ", en: "Operation & Support" },
    { vi: "Đồng hành 24/7, tối ưu hóa liên tục hiệu năng hệ thống.", en: "24/7 partnership with continuous system performance optimization." },
    { vi: "Con số ấn tượng", en: "Impressive Numbers" },
    { vi: "Quy trình triển khai", en: "Implementation Process" },

    // News Section
    { vi: "Cập nhật công nghệ", en: "Tech Updates" },
    { vi: "Tin tức doanh nghiệp", en: "Enterprise News" },
    { vi: "Giải pháp AI mới cho doanh nghiệp Việt Nam", en: "New AI Solutions for Vietnamese Enterprises" },
    { vi: "VNPT ra mắt hạ tầng Cloud Server thế hệ thứ 4", en: "VNPT Launches 4th Generation Cloud Server Infrastructure" },
    { vi: "Xu hướng Chuyển đổi số 2025: Những điều doanh nghiệp cần biết", en: "Digital Transformation Trends 2025: What Enterprises Need to Know" },
    { vi: "Đọc tiếp", en: "Read more" },

    // Buttons & Card Labels
    { vi: "Tìm hiểu thêm", en: "Learn more" },
    { vi: "Thêm vào giỏ", en: "Add to cart" },
    { vi: "Cần tư vấn thêm? Hãy để VNPT đồng hành cùng bạn", en: "Need more advice? Let VNPT partner with you" },
    { vi: "Đội ngũ chuyên gia của VNPT sẵn sàng hỗ trợ doanh nghiệp 24/7.", en: "VNPT expert team is ready to support your business 24/7." },
    { vi: "Đội ngũ chuyên gia của VNPT sẵn sàng hỗ trợ doanh nghiệp bạn 24/7.", en: "VNPT expert team is ready to support your business 24/7." },

    // Footer Links & Text
    { vi: "Tài liệu kỹ thuật", en: "Technical Docs" },
    { vi: "Trung tâm hỗ trợ", en: "Support Center" },
    { vi: "Chính sách bảo mật", en: "Privacy Policy" },
    { vi: "Điều khoản dịch vụ", en: "Terms of Service" },
    { vi: "Nền tảng dịch vụ số toàn diện, đồng hành cùng doanh nghiệp Việt Nam trên hành trình chuyển đổi số.", en: "Comprehensive digital service platform, empowering Vietnamese enterprises on their digital transformation journey." },
    { vi: "© 2025 VNPT. Bảo lưu mọi quyền. | Giấy phép kinh doanh số: 0100686209", en: "© 2025 VNPT. All rights reserved. | Business License No: 0100686209" },

    // User Dropdown & Modals
    { vi: "Hồ sơ cá nhân", en: "My Profile" },
    { vi: "Đơn hàng của tôi", en: "My Orders" },
    { vi: "Cài đặt", en: "Settings" },
    { vi: "Quản trị hệ thống", en: "Admin Panel" },
    { vi: "Đăng xuất", en: "Log Out" },
    { vi: "Giỏ hàng", en: "Shopping Cart" },
    { vi: "Giỏ hàng của bạn đang trống", en: "Your shopping cart is empty" },
    { vi: "Hãy thêm dịch vụ bạn quan tâm!", en: "Please add services you are interested in!" },
    { vi: "Tạm tính", en: "Subtotal" },
    { vi: "Tổng cộng", en: "Total" },
    { vi: "Tiến hành thanh toán", en: "Proceed to Checkout" },
    { vi: "Xóa tất cả", en: "Clear All" },
    { vi: "Chào mừng trở lại!", en: "Welcome Back!" },
    { vi: "Đăng nhập để trải nghiệm dịch vụ số toàn diện", en: "Log in to experience comprehensive digital services" },
    { vi: "Tạo tài khoản mới", en: "Create New Account" },
    { vi: "Đăng ký để bắt đầu hành trình chuyển đổi số", en: "Register to start your digital transformation journey" },
    { vi: "Chưa có tài khoản?", en: "Don't have an account?" },
    { vi: "Đăng ký ngay", en: "Register now" },
    { vi: "Đã có tài khoản?", en: "Already have an account?" },
    { vi: "Đăng nhập", en: "Log in" },
    { vi: "Đăng ký", en: "Register" },
    { vi: "Tiếp tục với Google", en: "Continue with Google" },
    { vi: "Tiếp tục với Facebook", en: "Continue with Facebook" },
    { vi: "Đăng ký với Google", en: "Register with Google" },
    { vi: "Đăng ký với Facebook", en: "Register with Facebook" },
    { vi: "hoặc đăng nhập bằng email", en: "or log in with email" },
    { vi: "hoặc đăng ký bằng email", en: "or register with email" },
    { vi: "Ghi nhớ đăng nhập", en: "Remember me" },
    { vi: "Quên mật khẩu?", en: "Forgot password?" },
    { vi: "Họ và tên", en: "Full Name" },
    { vi: "Họ", en: "First Name" },
    { vi: "Tên", en: "Last Name" },
    { vi: "Mật khẩu", en: "Password" },
    { vi: "Xác nhận mật khẩu", en: "Confirm Password" },
    { vi: "Số điện thoại", en: "Phone Number" },
    { vi: "Tôi đồng ý với", en: "I agree to the" },
    { vi: "Tạo tài khoản", en: "Create Account" },
    { vi: "Độ mạnh mật khẩu", en: "Password Strength" }
  ];

  let currentLang = localStorage.getItem('vnpt_lang') || 'vi';
  let isTranslating = false;

  function translateDOMNode(node, lang) {
    if (!node) return;

    // Sắp xếp các cụm từ theo độ dài giảm dần để dịch đúng nghĩa hoàn chỉnh trước
    const sortedPhrases = [...PHRASE_MAP].sort((a, b) => {
      const lenA = lang === 'en' ? a.vi.length : a.en.length;
      const lenB = lang === 'en' ? b.vi.length : b.en.length;
      return lenB - lenA;
    });

    if (node.nodeType === Node.TEXT_NODE) {
      let val = node.nodeValue;
      if (!val || !val.trim()) return;

      sortedPhrases.forEach(item => {
        const from = lang === 'en' ? item.vi : item.en;
        const to = lang === 'en' ? item.en : item.vi;
        if (val.includes(from)) {
          val = val.replaceAll(from, to);
        }
      });

      if (lang === 'en') {
        val = val.replace(/Từ/g, 'From').replace(/đ\/tháng/g, '/mo').replace(/\/tháng/g, '/mo');
      } else {
        val = val.replace(/From/g, 'Từ').replace(/\/mo/g, 'đ/tháng');
      }

      node.nodeValue = val;
    } else if (node.nodeType === Node.ELEMENT_NODE) {
      const tag = node.tagName.toLowerCase();
      if (['script', 'style', 'textarea', 'code'].includes(tag)) return;

      if (node.placeholder) {
        sortedPhrases.forEach(item => {
          const from = lang === 'en' ? item.vi : item.en;
          const to = lang === 'en' ? item.en : item.vi;
          if (node.placeholder.includes(from)) {
            node.placeholder = node.placeholder.replaceAll(from, to);
          }
        });
      }

      node.childNodes.forEach(child => translateDOMNode(child, lang));
    }
  }

  function applyLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('vnpt_lang', lang);

    // Update Language Button Label
    const langBtnLabel = document.querySelector('.btn-lang .tb-btn-label');
    if (langBtnLabel) {
      langBtnLabel.textContent = lang.toUpperCase();
    }

    // Run Universal DOM Translator across the entire document
    translateDOMNode(document.body, lang);

    if (window.lucide) lucide.createIcons();
  }

  function toggleLanguage() {
    const nextLang = currentLang === 'vi' ? 'en' : 'vi';
    applyLanguage(nextLang);
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    if (toast && toastMsg) {
      toastMsg.textContent = nextLang === 'en' ? 'Switched to English 🌐' : 'Đã chuyển sang Tiếng Việt 🇻🇳';
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2000);
    }
  }

  // MutationObserver theo dõi tự động dịch tức thì bất kể nội dung nào được sinh ra mới (SPA sub-pages, dropdowns)
  const observer = new MutationObserver(() => {
    if (currentLang === 'en' && !isTranslating) {
      isTranslating = true;
      observer.disconnect();
      applyLanguage('en');
      setTimeout(() => { 
        isTranslating = false;
        try { observer.observe(document.body, { childList: true, subtree: true }); } catch (_e) {}
      }, 300);
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    applyLanguage(currentLang);
    observer.observe(document.body, { childList: true, subtree: true });

    const langBtn = document.querySelector('.btn-lang');
    if (langBtn) {
      langBtn.addEventListener('click', (e) => {
        e.preventDefault();
        toggleLanguage();
      });
    }
  });

  // Automatically translate dynamic content when pages or auth state change
  document.addEventListener('vnpt:pagechange', () => applyLanguage(currentLang));
  document.addEventListener('vnpt:authchange', () => applyLanguage(currentLang));

  window.VNPTLang = { applyLanguage, toggleLanguage, getLang: () => currentLang };
})();
