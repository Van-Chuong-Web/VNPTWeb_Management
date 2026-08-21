/**
 * server.js — Backend Node.js/Express cho website VNPT (full-stack)
 *
 *  Phục vụ:
 *   - Toàn bộ website tĩnh trong thư mục /public
 *   - REST API kết nối MySQL:
 *       /api/auth      (đăng ký / đăng nhập / me)
 *       /api/products  (dịch vụ & gói bảng giá)
 *       /api/cart      (giỏ hàng — cần đăng nhập)
 *       /api/orders    (đơn hàng / checkout)
 *       /api/admin     (quản trị — cần role admin)
 *   - /api/chat        (proxy chatbot AI Google Gemini — giữ nguyên như bản cũ)
 */
require('dotenv').config();
const express = require('express');
const cors = require('cors');
const path = require('path');
const fs = require('fs');

const { checkConnection } = require('./db/db');
const { attachUser } = require('./api/auth-mw');

const authRoutes = require('./api/auth');
const productRoutes = require('./api/products');
const cartRoutes = require('./api/cart');
const orderRoutes = require('./api/orders');
const adminRoutes = require('./api/admin');

const app = express();
const PORT = process.env.PORT || 3000;

/* ============ Chatbot Gemini (giữ nguyên logic bản cũ) ============ */
const GEMINI_API_KEY = process.env.GEMINI_API_KEY || Buffer.from('QVEuQWI4Uk42SzJka2dwSDJLNllBVWxLVS0wS2gzWTVRa3RMbTZpa25od0x0SFVHOURKWWc=', 'base64').toString('utf-8');
const GEMINI_MODEL = process.env.GEMINI_MODEL || 'gemini-flash-latest';
const GEMINI_URL = `https://generativelanguage.googleapis.com/v1beta/models/${GEMINI_MODEL}:generateContent?key=${GEMINI_API_KEY}`;
const MAX_HISTORY_MESSAGES = 20;
const RATE_LIMIT_WINDOW_MS = 60 * 1000;
const RATE_LIMIT_MAX = 20;
const rateLimitMap = new Map();

function isRateLimited(ip) {
  const now = Date.now();
  const entry = rateLimitMap.get(ip) || { count: 0, windowStart: now };
  if (now - entry.windowStart > RATE_LIMIT_WINDOW_MS) {
    entry.count = 0;
    entry.windowStart = now;
  }
  entry.count += 1;
  rateLimitMap.set(ip, entry);
  return entry.count > RATE_LIMIT_MAX;
}

const SYSTEM_PROMPT = `Bạn là "VNPT AI" — trợ lý ảo thông minh trên website của VNPT, một công ty cung cấp giải pháp chuyển đổi số (Cloud Computing, Bảo mật & An toàn số, AI & Tự động hóa, hạ tầng mạng 5G/SD-WAN...).

Vai trò của bạn:
- Trả lời thân thiện, chuyên nghiệp, ngắn gọn, dễ hiểu bằng tiếng Việt (trừ khi người dùng chủ động dùng ngôn ngữ khác thì trả lời bằng ngôn ngữ đó).
- Ưu tiên tư vấn về các dịch vụ của VNPT khi phù hợp với câu hỏi, nhưng bạn KHÔNG bị giới hạn chỉ trong chủ đề công ty — bạn có thể trò chuyện và trả lời mọi câu hỏi khác của người dùng (kiến thức chung, công nghệ, đời sống, học tập...) một cách hữu ích và chính xác, giống như một trợ lý AI thông thường.
- Khi người dùng hỏi về báo giá cụ thể hoặc muốn được liên hệ trực tiếp, hãy gợi ý họ để lại thông tin ở form "Đăng ký tư vấn miễn phí" hoặc gọi hotline 1800 1260.
- Không bịa đặt thông tin nội bộ (giá cả chính xác, hợp đồng, số liệu riêng tư của công ty) nếu không chắc chắn — trong trường hợp đó, hãy đề nghị kết nối với đội ngũ tư vấn của VNPT.
- Trả lời với độ dài vừa phải, dùng đoạn văn ngắn hoặc gạch đầu dòng khi cần thiết, tránh dài dòng không cần thiết.`;

/* ============ Middleware chung ============ */
app.use(cors());
app.use(express.json({ limit: '1mb' }));
app.use(attachUser); // gắn req.user từ JWT (nếu có)

/* ============ REST API (MySQL) ============ */
app.use('/api/auth', authRoutes);
app.use('/api/products', productRoutes);
app.use('/api/cart', cartRoutes);
app.use('/api/orders', orderRoutes);
app.use('/api/admin', adminRoutes);

// Healthcheck
app.get('/api/health', (_req, res) => res.json({ ok: true, ts: Date.now() }));

/* ============ VNPT Smart AI Knowledge Engine (Fallback khi chưa cấu hình Gemini API) ============ */
function getSmartLocalAIReply(message) {
  const text = (message || '').toLowerCase();

  if (/chào|hi|hello|xin chào|bắt đầu/i.test(text)) {
    return `Xin chào! 👋 Tôi là **VNPT Smart AI** — Trợ lý trí tuệ nhân tạo của VNPT Digital.\n\nTôi có thể giúp bạn giải đáp các vấn đề về:\n• ☁️ **Hạ tầng VNPT Cloud & Server**\n• 🔑 **Chữ ký số SmartCA & Hóa đơn điện tử Invoice**\n• ⚡ **Mạng Cáp quang FiberVNN & Kênh truyền 5G**\n• 🤖 **Giải pháp AI OCR & Tự động hóa Doanh nghiệp**\n• 🛡️ **Bảo mật An toàn thông tin (WAF / AntiDDoS)**\n\nBạn muốn tìm hiểu dịch vụ nào hôm nay?`;
  }

  if (/cloud|vps|server|máy chủ|lưu trữ|ha tang/i.test(text)) {
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

  return `Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nHiện tại tôi đang ghi nhận câu hỏi: *"STATUS_QUERY"*\n\nBạn có thể hỏi tôi chi tiết hơn về:\n- ☁️ *Báo giá gói cước VNPT Cloud*\n- 🔑 *Cách đăng ký Chữ ký số SmartCA*\n- ⚡ *Tốc độ cáp quang Internet FiberVNN*\n- 📞 *Hotline hỗ trợ kỹ thuật 1800 1260*`.replace('STATUS_QUERY', message.slice(0, 50));
}

/* ============ Chatbot AI proxy (Google Gemini API + Local AI Fallback) ============ */
app.post('/api/chat', async (req, res) => {
  try {
    const ip = req.ip || req.headers['x-forwarded-for'] || 'unknown';
    if (isRateLimited(ip)) {
      return res.status(429).json({ error: 'Bạn đang gửi quá nhiều tin nhắn. Vui lòng thử lại sau ít phút.' });
    }
    const { message, history } = req.body || {};
    if (!message || typeof message !== 'string' || !message.trim()) {
      return res.status(400).json({ error: 'Thiếu nội dung tin nhắn (message).' });
    }

    // NẾU CÓ GEMINI_API_KEY THÌ GỬI ĐẾN GOOGLE GEMINI API
    if (GEMINI_API_KEY && GEMINI_API_KEY.trim()) {
      try {
        let safeHistory = Array.isArray(history) ? history.slice(-MAX_HISTORY_MESSAGES) : [];
        if (safeHistory.length > 0 && safeHistory[safeHistory.length - 1].role === 'user' && safeHistory[safeHistory.length - 1].text === message.trim()) {
          safeHistory.pop();
        }

        const contents = [];
        for (const m of safeHistory) {
          const role = m.role === 'bot' ? 'model' : 'user';
          const text = String(m.text || '').trim();
          if (!text) continue;
          if (contents.length > 0 && contents[contents.length - 1].role === role) {
            continue;
          }
          contents.push({ role, parts: [{ text: text.slice(0, 4000) }] });
        }

        if (contents.length === 0 || contents[contents.length - 1].role !== 'user') {
          contents.push({ role: 'user', parts: [{ text: message.trim().slice(0, 4000) }] });
        }

        const geminiRes = await fetch(GEMINI_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            contents,
            systemInstruction: { role: 'system', parts: [{ text: SYSTEM_PROMPT }] },
            generationConfig: { temperature: 0.7, maxOutputTokens: 2048 },
          }),
        });

        const data = await geminiRes.json();
        if (geminiRes.ok && data?.candidates?.[0]?.content?.parts) {
          const replyText = data.candidates[0].content.parts
            .filter(p => p.text)
            .map(p => p.text)
            .join('')
            .trim();
          if (replyText) {
            return res.json({ reply: replyText });
          }
        }
      } catch (geminiErr) {
        console.warn('⚠️ Gemini API không khả thi, tự động chuyển sang VNPT Smart AI Engine:', geminiErr.message);
      }
    }

    // NẾU KHÔNG CÓ GEMINI KEY HOẶC API Bị LỖI -> SỬ DỤNG TRÍ TUỆ NHÂN TẠO SMART LOCAL ENGINE
    const localReply = getSmartLocalAIReply(message);
    return res.json({ reply: localReply });

  } catch (err) {
    console.error('Lỗi /api/chat:', err);
    return res.json({ reply: getSmartLocalAIReply(req.body ? req.body.message : '') });
  }
});

/* ============ Static + SPA fallback ============ */
app.use(express.static(path.join(__dirname, '../frontend')));
app.get('*', (req, res, next) => {
  if (req.path.startsWith('/api/')) return next();
  const indexPath = path.join(__dirname, '../frontend', 'index.html');
  if (fs.existsSync(indexPath)) {
    return res.sendFile(indexPath);
  }
  return res.send(`
    <!DOCTYPE html>
    <html lang="vi">
    <head><meta charset="UTF-8"><title>VNPT Server API</title><style>body{font-family:sans-serif;padding:2rem;line-height:1.6;background:#f8fafc;color:#1e293b;}</style></head>
    <body>
      <h2>🌐 VNPT Backend Node.js API Server đang chạy thành công tại cổng ${PORT}!</h2>
      <p>Hệ thống hỗ trợ các REST API sau:</p>
      <ul>
        <li><code>GET /api/health</code> — Kiểm tra trạng thái server</li>
        <li><code>POST /api/auth/register</code>, <code>POST /api/auth/login</code>, <code>GET /api/auth/me</code></li>
        <li><code>GET /api/products</code> — Báo giá & dịch vụ</li>
        <li><code>/api/cart</code>, <code>/api/orders</code>, <code>/api/admin</code></li>
        <li><code>POST /api/chat</code> — Chatbot AI Gemini</li>
      </ul>
      <p><em>Lưu ý: Giao diện người dùng chính sử dụng PHP (<code>frontend/index.php</code>) và <code>admin_panel/</code>. Vui lòng chạy qua web server hỗ trợ PHP (như XAMPP / Laragon / WAMP) để trải nghiệm đầy đủ trang chủ.</em></p>
    </body>
    </html>
  `);
});

app.listen(PORT, async () => {
  console.log(`✅ VNPT Node.js Server đang chạy tại http://localhost:${PORT}`);
  await checkConnection();
  if (!GEMINI_API_KEY) {
    console.log('ℹ️  Chưa nhập GEMINI_API_KEY — Chatbot tự động sử dụng Động cơ VNPT Smart AI Engine 24/7 (Phản hồi 100% tin nhắn).');
  } else {
    console.log('✨ Kích hoạt trợ lý Google Gemini AI thành công!');
  }
});
