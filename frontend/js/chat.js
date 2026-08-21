document.addEventListener('DOMContentLoaded', () => {

  const toggleBtn   = document.getElementById('chatbotToggle');
  const closeBtn     = document.getElementById('chatClose');
  const windowEl     = document.getElementById('chatbotWindow');
  const messagesEl   = document.getElementById('chatMessages');
  const inputEl      = document.getElementById('chatInput');
  const sendBtn      = document.getElementById('chatSend');
  const typingEl     = document.getElementById('chatTyping');
  const badgeEl      = toggleBtn?.querySelector('.chat-badge');
  const openIcon     = toggleBtn?.querySelector('.open-icon');
  const closeIcon    = toggleBtn?.querySelector('.close-icon');
  const quickReplies = document.getElementById('quickReplies');

  if (!toggleBtn || !windowEl) return;

  let isOpen = false;
  let isSending = false;

  // Lịch sử hội thoại gửi kèm mỗi request để AI hiểu ngữ cảnh (giữ tối đa 20 tin gần nhất)
  const history = [];
  const MAX_HISTORY = 20;

  // Frontend (PHP) và API Node.js thường chạy trên 2 cổng khác nhau (xem giải
  // thích chi tiết trong api.js) — tự nhận diện đúng cổng của API thay vì hard-code.
  const API_BASE = (window.location.port === '3000')
    ? '' : `${window.location.protocol}//${window.location.hostname}:3000`;
  const API_ENDPOINT = API_BASE + '/api/chat';

  function nowLabel() {
    return new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
  }

  function scrollToBottom() {
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  /* Chuyển markdown đơn giản (từ Gemini) sang HTML an toàn: **bold**, *italic*, xuống dòng, gạch đầu dòng */
  function renderMarkdownLite(rawText) {
    const escapeHtml = (s) => s
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    let text = escapeHtml(rawText);

    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/(?:^|\n)[\-\*]\s+(.+)/g, '\n• $1');

    const paragraphs = text.split(/\n{2,}/).map(p => p.trim()).filter(Boolean);
    if (paragraphs.length === 0) return `<p>${text.replace(/\n/g, '<br>')}</p>`;

    return paragraphs.map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`).join('');
  }

  function addUserMessage(text) {
    const msg = document.createElement('div');
    msg.className = 'msg user-msg';
    msg.innerHTML = `
      <div class="msg-bubble"><p></p></div>
      <span class="msg-time">${nowLabel()}</span>
    `;
    msg.querySelector('p').textContent = text;
    messagesEl.appendChild(msg);
    scrollToBottom();
  }

  function addBotMessage(text) {
    const msg = document.createElement('div');
    msg.className = 'msg bot-msg';
    msg.innerHTML = `
      <div class="msg-avatar">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
          <circle cx="14" cy="14" r="14" fill="url(#msgGrad)"/>
          <path d="M7 14 L14 7 L21 14 L14 21 Z" fill="white" opacity="0.9"/>
          <circle cx="14" cy="14" r="4" fill="white"/>
        </svg>
      </div>
      <div class="msg-bubble"></div>
      <span class="msg-time">${nowLabel()}</span>
    `;
    msg.querySelector('.msg-bubble').innerHTML = renderMarkdownLite(text);
    messagesEl.appendChild(msg);
    scrollToBottom();
  }

  function addErrorMessage(text) {
    const msg = document.createElement('div');
    msg.className = 'msg bot-msg';
    msg.innerHTML = `
      <div class="msg-avatar">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
          <circle cx="14" cy="14" r="14" fill="#e63946"/>
          <path d="M14 8v6M14 18h.01" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="msg-bubble"><p></p></div>
      <span class="msg-time">${nowLabel()}</span>
    `;
    msg.querySelector('p').textContent = text;
    messagesEl.appendChild(msg);
    scrollToBottom();
  }

  function showTyping() {
    if (typingEl) typingEl.style.display = 'flex';
    scrollToBottom();
  }
  function hideTyping() {
    if (typingEl) typingEl.style.display = 'none';
  }

  function setSendingState(sending) {
    isSending = sending;
    if (sendBtn) sendBtn.disabled = sending;
    if (inputEl) inputEl.disabled = sending;
  }

  function getSmartLocalAIReply(message) {
    const text = (message || '').toLowerCase();

    if (/chào|hi|hello|xin chào|bắt đầu/i.test(text)) {
      return `Xin chào! 👋 Tôi là **VNPT Smart AI** — Trợ lý trí tuệ nhân tạo của VNPT Digital.\n\nTôi có thể giúp bạn giải đáp các vấn đề về:\n• ☁️ **Hạ tầng VNPT Cloud & Server**\n• 🔑 **Chữ ký số SmartCA & Hóa đơn điện tử Invoice**\n• ⚡ **Mạng Cáp quang FiberVNN & Kênh truyền 5G**\n• 🤖 **Giải pháp AI OCR & Tự động hóa Doanh nghiệp**\n• 🛡️ **Bảo mật An toàn thông tin (WAF / AntiDDoS)**\n\nBạn muốn tìm hiểu dịch vụ nào hôm nay?`;
    }

    if (/cloud|vps|server|máy chủ|lưu trữ|ha tang|doanh nghiệp/i.test(text)) {
      return `☁️ **Hạ tầng VNPT Cloud Enterprise**:\n- **Đặc điểm**: Đạt tiêu chuẩn Uptime Tier III Quốc tế, băng thông nội địa 10Gbps.\n- **Gói Cao Cấp**: **7.500.000 ₫/tháng** (Full vCPU High Performance, Backup tự động hàng ngày, Cam kết SLA 99.99%).\n- **Gói Doanh Nghiệp**: **2.900.000 ₫/tháng** phù hợp doanh nghiệp vừa và nhỏ.\n\n👉 *Bạn có thể chọn gói Cloud và bấm "Đăng ký ngay" để đưa vào giỏ hàng!*`;
    }

    if (/chữ ký số|smartca|invoice|hóa đơn|ký số|ca/i.test(text)) {
      return `🔑 **Giải pháp Chữ ký số SmartCA & VNPT Invoice**:\n- **SmartCA**: Ký số từ xa không cần USB Token trên Smartphone/Tablet mọi lúc mọi nơi.\n- **VNPT Invoice**: Hệ thống khởi tạo và phát hành Hóa đơn điện tử theo Chuẩn Tổng cục Thuế (Thông tư 78).\n- **Chi phí**: Từ **1.200.000 ₫/năm**.\n\n👉 *Hãy để lại thông tin tại form "Đăng ký tư vấn" hoặc gọi Hotline 1800 1260 để nhận ưu đãi chiết khấu 20%!*`;
    }

    if (/cáp quang|fiber|internet|wifi|5g|kênh truyền|băng thông|sport/i.test(text)) {
      return `⚡ **Hạ tầng Internet & Kênh Truyền VNPT FiberVNN**:\n- **Gói SPORT LITE**: **30.000 ₫/tháng** (Truyền hình thể thao + Internet tốc độ cao).\n- **Gói Fiber Doanh nghiệp**: Từ **350.000 ₫/tháng** (Cam kết băng thông quốc tế tối thiểu, IP Tĩnh miễn phí).\n- **Kết nối 5G / SD-WAN**: Kênh truyền bảo mật riêng biệt cho các chi nhánh ngân hàng, chuỗi cửa hàng.\n\n👉 *Bạn có thể thêm gói SPORT LITE trực tiếp vào giỏ hàng ngay trên trang chủ!*`;
    }

    if (/ai|ocr|chatbot|tự động|automation|nhận diện/i.test(text)) {
      return `🤖 **Giải pháp AI OCR & Chatbot Automation**:\n- **VNPT AI OCR**: Tự động bóc tách thông tin từ CCCD, Hộ chiếu, Đăng ký xe, Hóa đơn với độ chính xác **99.8%**.\n- **AI Chatbot**: Tự động trả lời và chăm sóc khách hàng 24/7 qua Website, Zalo OA, Facebook Messenger.\n- **Gói AI Enterprise**: **1.500.000 ₫/tháng**.\n\n👉 *Liên hệ kỹ sư VNPT để trải nghiệm demo AI miễn phí!*`;
    }

    if (/bảo mật|security|waf|dows|ddos|soc|an toàn/i.test(text)) {
      return `🛡️ **Bảo mật & An toàn thông tin VNPT Cyber Security**:\n- Tường lửa ứng dụng Web (WAF) chống tấn công SQL Injection, XSS.\n- Hệ thống lọc rửa lưu lượng chống tấn công từ chối dịch vụ Anti-DDoS lên tới 100Gbps.\n- Trung tâm giám sát An ninh mạng SOC hoạt động 24/7/365 với các chứng chỉ quốc tế ISO 27001.`;
    }

    if (/giá|bảng giá|chi phí|bao nhiêu|tốn bao nhiêu/i.test(text)) {
      return `💰 **Bảng giá tổng hợp các Dịch vụ số VNPT**:\n1. **SPORT LITE**: 30.000 ₫/tháng\n2. **Gói Doanh nghiệp**: 2.900.000 ₫/tháng\n3. **Gói Cao cấp Cloud**: 7.500.000 ₫/tháng\n4. **Chữ ký số SmartCA**: 1.200.000 ₫/năm\n\n👉 *Bạn hãy bấm vào mục "Đóng gói" trên thanh Menu để chọn cấu hình chi tiết!*`;
    }

    if (/đơn hàng|giỏ hàng|thanh toán|mua|checkout/i.test(text)) {
      return `🛒 **Hướng dẫn Thanh toán & Mua hàng**:\n1. Bạn chỉ cần chọn gói cước yêu thích và bấm nút **"Đăng ký ngay"**.\n2. Bấm vào biểu tượng **Giỏ hàng** góc trên bên phải để xem các dịch vụ đã chọn.\n3. Bấm **"Tiến hành thanh toán"** để nhận Mã Hóa đơn và quét mã VietQR tự động.`;
    }

    if (/liên hệ|hotline|tổng đài|điện thoại|địa chỉ|email|hỗ trợ/i.test(text)) {
      return `📞 **Thông tin liên hệ hỗ trợ khách hàng VNPT**:\n- **Tổng đài CSKH miễn cước 24/7**: **1800 1260**\n- **Email hỗ trợ**: **contact@vnpt.vn**\n- **Văn phòng**: Tòa nhà VNPT Tower, 57 Huỳnh Thúc Kháng, Đống Đa, Hà Nội.\n- **Form tư vấn**: Cuộn xuống cuối trang chủ để để lại yêu cầu gọi lại miễn phí.`;
    }

    return `Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *"${message.slice(0, 60)}"*.\n\nBạn có thể hỏi tôi chi tiết hơn về:\n- ☁️ *Báo giá gói cước VNPT Cloud Enterprise*\n- 🔑 *Cách đăng ký Chữ ký số SmartCA*\n- ⚡ *Tốc độ cáp quang Internet FiberVNN*\n- 📞 *Hotline hỗ trợ kỹ thuật 1800 1260*`;
  }

  async function fetchAIReply(userText) {
    // 1) Thử fetch tới Node.js API (Cổng 3000)
    try {
      const res = await fetch(API_ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userText, history }),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data?.reply) {
        return data.reply;
      }
    } catch (_err) {
      // Ignore node backend offline
    }

    // 2) Thử fetch tới PHP API (Cổng 8080 / Apache)
    try {
      const phpRes = await fetch('backend/api/chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userText, history }),
      });
      const phpData = await phpRes.json().catch(() => ({}));
      if (phpRes.ok && phpData?.reply) {
        return phpData.reply;
      }
    } catch (_err) {
      // Ignore php backend error
    }

    // 3) Fallback trực tiếp sang Smart AI Engine Javascript tại Frontend
    return getSmartLocalAIReply(userText);
  }

  async function sendMessage(text) {
    const trimmed = (text || '').trim();
    if (!trimmed || isSending) return;

    addUserMessage(trimmed);
    inputEl.value = '';
    if (quickReplies) quickReplies.style.display = 'none';

    history.push({ role: 'user', text: trimmed });
    if (history.length > MAX_HISTORY) history.splice(0, history.length - MAX_HISTORY);

    setSendingState(true);
    showTyping();

    try {
      const reply = await fetchAIReply(trimmed);
      hideTyping();
      addBotMessage(reply);
      history.push({ role: 'bot', text: reply });
      if (history.length > MAX_HISTORY) history.splice(0, history.length - MAX_HISTORY);
    } catch (err) {
      hideTyping();
      const localReply = getSmartLocalAIReply(trimmed);
      addBotMessage(localReply);
      history.push({ role: 'bot', text: localReply });
      if (history.length > MAX_HISTORY) history.splice(0, history.length - MAX_HISTORY);
    } finally {
      setSendingState(false);
      inputEl?.focus();
    }
  }

  /* ---- Toggle open/close ---- */
  function openChat() {
    isOpen = true;
    windowEl.classList.add('open');
    windowEl.style.display = 'flex';
    if (quickReplies && history.length === 0) quickReplies.style.display = 'flex';
    setTimeout(() => {
      windowEl.style.opacity = '1';
      windowEl.style.visibility = 'visible';
      windowEl.style.transform = 'translateY(0) scale(1)';
    }, 10);

    if (openIcon) openIcon.style.display = 'none';
    if (closeIcon) closeIcon.style.display = 'flex';
    if (badgeEl) badgeEl.style.display = 'none';
    if (inputEl) inputEl.focus();
    scrollToBottom();
  }

  function closeChat() {
    isOpen = false;
    windowEl.classList.remove('open');
    windowEl.style.opacity = '0';
    windowEl.style.visibility = 'hidden';
    windowEl.style.transform = 'translateY(16px) scale(0.96)';
    setTimeout(() => {
      if (!isOpen) windowEl.style.display = 'none';
    }, 280);

    if (openIcon) openIcon.style.display = 'flex';
    if (closeIcon) closeIcon.style.display = 'none';
  }

  toggleBtn.addEventListener('click', () => (isOpen ? closeChat() : openChat()));
  closeBtn?.addEventListener('click', closeChat);

  /* ---- External triggers (Trung tâm hỗ trợ) ---- */
  const extTrigger = document.getElementById('triggerLiveChatFromSupport');
  if (extTrigger) {
    extTrigger.addEventListener('click', () => {
      const supportModal = document.getElementById('supportCenterModal');
      if (supportModal) supportModal.style.display = 'none';
      const overlay = document.getElementById('modalOverlay');
      if (overlay) overlay.classList.remove('active');
      openChat();
    });
  }

  /* ---- Send message actions ---- */
  sendBtn?.addEventListener('click', () => sendMessage(inputEl.value));
  inputEl?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !isSending) sendMessage(inputEl.value);
  });

  /* ---- Quick reply buttons ---- */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.qr-btn');
    if (btn) {
      const msg = btn.getAttribute('data-query') || btn.getAttribute('data-msg') || btn.textContent;
      sendMessage(msg);
    }
  });

});
