/**
 * VNPT — Admin Entry Helper
 * Khi người dùng nhấn "Quản trị hệ thống", chuyển hướng trực tiếp đến hệ thống Quản trị Admin Panel (PHP + MySQL).
 */
(function () {
  'use strict';

  function handleAdminClick(e) {
    if (e) e.preventDefault();
    const user = window.VNPTAuth ? window.VNPTAuth.getCurrentUser() : null;
    const email = user ? (user.email || '') : '';
    const targetUrl = email ? `../admin_panel/index.php?user_email=${encodeURIComponent(email)}` : '../admin_panel/index.php';
    window.location.href = targetUrl;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openAdminBtn');
    if (openBtn) {
      openBtn.addEventListener('click', handleAdminClick);
    }
  });
})();
