/**
 * VNPT — Shopping Cart Module
 * Quản lý giỏ hàng trực tuyến & ngoại tuyến, hỗ trợ Thêm, Sửa số lượng, Xóa, Checkout & Thanh toán VietQR / MoMo / COD.
 */
document.addEventListener('DOMContentLoaded', function() {
    const cartToggle = document.getElementById('cartToggle');
    const cartBadge = document.getElementById('cartBadge');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartClose = document.getElementById('cartClose');
    const cartEmpty = document.getElementById('cartEmpty');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartFooter = document.getElementById('cartFooter');
    const cartCount = document.getElementById('cartCount');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('cartTotal');
    const clearCartBtn = document.getElementById('clearCartBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');

    const Api = window.VNPTApi;
    let currentCartItems = [];

    const checkoutModal = document.getElementById('checkoutModal');
    const closeCheckoutBtn = document.getElementById('closeCheckout');
    const paymentSuccessModal = document.getElementById('paymentSuccessModal');
    const closePaymentSuccessBtn = document.getElementById('closePaymentSuccess');

    function safeShowToast(msg, isError) {
        if (typeof window.showToast === 'function') {
            window.showToast(msg, isError);
            return;
        }
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

    function isStaffUser() {
        try {
            const uStr = localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user');
            if (!uStr) return false;
            const u = JSON.parse(uStr);
            if (!u) return false;
            const email = (u.email || '').toLowerCase();
            const rawRole = u.role || u.ten_vai_tro || '';
            return (u.loai_tai_khoan === 'nhan_vien') || 
                   email.endsWith('@vnpt.vn') || email.includes('admin') || email.includes('editor') || email.includes('manager') ||
                   ['admin', 'quan_tri_vien', 'bien_tap_vien', 'nhan_vien_ban_hang', 'quan_ly', 'editor', 'staff'].includes(rawRole);
        } catch(e) { return false; }
    }

    function openCart() {
        if (isStaffUser()) {
            safeShowToast('Tài khoản Quản trị / Nhân viên không sử dụng Giỏ hàng khách hàng!', true);
            return;
        }
        const cartSidebarEl = document.getElementById('cartSidebar') || cartSidebar;
        const cartOverlayEl = document.getElementById('cartOverlay') || cartOverlay;
        if (cartSidebarEl) cartSidebarEl.classList.add('open');
        if (cartOverlayEl) cartOverlayEl.classList.add('active');
        fetchCart(); 
    }
    window.openCart = openCart;

    function closeCart() {
        const cartSidebarEl = document.getElementById('cartSidebar') || cartSidebar;
        const cartOverlayEl = document.getElementById('cartOverlay') || cartOverlay;
        if (cartSidebarEl) cartSidebarEl.classList.remove('open');
        if (cartOverlayEl) cartOverlayEl.classList.remove('active');
    }
    window.closeCart = closeCart;

    if (cartToggle) {
        cartToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const cartSidebarEl = document.getElementById('cartSidebar') || cartSidebar;
            if (cartSidebarEl && cartSidebarEl.classList.contains('open')) {
                closeCart();
            } else {
                openCart();
            }
        });
    }
    if (cartClose) cartClose.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    if (closeCheckoutBtn) {
        closeCheckoutBtn.addEventListener('click', () => {
            if (checkoutModal) checkoutModal.classList.remove('open');
            if (cartOverlay) cartOverlay.classList.remove('active');
        });
    }

    if (closePaymentSuccessBtn) {
        closePaymentSuccessBtn.addEventListener('click', () => {
            if (paymentSuccessModal) paymentSuccessModal.classList.remove('open');
            if (cartOverlay) cartOverlay.classList.remove('active');
        });
    }

    /* ---- Lấy dữ liệu giỏ hàng (LocalStorage & PHP API Sync) ---- */
    async function fetchCart() {
        let items = [];
        try {
            items = JSON.parse(localStorage.getItem('vnpt_cart') || '[]');
            const map = new Map();
            items.forEach(it => {
                const key = (it.id || it.name || '').toLowerCase();
                if (!map.has(key)) {
                    map.set(key, { ...it, qty: 1 });
                }
            });
            items = Array.from(map.values());
            localStorage.setItem('vnpt_cart', JSON.stringify(items));
        } catch (_e) {
            items = [];
        }
        renderCart(items);

        // Đồng bộ với backend PHP API
        try {
            const cartUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/cart.php') : 'backend/api/cart.php';
            const res = await fetch(cartUrl);
            const data = await res.json();
            if (res.ok && data && Array.isArray(data.items) && data.items.length > 0) {
                const map = new Map();
                data.items.forEach(it => {
                    const key = (it.id || it.name || '').toLowerCase();
                    if (!map.has(key)) map.set(key, { ...it, qty: 1 });
                });
                items = Array.from(map.values());
                localStorage.setItem('vnpt_cart', JSON.stringify(items));
                renderCart(items);
            }
        } catch (_e) {}
    }

    /* ---- Render giao diện Giỏ hàng ---- */
    function renderCart(items) {
        // 🔴 Đảm bảo chỉ có 1 sản phẩm mỗi loại và số lượng = 1
        const map = new Map();
        (items || []).forEach(it => {
            const key = (it.id || it.name || '').toLowerCase();
            if (!map.has(key)) {
                map.set(key, { ...it, qty: 1 });
            }
        });
        currentCartItems = Array.from(map.values());
        if (!cartItemsContainer) return;

        const totalItems = currentCartItems.length;
        if (cartBadge) {
            if (totalItems > 0 && !isStaffUser()) {
                cartBadge.style.display = 'flex';
                cartBadge.innerText = totalItems > 99 ? '99+' : totalItems;
            } else {
                cartBadge.style.display = 'none';
            }
        }

        if (currentCartItems.length === 0) {
            if (cartEmpty) cartEmpty.style.display = 'flex';
            cartItemsContainer.innerHTML = '';
            if (cartFooter) cartFooter.style.display = 'none';
            return;
        }

        if (cartEmpty) cartEmpty.style.display = 'none';
        if (cartFooter) cartFooter.style.display = 'block';

        let html = '';
        let totalMoney = 0;

        currentCartItems.forEach((item) => {
            const qty = 1;
            item.qty = 1;
            totalMoney += item.price;
            html += `
                <div class="cart-item">
                    <div class="cart-item-icon" style="background: ${item.color || '#0066CC'}"><i data-lucide="${item.icon || 'package'}"></i></div>
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${new Intl.NumberFormat('vi-VN').format(item.price)} ₫</div>
                        <div style="display: inline-flex; align-items: center; gap: 4px; margin-top: 6px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 3px 8px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #64748B;">Số lượng: 1</span>
                            <span style="font-size: 0.75rem; color: #0066CC; font-weight: 600; margin-left: 4px;">(Tối đa 1)</span>
                        </div>
                    </div>
                    <div class="cart-item-remove" data-code="${item.id}" title="Xóa khỏi giỏ hàng"><i data-lucide="trash-2"></i></div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = html;
        
        const formattedTotal = new Intl.NumberFormat('vi-VN').format(totalMoney) + ' ₫';
        if (cartCount) cartCount.innerText = currentCartItems.reduce((sum, i) => sum + (i.qty || 1), 0);
        if (cartSubtotal) cartSubtotal.innerText = formattedTotal;
        if (cartTotal) cartTotal.innerText = formattedTotal;

        if (window.lucide) lucide.createIcons();
        bindCartItemEvents();
    }

    /* ---- Gán sự kiện Tăng/Giảm/Xóa trong Giỏ hàng ---- */
    function bindCartItemEvents() {
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                const code = btn.getAttribute('data-code');
                const currentQty = parseInt(btn.getAttribute('data-qty'), 10) || 1;
                const newQty = btn.classList.contains('btn-plus') ? currentQty + 1 : currentQty - 1;

                if (newQty <= 0) {
                    await removeFromCart(code);
                } else {
                    await updateCartQty(code, newQty);
                }
            };
        });

        document.querySelectorAll('.cart-item-remove').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                const code = btn.getAttribute('data-code');
                await removeFromCart(code);
            };
        });
    }

    async function updateCartQty(code, qty) {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('vnpt_cart') || '[]'); } catch (_e) {}

        const itemIdx = cart.findIndex(it => (it.id || '').toString().toLowerCase() === (code || '').toLowerCase() || (it.name || '').toString().toLowerCase() === (code || '').toLowerCase());
        if (itemIdx > -1) {
            cart[itemIdx].qty = Math.max(1, qty);
            localStorage.setItem('vnpt_cart', JSON.stringify(cart));
            renderCart(cart);

            try {
                const cartUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/cart.php') : 'backend/api/cart.php';
                fetch(cartUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: code, qty: Math.max(1, qty) })
                }).catch(() => {});
            } catch (_e) {}
        }
    }

    async function removeFromCart(code) {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('vnpt_cart') || '[]'); } catch (_e) {}

        const targetCode = (code || '').toString().toLowerCase().trim();
        cart = cart.filter(it => {
            if (!it) return false;
            const itemId = (it.id || '').toString().toLowerCase().trim();
            const itemName = (it.name || '').toString().toLowerCase().trim();
            return itemId !== targetCode && itemName !== targetCode;
        });

        localStorage.setItem('vnpt_cart', JSON.stringify(cart));
        renderCart(cart);

        try {
            const cartUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/cart.php?action=remove&remove_id=' + encodeURIComponent(code)) : 'backend/api/cart.php?action=remove&remove_id=' + encodeURIComponent(code);
            fetch(cartUrl).catch(() => {});
        } catch (_e) {}
    }

    if (clearCartBtn) {
        clearCartBtn.onclick = async (e) => {
            e.preventDefault();
            localStorage.removeItem('vnpt_cart');
            renderCart([]);
            showToast('Đã xóa tất cả sản phẩm khỏi giỏ hàng!');
            try {
                const cartUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/cart.php?action=clear') : 'backend/api/cart.php?action=clear';
                fetch(cartUrl).catch(() => {});
            } catch (_e) {}
        };
    }

    /* ---- Lấy danh sách mã sản phẩm đã mua của tài khoản hiện tại ---- */
    function getPurchasedProductCodes() {
        const user = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                     (window.VNPTApi && typeof window.VNPTApi.getCurrentUser === 'function' && window.VNPTApi.getCurrentUser());
        const userId = user ? (user.id || user.taiKhoanId || user.email || 'guest') : 'guest';
        try {
            return JSON.parse(localStorage.getItem('vnpt_purchased_' + userId) || '[]');
        } catch (_e) {
            return [];
        }
    }

    function markProductsAsPurchased(items) {
        const user = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                     (window.VNPTApi && typeof window.VNPTApi.getCurrentUser === 'function' && window.VNPTApi.getCurrentUser());
        const userId = user ? (user.id || user.taiKhoanId || user.email || 'guest') : 'guest';
        let purchased = getPurchasedProductCodes();
        (items || []).forEach(it => {
            if (it.id && !purchased.includes(it.id)) purchased.push(it.id);
            const slug = (it.name || '').toLowerCase().replace(/\s+/g, '-');
            if (slug && !purchased.includes(slug)) purchased.push(slug);
        });
        localStorage.setItem('vnpt_purchased_' + userId, JSON.stringify(purchased));
    }

    /* ---- Lắng nghe sự kiện Thêm sản phẩm vào giỏ từ toàn bộ giao diện ---- */
    document.addEventListener('click', async function(e) {
        // Bỏ qua nếu bấm vào nút xóa, nút tăng/giảm hoặc bên trong Sidebar giỏ hàng
        if (e.target.closest('.cart-item-remove, .qty-btn, .cart-sidebar, #cartSidebar, #cartOverlay, #cartFooter')) {
            return;
        }

        const addBtn = e.target.closest('.btn-add-cart, [data-action="add-cart"]');
        if (!addBtn) return;
        
        if (addBtn.id === 'openLogin' || 
            addBtn.id === 'openRegister' || 
            addBtn.closest('#authBtns') || 
            addBtn.closest('.auth-btns') || 
            addBtn.classList.contains('btn-open-consultation') || 
            addBtn.id === 'logoutBtn' || 
            addBtn.classList.contains('auth-link')) return;

        e.preventDefault();

        // 🟢 BẮT ĐIỀU KIỆN ĐĂNG NHẬP: Khi chưa đăng nhập thì KHÔNG THỂ thêm vào giỏ hàng
        const currentUser = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function')
                            ? window.VNPTAuth.getCurrentUser()
                            : (JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || 'null'));

        const isLoggedIn = !!(currentUser && (currentUser.email || currentUser.id));

        if (!isLoggedIn) {
            closeCart();
            const msg = '⚠️ Vui lòng <strong>Đăng nhập</strong> hoặc <strong>Đăng ký</strong> tài khoản để tiếp tục đăng ký dịch vụ!';
            if (typeof window.openLoginModal === 'function') {
                window.openLoginModal(msg);
            } else {
                const loginModal = document.getElementById('loginModal');
                const modalOverlay = document.getElementById('modalOverlay');
                const noticeBox = document.getElementById('loginNoticeBox');
                const noticeText = document.getElementById('loginNoticeText');
                if (noticeBox) {
                    if (noticeText) noticeText.innerHTML = msg;
                    noticeBox.style.display = 'flex';
                }
                if (loginModal) {
                    loginModal.style.display = 'flex';
                    loginModal.classList.add('open');
                }
                if (modalOverlay) modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            return;
        }

        const attrName = addBtn.getAttribute('data-name');
        const attrCode = addBtn.getAttribute('data-id') || addBtn.getAttribute('data-code');
        const attrPrice = addBtn.getAttribute('data-price');
        const attrIcon = addBtn.getAttribute('data-icon');
        const attrColor = addBtn.getAttribute('data-color');

        const card = addBtn.closest('.service-card, .price-card, .card, .product-card') || addBtn.parentElement;
        const titleEl = card ? card.querySelector('h3, .price-plan-name, .card-title, .product-title') : null;
        const priceEl = card ? card.querySelector('.price-num, .card-meta, .price, .product-price') : null;

        const name = attrName || (titleEl ? titleEl.textContent.trim() : 'Dịch vụ số VNPT');
        const code = attrCode || name.toLowerCase().replace(/\s+/g, '-');
        
        let price = 1500000;
        if (attrPrice && !isNaN(parseInt(attrPrice, 10))) {
            price = parseInt(attrPrice, 10);
        } else if (priceEl) {
            const rawPrice = priceEl.textContent.replace(/[^0-9]/g, '');
            if (rawPrice) price = parseInt(rawPrice, 10);
        }

        const icon = attrIcon || (card && card.querySelector('.card-icon, i') ? (card.querySelector('.card-icon, i').getAttribute('data-lucide') || 'package') : 'package');
        const color = attrColor || (card && card.querySelector('.card-icon-wrap') ? (card.querySelector('.card-icon-wrap').style.getPropertyValue('--card-color') || '#0066CC') : '#0066CC');

        const codeSlug = code.toLowerCase().replace(/\s+/g, '-');
        const nameSlug = name.toLowerCase().replace(/\s+/g, '-');

        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('vnpt_cart') || '[]'); } catch (_err) { cart = []; }

        const existingIndex = (cart || []).findIndex(item => {
            if (!item) return false;
            const itemId = (item.id || '').toString().toLowerCase();
            const itemName = (item.name || '').toString().toLowerCase().trim();
            return itemId === code.toLowerCase() || itemId === codeSlug || itemId === nameSlug || itemName === name.toLowerCase().trim();
        });

        if (existingIndex > -1) {
            cart[existingIndex].qty = 1;
            localStorage.setItem('vnpt_cart', JSON.stringify(cart));
            renderCart(cart);

            try {
                const cartUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/cart.php') : 'backend/api/cart.php';
                fetch(cartUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(cart[existingIndex])
                }).catch(() => {});
            } catch (_e) {}

            safeShowToast(`⚠️ Gói cước "${name}" đã có trong giỏ hàng của bạn!`, true);
            openCart();
            return;
        }

        const newItem = { id: code, name, price, qty: 1, icon, color };
        cart.push(newItem);
        localStorage.setItem('vnpt_cart', JSON.stringify(cart));
        renderCart(cart);

        // Gửi không bất đồng bộ tới backend PHP API
        try {
            const cartUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/cart.php') : 'backend/api/cart.php';
            fetch(cartUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newItem)
            }).catch(() => {});
        } catch (_e) {}

        safeShowToast(`🛒 Đã thêm gói "${name}" vào giỏ hàng!`);
        openCart();
    });

    /* ---- Checkout ---- */
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', async (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            const currentUser = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function')
                                ? window.VNPTAuth.getCurrentUser()
                                : (JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || 'null'));
            const isLoggedIn = !!(currentUser && (currentUser.email || currentUser.id));

            if (!isLoggedIn) {
                closeCart();
                const msg = '⚠️ Vui lòng <strong>Đăng nhập</strong> hoặc <strong>Đăng ký</strong> tài khoản để tiến hành thanh toán dịch vụ!';
                if (typeof window.openLoginModal === 'function') {
                    window.openLoginModal(msg);
                } else {
                    const loginModal = document.getElementById('loginModal');
                    const modalOverlay = document.getElementById('modalOverlay');
                    const noticeBox = document.getElementById('loginNoticeBox');
                    const noticeText = document.getElementById('loginNoticeText');
                    if (noticeBox) {
                        if (noticeText) noticeText.innerHTML = msg;
                        noticeBox.style.display = 'flex';
                    }
                    if (loginModal) {
                        loginModal.style.display = 'flex';
                        loginModal.classList.add('open');
                    }
                    if (modalOverlay) modalOverlay.classList.add('active');
                }
                return;
            }

            if (!currentCartItems || currentCartItems.length === 0) {
                try {
                    currentCartItems = JSON.parse(localStorage.getItem('vnpt_cart') || '[]');
                } catch (_e) {}
            }

            if (!currentCartItems || currentCartItems.length === 0) {
                safeShowToast('⚠️ Giỏ hàng của bạn đang trống!', true);
                return;
            }

            openCheckoutInvoiceModal();
        });
    }

    let lastCheckoutItems = [];

    function openCheckoutInvoiceModal() {
        const targetModal = document.getElementById('checkoutModal') || checkoutModal;
        const modalBody = document.getElementById('checkoutModalBody');
        const overlay = document.getElementById('modalOverlay');
        if (!modalBody) return;

        lastCheckoutItems = [...currentCartItems];
        const user = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                     JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || '{}');
        const orderCode = 'DH' + Date.now().toString().slice(-6);
        const totalMoney = currentCartItems.reduce((sum, item) => sum + (item.price * (item.qty || 1)), 0);
        const formattedTotal = new Intl.NumberFormat('vi-VN').format(totalMoney) + ' ₫';
        const nowStr = new Date().toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' });

        const qrUrl = `https://img.vietqr.io/image/970436-1012345678-compact2.png?amount=${totalMoney}&addInfo=${orderCode}&accountName=CONG%20TY%20DIGISERVICE%20VNPT`;

        const userName = `${user.firstName || user.ho_ten || 'Khách hàng'} ${user.lastName || ''}`.trim();
        const userEmail = user.email || 'lannguyen@gmail.com';
        const userPhone = user.phone || user.so_dien_thoai || '0901 234 567';

        modalBody.innerHTML = `
            <div class="checkout-grid" style="display: grid; grid-template-columns: 1.25fr 1fr; gap: 24px; padding: 24px 28px; box-sizing: border-box;">
                
                <!-- CỘT TRÁI: HÓA ĐƠN ĐIỆN TỬ DỊCH VỤ -->
                <div class="invoice-box" style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 22px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between;">
                    
                    <div>
                        <!-- Header Hóa đơn -->
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1.5px solid #E2E8F0; padding-bottom: 14px; margin-bottom: 16px;">
                            <div>
                                <div style="font-size: 0.78rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Hóa đơn bán hàng #</div>
                                <strong style="color: #0066CC; font-size: 1.2rem; font-weight: 800;">#${orderCode}</strong>
                                <div style="font-size: 0.78rem; color: #94A3B8; margin-top: 2px;"><i data-lucide="clock" style="width:12px; height:12px; display:inline-block; vertical-align:middle;"></i> ${nowStr}</div>
                            </div>
                            <span style="background: #EFF6FF; color: #0284C7; border: 1px solid #BAE6FD; padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;">
                              <span style="width: 7px; height: 7px; border-radius: 50%; background: #0284C7;"></span> Chờ thanh toán
                            </span>
                        </div>

                        <!-- Thông tin khách hàng -->
                        <div style="background: #F8FAFC; border: 1px solid #F1F5F9; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px; font-size: 0.85rem; color: #334155; line-height: 1.6;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                              <span style="color: #64748B; font-weight: 600;">Khách hàng:</span>
                              <strong style="color: #0F172A;">${userName}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                              <span style="color: #64748B; font-weight: 600;">Email:</span>
                              <span style="color: #0F172A;">${userEmail}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                              <span style="color: #64748B; font-weight: 600;">Số điện thoại:</span>
                              <span style="color: #0F172A;">${userPhone}</span>
                            </div>
                        </div>

                        <!-- Bảng chi tiết dịch vụ -->
                        <div class="invoice-items" style="max-height: 200px; overflow-y: auto; margin-bottom: 16px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #E2E8F0; color: #64748B; text-align: left; font-size: 0.78rem; text-transform: uppercase;">
                                        <th style="padding: 8px 0; font-weight: 700;">Tên Dịch Vụ</th>
                                        <th style="text-align: center; padding: 8px 0; font-weight: 700; width: 45px;">SL</th>
                                        <th style="text-align: right; padding: 8px 0; font-weight: 700;">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${currentCartItems.map(item => `
                                        <tr style="border-bottom: 1px dashed #E2E8F0;">
                                            <td style="padding: 10px 0; vertical-align: middle;">
                                              <strong style="color: #0F172A; display: block;">${item.name}</strong>
                                              <span style="font-size: 0.75rem; color: #64748B;">Bản quyền 12 tháng</span>
                                            </td>
                                            <td style="text-align: center; font-weight: 700; color: #334155; vertical-align: middle;">x1</td>
                                            <td style="text-align: right; color: #0066CC; font-weight: 700; vertical-align: middle;">${new Intl.NumberFormat('vi-VN').format(item.price)} ₫</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tổng thanh toán -->
                    <div style="border-top: 2px solid #0066CC; padding-top: 14px; margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; font-size: 0.85rem; color: #64748B;">
                          <span>Tạm tính dịch vụ:</span>
                          <span>${formattedTotal}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.05rem; font-weight: 800; color: #0F172A; margin-top: 6px;">
                            <span>Tổng thanh toán:</span>
                            <span style="color: #E53E3E; font-size: 1.35rem; font-weight: 800;">${formattedTotal}</span>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: PHƯƠNG THỨC THANH TOÁN -->
                <div class="payment-methods-box" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 14px; color: #0F172A; display: flex; align-items: center; gap: 8px;">
                          <i data-lucide="credit-card" style="width:18px; height:18px; color:#0066CC;"></i> Phương thức thanh toán
                        </h4>
                        
                        <div class="payment-options" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                            <label class="pm-option" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1.5px solid #0066CC; border-radius: 12px; background: #F0F9FF; cursor: pointer; transition: 0.2s;">
                                <input type="radio" name="payment_method" value="banking" checked onchange="togglePaymentTab('banking')" style="accent-color:#0066CC; width:16px; height:16px;">
                                <i data-lucide="qr-code" style="color:#0066CC; width:20px; height:20px;"></i>
                                <span style="font-weight: 700; font-size: 0.9rem; color:#0F172A;">Chuyển khoản QR Banking</span>
                            </label>
                            <label class="pm-option" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; cursor: pointer; transition: 0.2s;">
                                <input type="radio" name="payment_method" value="momo" onchange="togglePaymentTab('momo')" style="accent-color:#0066CC; width:16px; height:16px;">
                                <i data-lucide="wallet" style="color:#A855F7; width:20px; height:20px;"></i>
                                <span style="font-weight: 700; font-size: 0.9rem; color:#0F172A;">Ví MoMo / VNPAY</span>
                            </label>
                            <label class="pm-option" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; cursor: pointer; transition: 0.2s;">
                                <input type="radio" name="payment_method" value="cod" onchange="togglePaymentTab('cod')" style="accent-color:#0066CC; width:16px; height:16px;">
                                <i data-lucide="truck" style="color:#16A34A; width:20px; height:20px;"></i>
                                <span style="font-weight: 700; font-size: 0.9rem; color:#0F172A;">Thanh toán sau (COD)</span>
                            </label>
                        </div>

                        <!-- Chi tiết QR Banking -->
                        <div id="pmDetailBanking" style="text-align: center; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 16px; box-shadow: 0 4px 14px rgba(0,102,204,0.06);">
                            <div style="font-size: 0.8rem; color: #475569; margin-bottom: 10px; font-weight: 600;">Quét mã VietQR bằng ứng dụng Ngân hàng:</div>
                            <img src="${qrUrl}" alt="Mã QR Thanh Toán" style="width: 150px; height: 150px; margin: 0 auto 10px; border-radius: 10px; border: 1px solid #CBD5E1; padding: 4px; background: white;">
                            <div style="font-size: 0.82rem; line-height: 1.5; color: #334155;">
                                <div>Ngân hàng: <strong>Vietcombank</strong></div>
                                <div>Số TK: <strong style="color:#0066CC; font-size:0.95rem;">1012345678</strong></div>
                                <div>Chủ TK: <strong>VNPT DIGITAL</strong></div>
                                <div style="background: #FEF3C7; border: 1px dashed #D97706; padding: 5px 8px; border-radius: 6px; margin-top: 6px; font-size: 0.82rem; color: #92400E;">Nội dung CK: <strong style="color:#B45309; font-size:0.9rem;">${orderCode}</strong></div>
                            </div>
                        </div>

                        <div id="pmDetailMomo" style="display:none; text-align: center; background: #FFFFFF; border: 1.5px solid #F0ABFC; border-radius: 14px; padding: 16px; font-size: 0.85rem;">
                            <p style="color: #475569; margin: 0 0 10px; font-weight: 600;">Quét mã Ví MoMo để hoàn tất thanh toán:</p>
                            <div style="width: 140px; height: 140px; background: #FDF4FF; border: 1px dashed #C084FC; margin: 0 auto 10px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #A855F7;">
                                <i data-lucide="qr-code" style="width:64px;height:64px;"></i>
                            </div>
                        </div>

                        <div id="pmDetailCod" style="display:none; text-align: center; background: #FFFFFF; border: 1.5px solid #BBF7D0; border-radius: 14px; padding: 16px; font-size: 0.85rem; color: #334155;">
                            <i data-lucide="check-circle-2" style="color: #16A34A; width: 36px; height: 36px; margin-bottom: 8px;"></i>
                            <p style="margin: 0; line-height: 1.5;">Kỹ thuật viên VNPT sẽ liên hệ xác nhận và bàn giao dịch vụ tận nơi.</p>
                        </div>
                    </div>

                    <button class="btn-primary full-width" id="btnConfirmPay" style="margin-top: 18px; height: 48px; border-radius: 12px; font-weight: 800; font-size: 1rem; justify-content: center; background: linear-gradient(135deg, #0066CC, #00AAFF); border: none; color: white; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(0,102,204,0.35); transition: 0.2s;" onclick="window.VNPTConfirmPayment('${orderCode}', '${formattedTotal}')">
                        <i data-lucide="shield-check" style="width:20px; height:20px;"></i>
                        <span>Xác nhận đã thanh toán</span>
                    </button>
                </div>
            </div>
        `;

        if (targetModal) {
            targetModal.style.display = 'flex';
            targetModal.classList.add('open');
        }
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        closeCart();
        if (window.lucide) lucide.createIcons();
    }

    window.togglePaymentTab = function(type) {
        const bankEl = document.getElementById('pmDetailBanking');
        const momoEl = document.getElementById('pmDetailMomo');
        const codEl = document.getElementById('pmDetailCod');
        if (bankEl) bankEl.style.display = type === 'banking' ? 'block' : 'none';
        if (momoEl) momoEl.style.display = type === 'momo' ? 'block' : 'none';
        if (codEl) codEl.style.display = type === 'cod' ? 'block' : 'none';
    };

    window.VNPTConfirmPayment = async function(orderCode, totalFormatted) {
        const user = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                     JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || '{}');
        const userEmail = user.email || 'lannguyen@gmail.com';
        const itemsToPay = (lastCheckoutItems && lastCheckoutItems.length) ? lastCheckoutItems : currentCartItems;
        const totalAmount = itemsToPay.reduce((sum, item) => sum + (item.price * (item.qty || 1)), 0);

        let isSuccess = false;
        let returnedOrderCode = orderCode;

        try {
            const baseUrl = (typeof window.getApiPath === 'function') ? window.getApiPath('backend/api/orders.php') : 'backend/api/orders.php';
            const ordersUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'action=create&t=' + Date.now();
            const res = await fetch(ordersUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'create',
                    email: userEmail,
                    ma_don_hang: orderCode,
                    items: itemsToPay,
                    totalMoney: totalAmount,
                    note: 'Thanh toán đơn hàng #' + orderCode
                })
            });
            const data = await res.json();
            if (res.ok && data && (data.status === 'success' || data.orderCode)) {
                isSuccess = true;
                if (data.orderCode) returnedOrderCode = data.orderCode;
            } else {
                safeShowToast('⚠️ ' + (data.message || data.error || 'Lỗi lưu đơn hàng vào CSDL'), true);
            }
        } catch (err) {
            safeShowToast('⚠️ Lỗi kết nối máy chủ: ' + err.message, true);
        }

        if (!isSuccess) return;

        markProductsAsPurchased(itemsToPay);

        // Xóa giỏ hàng sau khi đặt thành công
        localStorage.removeItem('vnpt_cart');
        currentCartItems = [];
        renderCart([]);

        if (checkoutModal) checkoutModal.classList.remove('open');

        const successInfo = document.getElementById('paymentSuccessInfo');
        if (successInfo) {
            successInfo.innerHTML = `
                <div><strong>Mã đơn hàng:</strong> <span style="color:#0066CC;">#${returnedOrderCode}</span></div>
                <div><strong>Tổng thanh toán:</strong> <span style="color:#CC3300; font-weight:700;">${totalFormatted}</span></div>
                <div><strong>Trạng thái:</strong> <span style="color:#00AA55; font-weight:600;">Đã ghi nhận thanh toán</span></div>
                <div><strong>Thời gian:</strong> ${new Date().toLocaleString('vi-VN')}</div>
            `;
        }

        if (paymentSuccessModal) paymentSuccessModal.classList.add('open');
        showToast('🎉 Thanh toán thành công! Mã đơn: #' + returnedOrderCode);

        // Báo cho giao diện "Đơn hàng của tôi" tự động nạp lại dữ liệu đơn hàng mới từ MySQL
        document.dispatchEvent(new CustomEvent('vnpt:ordercreated'));
    };

    function resetCart() {
        try {
            localStorage.removeItem('vnpt_cart');
            sessionStorage.removeItem('vnpt_cart');
        } catch (_e) {}
        currentCartItems = [];
        renderCart([]);
        if (cartBadge) {
            cartBadge.textContent = '0';
            cartBadge.style.display = 'none';
        }
    }

    document.addEventListener('vnpt:authchange', function() {
        const user = window.VNPTAuth ? window.VNPTAuth.getCurrentUser() : null;
        if (!user) {
            resetCart();
        } else {
            fetchCart();
        }
    });

    window.VNPTCart = { fetchCart, resetCart, openCart, closeCart };

    fetchCart();
});