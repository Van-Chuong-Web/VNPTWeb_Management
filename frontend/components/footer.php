<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="nav-logo" style="margin-bottom:1rem">
          <div class="logo-icon">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
              <circle cx="16" cy="16" r="16" fill="url(#footerLogoGrad)"/>
              <path d="M8 16 L16 8 L24 16 L16 24 Z" fill="white" opacity="0.9"/>
              <circle cx="16" cy="16" r="4" fill="white"/>
              <defs>
                <linearGradient id="footerLogoGrad" x1="0" y1="0" x2="32" y2="32">
                  <stop offset="0%" stop-color="#0066CC"/>
                  <stop offset="100%" stop-color="#00AAFF"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
          <span class="logo-text">VNPT</span>
        </div>
        <p>Nền tảng dịch vụ số toàn diện, đồng hành cùng doanh nghiệp Việt Nam trên hành trình chuyển đổi số.</p>
        <div class="social-links">
          <a href="https://www.facebook.com/vinaphonefan/?locale=vi_VN" target="_blank" rel="noopener" aria-label="Facebook VNPT" title="Theo dõi VNPT trên Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://www.youtube.com/channel/UCCrkSbaFcot6hcLOuNADX8Q" target="_blank" rel="noopener" aria-label="Youtube VNPT" title="Xem kênh Youtube chính thức VNPT"><i class="fa-brands fa-youtube"></i></a>
          <a href="https://www.linkedin.com/company/t%E1%BB%95ng-c%C3%B4ng-ty-d%E1%BB%8Bch-v%E1%BB%A5-vi%E1%BB%85n-th%C3%B4ng-vnpt-vinaphone" target="_blank" rel="noopener" aria-label="Linkedin VNPT" title="Kết nối VNPT trên LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://x.com/VinaPhoneFan" target="_blank" rel="noopener" aria-label="Twitter/X VNPT" title="Theo dõi VNPT trên Twitter/X"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="display:inline-block; vertical-align:-2px;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Dịch vụ</h4>
        <ul>
          <li><a href="#services" class="footer-cat-link" data-cat="cloud">Cloud Computing</a></li>
          <li><a href="#services" class="footer-cat-link" data-cat="security">Bảo mật số</a></li>
          <li><a href="#services" class="footer-cat-link" data-cat="ai">AI &amp; Automation</a></li>
          <li><a href="#services" class="footer-cat-link" data-cat="5g">Hạ tầng 5G</a></li>
          <li><a href="#services" class="footer-cat-link" data-cat="enterprise">Quản trị DN</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Giải pháp</h4>
        <ul>
          <li><a href="#stats" class="footer-stat-link" data-type="customers">Doanh nghiệp SME</a></li>
          <li><a href="#stats" class="footer-stat-link" data-type="customers">Tập đoàn lớn</a></li>
          <li><a href="#stats" class="footer-stat-link" data-type="provinces">Chính phủ số</a></li>
          <li><a href="#services" class="footer-cat-link" data-cat="health">Y tế số</a></li>
          <li><a href="#services" class="footer-cat-link" data-cat="edu">Giáo dục số</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Hỗ trợ</h4>
        <ul>
          <li><a href="#" id="openTechDocsLink">Tài liệu kỹ thuật</a></li>
          <li><a href="#" id="openSupportCenterLink">Trung tâm hỗ trợ</a></li>
          <li><a href="#" id="openPrivacyModalLink">Chính sách bảo mật</a></li>
          <li><a href="#" id="openTermsModalLink">Điều khoản dịch vụ</a></li>
          <li><a href="#contact">Liên hệ trực tiếp</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 VNPT. Bảo lưu mọi quyền. | Giấy phép kinh doanh số: 0100686209</p>
      <div class="footer-badges">
        <span class="fbadge clickable-badge" id="badgeISO27001" title="Nhấp để xem chứng chỉ Bảo mật ISO 27001"><i data-lucide="shield-check"></i> ISO 27001</span>
        <span class="fbadge clickable-badge" id="badgeTop10ICT" title="Nhấp để xem danh hiệu Top 10 Doanh nghiệp ICT Việt Nam"><i data-lucide="award"></i> Top 10 ICT</span>
      </div>
    </div>
  </div>
</footer>

<div class="modal-overlay" id="modalOverlay"></div>
<div class="cart-overlay" id="cartOverlay"></div>

<aside class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <h3><i data-lucide="shopping-cart"></i> Giỏ hàng</h3>
    <button class="cart-close-btn" id="cartClose" aria-label="Đóng giỏ hàng">
      <i data-lucide="x"></i>
    </button>
  </div>

  <div class="cart-body" id="cartBody">
    <div class="cart-empty" id="cartEmpty">
      <div class="cart-empty-icon"><i data-lucide="shopping-bag"></i></div>
      <p>Giỏ hàng của bạn đang trống</p>
      <span>Hãy thêm dịch vụ bạn quan tâm!</span>
    </div>
    <div class="cart-items" id="cartItems"></div>
  </div>

  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-summary">
      <div class="cart-summary-row">
        <span>Tạm tính (<span id="cartCount">0</span> dịch vụ)</span>
        <span id="cartSubtotal">0 ₫</span>
      </div>
      <div class="cart-summary-row cart-total-row">
        <strong>Tổng cộng</strong>
        <strong id="cartTotal" class="cart-total-price">0 ₫</strong>
      </div>
    </div>
    <button class="btn-checkout" id="checkoutBtn">
      <i data-lucide="credit-card"></i> Tiến hành thanh toán
    </button>
    <button class="btn-clear-cart" id="clearCartBtn">
      <i data-lucide="trash-2"></i> Xóa tất cả
    </button>
  </div>
</aside>

<div class="auth-modal" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
  <div class="auth-modal-inner">
    <button class="auth-modal-close" id="closeLogin" aria-label="Đóng"><i data-lucide="x"></i></button>

    <div class="auth-modal-logo">
      <svg width="44" height="44" viewBox="0 0 44 44" fill="none">
        <circle cx="22" cy="22" r="22" fill="url(#authLogoGrad)"/>
        <path d="M11 22 L22 11 L33 22 L22 33 Z" fill="white" opacity="0.9"/>
        <circle cx="22" cy="22" r="6" fill="white"/>
        <defs>
          <linearGradient id="authLogoGrad" x1="0" y1="0" x2="44" y2="44">
            <stop offset="0%" stop-color="#0066CC"/>
            <stop offset="100%" stop-color="#00AAFF"/>
          </linearGradient>
        </defs>
      </svg>
      <span>VNPT</span>
    </div>

    <h2 id="loginTitle">Chào mừng trở lại!</h2>
    <p class="auth-subtitle">Đăng nhập để trải nghiệm dịch vụ số toàn diện</p>

    <!-- Hộp thông báo Yêu cầu Đăng nhập khi Mua/Đăng ký sản phẩm -->
    <div id="loginNoticeBox" class="login-notice-box" style="display:none; background: linear-gradient(135deg, #FEF3C7, #FDE68A); border: 1.5px solid #F59E0B; border-radius: 12px; padding: 12px 16px; margin: 14px 0 14px; color: #92400E; font-size: 0.88rem; font-weight: 700; text-align: center; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
      <i class="fa-solid fa-circle-exclamation" style="color: #D97706; font-size: 1.1rem; flex-shrink: 0;"></i>
      <span id="loginNoticeText">⚠️ Vui lòng <strong>Đăng nhập</strong> hoặc <strong>Đăng ký</strong> tài khoản để tiếp tục đăng ký gói cước!</span>
    </div>

    <div class="auth-social-btns">
      <button class="btn-social btn-google" id="loginGoogle">
        <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/></svg>
        Tiếp tục với Google
      </button>
      <button class="btn-social btn-facebook" id="loginFacebook">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
        Tiếp tục với Facebook
      </button>
    </div>

    <div class="auth-divider"><span>hoặc đăng nhập bằng email</span></div>

    <form class="auth-form" id="loginForm" novalidate>
      <div class="auth-field">
        <label for="loginEmail">Email</label>
        <div class="auth-input-wrap">
          <i data-lucide="mail"></i>
          <input type="email" id="loginEmail" placeholder="email@company.vn" autocomplete="email" required />
        </div>
        <span class="field-error" id="loginEmailErr"></span>
      </div>
      <div class="auth-field">
        <label for="loginPassword">Mật khẩu</label>
        <div class="auth-input-wrap">
          <i data-lucide="lock"></i>
          <input type="password" id="loginPassword" placeholder="Nhập mật khẩu" autocomplete="current-password" required />
          <button type="button" class="toggle-pw" data-target="loginPassword" aria-label="Hiện/ẩn mật khẩu">
            <i class="fa-solid fa-eye text-muted" style="font-size:13px;"></i>
          </button>
        </div>
        <span class="field-error" id="loginPasswordErr"></span>
      </div>
      <div class="auth-row">
        <label class="auth-checkbox">
          <input type="checkbox" id="rememberMe" /> Ghi nhớ đăng nhập
        </label>
        <a href="#" class="auth-link forgot-link" id="forgotLink">Quên mật khẩu?</a>
      </div>
      <div class="auth-error-box" id="loginError" style="display:none"></div>
      <button type="submit" class="btn-auth-submit" id="loginSubmit">
        <span>Đăng nhập</span>
        <i data-lucide="arrow-right"></i>
      </button>
    </form>

    <p class="auth-switch">Chưa có tài khoản? <button class="auth-link" id="switchToRegister">Đăng ký ngay</button></p>
  </div>
</div>

<div class="auth-modal" id="registerModal" role="dialog" aria-modal="true" aria-labelledby="registerTitle">
  <div class="auth-modal-inner">
    <button class="auth-modal-close" id="closeRegister" aria-label="Đóng"><i data-lucide="x"></i></button>

    <div class="auth-modal-logo">
      <svg width="44" height="44" viewBox="0 0 44 44" fill="none">
        <circle cx="22" cy="22" r="22" fill="url(#authLogoGrad2)"/>
        <path d="M11 22 L22 11 L33 22 L22 33 Z" fill="white" opacity="0.9"/>
        <circle cx="22" cy="22" r="6" fill="white"/>
        <defs>
          <linearGradient id="authLogoGrad2" x1="0" y1="0" x2="44" y2="44">
            <stop offset="0%" stop-color="#0066CC"/>
            <stop offset="100%" stop-color="#00AAFF"/>
          </linearGradient>
        </defs>
      </svg>
      <span>VNPT</span>
    </div>

    <h2 id="registerTitle">Tạo tài khoản mới</h2>
    <p class="auth-subtitle">Đăng ký để bắt đầu hành trình chuyển đổi số</p>

    <div class="auth-social-btns">
      <button class="btn-social btn-google" id="registerGoogle" type="button">
        <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/></svg>
        Đăng ký với Google
      </button>
      <button class="btn-social btn-facebook" id="registerFacebook" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
        Đăng ký với Facebook
      </button>
    </div>

    <div class="auth-divider"><span>hoặc đăng ký bằng email</span></div>

    <form class="auth-form" id="registerForm" novalidate>
      <div class="auth-row-2">
        <div class="auth-field">
          <label for="regFirstName">Họ</label>
          <div class="auth-input-wrap">
            <i data-lucide="user"></i>
            <input type="text" id="regFirstName" placeholder="Nguyễn" required />
          </div>
          <span class="field-error" id="regFirstNameErr"></span>
        </div>
        <div class="auth-field">
          <label for="regLastName">Tên</label>
          <div class="auth-input-wrap">
            <i data-lucide="user"></i>
            <input type="text" id="regLastName" placeholder="Văn A" required />
          </div>
          <span class="field-error" id="regLastNameErr"></span>
        </div>
      </div>
      <div class="auth-field">
        <label for="regEmail">Email</label>
        <div class="auth-input-wrap">
          <i data-lucide="mail"></i>
          <input type="email" id="regEmail" placeholder="email@company.vn" autocomplete="email" required />
        </div>
        <span class="field-error" id="regEmailErr"></span>
      </div>
      <div class="auth-field">
        <label for="regPhone">Số điện thoại</label>
        <div class="auth-input-wrap">
          <i data-lucide="phone"></i>
          <input type="tel" id="regPhone" placeholder="0901 234 567" autocomplete="tel" />
        </div>
        <span class="field-error" id="regPhoneErr"></span>
      </div>
      <div class="auth-field">
        <label for="regPassword">Mật khẩu</label>
        <div class="auth-input-wrap">
          <i data-lucide="lock"></i>
          <input type="password" id="regPassword" placeholder="Tối thiểu 8 ký tự" autocomplete="new-password" required />
          <button type="button" class="toggle-pw" data-target="regPassword" aria-label="Hiện/ẩn mật khẩu">
            <i class="fa-solid fa-eye text-muted" style="font-size:13px;"></i>
          </button>
        </div>
        <div class="pw-strength" id="pwStrength">
          <div class="pw-strength-bar"><div class="pw-strength-fill" id="pwStrengthFill"></div></div>
          <span id="pwStrengthLabel">Độ mạnh mật khẩu</span>
        </div>
        <span class="field-error" id="regPasswordErr"></span>
      </div>
      <div class="auth-field">
        <label for="regConfirmPassword">Xác nhận mật khẩu</label>
        <div class="auth-input-wrap">
          <i data-lucide="lock"></i>
          <input type="password" id="regConfirmPassword" placeholder="Nhập lại mật khẩu" autocomplete="new-password" required />
          <button type="button" class="toggle-pw" data-target="regConfirmPassword" aria-label="Hiện/ẩn mật khẩu">
            <i class="fa-solid fa-eye text-muted" style="font-size:13px;"></i>
          </button>
        </div>
        <span class="field-match" id="regConfirmMatch"></span>
        <span class="field-error" id="regConfirmPasswordErr"></span>
      </div>
      <label class="auth-checkbox terms-check">
        <input type="checkbox" id="agreeTerms" required />
        Tôi đồng ý với <a href="#page=dieu-khoan-dich-vu" data-page="dieu-khoan-dich-vu" target="_blank" rel="noopener" class="auth-link">Điều khoản dịch vụ</a> và <a href="#page=chinh-sach-bao-mat" data-page="chinh-sach-bao-mat" target="_blank" rel="noopener" class="auth-link">Chính sách bảo mật</a>
      </label>
      <span class="field-error" id="agreeTermsErr"></span>
      <div class="auth-error-box" id="registerError" style="display:none"></div>
      <button type="submit" class="btn-auth-submit" id="registerSubmit">
        <span>Tạo tài khoản</span>
        <i data-lucide="user-plus"></i>
      </button>
    </form>

    <p class="auth-switch">Đã có tài khoản? <button class="auth-link" id="switchToLogin">Đăng nhập</button></p>
  </div>
</div>

<!-- =========================================================
     MODAL QUÊN MẬT KHẨU (FORGOT PASSWORD MODAL)
     ========================================================= -->
<div class="auth-modal" id="forgotModal" role="dialog" aria-modal="true" aria-labelledby="forgotTitle">
  <div class="auth-modal-inner">
    <button class="auth-modal-close" id="closeForgot" aria-label="Đóng"><i data-lucide="x"></i></button>

    <div class="auth-modal-logo">
      <svg width="44" height="44" viewBox="0 0 44 44" fill="none">
        <circle cx="22" cy="22" r="22" fill="url(#authLogoGrad3)"/>
        <path d="M11 22 L22 11 L33 22 L22 33 Z" fill="white" opacity="0.9"/>
        <circle cx="22" cy="22" r="6" fill="white"/>
        <defs>
          <linearGradient id="authLogoGrad3" x1="0" y1="0" x2="44" y2="44">
            <stop offset="0%" stop-color="#0066CC"/>
            <stop offset="100%" stop-color="#00AAFF"/>
          </linearGradient>
        </defs>
      </svg>
      <span>VNPT</span>
    </div>

    <h2 id="forgotTitle">Khôi phục Mật khẩu</h2>
    <p class="auth-subtitle">Nhập email để nhận mã xác thực OTP 6 số</p>

    <!-- Bước 1: Gửi OTP -->
    <div id="fgStep1">
      <form class="auth-form" id="forgotSendForm" novalidate>
        <div class="auth-field">
          <label for="fgEmail">Email đã đăng ký</label>
          <div class="auth-input-wrap">
            <i data-lucide="mail"></i>
            <input type="email" id="fgEmail" placeholder="khachhang@domain.com" required />
          </div>
          <span class="field-error" id="fgEmailErr"></span>
        </div>
        <div class="auth-error-box" id="fgError1" style="display:none"></div>
        <button type="submit" class="btn-auth-submit" id="fgSendBtn">
          <span>Gửi mã OTP xác thực</span>
          <i data-lucide="send"></i>
        </button>
      </form>
    </div>

    <!-- Bước 2: Xác thực OTP -->
    <div id="fgStep2" style="display:none">
      <div id="fgLocalNotice" class="alert alert-info py-2 px-3 mb-3 small border-0" style="background:#e0f2fe; color:#0369a1; border-radius:10px; font-size:13px; display:none;"></div>
      <form class="auth-form" id="forgotVerifyForm" novalidate>
        <div class="auth-field">
          <label for="fgOtp">Nhập mã OTP (6 chữ số)</label>
          <div class="auth-input-wrap">
            <i data-lucide="shield-check"></i>
            <input type="text" id="fgOtp" maxlength="6" style="letter-spacing:0.3em;font-size:18px;font-weight:700;text-align:center;" placeholder="000000" required />
          </div>
          <span class="field-error" id="fgOtpErr"></span>
        </div>
        <div class="auth-error-box" id="fgError2" style="display:none"></div>
        <button type="submit" class="btn-auth-submit" id="fgVerifyBtn">
          <span>Xác nhận mã OTP</span>
          <i data-lucide="check-circle"></i>
        </button>
      </form>
    </div>

    <!-- Bước 3: Đổi mật khẩu mới -->
    <div id="fgStep3" style="display:none">
      <form class="auth-form" id="forgotResetForm" novalidate>
        <div class="auth-field">
          <label for="fgNewPassword">Mật khẩu mới</label>
          <div class="auth-input-wrap">
            <i data-lucide="lock"></i>
            <input type="password" id="fgNewPassword" placeholder="Nhập mật khẩu mới" minlength="6" required />
            <button type="button" class="toggle-pw" data-target="fgNewPassword" aria-label="Hiện/ẩn mật khẩu">
              <i class="fa-solid fa-eye text-muted" style="font-size:13px;"></i>
            </button>
          </div>
        </div>
        <div class="auth-field">
          <label for="fgConfirmPassword">Xác nhận mật khẩu mới</label>
          <div class="auth-input-wrap">
            <i data-lucide="lock"></i>
            <input type="password" id="fgConfirmPassword" placeholder="Nhập lại mật khẩu mới" minlength="6" required />
            <button type="button" class="toggle-pw" data-target="fgConfirmPassword" aria-label="Hiện/ẩn mật khẩu">
              <i class="fa-solid fa-eye text-muted" style="font-size:13px;"></i>
            </button>
          </div>
        </div>
        <div class="auth-error-box" id="fgError3" style="display:none"></div>
        <button type="submit" class="btn-auth-submit" id="fgResetBtn">
          <span>Lưu mật khẩu mới</span>
          <i data-lucide="check-square"></i>
        </button>
      </form>
    </div>

    <p class="auth-switch"><button class="auth-link" id="switchToLoginFromForgot">Quay lại Đăng nhập</button></p>
  </div>
</div>

<!-- =========================================================
     MODAL XÁC NHẬN ĐĂNG NHẬP GOOGLE & FACEBOOK (SOCIAL AUTH MODAL)
     ========================================================= -->
<div class="auth-modal" id="socialAccountModal" role="dialog" aria-modal="true" aria-labelledby="socialModalTitle">
  <div class="auth-modal-inner" style="max-width: 440px;">
    <button class="auth-modal-close" id="closeSocialModal" aria-label="Đóng"><i data-lucide="x"></i></button>

    <div class="auth-modal-logo">
      <div id="socialModalIcon" style="width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:#EBF8FF;"></div>
      <span id="socialModalTitle" style="font-size:1.1rem; font-weight:700; color:#0f172a;">Đăng nhập Mạng xã hội</span>
    </div>

    <p class="auth-subtitle mb-3" style="font-size:13px; color:#64748b;">Xác nhận tài khoản Google/Facebook của bạn để đăng nhập trực tiếp vào CSDL VNPT</p>

    <form class="auth-form" id="socialAccountForm" novalidate>
      <input type="hidden" id="socialProvider" value="google" />
      <div class="auth-field">
        <label for="socialEmail">Email tài khoản</label>
        <div class="auth-input-wrap">
          <i data-lucide="mail"></i>
          <input type="email" id="socialEmail" placeholder="your_account@gmail.com" required />
        </div>
      </div>
      <div class="auth-row-2">
        <div class="auth-field">
          <label for="socialFirstName">Họ</label>
          <div class="auth-input-wrap">
            <i data-lucide="user"></i>
            <input type="text" id="socialFirstName" placeholder="Họ" required />
          </div>
        </div>
        <div class="auth-field">
          <label for="socialLastName">Tên</label>
          <div class="auth-input-wrap">
            <i data-lucide="user"></i>
            <input type="text" id="socialLastName" placeholder="Tên" required />
          </div>
        </div>
      </div>
      <div class="auth-error-box" id="socialError" style="display:none"></div>
      <button type="submit" class="btn-auth-submit mt-2" id="submitSocialBtn">
        <span id="socialBtnText">Xác nhận Đăng nhập</span>
        <i data-lucide="arrow-right"></i>
      </button>
    </form>
  </div>
</div>

<!-- OFFICIAL GOOGLE & FACEBOOK OAUTH SDKs -->
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js"></script>

<!-- =========================================================
     MODAL THANH TOÁN & HÓA ĐƠN (CHECKOUT INVOICE MODAL)
     ========================================================= -->
<!-- MODAL THANH TOÁN & HÓA ĐƠN (CHECKOUT INVOICE MODAL) -->
<div class="auth-modal checkout-modal" id="checkoutModal" role="dialog" aria-modal="true" style="z-index: 999999;">
  <div class="auth-modal-inner checkout-modal-inner" style="max-width: 840px; width: 94%; max-height: 90vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div style="padding: 22px 28px; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; position: relative; flex-shrink: 0; display: flex; align-items: center; justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #0066CC, #00AAFF); display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 14px rgba(0,102,204,0.4);">
          <i data-lucide="receipt" style="width: 22px; height: 22px;"></i>
        </div>
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF; margin: 0;">Hóa đơn &amp; Thanh toán dịch vụ số</h3>
          <span style="font-size: 0.8rem; color: #94A3B8;">Hệ thống xác thực &amp; cấp phát hạ tầng VNPT Digital Billing</span>
        </div>
      </div>
      
      <button class="auth-modal-close" id="closeCheckout" aria-label="Đóng" style="position: relative; top: 0; right: 0; background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;"><i data-lucide="x"></i></button>
    </div>

    <div class="checkout-body" id="checkoutModalBody" style="flex-grow: 1; overflow-y: auto; background: #F8FAFC;">
      <!-- Generated by cart.js -->
    </div>
  </div>
</div>

<!-- MODAL ĐĂNG NHẬP XÃ HỘI GOOGLE & FACEBOOK -->
<div class="auth-modal" id="socialAccountModal" role="dialog" aria-modal="true" style="display:none; z-index:9999;">
  <div class="auth-modal-inner" style="max-width: 440px; padding: 32px 28px; background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
    <button class="auth-modal-close" id="closeSocialModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: #F1F5F9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

    <div class="text-center" style="margin-bottom: 20px; text-align: center;">
      <div id="socialModalIcon" style="width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #EBF8FF;">
        <svg width="28" height="28" viewBox="0 0 18 18" id="socialSvgIcon"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/></svg>
      </div>
      <h3 id="socialModalTitle" style="font-size: 1.3rem; font-weight: 800; color: #0F172A; margin-bottom: 4px;">Đăng nhập với Google</h3>
      <p style="font-size: 0.88rem; color: #64748B;">Xác thực tài khoản và tự động ghi nhận vào CSDL MySQL</p>
    </div>

    <form id="socialAccountForm">
      <input type="hidden" id="socialProvider" value="google">
      <div class="auth-field" style="margin-bottom: 14px;">
        <label for="socialEmail" style="font-size: 0.85rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Email tài khoản</label>
        <input type="email" id="socialEmail" placeholder="your.name@gmail.com" required style="width: 100%; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A;">
      </div>

      <div class="auth-row-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px;">
        <div class="auth-field">
          <label for="socialFirstName" style="font-size: 0.85rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Họ</label>
          <input type="text" id="socialFirstName" placeholder="Nguyễn" required style="width: 100%; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A;">
        </div>
        <div class="auth-field">
          <label for="socialLastName" style="font-size: 0.85rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Tên</label>
          <input type="text" id="socialLastName" placeholder="Văn A" required style="width: 100%; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A;">
        </div>
      </div>

      <button type="submit" id="submitSocialBtn" style="width: 100%; padding: 14px; font-size: 1rem; font-weight: 700; border-radius: 10px; background: #0066CC; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
        <span id="socialBtnText">Xác nhận Đăng nhập Google</span>
        <i data-lucide="arrow-right"></i>
      </button>
    </form>
  </div>
</div>



<!-- MODAL XEM DEMO VIDEO (DEMO VIDEO MODAL) -->
<div class="auth-modal" id="demoVideoModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 860px; width: 94%; max-height: 92vh; padding: 0; background: #0F172A; border: 1px solid rgba(255,255,255,0.15); border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.6); overflow: hidden; display: flex; flex-direction: column;">
    
    <!-- Modal Header -->
    <div style="padding: 20px 24px; background: linear-gradient(135deg, #0F172A, #1E293B); border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #0066CC, #00AAFF); display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 15px rgba(0,102,204,0.4);">
          <i data-lucide="play" style="width: 22px; height: 22px; stroke-width: 2.5px; fill: white;"></i>
        </div>
        <div>
          <h3 style="font-size: 1.2rem; font-weight: 800; color: #FFFFFF; margin: 0;">Video Giải Pháp Số VNPT</h3>
          <span style="font-size: 0.8rem; color: #94A3B8;">Hệ sinh thái Chuyển đổi số doanh nghiệp toàn diện</span>
        </div>
      </div>
      <button class="auth-modal-close" id="closeDemoVideoModal" aria-label="Đóng" style="background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;"><i data-lucide="x"></i></button>
    </div>

    <!-- Video Container -->
    <div style="padding: 20px; flex-grow: 1; background: #000000; display: flex; align-items: center; justify-content: center;">
      <video id="demoVideoPlayer" controls playsinline style="width: 100%; max-height: 520px; border-radius: 14px; outline: none; background: #000000;">
        <source id="demoVideoSource" src="assets/videos/demo.mp4" type="video/mp4">
        Trình duyệt của bạn không hỗ trợ phát video.
      </video>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 14px 24px; background: #0F172A; border-top: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: flex-end; flex-shrink: 0;">
      <button type="button" id="closeDemoVideoBtn" style="padding: 10px 22px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: white; font-weight: 700; cursor: pointer; transition: 0.2s;">
        Đóng video
      </button>
    </div>

  </div>
</div>

<!-- =========================================================
     MODAL TRẢI NGHIỆM 3D KHÔNG GIAN SỐ VNPT (3D HOLOGRAM PORTAL)
     ========================================================= -->
<div class="auth-modal" id="hero3DModal" role="dialog" aria-modal="true" style="display:none; z-index:9999999;">
  <div class="auth-modal-inner" style="max-width: 900px; width: 94%; max-height: 94vh; padding: 0; background: #0A0F1D; border: 1px solid rgba(0, 170, 255, 0.3); border-radius: 28px; box-shadow: 0 0 80px rgba(0, 102, 204, 0.5), inset 0 0 30px rgba(0, 170, 255, 0.15); overflow: hidden; display: flex; flex-direction: column;">
    
    <!-- Modal 3D Header -->
    <div style="padding: 22px 30px 16px; background: linear-gradient(135deg, #090D18, #111B33); border-bottom: 1px solid rgba(0, 170, 255, 0.2); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, #0066CC, #00E5FF); display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 0 20px rgba(0, 229, 255, 0.6); flex-shrink: 0;">
          <i data-lucide="box" style="width: 24px; height: 24px; stroke-width: 2.5px; fill: none;"></i>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <h3 style="font-size: 1.3rem; font-weight: 800; color: #FFFFFF; margin: 0; letter-spacing: 0.5px;">Không Gian Số 3D VNPT</h3>
            <span style="background: rgba(0, 229, 255, 0.15); color: #00E5FF; border: 1px solid rgba(0, 229, 255, 0.4); font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 10px;">3D HOLOGRAM</span>
          </div>
          <span style="font-size: 0.82rem; color: #94A3B8;">Rê chuột hoặc chạm để xoay 3D tương tác đa chiều hệ sinh thái</span>
        </div>
      </div>
      <button class="auth-modal-close" id="close3DModal" aria-label="Đóng" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;"><i data-lucide="x"></i></button>
    </div>

    <!-- 3D Holographic Stage -->
    <div id="stage3DContainer" style="position: relative; flex-grow: 1; min-height: 420px; background: radial-gradient(circle at center, #111C38 0%, #070B16 100%); overflow: hidden; display: flex; align-items: center; justify-content: center; perspective: 1200px; cursor: grab; user-select: none; -webkit-user-select: none;">
      
      <!-- Ambient Grid Background -->
      <div style="position: absolute; inset: -50%; background-image: radial-gradient(rgba(0, 170, 255, 0.15) 1px, transparent 1px); background-size: 30px 30px; transform: rotateX(60deg); pointer-events: none;"></div>

      <!-- Main 3D Orbiting Rig -->
      <div id="stage3DRig" style="position: relative; width: 380px; height: 380px; transform-style: preserve-3d; transition: transform 0.25s cubic-bezier(0.1, 0.9, 0.2, 1); will-change: transform;">
        
        <!-- Glowing Center 3D Core -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) translateZ(40px); width: 110px; height: 110px; border-radius: 50%; background: radial-gradient(circle, #00E5FF 0%, #0055BB 70%, #051026 100%); box-shadow: 0 0 60px #00AAFF, 0 0 120px rgba(0, 229, 255, 0.4); display: flex; align-items: center; justify-content: center; animation: pulse3d 3s ease-in-out infinite;">
          <svg width="60" height="60" viewBox="0 0 80 80" fill="none">
            <path d="M20 40 L40 20 L60 40 L40 60 Z" fill="white" opacity="0.9"/>
            <circle cx="40" cy="40" r="12" fill="#00E5FF"/>
          </svg>
        </div>

        <!-- 3D Outer Ring Glow -->
        <div style="position: absolute; inset: 0; border-radius: 50%; border: 2px dashed rgba(0, 229, 255, 0.4); transform: translateZ(10px); animation: spin3d 25s linear infinite;"></div>
        <div style="position: absolute; inset: 45px; border-radius: 50%; border: 1.5px solid rgba(0, 102, 204, 0.6); transform: translateZ(-20px); animation: spin3dRev 18s linear infinite;"></div>

        <!-- 6 Floating 3D Cards -->
        <div class="node3d" data-title="Cloud 5G Ultra" data-desc="Mạng siêu dữ liệu kết nối 5G tốc độ cao, độ trễ cực thấp cho doanh nghiệp số" style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%) translateZ(60px);">
          <div style="background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(0, 229, 255, 0.5); border-radius: 16px; padding: 10px 16px; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(0, 170, 255, 0.3); backdrop-filter: blur(8px);">
            <i data-lucide="wifi" style="color: #00E5FF; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.88rem;">Cloud 5G Ultra</span>
          </div>
        </div>

        <div class="node3d" data-title="AI & Tự Động Hóa" data-desc="Ứng dụng trí tuệ nhân tạo và học máy tối ưu 100% quy trình vận hành" style="position: absolute; top: 22%; right: -40px; transform: translateZ(40px);">
          <div style="background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(255, 107, 0, 0.5); border-radius: 16px; padding: 10px 16px; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(255, 107, 0, 0.3); backdrop-filter: blur(8px);">
            <i data-lucide="cpu" style="color: #FF6B00; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.88rem;">AI Automation</span>
          </div>
        </div>

        <div class="node3d" data-title="Bảo Mật Cyber Security" data-desc="Hệ thống giám sát an ninh mạng 24/7 tiêu chuẩn quốc tế ISO 27001" style="position: absolute; bottom: 22%; right: -40px; transform: translateZ(50px);">
          <div style="background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(0, 255, 136, 0.5); border-radius: 16px; padding: 10px 16px; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(0, 255, 136, 0.3); backdrop-filter: blur(8px);">
            <i data-lucide="shield-check" style="color: #00FF88; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.88rem;">Cyber Security</span>
          </div>
        </div>

        <div class="node3d" data-title="Big Data Analytics" data-desc="Hệ thống khai phá &amp; phân tích dữ liệu quy mô lớn thời gian thực" style="position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%) translateZ(70px);">
          <div style="background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(170, 0, 255, 0.5); border-radius: 16px; padding: 10px 16px; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(170, 0, 255, 0.3); backdrop-filter: blur(8px);">
            <i data-lucide="database" style="color: #AA00FF; width: 20px; height: 20px;"></i>
            <span style="font-size: 0.88rem; font-weight: 700;">Big Data Analytics</span>
          </div>
        </div>

        <div class="node3d" data-title="IoT &amp; Smart City" data-desc="Nền tảng kết nối vạn vật và điều hành đô thị thông minh toàn diện" style="position: absolute; bottom: 22%; left: -40px; transform: translateZ(40px);">
          <div style="background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(255, 204, 0, 0.5); border-radius: 16px; padding: 10px 16px; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(255, 204, 0, 0.3); backdrop-filter: blur(8px);">
            <i data-lucide="globe" style="color: #FFCC00; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.88rem;">Smart City IoT</span>
          </div>
        </div>

        <div class="node3d" data-title="Quản Trị Doanh Nghiệp Số" data-desc="Hệ thống quản lý tài nguyên &amp; điều hành hạ tầng doanh nghiệp VNPT" style="position: absolute; top: 22%; left: -40px; transform: translateZ(55px);">
          <div style="background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(0, 170, 255, 0.5); border-radius: 16px; padding: 10px 16px; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(0, 170, 255, 0.3); backdrop-filter: blur(8px);">
            <i data-lucide="briefcase" style="color: #00AAFF; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.88rem;">Digital Enterprise</span>
          </div>
        </div>

      </div>

      <!-- Active Node Info Tooltip -->
      <div id="info3DBox" style="position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%); background: rgba(15, 23, 42, 0.92); border: 1px solid rgba(0, 229, 255, 0.4); padding: 12px 24px; border-radius: 20px; color: white; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); backdrop-filter: blur(10px); width: 88%; max-width: 520px; transition: 0.3s;">
        <h4 id="info3DTitle" style="margin: 0 0 2px; font-size: 1.05rem; font-weight: 800; color: #00E5FF;">Hệ Sinh Thái Số VNPT 3D</h4>
        <p id="info3DDesc" style="margin: 0; font-size: 0.85rem; color: #CBD5E1;">Di chuột qua các điểm kết nối 3D để xem thông tin dịch vụ</p>
      </div>

    </div>

    <!-- 3D Modal Footer -->
    <div style="padding: 14px 28px; background: #070B16; border-top: 1px solid rgba(0, 170, 255, 0.2); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; flex-wrap: wrap; gap: 12px;">
      <div style="font-size: 0.85rem; color: #94A3B8; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="sparkles" style="width: 16px; height: 16px; color: #00E5FF;"></i>
        <span>Di chuyển con trỏ chuột trên vùng hiển thị để xoay không gian 3D</span>
      </div>
      <button type="button" id="close3DBtn" style="padding: 10px 24px; border-radius: 12px; border: 1px solid rgba(0, 229, 255, 0.4); background: linear-gradient(135deg, #0066CC, #0099FF); color: white; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 15px rgba(0,102,204,0.4);">
        Thoát chế độ 3D
      </button>
    </div>

  </div>
</div>

<!-- MODAL CHI TIẾT CHỈ SỐ NĂNG LỰC VNPT (STAT DETAIL SHOWCASE MODAL) -->
<div class="auth-modal" id="statDetailModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 720px; width: 94%; max-height: 88vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div id="statModalHeader" style="padding: 28px 28px 20px; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; position: relative; flex-shrink: 0;">
      <button class="auth-modal-close" id="closeStatModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
        <div id="statModalIconWrap" style="width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: #0066CC; box-shadow: 0 8px 20px rgba(0,102,204,0.4); flex-shrink: 0;">
          <i data-lucide="award" id="statModalIcon" style="width: 26px; height: 26px; color: white; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <span style="display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(0, 170, 255, 0.2); color: #00AAFF; border: 1px solid rgba(0, 170, 255, 0.4); margin-bottom: 4px;">Năng Lực VNPT</span>
          <h3 id="statModalTitle" style="font-size: 1.35rem; font-weight: 800; color: #FFFFFF; margin: 0;">Chi Tiết Chỉ Số</h3>
        </div>
      </div>
      <p id="statModalSub" style="font-size: 0.92rem; color: rgba(255,255,255,0.85); margin: 0; line-height: 1.5;">Thông tin chứng nhận và hạ tầng dịch vụ số hàng đầu Việt Nam</p>
    </div>

    <div id="statModalBody" style="padding: 24px 28px; flex-grow: 1; overflow-y: auto; color: #334155;">
      <!-- Populated dynamically by JS -->
    </div>

    <div style="padding: 16px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; gap: 12px;">
      <span style="font-size: 0.82rem; color: #64748B; font-weight: 500;">VNPT Digital Infrastructure Report 2026</span>
      <button type="button" id="closeStatBtn" style="padding: 10px 24px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer; transition: 0.2s;">
        Đóng
      </button>
    </div>

  </div>
</div>

<!-- MODAL DĂNG KÝ TƯ VẤN BẮT ĐẦU NGAY (QUICK CONSULTATION MODAL) -->
<div class="auth-modal" id="consultationModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 520px; width: 92%; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); overflow: hidden;">
    
    <div style="padding: 28px 28px 20px; background: linear-gradient(135deg, #0055BB, #00AAFF); color: white; position: relative;">
      <button class="auth-modal-close" id="closeConsultationModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.2); border: none; width: 32px; height: 32px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
        <div style="width: 44px; height: 44px; border-radius: 14px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white;">
          <i data-lucide="rocket" style="width: 24px; height: 24px; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <h3 style="font-size: 1.3rem; font-weight: 800; color: #FFFFFF; margin: 0;">Bắt Đầu Chuyển Đổi Số</h3>
          <span style="font-size: 0.82rem; color: rgba(255,255,255,0.9);">Nhận tư vấn chuyên sâu miễn phí từ chuyên gia VNPT</span>
        </div>
      </div>
    </div>

    <form id="consultationForm" style="padding: 24px 28px;">
      <div style="margin-bottom: 14px;">
        <label style="font-size: 0.85rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Họ tên / Tên doanh nghiệp</label>
        <input type="text" id="consultName" placeholder="Tập đoàn / Doanh nghiệp / Cá nhân" required style="width: 100%; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="font-size: 0.85rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Số điện thoại liên hệ</label>
        <input type="tel" id="consultPhone" placeholder="0901 234 567" required style="width: 100%; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A;">
      </div>

      <div style="margin-bottom: 20px;">
        <label style="font-size: 0.85rem; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Dịch vụ bạn quan tâm</label>
        <select id="consultService" style="width: 100%; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A; background: white;">
          <option value="cloud">Hạ tầng Điện toán Đám mây (Cloud & Data Center)</option>
          <option value="security">Giải pháp Bảo mật & An toàn thông tin</option>
          <option value="ai">Trí tuệ nhân tạo (AI & Automation)</option>
          <option value="5g">Mạng 5G & Truyền dẫn tốc độ cao</option>
          <option value="enterprise">Hệ sinh thái Doanh nghiệp Số toàn diện</option>
        </select>
      </div>

      <button type="submit" style="width: 100%; padding: 14px; font-size: 1rem; font-weight: 700; border-radius: 12px; background: #0066CC; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(0,102,204,0.35);">
        <span>Gửi Yêu Cầu Tư Vấn Ngay</span>
        <i data-lucide="arrow-right"></i>
      </button>
    </form>

  </div>
</div>

<!-- MODAL TÀI LIỆU KỸ THUẬT (TECHNICAL DOCS MODAL) -->
<div class="auth-modal" id="techDocsModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 760px; width: 94%; max-height: 88vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div style="padding: 24px 28px; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; position: relative; flex-shrink: 0;">
      <button class="auth-modal-close" id="closeTechDocsModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 46px; height: 46px; border-radius: 14px; background: linear-gradient(135deg, #0066CC, #00E5FF); display: flex; align-items: center; justify-content: center; color: white;">
          <i data-lucide="book-open" style="width: 24px; height: 24px; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <h3 style="font-size: 1.3rem; font-weight: 800; color: #FFFFFF; margin: 0;">Trung Tâm Tài Liệu Kỹ Thuật VNPT</h3>
          <span style="font-size: 0.82rem; color: #94A3B8;">Hướng dẫn tích hợp API, SDK &amp; Hạ tầng Cloud Enterprise</span>
        </div>
      </div>
    </div>

    <div style="padding: 24px 28px; flex-grow: 1; overflow-y: auto; color: #334155;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px;">
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 14px;">
          <div style="font-weight: 800; color: #0066CC; font-size: 0.95rem; margin-bottom: 4px;">⚡ VNPT Cloud API v2.4</div>
          <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 10px;">RESTful API quản lý Virtual Server, Storage S3 &amp; Load Balancer</p>
          <span style="background: #E0F2FE; color: #0369A1; padding: 3px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">OpenAPI 3.0</span>
        </div>

        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 14px;">
          <div style="font-weight: 800; color: #00AA55; font-size: 0.95rem; margin-bottom: 4px;">🛡️ Security &amp; OAuth 2.0</div>
          <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 10px;">Chuẩn xác thực JWT Token &amp; Chữ ký số VNPT SmartCA</p>
          <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">ISO 27001</span>
        </div>

        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 14px;">
          <div style="font-weight: 800; color: #FF6B00; font-size: 0.95rem; margin-bottom: 4px;">🤖 AI Automation SDK</div>
          <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 10px;">Bộ SDK Python, Node.js &amp; Java trích xuất OCR, AI Chatbot</p>
          <span style="background: #FFEDD5; color: #C2410C; padding: 3px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">Python / JS / Java</span>
        </div>
      </div>

      <h4 style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin-bottom: 10px;">Tệp Hướng Dẫn Nhanh:</h4>
      <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; font-size: 0.88rem; color: #475569;">
        <li style="display: flex; align-items: center; justify-content: space-between; background: #F1F5F9; padding: 10px 14px; border-radius: 10px;">
          <span>📄 Hướng dẫn khởi tạo Server Cloud VNPT trên Linux/Windows</span>
          <span style="font-weight: 700; color: #0066CC; font-size: 0.8rem;">[PDF - 2.4MB]</span>
        </li>
        <li style="display: flex; align-items: center; justify-content: space-between; background: #F1F5F9; padding: 10px 14px; border-radius: 10px;">
          <span>📄 Tích hợp Chữ ký số Hóa đơn Điện tử VNPT Invoice API</span>
          <span style="font-weight: 700; color: #0066CC; font-size: 0.8rem;">[DOCX - 1.8MB]</span>
        </li>
      </ul>
    </div>

    <div style="padding: 14px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
      <button type="button" id="closeTechDocsBtn" style="padding: 10px 24px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer;">Đóng tài liệu</button>
    </div>

  </div>
</div>

<!-- MODAL CHÍNH SÁCH BẢO MẬT & ĐIỀU KHOẢN (POLICY MODAL) -->
<div class="auth-modal" id="footerPolicyModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 720px; width: 94%; max-height: 88vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div style="padding: 24px 28px; background: linear-gradient(135deg, #0055BB, #0088FF); color: white; position: relative; flex-shrink: 0;">
      <button class="auth-modal-close" id="closeFooterPolicyModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.2); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 14px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white;">
          <i data-lucide="shield-check" style="width: 24px; height: 24px; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <h3 id="footerPolicyTitle" style="font-size: 1.3rem; font-weight: 800; color: #FFFFFF; margin: 0;">Chính Sách Bảo Mật Dữ Liệu</h3>
          <span style="font-size: 0.82rem; color: rgba(255,255,255,0.9);">Cam kết an toàn thông tin tiêu chuẩn ISO 27001 VNPT</span>
        </div>
      </div>
    </div>

    <div id="footerPolicyContent" style="padding: 24px 28px; flex-grow: 1; overflow-y: auto; color: #334155; font-size: 0.92rem; line-height: 1.6;">
      <!-- Populated by JS -->
    </div>

    <div style="padding: 14px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
      <button type="button" id="closeFooterPolicyBtn" style="padding: 10px 24px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer;">Đóng điều khoản</button>
    </div>

  </div>
</div>

<!-- MODAL BẰNG KHEN CHỨNG NHẬN (BADGE MODAL) -->
<div class="auth-modal" id="footerBadgeModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 580px; width: 92%; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div id="badgeHeader" style="padding: 24px 28px; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; position: relative;">
      <button class="auth-modal-close" id="closeFooterBadgeModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 14px;">
        <div id="badgeIconBox" style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #0066CC, #00E5FF); display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
          <i data-lucide="shield-check" id="badgeModalIcon" style="width: 24px; height: 24px; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <h3 id="badgeModalTitle" style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF; margin: 0;">Chứng Nhận Tiêu Chuẩn</h3>
          <span id="badgeModalSub" style="font-size: 0.82rem; color: #94A3B8;">Thông tin xác thực chất lượng hạ tầng dịch vụ VNPT</span>
        </div>
      </div>
    </div>

    <div id="badgeModalBody" style="padding: 24px 28px; color: #334155;">
      <!-- Populated by JS -->
    </div>

    <div style="padding: 14px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
      <button type="button" id="closeBadgeBtn" style="padding: 10px 24px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer;">Đóng</button>
    </div>

  </div>
</div>

<!-- MODAL GIẢI PHÁP CHUYÊN NGHÀNH (SOLUTION SHOWCASE MODAL) -->
<div class="auth-modal" id="solutionModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 760px; width: 94%; max-height: 88vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div id="solutionHeader" style="padding: 26px 28px 20px; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; position: relative; flex-shrink: 0;">
      <button class="auth-modal-close" id="closeSolutionModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
        <div id="solutionIconBox" style="width: 50px; height: 50px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: #0066CC; box-shadow: 0 8px 20px rgba(0,102,204,0.4); flex-shrink: 0;">
          <i data-lucide="layers" id="solutionModalIcon" style="width: 26px; height: 26px; color: white; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <span style="display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(0, 170, 255, 0.2); color: #00AAFF; border: 1px solid rgba(0, 170, 255, 0.4); margin-bottom: 4px;">Gói Giải Pháp VNPT</span>
          <h3 id="solutionModalTitle" style="font-size: 1.35rem; font-weight: 800; color: #FFFFFF; margin: 0;">Chi Tiết Giải Pháp</h3>
        </div>
      </div>
      <p id="solutionModalSub" style="font-size: 0.92rem; color: rgba(255,255,255,0.85); margin: 0; line-height: 1.5;">Bộ giải pháp chuyển đổi số toàn diện thiết kế riêng theo từng lĩnh vực</p>
    </div>

    <div id="solutionModalBody" style="padding: 24px 28px; flex-grow: 1; overflow-y: auto; color: #334155;">
      <!-- Populated dynamically by JS -->
    </div>

    <div style="padding: 16px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; gap: 12px;">
      <span style="font-size: 0.82rem; color: #64748B; font-weight: 500;">VNPT Enterprise Solutions 2026</span>
      <div style="display: flex; gap: 10px;">
        <button type="button" id="solutionConsultBtn" style="padding: 10px 20px; border-radius: 10px; background: #0066CC; color: white; font-weight: 700; border: none; cursor: pointer;">Đăng ký tư vấn gói này</button>
        <button type="button" id="closeSolutionBtn" style="padding: 10px 20px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer;">Đóng</button>
      </div>
    </div>

  </div>
</div>

<!-- MODAL TRUNG TÂM HỖ TRỢ & CSKH 24/7 (SUPPORT CENTER MODAL) -->
<div class="auth-modal" id="supportCenterModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 650px; width: 94%; max-height: 88vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div style="padding: 24px 28px; background: linear-gradient(135deg, #0055BB, #00AAFF); color: white; position: relative;">
      <button class="auth-modal-close" id="closeSupportCenterModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.2); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
          <i data-lucide="headset" style="width: 26px; height: 26px; stroke-width: 2.5px;"></i>
        </div>
        <div>
          <h3 style="font-size: 1.3rem; font-weight: 800; color: #FFFFFF; margin: 0;">Trung Tâm Hỗ Trợ Khách Hàng VNPT 24/7</h3>
          <span style="font-size: 0.82rem; color: rgba(255,255,255,0.9);">Kênh hỗ trợ kỹ thuật, giải đáp cước &amp; xử lý sự cố hạ tầng</span>
        </div>
      </div>
    </div>

    <div style="padding: 24px 28px; flex-grow: 1; overflow-y: auto; color: #334155;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;">
        <a href="tel:18001260" style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 18px; border-radius: 16px; text-decoration: none; display: flex; align-items: center; gap: 14px;">
          <div style="width: 40px; height: 40px; border-radius: 12px; background: #0284C7; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="phone-call" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #0369A1;">1800 1260</div>
            <div style="font-size: 0.78rem; color: #0284C7; font-weight: 600;">Tổng đài miễn cước 24/7</div>
          </div>
        </a>

        <a href="mailto:contact@vnpt.vn" style="background: #F0FDF4; border: 1px solid #BBF7D0; padding: 18px; border-radius: 16px; text-decoration: none; display: flex; align-items: center; gap: 14px;">
          <div style="width: 40px; height: 40px; border-radius: 12px; background: #16A34A; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="mail" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <div style="font-size: 0.95rem; font-weight: 800; color: #15803D;">contact@vnpt.vn</div>
            <div style="font-size: 0.78rem; color: #16A34A; font-weight: 600;">Hỗ trợ qua Email</div>
          </div>
        </a>
      </div>

      <h4 style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin-bottom: 10px;">Các Kênh Hỗ Trợ Khác:</h4>
      <div style="display: flex; flex-direction: column; gap: 10px;">
        <div id="triggerLiveChatFromSupport" style="cursor: pointer; background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px 18px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <i data-lucide="bot" style="color: #0066CC; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.9rem; color: #1E293B;">Trò chuyện trực tiếp với Trợ lý AI VNPT SmartBot</span>
          </div>
          <span style="color: #0066CC; font-weight: 700; font-size: 0.8rem;">Mở Chat 💬</span>
        </div>

        <a href="#contact" id="triggerConsultFromSupport" style="text-decoration: none; background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px 18px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <i data-lucide="user-check" style="color: #00AA55; width: 20px; height: 20px;"></i>
            <span style="font-weight: 700; font-size: 0.9rem; color: #1E293B;">Yêu cầu Kỹ sư VNPT gọi điện hỗ trợ xử lý tận nơi</span>
          </div>
          <span style="color: #00AA55; font-weight: 700; font-size: 0.8rem;">Đặt lịch 📅</span>
        </a>
      </div>
    </div>

    <div style="padding: 14px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
      <button type="button" id="closeSupportCenterBtn" style="padding: 10px 24px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer;">Đóng</button>
    </div>

  </div>
</div>

<!-- MODAL CHI TIẾT CASE STUDY KHÁCH HÀNG (REVIEW DETAIL MODAL) -->
<div class="auth-modal" id="reviewDetailModal" role="dialog" aria-modal="true" style="display:none; z-index:999999;">
  <div class="auth-modal-inner" style="max-width: 720px; width: 94%; max-height: 88vh; padding: 0; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); overflow: hidden; display: flex; flex-direction: column;">
    
    <div id="reviewDetailHeader" style="padding: 24px 28px; background: linear-gradient(135deg, #0F172A, #1E293B); color: white; position: relative; flex-shrink: 0;">
      <button class="auth-modal-close" id="closeReviewDetailModal" aria-label="Đóng" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.15); border: none; width: 34px; height: 34px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i data-lucide="x"></i></button>

      <div style="display: flex; align-items: center; gap: 14px;">
        <div id="reviewDetailAvatar" style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">NT</div>
        <div>
          <h3 id="reviewDetailAuthor" style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF; margin: 0;">Nguyễn Thanh Tùng</h3>
          <span id="reviewDetailRole" style="font-size: 0.85rem; color: #94A3B8;">CTO – Công ty CP Thương mại ABC</span>
        </div>
      </div>
    </div>

    <div id="reviewDetailBody" style="padding: 24px 28px; flex-grow: 1; overflow-y: auto; color: #334155; line-height: 1.6;">
      <!-- Dynamic Case study content populated by JS -->
    </div>

    <div style="padding: 14px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
      <button type="button" id="closeReviewDetailBtn" style="padding: 10px 24px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: white; color: #334155; font-weight: 700; cursor: pointer;">Đóng Case Study</button>
    </div>

  </div>
</div>



<!-- CHATBOT AI WIDGET -->
<div class="chatbot-wrapper" id="chatbotWrapper" style="position: fixed; bottom: 24px; right: 24px; z-index: 999999;">
  <button class="chatbot-toggle" id="chatbotToggle" aria-label="Mở Trợ lý AI VNPT" style="position: relative; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(0,102,204,0.5); transition: transform 0.25s;">
    <span class="toggle-icon open-icon" style="display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-robot" style="font-size: 26px;"></i></span>
    <span class="toggle-icon close-icon" style="display: none; align-items: center; justify-content: center;"><i class="fa-solid fa-xmark" style="font-size: 24px;"></i></span>
    <span class="chat-badge" style="position: absolute; top: -4px; right: -4px; background: #FF6B00; color: white; padding: 2px 7px; border-radius: 20px; font-size: 0.68rem; font-weight: 800; border: 2px solid white;">AI</span>
  </button>

  <div class="chatbot-window" id="chatbotWindow" role="dialog" aria-modal="true" style="position: absolute; bottom: 76px; right: 0; width: 380px; max-width: 92vw; height: 530px; max-height: 78vh; background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); display: none; flex-direction: column; overflow: hidden; opacity: 0; visibility: hidden; transform: translateY(16px) scale(0.96); transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);">
    
    <div class="chat-header" style="background: linear-gradient(135deg, #0F172A, #1E293B); padding: 14px 18px; color: white; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="chat-avatar" style="position: relative;">
          <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #0066CC, #00AAFF); display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(0,102,204,0.4);">
            <i class="fa-solid fa-robot" style="font-size: 20px;"></i>
          </div>
          <span class="online-dot" style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #22C55E; border-radius: 50%; border: 2px solid #0F172A;" title="Đang hoạt động"></span>
        </div>
        <div class="chat-header-info">
          <strong style="font-size: 0.95rem; font-weight: 800; color: #FFFFFF; display: block; margin-bottom: 1px;">VNPT Smart AI</strong>
          <span style="font-size: 0.75rem; color: #94A3B8; display: flex; align-items: center; gap: 4px;">
            Trợ lý AI Chuyển Đổi Số 24/7
          </span>
        </div>
      </div>
      <button class="chat-close" id="chatClose" aria-label="Đóng chat" style="background: rgba(255,255,255,0.12); border: none; width: 32px; height: 32px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;"><i class="fa-solid fa-xmark" style="font-size: 16px;"></i></button>
    </div>

    <div class="chat-messages" id="chatMessages" style="flex: 1; overflow-y: auto; padding: 16px; background: #F8FAFC; display: flex; flex-direction: column; gap: 14px;">
      <div class="msg bot-msg" style="display: flex; gap: 10px; max-width: 90%;">
        <div class="msg-avatar" style="flex-shrink: 0;">
          <div style="width: 32px; height: 32px; border-radius: 10px; background: #0066CC; color: white; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-robot" style="font-size: 16px;"></i>
          </div>
        </div>
        <div>
          <div class="msg-bubble" style="background: white; border: 1px solid #E2E8F0; padding: 12px 14px; border-radius: 16px 16px 16px 4px; font-size: 0.88rem; color: #1E293B; line-height: 1.55; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            Xin chào! 👋 Tôi là <strong>VNPT Smart AI</strong> — Trợ lý ảo tư vấn hạ tầng &amp; dịch vụ số VNPT.<br><br>Bạn cần tôi hỗ trợ câu hỏi hoặc báo giá gói dịch vụ nào hôm nay?
          </div>
          <span class="msg-time" style="font-size: 0.7rem; color: #94A3B8; margin-top: 4px; display: block;">Vừa xong</span>
        </div>
      </div>

      <!-- Quick replies / Gợi ý nhanh -->
      <div class="quick-replies" id="quickReplies" style="display: flex; flex-wrap: wrap; gap: 6px; margin-left: 42px;">
        <button class="qr-btn" data-query="Tư vấn gói Cloud Enterprise" style="padding: 6px 12px; border-radius: 20px; background: white; border: 1px solid #BAE6FD; color: #0284C7; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: 0.2s;">☁️ Gói Cloud Enterprise</button>
        <button class="qr-btn" data-query="Hướng dẫn đăng ký SmartCA & Hóa đơn số" style="padding: 6px 12px; border-radius: 20px; background: white; border: 1px solid #BAE6FD; color: #0284C7; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: 0.2s;">🔑 Chữ ký số SmartCA</button>
        <button class="qr-btn" data-query="Bảng giá Internet Cáp quang FiberVNN" style="padding: 6px 12px; border-radius: 20px; background: white; border: 1px solid #BAE6FD; color: #0284C7; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: 0.2s;">⚡ Cáp quang FiberVNN</button>
        <button class="qr-btn" data-query="Cách thức liên hệ hỗ trợ kỹ thuật 24/7" style="padding: 6px 12px; border-radius: 20px; background: white; border: 1px solid #BAE6FD; color: #0284C7; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: 0.2s;">📞 Tổng đài 1800 1260</button>
      </div>
    </div>

    <!-- Typing indicator -->
    <div class="chat-typing" id="chatTyping" style="display: none; align-items: center; gap: 8px; padding: 8px 16px; background: #F1F5F9; font-size: 0.78rem; color: #64748B; border-top: 1px solid #E2E8F0;">
      <div class="typing-dots" style="display: flex; gap: 4px;">
        <span style="width: 6px; height: 6px; border-radius: 50%; background: #0066CC; animation: typing 1.2s infinite ease-in-out;"></span>
        <span style="width: 6px; height: 6px; border-radius: 50%; background: #0066CC; animation: typing 1.2s infinite ease-in-out 0.2s;"></span>
        <span style="width: 6px; height: 6px; border-radius: 50%; background: #0066CC; animation: typing 1.2s infinite ease-in-out 0.4s;"></span>
      </div>
      <span>VNPT Smart AI đang soạn phản hồi...</span>
    </div>

    <div class="chat-input-area" style="padding: 12px 14px; background: white; border-top: 1px solid #E2E8F0; display: flex; align-items: center; gap: 8px;">
      <input type="text" id="chatInput" placeholder="Nhập thắc mắc cần AI giải đáp..." style="flex: 1; padding: 10px 16px; border-radius: 20px; border: 1.5px solid #CBD5E1; font-size: 0.88rem; outline: none;" />
      <button id="chatSend" aria-label="Gửi tin nhắn" style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0066CC, #00AAFF); border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,102,204,0.3);">
        <i class="fa-solid fa-paper-plane" style="font-size: 14px;"></i>
      </button>
    </div>
  </div>
</div>

<!-- Modal Tra cứu tin nhắn & Phản hồi tư vấn dành cho Khách hàng -->
<div class="modal" id="checkSupportModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
  <div class="modal-dialog" style="max-width: 680px; width: 92%; background: white; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
    <div class="modal-header" style="background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fa-solid fa-comments fs-3"></i>
        <div>
          <h3 style="color: white; margin: 0; font-size: 1.15rem; font-weight: 800;">Hòm Thư Phản Hồi Tư Vấn Khách Hàng</h3>
          <p style="margin: 0; font-size: 0.82rem; opacity: 0.92;">Nhập Email hoặc Số điện thoại để xem câu trả lời từ Chuyên viên VNPT</p>
        </div>
      </div>
      <button id="closeCheckSupportModal" style="color: white; font-size: 1.6rem; background: none; border: none; cursor: pointer; opacity: 0.9;">&times;</button>
    </div>
    <div style="padding: 24px;">
      <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" id="checkSupportQueryInput" placeholder="Nhập Email hoặc Số điện thoại đăng ký tư vấn..." style="flex: 1; padding: 12px 16px; border-radius: 12px; border: 1.5px solid #CBD5E1; font-size: 0.95rem; outline: none;" />
        <button id="btnDoCheckSupport" style="padding: 12px 22px; border-radius: 12px; font-weight: 700; background: #0066CC; color: white; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-magnifying-glass"></i> Tra cứu
        </button>
      </div>

      <div id="checkSupportResultsList" style="max-height: 420px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; padding-right: 4px;">
        <div style="text-align: center; color: #64748B; padding: 40px 10px;">
          <i class="fa-solid fa-envelope-open-text fa-3x mb-3" style="color: #94A3B8;"></i>
          <p style="font-size: 0.95rem; margin: 0;">Nhập Số điện thoại hoặc Email bạn đã điền lúc Đăng ký tư vấn để xem tin nhắn trả lời từ Nhân viên VNPT.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Thông báo từ Hệ thống / Admin gửi cho Khách hàng -->
<div class="modal" id="customerNotifModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
  <div class="modal-dialog" style="max-width: 650px; width: 92%; background: white; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
    <div class="modal-header" style="background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fa-solid fa-bell fs-3"></i>
        <div>
          <h3 style="color: white; margin: 0; font-size: 1.15rem; font-weight: 800;" id="notifModalTitle">Thông Báo Từ VNPT</h3>
          <p style="margin: 0; font-size: 0.82rem; opacity: 0.92;" id="notifModalSub">Các thông tin cập nhật, ưu đãi và dịch vụ dành riêng cho bạn</p>
        </div>
      </div>
      <button id="closeCustomerNotifModal" style="color: white; font-size: 1.6rem; background: none; border: none; cursor: pointer; opacity: 0.9;">&times;</button>
    </div>
    <div style="padding: 24px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
        <span style="font-weight: 700; color: #334155; font-size: 0.9rem;"><i class="fa-solid fa-list-check me-1 text-primary"></i> Danh sách thông báo</span>
        <button type="button" id="btnMarkAllNotifsRead" style="background: #F0F9FF; border: 1px solid #BAE6FD; color: #0066CC; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-check-double"></i> Đánh dấu tất cả là đã đọc
        </button>
      </div>

      <div id="customerNotifList" style="max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; padding-right: 4px;">
        <div style="text-align: center; color: #64748B; padding: 40px 10px;">
          <i class="fa-solid fa-bell-slash fa-3x mb-3 text-muted"></i>
          <p style="font-size: 0.95rem; margin: 0;">Bạn chưa có thông báo nào từ hệ thống.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<button class="scroll-to-top" id="scrollToTopBtn" aria-label="Cuộn lên đầu trang" title="Cuộn lên đầu trang">
  <i class="fa-solid fa-chevron-up"></i>
</button>

<div class="toast" id="toast">
  <i data-lucide="check-circle"></i>
  <span id="toastMsg">Thao tác thành công!</span>
</div>

<script src="frontend/js/api.js?v=<?= time(); ?>"></script>
<script src="frontend/js/i18n.js?v=<?= time(); ?>"></script>
<script src="frontend/js/pages.js?v=<?= time(); ?>"></script>
<script src="frontend/js/main.js?v=<?= time(); ?>"></script>
<script src="frontend/js/chat.js?v=<?= time(); ?>"></script>
<script src="frontend/js/cart.js?v=<?= time(); ?>"></script>
<script src="frontend/js/auth.js?v=<?= time(); ?>"></script>
<script src="frontend/js/account.js?v=<?= time(); ?>"></script>
<script src="frontend/js/admin.js?v=<?= time(); ?>"></script>
<script src="frontend/js/carousel.js?v=<?= time(); ?>"></script>
<script src="frontend/js/search.js?v=<?= time(); ?>"></script>
</body>
</html>