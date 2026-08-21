<?php
header('Content-Type: application/json; charset=utf-8');

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];
$message = isset($data['message']) ? trim($data['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Thiếu nội dung tin nhắn.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$text = mb_strtolower($message, 'UTF-8');

if (preg_match('/chào|hi|hello|xin chào|bắt đầu/ui', $text)) {
    $reply = "Xin chào! 👋 Tôi là **VNPT Smart AI** — Trợ lý trí tuệ nhân tạo của VNPT Digital.\n\nTôi có thể giúp bạn giải đáp các vấn đề về:\n• ☁️ **Hạ tầng VNPT Cloud & Server**\n• 🔑 **Chữ ký số SmartCA & Hóa đơn điện tử Invoice**\n• ⚡ **Mạng Cáp quang FiberVNN & Kênh truyền 5G**\n• 🤖 **Giải pháp AI OCR & Tự động hóa Doanh nghiệp**\n• 🛡️ **Bảo mật An toàn thông tin (WAF / AntiDDoS)**\n\nBạn muốn tìm hiểu dịch vụ nào hôm nay?";
} elseif (preg_match('/cloud|vps|server|máy chủ|lưu trữ|ha tang|doanh nghiệp/ui', $text)) {
    $reply = "☁️ **Hạ tầng VNPT Cloud Enterprise**:\n- **Đặc điểm**: Đạt tiêu chuẩn Uptime Tier III Quốc tế, băng thông nội địa 10Gbps.\n- **Gói Cao Cấp**: **7.500.000 ₫/tháng** (Full vCPU High Performance, Backup tự động hàng ngày, Cam kết SLA 99.99%).\n- **Gói Doanh Nghiệp**: **2.900.000 ₫/tháng** phù hợp doanh nghiệp vừa và nhỏ.\n\n👉 *Bạn có thể chọn gói Cloud và bấm \"Đăng ký ngay\" để đưa vào giỏ hàng!*";
} elseif (preg_match('/chữ ký số|smartca|invoice|hóa đơn|ký số|ca/ui', $text)) {
    $reply = "🔑 **Giải pháp Chữ ký số SmartCA & VNPT Invoice**:\n- **SmartCA**: Ký số từ xa không cần USB Token trên Smartphone/Tablet mọi lúc mọi nơi.\n- **VNPT Invoice**: Hệ thống khởi tạo và phát hành Hóa đơn điện tử theo Chuẩn Tổng cục Thuế (Thông tư 78).\n- **Chi phí**: Từ **1.200.000 ₫/năm**.\n\n👉 *Hãy để lại thông tin tại form \"Đăng ký tư vấn\" hoặc gọi Hotline 1800 1260 để nhận ưu đãi chiết khấu 20%!*";
} elseif (preg_match('/cáp quang|fiber|internet|wifi|5g|kênh truyền|băng thông|sport/ui', $text)) {
    $reply = "⚡ **Hạ tầng Internet & Kênh Truyền VNPT FiberVNN**:\n- **Gói SPORT LITE**: **30.000 ₫/tháng** (Truyền hình thể thao + Internet tốc độ cao).\n- **Gói Fiber Doanh nghiệp**: Từ **350.000 ₫/tháng** (Cam kết băng thông quốc tế tối thiểu, IP Tĩnh miễn phí).\n- **Kết nối 5G / SD-WAN**: Kênh truyền bảo mật riêng biệt cho các chi nhánh ngân hàng, chuỗi cửa hàng.\n\n👉 *Bạn có thể thêm gói SPORT LITE trực tiếp vào giỏ hàng ngay trên trang chủ!*";
} elseif (preg_match('/ai|ocr|chatbot|tự động|automation|nhận diện/ui', $text)) {
    $reply = "🤖 **Giải pháp AI OCR & Chatbot Automation**:\n- **VNPT AI OCR**: Tự động bóc tách thông tin từ CCCD, Hộ chiếu, Đăng ký xe, Hóa đơn với độ chính xác **99.8%**.\n- **AI Chatbot**: Tự động trả lời và chăm sóc khách hàng 24/7 qua Website, Zalo OA, Facebook Messenger.\n- **Gói AI Enterprise**: **1.500.000 ₫/tháng**.\n\n👉 *Liên hệ kỹ sư VNPT để trải nghiệm demo AI miễn phí!*";
} elseif (preg_match('/bảo mật|security|waf|dows|ddos|soc|an toàn/ui', $text)) {
    $reply = "🛡️ **Bảo mật & An toàn thông tin VNPT Cyber Security**:\n- Tường lửa ứng dụng Web (WAF) chống tấn công SQL Injection, XSS.\n- Hệ thống lọc rửa lưu lượng chống tấn công từ chối dịch vụ Anti-DDoS lên tới 100Gbps.\n- Trung tâm giám sát An ninh mạng SOC hoạt động 24/7/365 với các chứng chỉ quốc tế ISO 27001.";
} elseif (preg_match('/giá|bảng giá|chi phí|bao nhiêu|tốn bao nhiêu/ui', $text)) {
    $reply = "💰 **Bảng giá tổng hợp các Dịch vụ số VNPT**:\n1. **SPORT LITE**: 30.000 ₫/tháng\n2. **Gói Doanh nghiệp**: 2.900.000 ₫/tháng\n3. **Gói Cao cấp Cloud**: 7.500.000 ₫/tháng\n4. **Chữ ký số SmartCA**: 1.200.000 ₫/năm\n\n👉 *Bạn hãy bấm vào mục \"Đóng gói\" trên thanh Menu để chọn cấu hình chi tiết!*";
} elseif (preg_match('/đơn hàng|giỏ hàng|thanh toán|mua|checkout/ui', $text)) {
    $reply = "🛒 **Hướng dẫn Thanh toán & Mua hàng**:\n1. Bạn chỉ cần chọn gói cước yêu thích và bấm nút **\"Đăng ký ngay\"**.\n2. Bấm vào biểu tượng **Giỏ hàng** góc trên bên phải để xem các dịch vụ đã chọn.\n3. Bấm **\"Tiến hành thanh toán\"** để nhận Mã Hóa đơn và quét mã VietQR tự động.";
} elseif (preg_match('/liên hệ|hotline|tổng đài|điện thoại|địa chỉ|email|hỗ trợ/ui', $text)) {
    $reply = "📞 **Thông tin liên hệ hỗ trợ khách hàng VNPT**:\n- **Tổng đài CSKH miễn cước 24/7**: **1800 1260**\n- **Email hỗ trợ**: **contact@vnpt.vn**\n- **Văn phòng**: Tòa nhà VNPT Tower, 57 Huỳnh Thúc Kháng, Đống Đa, Hà Nội.\n- **Form tư vấn**: Cuộn xuống cuối trang chủ để để lại yêu cầu gọi lại miễn phí.";
} else {
    $reply = "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\nBạn có thể hỏi tôi chi tiết hơn về:\n- ☁️ *Báo giá gói cước VNPT Cloud Enterprise*\n- 🔑 *Cách đăng ký Chữ ký số SmartCA*\n- ⚡ *Tốc độ cáp quang Internet FiberVNN*\n- 📞 *Hotline hỗ trợ kỹ thuật 1800 1260*";
}

echo json_encode(['reply' => $reply], JSON_UNESCAPED_UNICODE);
