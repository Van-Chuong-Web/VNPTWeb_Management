/**
 * public/api.js — Lớp gọi API backend cho frontend VNPT (Kết nối 100% PHP/MySQL).
 */
(function () {
  'use strict';

  const TOKEN_KEY = 'vnpt_token';
  const BASE = '';

  window.getApiPath = function(endpoint) {
    if (!endpoint) return '';
    if (endpoint.startsWith('http://') || endpoint.startsWith('https://') || endpoint.startsWith('data:')) {
      return endpoint;
    }
    return endpoint.replace(/^(\.\.\/|\/)+/, '');
  };

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
  function remembered() { return !!localStorage.getItem(TOKEN_KEY); }

  async function request(method, url, body) {
    const headers = { 'Content-Type': 'application/json' };
    const token = getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    const targetUrl = window.getApiPath(url);
    const res = await fetch(targetUrl, {
      method,
      headers,
      body: body != null ? JSON.stringify(body) : undefined,
    });

    let data = null;
    try { data = await res.json(); } catch (_e) { data = null; }

    if (!res.ok) {
      const err = new Error((data && (data.error || data.message)) || ('Lỗi ' + res.status));
      err.status = res.status;
      err.data = data;
      throw err;
    }
    return data;
  }

  const VNPTApi = {
    available: true,
    getToken,
    setToken,
    remembered,

    async detect() {
      this.available = true;
      return true;
    },

    /* ---- Auth ---- */
    register(payload) { return request('POST', 'backend/api/register.php', payload); },
    login(email, password) { return request('POST', 'backend/api/login.php', { email, password }); },
    me() { return request('GET', 'backend/api/update_profile.php?action=get'); },
    updateProfile(payload) { return request('POST', 'backend/api/update_profile.php', payload); },
    changePassword(currentPassword, newPassword) { return request('POST', 'backend/api/change_password.php', { currentPassword, newPassword }); },

    /* ---- Products ---- */
    products(query) {
      const qs = query ? ('?' + new URLSearchParams(query).toString()) : '';
      return request('GET', 'backend/api/products.php' + qs);
    },
    product(code) { return request('GET', 'backend/api/products.php?code=' + encodeURIComponent(code)); },

    /* ---- Cart ---- */
    getCart() { return request('GET', 'backend/api/cart.php'); },
    addToCart(code, qty) { return request('POST', 'backend/api/cart.php', { id: code, qty: qty || 1 }); },
    setCartQty(code, qty) { return request('POST', 'backend/api/cart.php', { id: code, qty }); },
    removeFromCart(code) { return request('GET', 'backend/api/cart.php?action=remove&remove_id=' + encodeURIComponent(code)); },
    clearCart() { return request('GET', 'backend/api/cart.php?action=clear'); },

    /* ---- Orders ---- */
    checkout(note) { return request('POST', 'backend/api/orders.php', { note: note || null }); },
    myOrders() { return request('GET', 'backend/api/orders.php'); },
    myOrder(code) { return request('GET', 'backend/api/orders.php?code=' + encodeURIComponent(code)); },

    /* ---- Admin ---- */
    adminStats() { return request('GET', 'admin_panel/api/stats.php'); },
    adminUsers() { return request('GET', 'admin_panel/api/users.php'); },
    adminProducts() { return request('GET', 'admin_panel/api/products.php'); },
    adminOrders() { return request('GET', 'admin_panel/api/orders.php'); },
  };

  window.VNPTApi = VNPTApi;
  VNPTApi.detect();
})();
