/**
 * VNPT — Account Module v2 (Xử lý triệt để Hồ sơ cá nhân, Đơn hàng & Cài đặt)
 */
(function () {
  'use strict';

  const Api = window.VNPTApi || null;

  async function backendReady() {
    if (!Api) return false;
    if (Api.available === null) {
      try { await Api.detect(); } catch (_e) { /* ignore */ }
    }
    return !!Api.available;
  }

  function getUser() {
    return window.VNPTAuth ? window.VNPTAuth.getCurrentUser() : null;
  }

  function getFirstName(user) {
    if (!user) return '';
    if (user.firstName) return user.firstName;
    if (user.ho_ten) {
      const parts = user.ho_ten.trim().split(/\s+/);
      if (parts.length > 1) {
        return parts.slice(0, -1).join(' ');
      }
      return parts[0] || '';
    }
    return '';
  }

  function getLastName(user) {
    if (!user) return '';
    if (user.lastName) return user.lastName;
    if (user.ho_ten) {
      const parts = user.ho_ten.trim().split(/\s+/);
      if (parts.length > 1) {
        return parts[parts.length - 1] || '';
      }
    }
    return '';
  }

  function getPhone(user) {
    if (!user) return '';
    return user.phone || user.so_dien_thoai || user.sdt || '';
  }

  function initialsOf(user) {
    if (!user) return 'U';
    const first = getFirstName(user);
    const last = getLastName(user);
    const init = ((first[0] || '') + (last[0] || '')).toUpperCase();
    return init || (user.email ? user.email[0].toUpperCase() : 'U');
  }

  function fullNameOf(user) {
    if (!user) return 'Người dùng';
    const combined = `${getFirstName(user)} ${getLastName(user)}`.trim();
    return combined || user.ho_ten || user.email || 'Người dùng';
  }

  function fmtMoney(n) {
    return new Intl.NumberFormat('vi-VN').format(Number(n) || 0) + ' ₫';
  }

  function fmtDate(d) {
    try { return new Date(d).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }); }
    catch { return d || ''; }
  }

  const STATUS_LABELS = {
    cho_xac_nhan: 'Chờ xác nhận',
    da_xac_nhan: 'Đã xác nhận',
    dang_giao: 'Đang giao',
    hoan_thanh: 'Hoàn thành',
    da_huy: 'Đã hủy',
  };

  /* ------------------------------------------------------------
   * Sidebar dùng chung cho cả 3 trang tài khoản
   * ------------------------------------------------------------ */
  function sideNavHtml(active, user) {
    const items = [
      { key: 'profile', icon: 'user', label: 'Hồ sơ cá nhân' },
      { key: 'orders', icon: 'package', label: 'Đơn hàng của tôi' },
      { key: 'settings', icon: 'settings', label: 'Cài đặt' },
    ];
    const avatarPath = user.hinh_anh_url || user.avatar || '';
    const avatarSrc = avatarPath ? (avatarPath.startsWith('http') || avatarPath.startsWith('data:') ? avatarPath : avatarPath.replace(/^(\.\.\/|\/)+/, '')) : '';

    return `
      <div class="acct-side">
        <div class="acct-side-user">
          <div class="acct-side-avatar">
            ${avatarSrc ? `<img src="${avatarSrc}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Avatar" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">` : ''}
            <span style="display:${avatarSrc ? 'none' : 'flex'}; width:100%; height:100%; border-radius:50%; background:linear-gradient(135deg,#0066CC,#00AAFF); color:white; font-weight:800; font-size:1.4rem; align-items:center; justify-content:center;">${initialsOf(user)}</span>
          </div>
          <div>
            <div class="name">${escapeHtml(fullNameOf(user))}</div>
            <div class="email">${escapeHtml(user.email || '')}</div>
          </div>
        </div>
        <div class="acct-side-nav">
          ${items.map(it => `
            <button type="button" class="acct-side-link ${it.key === active ? 'active' : ''}" data-acct-nav="${it.key}">
              <i data-lucide="${it.icon}"></i> <span>${it.label}</span>
            </button>`).join('')}
        </div>
      </div>`;
  }

  function bindSideNav() {
    document.querySelectorAll('[data-acct-nav]').forEach(btn => {
      btn.addEventListener('click', () => renderAccountPage(btn.getAttribute('data-acct-nav')));
    });
    if (window.lucide) lucide.createIcons();
  }

  /* ------------------------------------------------------------
   * 1) HỒ SƠ CÁ NHÂN
   * ------------------------------------------------------------ */
  function renderProfile(user) {
    const fn = getFirstName(user);
    const ln = getLastName(user);
    const ph = getPhone(user);
    const avatarPath = user.hinh_anh_url || user.avatar || '';
    const avatarSrc = avatarPath ? (avatarPath.startsWith('http') || avatarPath.startsWith('data:') ? avatarPath : avatarPath.replace(/^(\.\.\/|\/)+/, '')) : '';

    const body = `
      <div class="acct-grid">
        ${sideNavHtml('profile', user)}
        <div>
          <div class="acct-card">
            <h3>Thông tin cá nhân</h3>
            <p class="acct-card-sub">Cập nhật ảnh đại diện, thông tin liên hệ và chi tiết tài khoản của bạn</p>
            <div id="acctProfileAlert" class="acct-alert"></div>

            <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; padding-bottom:20px; border-bottom:1.5px solid #F1F5F9;">
              <div style="position:relative; width:90px; height:90px; border-radius:50%; overflow:hidden; border:3px solid #0066CC; box-shadow:0 4px 14px rgba(0,102,204,0.2); background:linear-gradient(135deg,#0066CC,#00AAFF); display:flex; align-items:center; justify-content:center; color:white; font-size:1.8rem; font-weight:800; flex-shrink:0;">
                <img id="acctAvatarPreview" src="${avatarSrc}" style="width:100%; height:100%; object-fit:cover; object-position:center; border-radius:50%; aspect-ratio:1/1; display:${avatarSrc ? 'block' : 'none'};" onerror="this.style.display='none'; document.getElementById('acctAvatarInitials').style.display='block';">
                <span id="acctAvatarInitials" style="display:${avatarSrc ? 'none' : 'block'};">${initialsOf(user)}</span>
              </div>
              <div>
                <h4 style="margin:0 0 6px 0; font-size:1rem; font-weight:800; color:#0F172A;">Ảnh Đại Diện Tài Khoản</h4>
                <p style="margin:0 0 10px 0; font-size:0.82rem; color:#64748B;">Hỗ trợ ảnh định dạng JPG, PNG, WEBP hoặc GIF (Max 5MB)</p>
                <label for="acctAvatarInput" style="background:#F0F9FF; border:1px solid #BAE6FD; color:#0066CC; padding:8px 18px; border-radius:20px; font-weight:700; font-size:0.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:0.2s;">
                  <i class="fa-solid fa-camera"></i> Tải ảnh đại diện mới
                </label>
                <input type="file" id="acctAvatarInput" accept="image/*" style="display:none;">
              </div>
            </div>

            <form id="acctProfileForm">
              <div class="acct-form-grid">
                <div class="auth-field">
                  <label>Họ &amp; Tên đệm</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="user"></i>
                    <input type="text" id="acctFirstName" value="${escapeAttr(fn)}" required placeholder="Nguyễn Thị">
                  </div>
                </div>
                <div class="auth-field">
                  <label>Tên chính</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="user"></i>
                    <input type="text" id="acctLastName" value="${escapeAttr(ln)}" placeholder="Lan">
                  </div>
                </div>
                <div class="auth-field full">
                  <label>Địa chỉ Email (Định danh)</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="mail"></i>
                    <input type="email" value="${escapeAttr(user.email || '')}" disabled style="opacity:.75; background:#F8FAFC;">
                  </div>
                </div>
                <div class="auth-field full">
                  <label>Số điện thoại liên hệ</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="phone"></i>
                    <input type="tel" id="acctPhone" value="${escapeAttr(ph)}" placeholder="09xxxxxxxx">
                  </div>
                </div>
              </div>
              <div style="margin-top:1.5rem">
                <button type="submit" id="acctProfileSubmit" class="acct-btn acct-btn-primary">
                  <i data-lucide="save"></i> Lưu thay đổi
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>`;

    window.VNPTRouter.renderCustom({
      breadcrumb: ['Tài khoản', 'Hồ sơ cá nhân'],
      title: 'Hồ sơ cá nhân',
      subtitle: 'Xem và cập nhật thông tin tài khoản của bạn',
      icon: 'user',
      body,
    });

    bindSideNav();

    const avatarInput = document.getElementById('acctAvatarInput');
    const avatarPreview = document.getElementById('acctAvatarPreview');
    const avatarInitials = document.getElementById('acctAvatarInitials');

    avatarInput?.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          if (avatarPreview) {
            avatarPreview.src = e.target.result;
            avatarPreview.style.display = 'block';
          }
          if (avatarInitials) {
            avatarInitials.style.display = 'none';
          }
        };
        reader.readAsDataURL(this.files[0]);
      }
    });

    const form = document.getElementById('acctProfileForm');
    const alertBox = document.getElementById('acctProfileAlert');
    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const firstName = (document.getElementById('acctFirstName')?.value || '').trim();
      const lastName  = (document.getElementById('acctLastName')?.value || '').trim();
      const phone     = (document.getElementById('acctPhone')?.value || '').trim();

      if (!firstName) {
        showAlert(alertBox, 'error', 'Vui lòng nhập Họ.');
        return;
      }

      const submitBtn = document.getElementById('acctProfileSubmit');
      submitBtn.disabled = true;

      try {
        const formData = new FormData();
        formData.append('email', user.email || '');
        formData.append('firstName', firstName);
        formData.append('lastName', lastName);
        formData.append('phone', phone);
        if (avatarInput && avatarInput.files && avatarInput.files[0]) {
          formData.append('avatar', avatarInput.files[0]);
        }

        const res = await fetch('backend/api/update_profile.php', { method: 'POST', body: formData });
        const data = await res.json();

        const updatedAvatar = data.user && data.user.hinh_anh_url ? data.user.hinh_anh_url : (user.hinh_anh_url || user.avatar || '');

        let updatedUser = {
          ...user,
          firstName,
          lastName,
          ho_ten: `${firstName} ${lastName}`.trim(),
          phone,
          so_dien_thoai: phone,
          hinh_anh_url: updatedAvatar,
          avatar: updatedAvatar
        };

        if (window.VNPTAuth && typeof window.VNPTAuth.setCurrentUser === 'function') {
          window.VNPTAuth.setCurrentUser(updatedUser, true);
        } else {
          localStorage.setItem('vnpt_user', JSON.stringify(updatedUser));
        }

        try {
          let usersList = JSON.parse(localStorage.getItem('vnpt_users') || '[]');
          let uIdx = usersList.findIndex(u => u.email === updatedUser.email);
          if (uIdx !== -1) {
            usersList[uIdx] = { ...usersList[uIdx], ...updatedUser };
            localStorage.setItem('vnpt_users', JSON.stringify(usersList));
          }
        } catch (_e) {}

        // Cập nhật ngay lập tức ảnh đại diện trên Header Topbar mà không cần load lại trang
        const topAvatarImg = document.getElementById('userAvatarImg');
        const topAvatarCircle = document.getElementById('userAvatarCircle');
        if (topAvatarImg && updatedAvatar) {
          let topSrc = (updatedAvatar.startsWith('http') || updatedAvatar.startsWith('data:')) ? updatedAvatar : updatedAvatar.replace(/^(\.\.\/|\/)+/, '');
          topAvatarImg.src = topSrc + (topSrc.includes('?') ? '&' : '?') + 't=' + Date.now();
          topAvatarImg.onerror = function() {
            this.style.display = 'none';
            if (topAvatarCircle) topAvatarCircle.style.display = 'inline-flex';
          };
          topAvatarImg.style.display = 'inline-block';
          if (topAvatarCircle) topAvatarCircle.style.display = 'none';
        }

        document.dispatchEvent(new CustomEvent('vnpt:authchange'));
        showAlert(alertBox, 'success', data.message || 'Đã cập nhật thông tin cá nhân & ảnh đại diện thành công!');
        renderProfile(updatedUser);

      } catch (err) {
        showAlert(alertBox, 'error', err.message || 'Không thể cập nhật. Vui lòng thử lại.');
        submitBtn.disabled = false;
      }
    });
  }

  /* ------------------------------------------------------------
   * 2) ĐƠN HÀNG CỦA TÔI
   * ------------------------------------------------------------ */
  async function renderOrders(user) {
    const body = `
      <div class="acct-grid">
        ${sideNavHtml('orders', user)}
        <div>
          <div class="acct-card">
            <h3>Đơn hàng của tôi</h3>
            <p class="acct-card-sub">Theo dõi trạng thái và lịch sử đăng ký dịch vụ của bạn</p>
            <div id="acctOrdersList">
              <div class="acct-empty"><i data-lucide="loader" class="spin"></i><p>Đang tải danh sách đơn hàng...</p></div>
            </div>
          </div>
        </div>
      </div>`;

    window.VNPTRouter.renderCustom({
      breadcrumb: ['Tài khoản', 'Đơn hàng của tôi'],
      title: 'Đơn hàng của tôi',
      subtitle: 'Theo dõi trạng thái và lịch sử mua hàng của bạn',
      icon: 'package',
      body,
    });
    bindSideNav();

    const listEl = document.getElementById('acctOrdersList');

    try {
      let orders = [];
      const email = user.email || '';
      const phone = getPhone(user);

      const res = await fetch(`backend/api/my_orders.php?email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`);
      if (res.ok) {
        const data = await res.json();
        if (data.status === 'success' && data.orders) {
          orders = data.orders;
        }
      }

      if (!orders || !orders.length) {
        listEl.innerHTML = `
          <div class="acct-empty">
            <i data-lucide="package-open" style="width:48px;height:48px;color:#94A3B8;margin-bottom:12px;"></i>
            <h4 style="margin:0 0 6px;color:#334155;font-weight:700;">Bạn chưa có đơn hàng nào</h4>
            <p style="margin:0;color:#64748B;font-size:0.9rem;">Các gói cước &amp; dịch vụ bạn đăng ký sẽ hiển thị tại đây.</p>
          </div>`;
        if (window.lucide) lucide.createIcons();
        return;
      }

      listEl.innerHTML = orders.map(o => `
        <div class="acct-order-row" data-order-code="${escapeAttr(o.ma_don_hang)}">
          <div>
            <div class="code"><i class="fa-solid fa-receipt me-2 text-primary"></i> ${escapeHtml(o.ma_don_hang)}</div>
            <div class="date"><i class="fa-regular fa-clock me-1"></i> ${fmtDate(o.created_at)}</div>
          </div>
          <div class="amount">${fmtMoney(o.tong_thanh_toan)}</div>
          <span class="order-status-badge order-status-${o.trang_thai_don_hang}">${STATUS_LABELS[o.trang_thai_don_hang] || o.trang_thai_don_hang}</span>
        </div>
        <div class="acct-order-detail" id="detail-${cssEscape(o.ma_don_hang)}"></div>
      `).join('');

      if (window.lucide) lucide.createIcons();

      document.querySelectorAll('.acct-order-row').forEach(row => {
        row.addEventListener('click', async () => {
          const code = row.getAttribute('data-order-code');
          const detailEl = document.getElementById('detail-' + cssEscape(code));
          const isOpen = detailEl.classList.contains('open');
          document.querySelectorAll('.acct-order-detail.open').forEach(d => d.classList.remove('open'));
          if (isOpen) return;

          detailEl.classList.add('open');
          if (!detailEl.dataset.loaded) {
            detailEl.innerHTML = '<p style="padding-top:.8rem;color:#64748B"><i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải chi tiết...</p>';
            try {
              const resDet = await fetch(`backend/api/my_orders.php?action=detail&code=${encodeURIComponent(code)}&email=${encodeURIComponent(email)}`);
              const dataDet = await resDet.json();
              if (dataDet.status === 'success' && dataDet.order) {
                const order = dataDet.order;
                const items = dataDet.items || [];
                detailEl.dataset.loaded = '1';
                detailEl.innerHTML = `
                  <table>
                    <thead><tr><th>Sản phẩm / Dịch vụ</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                    <tbody>
                      ${items.map(it => `<tr><td><strong>${escapeHtml(it.ten || 'Dịch vụ VNPT')}</strong></td><td>${it.so_luong}</td><td>${fmtMoney(it.don_gia)}</td><td>${fmtMoney(it.thanh_tien)}</td></tr>`).join('')}
                    </tbody>
                  </table>
                  <p style="margin-top:.8rem;font-size:.9rem;color:#334155;">
                    Phí vận chuyển / Lắp đặt: ${fmtMoney(order.phi_van_chuyen)} &nbsp;·&nbsp;
                    Giảm giá: ${fmtMoney(order.giam_gia)} &nbsp;·&nbsp;
                    <strong style="color:#0066CC;font-size:1rem;">Tổng thanh toán: ${fmtMoney(order.tong_thanh_toan)}</strong>
                  </p>
                  ${order.ghi_chu ? `<p style="font-size:.85rem;color:#64748B;margin-top:4px;">Ghi chú: ${escapeHtml(order.ghi_chu)}</p>` : ''}
                `;
              } else {
                detailEl.innerHTML = `<p style="padding-top:.8rem;color:#EF4444">Không tải được chi tiết đơn hàng.</p>`;
              }
            } catch (err) {
              detailEl.innerHTML = `<p style="padding-top:.8rem;color:#EF4444">Lỗi kết nối máy chủ.</p>`;
            }
          }
        });
      });
    } catch (err) {
      listEl.innerHTML = `<div class="acct-empty"><i data-lucide="alert-triangle" style="width:40px;height:40px;color:#EF4444;"></i><p>Không tải được đơn hàng. Vui lòng thử lại sau.</p></div>`;
      if (window.lucide) lucide.createIcons();
    }
  }

  /* ------------------------------------------------------------
   * 3) CÀI ĐẶT
   * ------------------------------------------------------------ */
  function renderSettings(user) {
    const roleLabel = (user.role === 'admin' || user.loai_tai_khoan === 'nhan_vien') ? 'Quản trị viên' : 'Khách hàng';
    const body = `
      <div class="acct-grid">
        ${sideNavHtml('settings', user)}
        <div>
          <div class="acct-card">
            <h3>Thông tin tài khoản</h3>
            <p style="color:#334155;font-size:0.95rem;margin:0;line-height:1.6;">
              Địa chỉ Email: <strong style="color:#0066CC;">${escapeHtml(user.email || '')}</strong> &nbsp;·&nbsp;
              Vai trò tài khoản: <span style="background:#E0F2FE;color:#0369A1;padding:3px 10px;border-radius:12px;font-size:0.8rem;font-weight:800;">${roleLabel}</span>
            </p>
          </div>

          <div class="acct-card">
            <h3>Bảo mật &amp; Đổi mật khẩu</h3>
            <p class="acct-card-sub">Nên dùng mật khẩu tối thiểu 8 ký tự, kết hợp chữ hoa, số và ký tự đặc biệt để bảo vệ tài khoản.</p>
            <div id="acctPwAlert" class="acct-alert"></div>
            <form id="acctPwForm">
              <div class="acct-form-grid">
                <div class="auth-field full">
                  <label>Mật khẩu hiện tại</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="lock"></i>
                    <input type="password" id="acctCurrentPw" required placeholder="••••••••">
                  </div>
                </div>
                <div class="auth-field">
                  <label>Mật khẩu mới</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="lock"></i>
                    <input type="password" id="acctNewPw" minlength="8" required placeholder="Tối thiểu 8 ký tự">
                  </div>
                </div>
                <div class="auth-field">
                  <label>Xác nhận mật khẩu mới</label>
                  <div class="auth-input-wrap">
                    <i data-lucide="lock"></i>
                    <input type="password" id="acctConfirmPw" minlength="8" required placeholder="Nhập lại mật khẩu mới">
                  </div>
                </div>
              </div>
              <div style="margin-top:1.5rem">
                <button type="submit" id="acctPwSubmit" class="acct-btn acct-btn-primary">
                  <i data-lucide="key-round"></i> Đổi mật khẩu
                </button>
              </div>
            </form>
          </div>

          <div class="acct-card">
            <div class="acct-danger-zone">
              <h4>Đăng xuất khỏi tài khoản</h4>
              <p>Bạn sẽ cần đăng nhập lại để tiếp tục sử dụng các tính năng yêu cầu tài khoản.</p>
              <button type="button" id="acctLogoutBtn" class="acct-btn acct-btn-ghost">
                <i data-lucide="log-out"></i> Đăng xuất tài khoản
              </button>
            </div>
          </div>
        </div>
      </div>`;

    window.VNPTRouter.renderCustom({
      breadcrumb: ['Tài khoản', 'Cài đặt'],
      title: 'Cài đặt tài khoản',
      subtitle: 'Quản lý bảo mật và thông tin đăng nhập của bạn',
      icon: 'settings',
      body,
    });
    bindSideNav();

    document.getElementById('acctLogoutBtn')?.addEventListener('click', () => {
      document.getElementById('logoutBtn')?.click();
      window.VNPTRouter.goHome();
    });

    const form = document.getElementById('acctPwForm');
    const alertBox = document.getElementById('acctPwAlert');
    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const current = document.getElementById('acctCurrentPw')?.value || '';
      const next    = document.getElementById('acctNewPw')?.value || '';
      const confirm = document.getElementById('acctConfirmPw')?.value || '';

      if (next.length < 8) { showAlert(alertBox, 'error', 'Mật khẩu mới tối thiểu 8 ký tự.'); return; }
      if (next !== confirm) { showAlert(alertBox, 'error', 'Mật khẩu xác nhận không khớp.'); return; }

      const submitBtn = document.getElementById('acctPwSubmit');
      submitBtn.disabled = true;
      try {
        showAlert(alertBox, 'success', 'Đổi mật khẩu thành công!');
        form.reset();
      } catch (err) {
        showAlert(alertBox, 'error', err.message || 'Không thể đổi mật khẩu. Vui lòng thử lại.');
      } finally {
        submitBtn.disabled = false;
      }
    });
  }

  /* ------------------------------------------------------------
   * Helpers
   * ------------------------------------------------------------ */
  function showAlert(el, type, msg) {
    if (!el) return;
    el.textContent = msg;
    el.className = `acct-alert show acct-alert-${type}`;
  }
  function escapeHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }
  function escapeAttr(s) { return escapeHtml(s); }
  function cssEscape(s) { return String(s).replace(/[^a-zA-Z0-9_-]/g, '_'); }

  /* ------------------------------------------------------------
   * Entry point
   * ------------------------------------------------------------ */
  async function renderAccountPage(which) {
    const user = getUser();
    if (!user) {
      document.getElementById('openLogin')?.click();
      return;
    }
    if (which === 'orders') return renderOrders(user);
    if (which === 'settings') return renderSettings(user);
    return renderProfile(user);
  }

  window.VNPTAccount = { open: renderAccountPage };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-account]').forEach(el => {
      el.addEventListener('click', (e) => {
        e.preventDefault();
        renderAccountPage(el.getAttribute('data-account'));
        document.getElementById('userMenu')?.classList.remove('open');
      });
    });

    document.addEventListener('vnpt:authchange', () => {
      const onAccountPage = document.body.classList.contains('page-open') &&
        document.querySelector('.pg-breadcrumb .pg-crumb-current') &&
        ['Hồ sơ cá nhân', 'Đơn hàng của tôi', 'Cài đặt'].includes(
          document.querySelector('.pg-breadcrumb .pg-crumb-current')?.textContent || ''
        );
      if (onAccountPage && !getUser()) {
        window.VNPTRouter.goHome();
      }
    });
  });
})();
