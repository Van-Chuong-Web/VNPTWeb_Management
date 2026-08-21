<?php
/**
 * backend/api/chat.php — API Chatbot VNPT Smart AI Trí tuệ Nhân tạo Đa năng & Động (Live Dynamic AI Engine)
 */

@set_time_limit(30);
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];
$message = isset($data['message']) ? trim($data['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Thiếu nội dung tin nhắn.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Hàm tra cứu Tri thức Động Thời gian thực (Dynamic Knowledge Engine) ─────
function fetchDynamicKnowledge($userQuery) {
    $cleanQuery = trim($userQuery);
    if (empty($cleanQuery)) return null;

    // Loại bỏ các từ thừa phổ biến để lấy từ khóa cốt lõi
    $keywords = preg_replace('/^(tổng quát về|thông tin về|cho tôi biết về|là gì|tìm hiểu về|giới thiệu về|hỏi về|chi tiết về|câu hỏi về)\s+/ui', '', $cleanQuery);
    $keywords = trim($keywords);

    $queriesToTry = array_unique(array_filter([$keywords, $cleanQuery]));

    foreach ($queriesToTry as $q) {
        $url = "https://vi.wikipedia.org/api/rest_v1/page/summary/" . rawurlencode($q);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_USERAGENT, 'VNPT_Smart_AI/2.0 (https://vnpt.vn)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res && $code === 200) {
            $json = json_decode($res, true);
            if (!empty($json['extract'])) {
                $title = $json['title'] ?? $q;
                $extract = trim($json['extract']);
                return "🤖 **VNPT Smart AI — Tri thức Tổng quát ({$title})**:\n\n{$extract}\n\n👉 *Bạn có cần tư vấn thêm dịch vụ Cáp quang, Cloud hay Chữ ký số của VNPT không?*";
            }
        }
    }

    return null;
}

// ── Bộ máy Xử lý Ngôn ngữ VNPT Smart AI Engine ──────────────────────────────
function getVNPTOrSmartReply($message) {
    $text = mb_strtolower($message, 'UTF-8');

    // 1. Chào hỏi & Hỗ trợ chung
    if (preg_match('/chào|hi|hello|xin chào|bắt đầu|giúp/ui', $text)) {
        return "Xin chào! 👋 Tôi là **VNPT Smart AI** — Trợ lý trí tuệ nhân tạo của VNPT Digital.\n\nTôi sẵn sàng tư vấn cho bạn các dịch vụ hạ tầng số & giải pháp công nghệ:\n• ☁️ **VNPT Cloud Enterprise & Máy chủ VPS**\n• 🔑 **Chữ ký số SmartCA & VNPT Invoice**\n• ⚡ **Mạng Cáp quang FiberVNN & Kênh truyền 5G**\n• 🤖 **Giải pháp AI OCR & Tự động hóa Doanh nghiệp**\n• 🛡️ **Bảo mật An toàn thông tin WAF / AntiDDoS**\n\nBạn cần hỗ trợ thông tin nào hôm nay?";
    }

    // 2. Dịch vụ VNPT Cloud
    if (preg_match('/cloud|vps|server|máy chủ|lưu trữ|ha tang/ui', $text)) {
        return "☁️ **Hạ tầng VNPT Cloud Enterprise**:\n- Standard Uptime Tier III Quốc tế, băng thông nội địa 10Gbps.\n- **Gói Doanh Nghiệp**: **2.900.000 ₫/tháng** (phù hợp doanh nghiệp vừa & nhỏ).\n- **Gói Cao Cấp**: **7.500.000 ₫/tháng** (Full vCPU High Performance, Backup tự động).\n\n👉 *Bạn có thể chọn gói Cloud và bấm \"Đăng ký ngay\" để đưa vào giỏ hàng!*";
    }

    // 3. Chữ ký số SmartCA & VNPT Invoice
    if (preg_match('/chữ ký số|smartca|invoice|hóa đơn|ký số/ui', $text)) {
        return "🔑 **Giải pháp Chữ ký số SmartCA & Hóa đơn điện tử VNPT Invoice**:\n- **SmartCA**: Ký số từ xa không cần USB Token trên điện thoại di động.\n- **VNPT Invoice**: Hệ thống Hóa đơn điện tử theo Chuẩn Tổng cục Thuế (Thông tư 78).\n- **Giá chỉ từ**: **1.200.000 ₫/năm**.\n\n👉 *Liên hệ Hotline 1800 1260 để nhận ưu đãi chiết khấu 20%!*";
    }

    // 4. Cáp quang FiberVNN & 5G
    if (preg_match('/cáp quang|fiber|internet|wifi|5g|kênh truyền|sport/ui', $text)) {
        return "⚡ **Hạ tầng Cáp quang & Kênh truyền VNPT FiberVNN**:\n- **Gói SPORT LITE**: **30.000 ₫/tháng** (Truyền hình thể thao + Internet tốc độ cao).\n- **Gói Fiber Doanh nghiệp**: Từ **350.000 ₫/tháng** (Tặng IP tĩnh miễn phí).\n\n👉 *Bạn có thể thêm gói SPORT LITE trực tiếp vào giỏ hàng ngay trên trang chủ!*";
    }

    // 5. AI OCR & Automation
    if (preg_match('/ai|ocr|chatbot|tự động|automation/ui', $text)) {
        return "🤖 **Giải pháp VNPT AI OCR & Chatbot Automation**:\n- **VNPT AI OCR**: Tự động bóc tách CCCD, Hóa đơn, Đăng ký xe chính xác **99.8%**.\n- **AI Chatbot**: Tự động trả lời & chăm sóc khách hàng 24/7.\n- **Gói AI Enterprise**: **1.500.000 ₫/tháng**.";
    }

    // 6. Thông tin liên hệ
    if (preg_match('/liên hệ|hotline|tổng đài|điện thoại|email/ui', $text)) {
        return "📞 **Thông tin hỗ trợ khách hàng VNPT**:\n- **Tổng đài CSKH 24/7**: **1800 1260** (Miễn phí cước gọi)\n- **Email hỗ trợ**: **contact@vnpt.vn**\n- **Trụ sở**: Tòa nhà VNPT Tower, 57 Huỳnh Thúc Kháng, Đống Đa, Hà Nội.";
    }

    // 7. Tra cứu Tri thức Động Thời gian thực (Hỗ trợ MỌI câu hỏi như Viettel, Mỹ, Pháp, Lịch sử, Khoa học...)
    $dynamicReply = fetchDynamicKnowledge($message);
    if ($dynamicReply !== null) {
        return $dynamicReply;
    }

    // 8. Phản hồi mặc định thân thiện
    return "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được yêu cầu: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\nTôi có thể hỗ trợ bạn tìm hiểu về các dịch vụ số VNPT (Cloud, SmartCA, FiberVNN, AI OCR) hoặc giải đáp các thắc mắc chung!";
}

$reply = getVNPTOrSmartReply($message);

echo json_encode([
    'reply'  => $reply,
    'source' => 'vnpt_dynamic_ai_engine',
    'status' => 'success'
], JSON_UNESCAPED_UNICODE);
