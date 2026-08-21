/**
 * public/api.js — Lớp gọi API backend cho frontend VNPT.
 *
 * Cung cấp window.VNPTApi với các phương thức auth / products / cart / orders / admin.
 * - Tự động gắn JWT (Bearer) lấy từ localStorage 'vnpt_token'.
 * - Tự dò backend qua /api/health: nếu KHÔNG có backend (vd xem bản tĩnh),
 *   VNPTApi.available = false để các module tự chuyển sang chế độ localStorage (offline).
 */
(function () {
  'use strict';

  const TOKEN_KEY = 'vnpt_token';
  // Base URL của API:
  // - Frontend (index.php/header.php/footer.php) LUÔN cần PHP để chạy (dùng include),
  //   nên hầu như luôn được mở qua Apache/XAMPP (vd http://localhost:8080/...),
  //   trong khi API Node.js chạy RIÊNG trên cổng 3000 — đây là 2 origin khác nhau.
  // - Nếu để trống/tương đối, request sẽ bay nhầm sang cổng của Apache (không có
  //   API) và luôn báo "Failed to fetch". Nên: tự nhận diện, và trỏ đúng sang cổng
  //   3000 trên CÙNG hostname mà bạn đang truy cập (không hard-code "localhost" —
  //   vẫn đúng khi truy cập qua IP LAN hay domain thật).
  // - Trường hợp hiếm khi trang lại được phục vụ trực tiếp từ chính Node (cổng
  //   3000), dùng đường dẫn tương đối như bình thường.
  const BASE = (window.location.port === '3000')
    ? ''
    : `${window.location.protocol}//${window.location.hostname}:3000`;

  window.getApiPath = function(endpoint) {
    if (!endpoint) return '';
    if (endpoint.startsWith('http://') || endpoint.startsWith('https://') || endpoint.startsWith('data:')) {
      return endpoint;
    }
    let cleanEndpoint = endpoint.replace(/^(\.\.\/|\/)+/, '');
    const pathname = window.location.pathname;
    if (pathname.includes('/frontend/')) {
      return '../' + cleanEndpoint;
    }
    return cleanEndpoint;
  };

  // ---- "Ghi nhớ đăng nhập": token lưu ở localStorage (còn sau khi đóng trình
  // duyệt) khi được chọn, ngược lại lưu ở sessionStorage (mất khi đóng tab/trình
  // duyệt) để bảo mật hơn trên máy dùng chung. getToken() luôn kiểm tra cả hai.
  function getToken() {
    return localStorage.getItem(TOKEN_KEY) || sessionStorage.getItem(TOKEN_KEY) || '';
  }
  function setToken(t, remember) {
    localStorage.removeItem(TOKEN_KEY);
    sessionStorage.removeItem(TOKEN_KEY);
    if (!t) return;
    if (remember) localStorage.setItem(TOKEN_KEY, t);
    else sessionStorage.setItem(TOKEN_KEY, t);
  }
  // true nếu phiên đăng nhập hiện tại đang được "ghi nhớ" (token ở localStorage).
  function remembered() { return !!localStorage.getItem(TOKEN_KEY); }

  async function request(method, url, body) {
    const headers = { 'Content-Type': 'application/json' };
    const token = getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    const res = await fetch(BASE + url, {
      method,
      headers,
      body: body != null ? JSON.stringify(body) : undefined,
    });

    let data = null;
    try { data = await res.json(); } catch (_e) { data = null; }

    if (!res.ok) {
      const err = new Error((data && data.error) || ('Lỗi ' + res.status));
      err.status = res.status;
      err.data = data;
      throw err;
    }
    return data;
  }

  const VNPTApi = {
    available: null, // null = chưa dò; true/false sau khi gọi detect()
    getToken,
    setToken,
    remembered,

    // Dò xem backend có sống không (dùng để bật/tắt chế độ offline).
    async detect() {
      try {
        const ctrl = new AbortController();
        const t = setTimeout(() => ctrl.abort(), 2500);
        const res = await fetch(BASE + '/api/health', { signal: ctrl.signal });
        clearTimeout(t);
        this.available = res.ok;
      } catch (_e) {
        this.available = false;
      }
      return this.available;
    },

    /* ---- Auth ---- */
    register(payload) { return request('POST', '/api/auth/register', payload); },
    login(email, password) { return request('POST', '/api/auth/login', { email, password }); },
    me() { return request('GET', '/api/auth/me'); },
    updateProfile(payload) { return request('PUT', '/api/auth/me', payload); },
    changePassword(currentPassword, newPassword) { return request('PUT', '/api/auth/password', { currentPassword, newPassword }); },

    /* ---- Products ---- */
    products(query) {
      const qs = query ? ('?' + new URLSearchParams(query).toString()) : '';
      return request('GET', '/api/products' + qs);
    },
    product(code) { return request('GET', '/api/products/' + encodeURIComponent(code)); },

    /* ---- Cart ---- */
    getCart() { return request('GET', '/api/cart'); },
    addToCart(code, qty) { return request('POST', '/api/cart', { code, qty: qty || 1 }); },
    setCartQty(code, qty) { return request('PUT', '/api/cart/' + encodeURIComponent(code), { qty }); },
    removeFromCart(code) { return request('DELETE', '/api/cart/' + encodeURIComponent(code)); },
    clearCart() { return request('DELETE', '/api/cart'); },

    /* ---- Orders ---- */
    checkout(note) { return request('POST', '/api/orders', { note: note || null }); },
    myOrders() { return request('GET', '/api/orders'); },
    myOrder(code) { return request('GET', '/api/orders/' + encodeURIComponent(code)); },

    /* ---- Admin ---- */
    adminStats() { return request('GET', '/api/admin/stats'); },
    adminUsers() { return request('GET', '/api/admin/users'); },
    adminProducts() { return request('GET', '/api/admin/products'); },
    adminOrders() { return request('GET', '/api/admin/orders'); },
  };

  window.VNPTApi = VNPTApi;
  // Dò backend ngay khi tải trang (các module chờ qua VNPTApi.available).
  VNPTApi.detect();
})();
