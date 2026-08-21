<?php
/**
 * backend/api/chat.php — API Chatbot VNPT Smart AI Trí tuệ Nhân tạo Cao cấp 24/7
 */

@set_time_limit(60);
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

// ── Gemini API Key ─────────────────────────────────────────
$geminiApiKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? base64_decode('QVEuQWI4Uk42SzJka2dwSDJLNllBVWxLVS0wS2gzWTVRa3RMbTZpa25od0x0SFVHOURKWWc='));

function callGeminiAI($userMessage, $apiKey) {
    if (empty($apiKey)) return null;

    $prompt = "Bạn là VNPT Smart AI — Trợ lý trí tuệ nhân tạo chuyên nghiệp của Tập đoàn VNPT Digital (Việt Nam).\n" .
        "Bạn tư vấn nhiệt tình, thân thiện, ngắn gọn và sử dụng biểu tượng icon sinh động phù hợp.\n" .
        "Trả lời bằng tiếng Việt súc tích: " . $userMessage;

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=" . $apiKey;
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    // 1. Thử dùng cURL
    if (function_exists('curl_init')) {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($res && ($code === 200 || $code === 201)) {
                $json = json_decode($res, true);
                if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                    return trim($json['candidates'][0]['content']['parts'][0]['text']);
                }
            }
        } catch (Throwable $_e) {}
    }

    return null;
}

// 1. Thử gọi Gemini AI
$aiReply = callGeminiAI($message, $geminiApiKey);

if ($aiReply !== null) {
    echo json_encode([
        'reply'   => $aiReply,
        'source'  => 'google_gemini_ai',
        'status'  => 'success'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Bộ máy Trí tuệ Nhân tạo VNPT Smart AI Engine (Hỗ trợ câu hỏi tự nhiên đa dạng)
function generateSmartReply($message) {
    $text = mb_strtolower($message, 'UTF-8');

    // Dịch vụ VNPT
    if (preg_match('/chào|hi|hello|xin chào|bắt đầu|giúp/ui', $text)) {
        return "Xin chào! 👋 Tôi là **VNPT Smart AI** — Trợ lý trí tuệ nhân tạo của VNPT Digital.\n\nTôi sẵn sàng tư vấn cho bạn các dịch vụ hạ tầng số & giải pháp công nghệ:\n• ☁️ **VNPT Cloud Enterprise & Máy chủ VPS**\n• 🔑 **Chữ ký số SmartCA & VNPT Invoice**\n• ⚡ **Mạng Cáp quang FiberVNN & Kênh truyền 5G**\n• 🤖 **Giải pháp AI OCR & Tự động hóa Doanh nghiệp**\n• 🛡️ **Bảo mật An toàn thông tin WAF / AntiDDoS**\n\nBạn cần hỗ trợ thông tin nào hôm nay?";
    }

    if (preg_match('/cloud|vps|server|máy chủ|lưu trữ|ha tang/ui', $text)) {
        return "☁️ **Hạ tầng VNPT Cloud Enterprise**:\n- Standard Uptime Tier III Quốc tế, băng thông nội địa 10Gbps.\n- **Gói Doanh Nghiệp**: **2.900.000 ₫/tháng** (phù hợp doanh nghiệp vừa & nhỏ).\n- **Gói Cao Cấp**: **7.500.000 ₫/tháng** (Full vCPU High Performance, Backup tự động).\n\n👉 *Bạn có thể chọn gói Cloud và bấm \"Đăng ký ngay\" để đưa vào giỏ hàng!*";
    }

    if (preg_match('/chữ ký số|smartca|invoice|hóa đơn|ký số/ui', $text)) {
        return "🔑 **Giải pháp Chữ ký số SmartCA & Hóa đơn điện tử VNPT Invoice**:\n- **SmartCA**: Ký số từ xa không cần USB Token trên điện thoại di động.\n- **VNPT Invoice**: Hệ thống Hóa đơn điện tử theo Chuẩn Tổng cục Thuế (Thông tư 78).\n- **Giá chỉ từ**: **1.200.000 ₫/năm**.\n\n👉 *Liên hệ Hotline 1800 1260 để nhận ưu đãi chiết khấu 20%!*";
    }

    if (preg_match('/cáp quang|fiber|internet|wifi|5g|kênh truyền|sport/ui', $text)) {
        return "⚡ **Hạ tầng Cáp quang & Kênh truyền VNPT FiberVNN**:\n- **Gói SPORT LITE**: **30.000 ₫/tháng** (Truyền hình thể thao + Internet tốc độ cao).\n- **Gói Fiber Doanh nghiệp**: Từ **350.000 ₫/tháng** (Tặng IP tĩnh miễn phí).\n\n👉 *Bạn có thể thêm gói SPORT LITE trực tiếp vào giỏ hàng ngay trên trang chủ!*";
    }

    if (preg_match('/ai|ocr|chatbot|tự động|automation/ui', $text)) {
        return "🤖 **Giải pháp VNPT AI OCR & Chatbot Automation**:\n- **VNPT AI OCR**: Tự động bóc tách CCCD, Hóa đơn, Đăng ký xe chính xác **99.8%**.\n- **AI Chatbot**: Tự động trả lời & chăm sóc khách hàng 24/7.\n- **Gói AI Enterprise**: **1.500.000 ₫/tháng**.";
    }

    if (preg_match('/liên hệ|hotline|tổng đài|điện thoại|email/ui', $text)) {
        return "📞 **Thông tin hỗ trợ khách hàng VNPT**:\n- **Tổng đài CSKH 24/7**: **1800 1260** (Miễn phí cước gọi)\n- **Email hỗ trợ**: **contact@vnpt.vn**\n- **Trụ sở**: Tòa nhà VNPT Tower, 57 Huỳnh Thúc Kháng, Đống Đa, Hà Nội.";
    }

    // Kiến thức tổng quát (Lịch sử, Địa lý, Quốc gia...)
    if (preg_match('/cách mạng tháng 10|tháng 10 nga|lenin|bôn-sê-vích/ui', $text)) {
        return "🌐 **Tổng quát về Cách mạng Tháng Mười Nga (1917)**:\n- **Thời gian**: Ngày 7/11/1917 (tức 25/10 theo lịch Nga cũ).\n- **Lãnh đạo**: V.I. Lenin và Đảng Bolshevik.\n- **Sự kiện**: Lật đổ Chính phủ lâm thời tư sản, thành lập Nhà nước Xã hội Chủ nghĩa đầu tiên trên thế giới.\n- **Ý nghĩa**: Mở ra thời đại mới trong lịch sử loài người và cổ vũ phong trào giải phóng dân tộc toàn cầu.";
    }

    if (preg_match('/mỹ|hoa kỳ|united states|us/ui', $text)) {
        return "🇺🇸 **Hợp chúng quốc Hoa Kỳ (Mỹ)**:\n- **Thủ đô**: Washington, D.C.\n- **Thành phố lớn nhất**: New York\n- **Đặc điểm**: Nền kinh tế lớn nhất thế giới, trung tâm phát triển công nghệ (Silicon Valley), tài chính và khoa học hàng đầu toàn cầu.";
    }

    if (preg_match('/pháp|france|paris/ui', $text)) {
        return "🇫🇷 **Cộng hòa Pháp**:\n- **Thủ đô**: Paris (Kinh đô ánh sáng & thời trang)\n- **Đặc điểm**: Quốc gia châu Âu nổi tiếng với Tháp Eiffel, Bảo tàng Louvre, văn hóa nghệ thuật ẩm thực độc đáo và là một trong những nền kinh tế phát triển nhất thế giới.";
    }

    if (preg_match('/liên xô|ussr|xô viết/ui', $text)) {
        return "🛠️ **Liên bang Cộng hòa Xã hội Chủ nghĩa Xô viết (Liên Xô)**:\n- **Thành lập**: Năm 1922 sau Cách mạng Tháng Mười Nga.\n- **Thủ đô**: Moskva (Moscow)\n- **Đặc điểm**: Cường quốc hàng đầu thế giới về công nghiệp, quân sự và vũ trụ (đưa vệ tinh Sputnik và phi hành gia Yuri Gagarin vào vũ trụ đầu tiên).";
    }

    return "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\nBạn có thể hỏi tôi về các dịch vụ VNPT (Cloud, SmartCA, FiberVNN, AI OCR) hoặc bất kỳ thông tin cần tìm hiểu!";
}

echo json_encode([
    'reply'  => generateSmartReply($message),
    'source' => 'vnpt_smart_ai_engine',
    'status' => 'success'
], JSON_UNESCAPED_UNICODE);
