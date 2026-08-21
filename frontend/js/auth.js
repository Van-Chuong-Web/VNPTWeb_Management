/* ---- Global Window Modal Helpers (Accessible Anywhere Instantly) ---- */
window.openLoginModal = function() {
  const modal = document.getElementById('loginModal');
  const overlay = document.getElementById('modalOverlay');
  if (modal) {
    modal.classList.add('open');
    modal.style.setProperty('display', 'flex', 'important');
    modal.style.setProperty('opacity', '1', 'important');
    modal.style.setProperty('visibility', 'visible', 'important');
    modal.style.setProperty('pointer-events', 'auto', 'important');
    modal.style.setProperty('z-index', '9999999', 'important');
  }
  if (overlay) {
    overlay.classList.add('active');
    overlay.style.setProperty('display', 'block', 'important');
    overlay.style.setProperty('opacity', '1', 'important');
    overlay.style.setProperty('visibility', 'visible', 'important');
    overlay.style.setProperty('z-index', '999999', 'important');
  }
  document.body.style.overflow = 'hidden';
};

window.openRegisterModal = function() {
  const modal = document.getElementById('registerModal');
  const overlay = document.getElementById('modalOverlay');
  if (modal) {
    modal.classList.add('open');
    modal.style.setProperty('display', 'flex', 'important');
    modal.style.setProperty('opacity', '1', 'important');
    modal.style.setProperty('visibility', 'visible', 'important');
    modal.style.setProperty('pointer-events', 'auto', 'important');
    modal.style.setProperty('z-index', '9999999', 'important');
  }
  if (overlay) {
    overlay.classList.add('active');
    overlay.style.setProperty('display', 'block', 'important');
    overlay.style.setProperty('opacity', '1', 'important');
    overlay.style.setProperty('visibility', 'visible', 'important');
    overlay.style.setProperty('z-index', '999999', 'important');
  }
  document.body.style.overflow = 'hidden';
};

window.closeAllAuthModals = function() {
  ['loginModal', 'registerModal', 'forgotModal'].forEach(id => {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.remove('open');
      modal.style.setProperty('display', 'none', 'important');
      modal.style.setProperty('opacity', '0', 'important');
      modal.style.setProperty('visibility', 'hidden', 'important');
      modal.style.setProperty('pointer-events', 'none', 'important');
    }
  });
  const overlay = document.getElementById('modalOverlay');
  if (overlay) {
    overlay.classList.remove('active');
    overlay.style.setProperty('display', 'none', 'important');
    overlay.style.setProperty('opacity', '0', 'important');
    overlay.style.setProperty('visibility', 'hidden', 'important');
  }
  document.body.style.overflow = '';
};

(function () {
  'use strict';

  /* ---- Configuration for Real Google & Facebook OAuth Credentials ---- */
  const GOOGLE_CLIENT_ID = '653013164885-0q59b5044gn602dgjt4rl1btjor3c4ga.apps.googleusercontent.com';
  const FB_APP_ID = '1043538668533467';

  window.VNPT_CONFIG = {
    GOOGLE_CLIENT_ID: GOOGLE_CLIENT_ID,
    FB_APP_ID: FB_APP_ID
  };

  /* ---- DOM refs ---- */
  const loginModal      = document.getElementById('loginModal');
  const registerModal   = document.getElementById('registerModal');
  const modalOverlay    = document.getElementById('modalOverlay');
  const openLoginBtn    = document.getElementById('openLogin');
  const openRegisterBtn = document.getElementById('openRegister');
  const closeLoginBtn   = document.getElementById('closeLogin');
  const closeRegisterBtn= document.getElementById('closeRegister');
  const switchToReg     = document.getElementById('switchToRegister');
  const switchToLog     = document.getElementById('switchToLogin');
  const authBtns        = document.getElementById('authBtns');
  const userMenu        = document.getElementById('userMenu');
  const userAvatarBtn   = document.getElementById('userAvatarBtn');
  const userDropdown    = document.getElementById('userDropdown');
  const userAvatarCircle= document.getElementById('userAvatarCircle');
  const userDisplayName = document.getElementById('userDisplayName');
  const userDropdownName= document.getElementById('userDropdownName');
  const userDropdownEmail=document.getElementById('userDropdownEmail');
  const logoutBtn       = document.getElementById('logoutBtn');
  const loginForm       = document.getElementById('loginForm');
  const registerForm    = document.getElementById('registerForm');
  const loginError      = document.getElementById('loginError');
  const registerError   = document.getElementById('registerError');
  const pwStrengthFill  = document.getElementById('pwStrengthFill');
  const pwStrengthLabel = document.getElementById('pwStrengthLabel');
  const regPassword     = document.getElementById('regPassword');

  /* ---- Backend availability ---- */
  const Api = window.VNPTApi || null;
  // true nếu đã dò được backend sống. Nếu chưa dò xong, chờ tối đa ~2.5s.
  async function backendReady() {
    if (!Api) return false;
    if (Api.available === null) {
      try { await Api.detect(); } catch (_e) { /* ignore */ }
    }
    return !!Api.available;
  }

  /* ---- Helpers ---- */
  function showToast(msg, isError) {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    if (toast && toastMsg) {
      toastMsg.textContent = msg;
      toast.style.background = isError ? '#E53E3E' : '';
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3500);
      return;
    }

    // Fallback toast UI nếu chưa có DOM #toast
    let dynamicToast = document.getElementById('vnptDynamicToast');
    if (!dynamicToast) {
      dynamicToast = document.createElement('div');
      dynamicToast.id = 'vnptDynamicToast';
      dynamicToast.style.cssText = 'position:fixed; bottom:28px; left:50%; transform:translateX(-50%) translateY(20px); z-index:9999999; background:#0F172A; color:#FFFFFF; padding:14px 24px; border-radius:14px; font-weight:600; font-size:14px; box-shadow:0 12px 35px rgba(0,0,0,0.35); display:flex; align-items:center; gap:10px; opacity:0; transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1); pointer-events:none; white-space:nowrap;';
      document.body.appendChild(dynamicToast);
    }
    const cleanMsg = String(msg || '').replace(/^(⚠️|✅|\s)+/u, '').trim();
    const icon = isError ? '⚠️ ' : '✅ ';
    dynamicToast.innerHTML = icon + cleanMsg;
    dynamicToast.style.opacity = '1';
    dynamicToast.style.transform = 'translateX(-50%) translateY(0)';
    clearTimeout(dynamicToast._timer);
    dynamicToast._timer = setTimeout(() => {
      dynamicToast.style.opacity = '0';
      dynamicToast.style.transform = 'translateX(-50%) translateY(20px)';
    }, 3500);
  }
  window.showToast = showToast;

  function setFieldError(inputId, errId, msg) {
    const input = document.getElementById(inputId);
    const err   = document.getElementById(errId);
    if (input) input.classList.toggle('error', !!msg);
    if (err)   err.textContent = msg || '';
  }

  function clearErrors(...errIds) {
    errIds.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = '';
    });
    document.querySelectorAll('.auth-input-wrap input.error').forEach(el => el.classList.remove('error'));
  }

  /* ---- localStorage helpers (dùng cho fallback OFFLINE + lưu user hiện tại) ---- */
  function getUsers() {
    try { return JSON.parse(localStorage.getItem('vnpt_users') || '[]'); }
    catch { return []; }
  }
  function saveUsers(users) {
    localStorage.setItem('vnpt_users', JSON.stringify(users));
  }
  function getCurrentUser() {
    try {
      const raw = localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || 'null';
      return JSON.parse(raw);
    }
    catch { return null; }
  }
  function setCurrentUser(user, remember) {
    localStorage.removeItem('vnpt_user');
    sessionStorage.removeItem('vnpt_user');
    if (!user) return;
    const store = remember ? localStorage : sessionStorage;
    store.setItem('vnpt_user', JSON.stringify(user));
    updateAuthUI();
  }

  /* ---- Role helpers ---- */
  function isAdmin() {
    const u = getCurrentUser();
    return !!(u && (u.role === 'admin' || u.role === 'quan_tri_vien'));
  }
  function getLoginModal() { return document.getElementById('loginModal'); }
  function getRegisterModal() { return document.getElementById('registerModal'); }
  function getModalOverlay() { return document.getElementById('modalOverlay'); }

  window.openLoginModal = () => openModal(getLoginModal());
  window.openRegisterModal = () => openModal(getRegisterModal());
  window.VNPTAuth = { getCurrentUser, setCurrentUser, isAdmin, getUsers, updateAuthUI, openLoginModal: window.openLoginModal, openRegisterModal: window.openRegisterModal };

  /* ---- Modal open/close ---- */
  function openModal(modal) {
    const modalEl = (typeof modal === 'string') ? document.getElementById(modal) : (modal || getLoginModal());
    const overlayEl = getModalOverlay();
    if (!modalEl) return;

    modalEl.style.removeProperty('display');
    modalEl.style.removeProperty('opacity');
    modalEl.style.removeProperty('visibility');
    modalEl.style.removeProperty('pointer-events');

    modalEl.style.setProperty('display', 'flex', 'important');
    modalEl.style.setProperty('opacity', '1', 'important');
    modalEl.style.setProperty('visibility', 'visible', 'important');
    modalEl.style.setProperty('pointer-events', 'auto', 'important');
    modalEl.classList.add('open');

    if (overlayEl) {
      overlayEl.style.removeProperty('display');
      overlayEl.style.removeProperty('opacity');
      overlayEl.style.removeProperty('visibility');
      overlayEl.style.removeProperty('pointer-events');

      overlayEl.style.setProperty('display', 'block', 'important');
      overlayEl.style.setProperty('opacity', '1', 'important');
      overlayEl.style.setProperty('visibility', 'visible', 'important');
      overlayEl.style.setProperty('pointer-events', 'auto', 'important');
      overlayEl.classList.add('active');
    }
    document.body.style.overflow = 'hidden';
  }

  function closeModal(modal) {
    const modalEl = (typeof modal === 'string') ? document.getElementById(modal) : modal;
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.removeProperty('display');
      modalEl.style.removeProperty('opacity');
      modalEl.style.removeProperty('visibility');
      modalEl.style.removeProperty('pointer-events');
      modalEl.style.display = 'none';
    }
    const overlayEl = getModalOverlay();
    const anyOpen = document.querySelector('.auth-modal.open, .modal.open, .modal-custom.open');
    if (!anyOpen) {
      if (overlayEl) {
        overlayEl.classList.remove('active');
        overlayEl.style.removeProperty('display');
        overlayEl.style.removeProperty('opacity');
        overlayEl.style.removeProperty('visibility');
        overlayEl.style.removeProperty('pointer-events');
        overlayEl.style.display = 'none';
      }
      document.body.style.overflow = '';
    }
  }

  function closeAllModals() {
    document.querySelectorAll('.auth-modal, .modal, .modal-custom, #checkoutModal, #paymentSuccessModal, #forgotModal, #productDetailModal, #reviewDetailModal, #customerReviewModal, #consultationModal, #supportCenterModal, #checkSupportModal, #customerNotifModal, #techDocsModal, #hero3DModal, #statDetailModal, #demoVideoModal, #footerPolicyModal, #footerBadgeModal, #solutionModal').forEach(m => {
      m.classList.remove('open');
      m.style.removeProperty('display');
      m.style.removeProperty('opacity');
      m.style.removeProperty('visibility');
      m.style.removeProperty('pointer-events');
      m.style.display = 'none';
    });
    const overlayEl = getModalOverlay();
    if (overlayEl) {
      overlayEl.classList.remove('active');
      overlayEl.style.removeProperty('display');
      overlayEl.style.removeProperty('opacity');
      overlayEl.style.removeProperty('visibility');
      overlayEl.style.removeProperty('pointer-events');
      overlayEl.style.display = 'none';
    }
    document.body.style.overflow = '';
  }

  window.openModal = openModal;
  window.closeModal = closeModal;
  window.closeAllModals = closeAllModals;

  /* ---- UI state: logged in / out ---- */
  function updateAuthUI() {
    const user = getCurrentUser();
    if (user && (user.trang_thai === 'khoa' || user.status === 'khoa' || user.is_locked)) {
      logout();
      alert('⚠️ Tài khoản của bạn đã bị Quản trị viên khóa. Hệ thống tự động đăng xuất!');
      return;
    }
    if (user) {
      authBtns && (authBtns.style.display = 'none');
      userMenu && (userMenu.style.display = 'flex');
      const initials = ((user.firstName || user.ho_ten || '')[0] + (user.lastName || '')[0]).toUpperCase() || 'U';
      const avatarUrl = user.hinh_anh_url || user.avatar || '';
      const userAvatarImg = document.getElementById('userAvatarImg');
      if (avatarUrl && userAvatarImg) {
        let finalSrc = (avatarUrl.startsWith('http') || avatarUrl.startsWith('data:')) ? avatarUrl : avatarUrl.replace(/^(\.\.\/|\/)+/, '');
        const cacheBustSrc = finalSrc + (finalSrc.includes('?') ? '&' : '?') + 't=' + Date.now();
        userAvatarImg.src = cacheBustSrc;
        userAvatarImg.style.display = 'inline-block';
        if (userAvatarCircle) userAvatarCircle.style.display = 'none';

        userAvatarImg.onerror = function() {
          if (!this.dataset.retried) {
            this.dataset.retried = '1';
            this.src = finalSrc;
          } else {
            this.style.display = 'none';
            if (userAvatarCircle) {
              userAvatarCircle.style.display = 'flex';
              userAvatarCircle.textContent = initials;
            }
          }
        };
      } else {
        if (userAvatarImg) userAvatarImg.style.display = 'none';
        if (userAvatarCircle) {
          userAvatarCircle.style.display = 'flex';
          userAvatarCircle.textContent = initials;
        }
      }
      const fullName = `${user.firstName || ''} ${user.lastName || ''}`.trim() || user.ho_ten || user.email;

      const email = (user.email || '').toLowerCase();
      const rawRole = user.role || user.ten_vai_tro || '';
      const isStaff = (user.loai_tai_khoan === 'nhan_vien') || 
                      email.endsWith('@vnpt.vn') || email.includes('admin') || email.includes('editor') || email.includes('manager') ||
                      ['admin', 'quan_tri_vien', 'bien_tap_vien', 'nhan_vien_ban_hang', 'quan_ly', 'editor', 'staff'].includes(rawRole);

      let roleLabel = 'Khách hàng';
      if (rawRole === 'admin' || rawRole === 'quan_tri_vien' || email.includes('admin')) {
        roleLabel = 'Quản trị viên';
      } else if (rawRole === 'bien_tap_vien' || rawRole === 'editor' || email.includes('editor')) {
        roleLabel = 'Biên tập viên';
      } else if (rawRole === 'nhan_vien_ban_hang' || rawRole === 'quan_ly' || email.includes('manager') || email.includes('sales')) {
        roleLabel = 'NV Bán hàng';
      } else if (isStaff) {
        roleLabel = 'Nhân viên';
      }

      if (userDisplayName) userDisplayName.textContent = fullName;
      if (userDropdownName) {
        userDropdownName.innerHTML = `${fullName} <span class="role-badge ${isStaff ? 'admin' : 'customer'}">${roleLabel}</span>`;
      }
      if (userDropdownEmail) userDropdownEmail.textContent = user.email;

      document.body.classList.toggle('is-admin', isStaff);

      const adminBtn = document.getElementById('openAdminBtn');
      if (adminBtn) {
        adminBtn.style.setProperty('display', isStaff ? 'flex' : 'none', 'important');
      }

      // Ẩn nút Giỏ hàng & Đơn hàng của tôi đối với tài khoản Nhân viên / Quản trị viên
      const cartToggle = document.getElementById('cartToggle');
      if (cartToggle) {
        cartToggle.style.display = isStaff ? 'none' : 'inline-flex';
      }

      const ordersItem = document.getElementById('userOrdersDropdownItem');
      if (ordersItem) {
        ordersItem.style.display = isStaff ? 'none' : 'flex';
      }
    } else {
      authBtns && (authBtns.style.display = 'flex');
      userMenu && (userMenu.style.display = 'none');
      document.body.classList.remove('is-admin');

      // Khôi phục trạng thái Giỏ hàng & Đơn hàng của tôi khi Đăng xuất (Hoặc chưa đăng nhập)
      const cartToggle = document.getElementById('cartToggle');
      if (cartToggle) {
        cartToggle.style.display = 'inline-flex';
      }
      const ordersItem = document.getElementById('userOrdersDropdownItem');
      if (ordersItem) {
        ordersItem.style.display = 'flex';
      }
    }
    document.dispatchEvent(new CustomEvent('vnpt:authchange'));
    if (window.lucide) lucide.createIcons();
    if (typeof window.loadTestimonialsFromApi === 'function') {
      window.loadTestimonialsFromApi();
    }
  }

  /* ---- Password strength ---- */
  function checkPasswordStrength(pw) {
    let score = 0;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    const levels = [
      { label: 'Rất yếu',  color: '#E53E3E', pct: '20%' },
      { label: 'Yếu',      color: '#ED8936', pct: '40%' },
      { label: 'Trung bình',color: '#ECC94B', pct: '60%' },
      { label: 'Mạnh',     color: '#48BB78', pct: '80%' },
      { label: 'Rất mạnh', color: '#38A169', pct: '100%' },
    ];
    const lvl = levels[Math.min(score, 4)];
    if (pwStrengthFill) {
      pwStrengthFill.style.width      = pw.length ? lvl.pct : '0%';
      pwStrengthFill.style.background = lvl.color;
    }
    if (pwStrengthLabel) {
      pwStrengthLabel.textContent = pw.length ? lvl.label : 'Độ mạnh mật khẩu';
      pwStrengthLabel.style.color = pw.length ? lvl.color : '';
    }
    return score;
  }

  /* ---- Toggle password visibility (Event Delegation) ---- */
  document.addEventListener('click', (e) => {
    const toggleBtn = e.target.closest('.toggle-pw');
    if (toggleBtn) {
      e.preventDefault();
      const targetId = toggleBtn.dataset.target || toggleBtn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (!input) return;
      const isText = input.type === 'text';
      input.type = isText ? 'password' : 'text';
      toggleBtn.innerHTML = isText
        ? '<i class="fa-solid fa-eye text-muted" style="font-size:13px;"></i>'
        : '<i class="fa-solid fa-eye-slash text-muted" style="font-size:13px;"></i>';
    }
  });

  /* ---- Login form ---- */
  loginForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors('loginEmailErr', 'loginPasswordErr');
    if (loginError) loginError.style.display = 'none';

    const email    = document.getElementById('loginEmail')?.value.trim();
    const password = document.getElementById('loginPassword')?.value;
    let valid = true;

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setFieldError('loginEmail', 'loginEmailErr', 'Email không hợp lệ');
      valid = false;
    }
    if (!password || password.length < 6) {
      setFieldError('loginPassword', 'loginPasswordErr', 'Mật khẩu tối thiểu 6 ký tự');
      valid = false;
    }
    if (!valid) return;

    const submitBtn = document.getElementById('loginSubmit');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.classList.add('loading'); }

    const finish = () => { if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('loading'); } };
    const failLogin = (msg) => {
      finish();
      if (loginError) { loginError.textContent = msg; loginError.style.display = 'block'; }
    };

    const remember = !!document.getElementById('rememberMe')?.checked;

    try {
      // Đăng nhập 100% thuần CSDL MySQL
      const loginUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/login.php') : 'backend/api/login.php';
      const res = await fetch(loginUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      const data = await res.json();

      if (res.ok && data.status === 'success' && data.user) {
        Api.setToken(data.token, remember);
        setCurrentUser(data.user, remember);
        finish();
        updateAuthUI();
        closeAllModals();
        loginForm.reset();
        showToast(`Chào mừng trở lại, ${data.user.firstName || data.user.ho_ten || data.user.email}! 👋`);
        if (window.VNPTCart && typeof window.VNPTCart.fetchCart === 'function') {
          window.VNPTCart.fetchCart();
        }
        return;
      }

      failLogin(data?.error || 'Email hoặc mật khẩu không đúng. Vui lòng kiểm tra lại.');
      return;
    } catch (err) {
      failLogin('Lỗi kết nối tới hệ thống cơ sở dữ liệu. Vui lòng thử lại sau.');
      return;
    }
  });

  /* ---- Register form ---- */
  registerForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors('regFirstNameErr','regLastNameErr','regEmailErr','regPhoneErr','regPasswordErr','regConfirmPasswordErr','agreeTermsErr');
    if (registerError) registerError.style.display = 'none';

    const firstName = document.getElementById('regFirstName')?.value.trim();
    const lastName  = document.getElementById('regLastName')?.value.trim();
    const email     = document.getElementById('regEmail')?.value.trim();
    const phone     = document.getElementById('regPhone')?.value.trim();
    const password  = document.getElementById('regPassword')?.value;
    const confirm   = document.getElementById('regConfirmPassword')?.value;
    const agreed    = document.getElementById('agreeTerms')?.checked;
    let valid = true;

    if (!firstName) { setFieldError('regFirstName','regFirstNameErr','Vui lòng nhập họ'); valid = false; }
    if (!lastName)  { setFieldError('regLastName','regLastNameErr','Vui lòng nhập tên'); valid = false; }
    if (!email || !/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
      setFieldError('regEmail','regEmailErr','Địa chỉ Email không hợp lệ (ví dụ: tenban@gmail.com)'); valid = false;
    }
    if (phone && !/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/.test(phone.replace(/\s/g,''))) {
      setFieldError('regPhone','regPhoneErr','Số điện thoại không hợp lệ (10 số, bắt đầu 03,05,07,08,09)'); valid = false;
    }
    if (!password || password.length < 8) {
      setFieldError('regPassword','regPasswordErr','Mật khẩu tối thiểu 8 ký tự'); valid = false;
    }
    if (password !== confirm) {
      setFieldError('regConfirmPassword','regConfirmPasswordErr','Mật khẩu xác nhận không khớp'); valid = false;
    }
    if (!agreed) {
      const err = document.getElementById('agreeTermsErr');
      if (err) err.textContent = 'Bạn cần đồng ý với điều khoản dịch vụ';
      valid = false;
    }
    if (!valid) return;

    const submitBtn = document.getElementById('registerSubmit');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.classList.add('loading'); }
    const finish = () => { if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('loading'); } };
    const failReg = (msg) => {
      finish();
      if (registerError) { registerError.textContent = msg; registerError.style.display = 'block'; }
    };
    const succeed = (user, token) => {
      finish();
      // Đăng ký xong mặc định "ghi nhớ" luôn — người dùng vừa tạo tài khoản mới,
      // không có lý do gì bắt họ đăng nhập lại ngay khi đóng trình duyệt.
      if (token && Api) Api.setToken(token, true);
      setCurrentUser(user, true);
      updateAuthUI();
      closeAllModals();
      registerForm.reset();
      checkPasswordStrength('');
      if (regConfirmMatch) { regConfirmMatch.textContent = ''; regConfirmMatch.className = 'field-match'; }
      showToast(`Đăng ký thành công! Chào mừng ${user.firstName} đến với VNPT 🎉`);
      if (window.VNPTCart && typeof window.VNPTCart.fetchCart === 'function') {
        window.VNPTCart.fetchCart();
      }
    };

    try {
      // Đăng ký trực tiếp vào CSDL MySQL qua PHP API
      const regUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/register.php') : 'backend/api/register.php';
      const res = await fetch(regUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ firstName, lastName, email, phone, password })
      });
      const data = await res.json();

      if (res.ok && data.status === 'success' && data.user) {
        succeed(data.user, data.token);
        return;
      }

      failReg(data?.error || 'Đăng ký không thành công. Vui lòng thử lại.');
      return;
    } catch (err) {
      failReg('Lỗi kết nối tới hệ thống cơ sở dữ liệu. Vui lòng thử lại sau.');
      return;
    }
  });

  /* ---- Password strength live ---- */
  regPassword?.addEventListener('input', () => checkPasswordStrength(regPassword.value));

  /* ---- Xác nhận mật khẩu: kiểm tra khớp theo thời gian thực ---- */
  const regConfirmInput = document.getElementById('regConfirmPassword');
  const regConfirmMatch = document.getElementById('regConfirmMatch');
  function checkConfirmMatch() {
    if (!regConfirmInput || !regConfirmMatch) return;
    const pw = regPassword?.value || '';
    const confirm = regConfirmInput.value;
    if (!confirm) { regConfirmMatch.textContent = ''; regConfirmMatch.className = 'field-match'; return; }
    if (pw === confirm) {
      regConfirmMatch.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L8.3 11.6l6.3-6.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg> Mật khẩu khớp';
      regConfirmMatch.className = 'field-match match';
    } else {
      regConfirmMatch.textContent = 'Mật khẩu chưa khớp';
      regConfirmMatch.className = 'field-match mismatch';
    }
  }
  regPassword?.addEventListener('input', checkConfirmMatch);
  regConfirmInput?.addEventListener('input', checkConfirmMatch);

  /* ---- Logout ---- */
  logoutBtn?.addEventListener('click', () => {
    setCurrentUser(null);
    if (Api) Api.setToken('');
    try {
      localStorage.removeItem('vnpt_cart');
      sessionStorage.removeItem('vnpt_cart');
    } catch (_e) {}
    if (window.VNPTCart && typeof window.VNPTCart.resetCart === 'function') {
      window.VNPTCart.resetCart();
    }
    updateAuthUI();
    userMenu?.classList.remove('open');
    showToast('Đã đăng xuất thành công');
  });

  /* ---- User dropdown toggle ---- */
  userAvatarBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    userMenu?.classList.toggle('open');
  });
  document.addEventListener('click', (e) => {
    if (userMenu && !userMenu.contains(e.target)) userMenu.classList.remove('open');

    const closeBtn = e.target.closest('#closeLogin, #closeRegister, #closeForgot, .auth-modal-close, [aria-label="Đóng"]');
    if (closeBtn) {
      e.preventDefault();
      closeAllModals();
      return;
    }

    const overlay = e.target.closest('#modalOverlay, .modal-overlay');
    if (overlay && (e.target === overlay || e.target.id === 'modalOverlay')) {
      e.preventDefault();
      closeAllModals();
      return;
    }

    const btnLog = e.target.closest('#openLogin, .btn-login, [data-action="login"]');
    if (btnLog) {
      e.preventDefault();
      openModal(getLoginModal());
      return;
    }

    const btnReg = e.target.closest('#openRegister, .btn-register, [data-action="register"]');
    if (btnReg) {
      e.preventDefault();
      openModal(getRegisterModal());
      return;
    }
  });

  /* ---- Modal triggers ---- */
  document.getElementById('openLogin')?.addEventListener('click',    (e) => { e.preventDefault(); openModal(getLoginModal()); });
  document.getElementById('openRegister')?.addEventListener('click', (e) => { e.preventDefault(); openModal(getRegisterModal()); });
  document.getElementById('closeLogin')?.addEventListener('click',   () => closeModal(getLoginModal()));
  document.getElementById('closeRegister')?.addEventListener('click',() => closeModal(getRegisterModal()));
  document.getElementById('modalOverlay')?.addEventListener('click', closeAllModals);

  const openAdminBtnEl = document.getElementById('openAdminBtn');
  openAdminBtnEl?.addEventListener('click', (e) => {
    e.preventDefault();
    const u = getCurrentUser();
    const email = u ? (u.email || '') : '';
    window.location.href = 'admin_panel/index.php' + (email ? '?user_email=' + encodeURIComponent(email) : '');
  });

  switchToReg?.addEventListener('click', (e) => {
    e.preventDefault();
    closeModal(loginModal);
    setTimeout(() => openModal(registerModal), 150);
  });
  switchToLog?.addEventListener('click', (e) => {
    e.preventDefault();
    closeModal(registerModal);
    setTimeout(() => openModal(loginModal), 150);
  });

  /* ============================================================
   * REAL GOOGLE & FACEBOOK OAUTH INTEGRATION (MYSQL & NODE.JS API)
   * ============================================================ */

  async function handleSocialSuccess(token, user, providerLabel, rawProfileUrl = null) {
    if (Api && typeof Api.setToken === 'function') Api.setToken(token, true);
    
    // Đè ảnh avatar xịn từ MXH vào đối tượng user
    if (rawProfileUrl) {
      user.avatar = rawProfileUrl;
      user.hinh_anh_url = rawProfileUrl;
      user.provider = providerLabel.toLowerCase();
    }
    
    setCurrentUser(user, true);
    localStorage.setItem('vnpt_login_signal', Date.now().toString());
    
    updateAuthUI();
    closeAllModals();
    showToast(`Chào mừng ${user.firstName || user.email || 'bạn'} đã đăng nhập bằng ${providerLabel}! 🎉`);
    if (window.VNPTCart && typeof window.VNPTCart.fetchCart === 'function') {
      window.VNPTCart.fetchCart();
    }
  }

  // API Đẩy thông tin sang Backend PHP MySQL (hoặc Node.js API)
  async function sendSocialAuthToBackend(payload, providerName) {
    try {
      const phpEndpoint = 'backend/api/social_login.php';
      const nodeEndpoint = (typeof window.VNPTApi !== 'undefined' && window.VNPTApi.baseUrl) 
                           ? `${window.VNPTApi.baseUrl}/auth/social`
                           : 'http://127.0.0.1:3000/api/auth/social';

      let targetUrl = phpEndpoint;
      if (Api && typeof Api.available === 'boolean' && Api.available && window.VNPTApi.baseUrl) {
        targetUrl = nodeEndpoint;
      }

      const res = await fetch(targetUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      
      const data = await res.json();
      
      if (res.ok && (data.status === 'success' || data.success)) {
        const userObj = data.user || data;
        const tokenStr = data.token || ('token_social_' + Date.now());
        await handleSocialSuccess(tokenStr, userObj, providerName, payload.avatar);
        return true;
      } else {
        throw new Error(data.error || data.message || 'Lỗi kết nối máy chủ.');
      }
    } catch (err) {
      console.warn(`Lỗi API ${providerName}, sử dụng fallback phiên local:`, err);
      const fallbackUser = {
        id: payload.providerId,
        firstName: payload.firstName,
        lastName: payload.lastName,
        email: payload.email,
        phone: '',
        role: 'customer',
        provider: payload.provider,
        avatar: payload.avatar,
        hinh_anh_url: payload.avatar
      };
      await handleSocialSuccess('token_fallback_' + Date.now(), fallbackUser, providerName, payload.avatar);
      return false;
    }
  }

  // --- GOOGLE LOGIN ---
  let googleClient;
  function initGoogleOAuth() {
    if (window.google && window.google.accounts && window.google.accounts.oauth2) {
      try {
        googleClient = google.accounts.oauth2.initTokenClient({
          client_id: GOOGLE_CLIENT_ID,
          scope: 'email profile',
          callback: async (response) => {
            if (response.error) {
              showToast('Đăng nhập Google bị hủy hoặc thất bại.', true);
              return;
            }
            showToast('Đang kết nối hệ thống Google...', false);
            try {
              // Lấy ảnh và thông tin chi tiết từ Google API
              const res = await fetch('https://www.googleapis.com/oauth2/v3/userinfo', {
                headers: { Authorization: `Bearer ${response.access_token}` }
              });
              const profile = await res.json();
              const avatarUrl = profile.picture || '';

              const payload = {
                provider: 'google',
                email: profile.email,
                firstName: profile.given_name || profile.name || 'Google',
                lastName: profile.family_name || 'User',
                providerId: profile.sub || ('google_' + Date.now()),
                avatar: avatarUrl
              };
              await sendSocialAuthToBackend(payload, 'Google');
            } catch (err) {
              showToast('Lỗi lấy thông tin Google: ' + err.message, true);
            }
          },
        });
      } catch (_e) {}
    }
  }

  function triggerGoogleLogin(e) {
    if (e && e.preventDefault) e.preventDefault();
    if (googleClient) {
      try {
        googleClient.requestAccessToken();
        return;
      } catch (_err) {}
    }
    const googleAuthUrl = `https://accounts.google.com/signin/v2/identifier?flowName=GlifWebSignIn`;
    window.open(googleAuthUrl, 'GoogleAuthWindow', 'width=520,height=630,top=100,left=100');
  }

  // --- FACEBOOK LOGIN ---
  window.fbAsyncInit = function() {
    if (window.FB) {
      try {
        window.FB.init({
          appId: FB_APP_ID,
          cookie: true,
          xfbml: true,
          version: 'v18.0'
        });
      } catch (_e) {}
    }
  };

  function triggerFacebookLogin(e) {
    if (e && e.preventDefault) e.preventDefault();
    if (window.FB && typeof window.FB.login === 'function') {
      showToast('Đang kết nối Facebook...', false);
      try {
        FB.login(function(response) {
          if (response && response.authResponse) {
             try {
                 FB.api('/me', { fields: 'id, name, first_name, last_name, email, picture.type(large)' }, function(profile) {
                     const fbAvatarUrl = (profile && profile.picture && profile.picture.data && profile.picture.data.url) || '';
                     const payload = {
                        provider: 'facebook',
                        email: (profile && profile.email) || (`fb_${profile.id}@facebook.com`),
                        firstName: (profile && profile.first_name) || (profile && profile.name) || 'Facebook',
                        lastName: (profile && profile.last_name) || 'User',
                        providerId: (profile && profile.id) || ('fb_' + Date.now()),
                        avatar: fbAvatarUrl
                     };
                     sendSocialAuthToBackend(payload, 'Facebook');
                 });
             } catch (err) {
                 showToast('Lỗi xử lý Facebook: ' + err.message, true);
             }
          } else {
            showToast('Đăng nhập Facebook bị hủy.', true);
          }
        }, { scope: 'public_profile,email' });
        return;
      } catch (_e) {}
    }
    const fbAuthUrl = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${encodeURIComponent(FB_APP_ID)}&redirect_uri=${encodeURIComponent(window.location.origin + window.location.pathname.split('#')[0])}&scope=email,public_profile`;
    window.open(fbAuthUrl, 'FBAuthWindow', 'width=600,height=650,top=100,left=100');
  }

  // Hiển thị Modal Đăng nhập Xã hội chuyên nghiệp
  function openSocialAccountModal(provider) {
    const socialModal = document.getElementById('socialAccountModal');
    const modalTitle = document.getElementById('socialModalTitle');
    const modalIcon = document.getElementById('socialModalIcon');
    const btnText = document.getElementById('socialBtnText');
    const submitBtn = document.getElementById('submitSocialBtn');
    const providerInput = document.getElementById('socialProvider');
    const emailInput = document.getElementById('socialEmail');
    const firstNameInput = document.getElementById('socialFirstName');
    const lastNameInput = document.getElementById('socialLastName');

    if (!socialModal) return;

    const isGoogle = (provider === 'google');
    const providerName = isGoogle ? 'Google' : 'Facebook';

    if (providerInput) providerInput.value = provider;
    if (modalTitle) modalTitle.textContent = `Đăng nhập với ${providerName}`;
    if (btnText) btnText.textContent = `Xác nhận Đăng nhập ${providerName}`;

    if (modalIcon) {
      if (isGoogle) {
        modalIcon.style.background = '#EBF8FF';
        modalIcon.innerHTML = `<svg width="28" height="28" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/></svg>`;
      } else {
        modalIcon.style.background = '#E8F0FE';
        modalIcon.innerHTML = `<svg width="28" height="28" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>`;
      }
    }

    if (submitBtn) {
      submitBtn.style.background = isGoogle ? '#0066CC' : '#1877F2';
    }

    if (emailInput) emailInput.value = isGoogle ? 'user.google@gmail.com' : 'user.facebook@facebook.com';
    if (firstNameInput) firstNameInput.value = 'Nguyễn';
    if (lastNameInput) lastNameInput.value = isGoogle ? 'Google' : 'Facebook';

    closeAllModals();
    socialModal.style.display = 'flex';
    socialModal.classList.add('open');
    if (modalOverlay) modalOverlay.classList.add('active');
  }

  // Đóng Modal Social
  document.getElementById('closeSocialModal')?.addEventListener('click', () => {
    const socialModal = document.getElementById('socialAccountModal');
    if (socialModal) {
      socialModal.style.display = 'none';
      socialModal.classList.remove('open');
    }
    if (modalOverlay) modalOverlay.classList.remove('active');
  });

  // Submit Form Social Account
  document.getElementById('socialAccountForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const provider = document.getElementById('socialProvider')?.value || 'google';
    const email = document.getElementById('socialEmail')?.value.trim();
    const firstName = document.getElementById('socialFirstName')?.value.trim();
    const lastName = document.getElementById('socialLastName')?.value.trim();
    const providerName = provider === 'google' ? 'Google' : 'Facebook';

    if (!email) return;

    const payload = {
      provider: provider,
      email: email,
      firstName: firstName || 'Khách hàng',
      lastName: lastName || providerName,
      providerId: (provider === 'google' ? 'goog_' : 'fb_') + Date.now()
    };

    const success = await sendSocialAuthToBackend(payload, providerName);
    if (success) {
      const socialModal = document.getElementById('socialAccountModal');
      if (socialModal) {
        socialModal.style.display = 'none';
        socialModal.classList.remove('open');
      }
      if (modalOverlay) modalOverlay.classList.remove('active');
    }
  });

  // Hộp thoại nhập Email thật khi ứng dụng chạy môi trường localhost
  function promptFallbackSocial(provider) {
    openSocialAccountModal(provider);
  }

  // Gắn sự kiện cho các nút bấm Google và Facebook
  document.getElementById('loginGoogle')?.addEventListener('click', triggerGoogleLogin);
  document.getElementById('loginFacebook')?.addEventListener('click', triggerFacebookLogin);
  document.getElementById('registerGoogle')?.addEventListener('click', triggerGoogleLogin);
  document.getElementById('registerFacebook')?.addEventListener('click', triggerFacebookLogin);

  setTimeout(initGoogleOAuth, 1000);



  /* ---- Keyboard: Escape closes modals ---- */
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAllModals();
  });

  let sessionRestored = false;
  /* ---- Khôi phục phiên đăng nhập từ token (ONLINE) ---- */
  async function restoreSession() {
    const urlParams = new URLSearchParams(window.location.search);
    const isLoggedOut = urlParams.has('logged_out') || window.location.hash.includes('logged_out') || localStorage.getItem('vnpt_force_logout') === '1';

    if (isLoggedOut) {
      if (Api && typeof Api.setToken === 'function') Api.setToken('');
      localStorage.removeItem('vnpt_user');
      localStorage.removeItem('vnpt_token');
      localStorage.removeItem('vnpt_force_logout');
      sessionStorage.removeItem('vnpt_user');
      sessionStorage.removeItem('vnpt_token');
      setCurrentUser(null);
      if (window.history && window.history.replaceState) {
        window.history.replaceState({}, document.title, window.location.pathname);
      }
      updateAuthUI();
      return;
    }

    if (sessionRestored) return;
    sessionRestored = true;

    // Nếu KHÔNG tích "Ghi nhớ đăng nhập" (không có token lưu trong localStorage), tự động đăng xuất khi load lại trang
    if (Api && typeof Api.remembered === 'function' && !Api.remembered()) {
      if (Api && typeof Api.setToken === 'function') Api.setToken('');
      localStorage.removeItem('vnpt_user');
      localStorage.removeItem('vnpt_token');
      sessionStorage.removeItem('vnpt_user');
      sessionStorage.removeItem('vnpt_token');
      setCurrentUser(null);
      updateAuthUI();
      return;
    }

    const curUser = getCurrentUser();

    if (await backendReady() && Api.getToken()) {
      const isSocialUser = curUser && (curUser.provider === 'google' || curUser.provider === 'facebook' || (curUser.id && String(curUser.id).includes('goog')));
      if (!isSocialUser) {
        try {
          const { user } = await Api.me();
          setCurrentUser(user, Api.remembered());
        } catch (_e) {
          if (!curUser) {
            Api.setToken('');
            setCurrentUser(null);
          }
        }
      }
    }

    const latestUser = getCurrentUser();
    if (latestUser && latestUser.email) {
      try {
        const res = await fetch('backend/api/update_profile.php?action=get&email=' + encodeURIComponent(latestUser.email));
        const data = await res.json();
        if (data.status === 'success' && data.user) {
          const merged = { ...latestUser, ...data.user };
          setCurrentUser(merged, true);
        }
      } catch (_e) {}
    }

    updateAuthUI();
  }

  /* ---- Forgot Password Modal Logic ---- */
  const forgotModal = document.getElementById('forgotModal');
  const closeForgotBtn = document.getElementById('closeForgot');
  const forgotLink = document.getElementById('forgotLink');
  const switchToLoginFromForgot = document.getElementById('switchToLoginFromForgot');

  const fgStep1 = document.getElementById('fgStep1');
  const fgStep2 = document.getElementById('fgStep2');
  const fgStep3 = document.getElementById('fgStep3');

  const fgError1 = document.getElementById('fgError1');
  const fgError2 = document.getElementById('fgError2');
  const fgError3 = document.getElementById('fgError3');

  let currentFgEmail = '';
  let currentFgOtp = '';

  function openForgotModal() {
    clearErrors('loginEmailErr', 'loginPasswordErr');
    const existingEmail = document.getElementById('loginEmail')?.value.trim() || '';
    closeModal(loginModal);
    closeModal(registerModal);
    if (forgotModal) {
      openModal(forgotModal);
      resetForgotForm();
      if (existingEmail && document.getElementById('fgEmail')) {
        document.getElementById('fgEmail').value = existingEmail;
      }
    }
  }

  function resetForgotForm() {
    if (fgStep1) fgStep1.style.display = 'block';
    if (fgStep2) fgStep2.style.display = 'none';
    if (fgStep3) fgStep3.style.display = 'none';
    if (fgError1) fgError1.style.display = 'none';
    if (fgError2) fgError2.style.display = 'none';
    if (fgError3) fgError3.style.display = 'none';
    currentFgEmail = '';
    currentFgOtp = '';
  }

  if (forgotLink) {
    forgotLink.addEventListener('click', (e) => {
      e.preventDefault();
      openForgotModal();
    });
  }

  if (closeForgotBtn) {
    closeForgotBtn.addEventListener('click', () => {
      closeModal(forgotModal);
    });
  }

  if (switchToLoginFromForgot) {
    switchToLoginFromForgot.addEventListener('click', (e) => {
      e.preventDefault();
      closeModal(forgotModal);
      setTimeout(() => openModal(loginModal), 150);
    });
  }

  // Bước 1: Gửi OTP
  const forgotSendForm = document.getElementById('forgotSendForm');
  if (forgotSendForm) {
    forgotSendForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('fgEmail').value.trim();
      if (!email) return;

      const btn = document.getElementById('fgSendBtn');
      btn.disabled = true;
      btn.innerHTML = '<span>Đang gửi mã...</span>';

      try {
        const res = await fetch('backend/api/forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'send_otp', email: email, type: 'frontend' })
        });
        const data = await res.json();

        if (data.success) {
          currentFgEmail = email;
          fgStep1.style.display = 'none';
          fgStep2.style.display = 'block';
          document.getElementById('fgOtp').value = '';
          const notice = document.getElementById('fgLocalNotice');
          if (notice && data.otp) {
            notice.innerHTML = `⚡ <strong>Mã OTP xác thực của bạn:</strong> <span style="font-size:16px; font-weight:700; color:#0284c7; letter-spacing:2px;">${data.otp}</span>`;
            notice.style.display = 'block';
          }
          showToast(`Đã gửi mã OTP cho ${email}!`);
        } else {
          fgError1.textContent = data.message;
          fgError1.style.display = 'block';
        }
      } catch (err) {
        fgError1.textContent = 'Lỗi kết nối: ' + err.message;
        fgError1.style.display = 'block';
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Gửi mã OTP xác thực</span><i data-lucide="send"></i>';
        if (window.lucide) lucide.createIcons();
      }
    });
  }

  // Bước 2: Xác thực OTP
  const forgotVerifyForm = document.getElementById('forgotVerifyForm');
  if (forgotVerifyForm) {
    forgotVerifyForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const otp = document.getElementById('fgOtp').value.trim();
      if (!otp) return;

      const btn = document.getElementById('fgVerifyBtn');
      btn.disabled = true;
      btn.innerHTML = '<span>Đang xác nhận...</span>';

      try {
        const res = await fetch('backend/api/forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'verify_otp', email: currentFgEmail, otp: otp })
        });
        const data = await res.json();

        if (data.success) {
          currentFgOtp = otp;
          fgStep2.style.display = 'none';
          fgStep3.style.display = 'block';
        } else {
          fgError2.textContent = data.message;
          fgError2.style.display = 'block';
        }
      } catch (err) {
        fgError2.textContent = 'Lỗi kết nối: ' + err.message;
        fgError2.style.display = 'block';
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Xác nhận mã OTP</span><i data-lucide="check-circle"></i>';
        if (window.lucide) lucide.createIcons();
      }
    });
  }

  // Bước 3: Đổi mật khẩu
  const forgotResetForm = document.getElementById('forgotResetForm');
  if (forgotResetForm) {
    forgotResetForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const newPw = document.getElementById('fgNewPassword').value;
      const confirmPw = document.getElementById('fgConfirmPassword').value;

      if (newPw !== confirmPw) {
        fgError3.textContent = 'Mật khẩu xác nhận không trùng khớp.';
        fgError3.style.display = 'block';
        return;
      }

      const btn = document.getElementById('fgResetBtn');
      btn.disabled = true;
      btn.innerHTML = '<span>Đang lưu mật khẩu...</span>';

      try {
        const res = await fetch('backend/api/forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'reset_password',
            email: currentFgEmail,
            otp: currentFgOtp,
            new_password: newPw
          })
        });
        const data = await res.json();

        if (data.success) {
          alert(data.message);
          closeModal(forgotModal);
          setTimeout(() => openModal(loginModal), 150);
        } else {
          fgError3.textContent = data.message;
          fgError3.style.display = 'block';
        }
      } catch (err) {
        fgError3.textContent = 'Lỗi kết nối: ' + err.message;
        fgError3.style.display = 'block';
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Lưu mật khẩu mới</span><i data-lucide="check-square"></i>';
        if (window.lucide) lucide.createIcons();
      }
    });
  }

  /* ---- Init ---- */
  document.addEventListener('DOMContentLoaded', () => {
    restoreSession();
    if (window.lucide) lucide.createIcons();
  });
  if (document.readyState !== 'loading') restoreSession();

})();
