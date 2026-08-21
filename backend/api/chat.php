<?php
/**
 * backend/api/chat.php — API Chatbot VNPT Smart AI Tích hợp Google Gemini AI Trực tiếp
 */

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

    $systemInstruction = "Bạn là VNPT Smart AI — Trợ lý trí tuệ nhân tạo chuyên nghiệp của Tập đoàn VNPT Digital (Việt Nam).\n" .
        "Bạn tư vấn nhiệt tình, thân thiện, súc tích, chuyên nghiệp và sử dụng các biểu tượng icon sinh động.\n" .
        "Thông tin cốt lõi về dịch vụ VNPT:\n" .
        "- Cáp quang FiberVNN & Internet 5G: Gói SPORT LITE 30.000đ/tháng, Fiber Doanh nghiệp 350.000đ/tháng.\n" .
        "- VNPT Cloud & Server: Chuẩn Uptime Tier III, Gói Doanh Nghiệp 2.900.000đ/tháng, Gói Cao Cấp 7.500.000đ/tháng.\n" .
        "- Chữ ký số SmartCA & Hóa đơn điện tử VNPT Invoice: Ký từ xa trên điện thoại, giá từ 1.200.000đ/năm.\n" .
        "- VNPT AI OCR & Chatbot: Bóc tách CCCD/Hóa đơn chính xác 99.8%, Gói Enterprise 1.500.000đ/tháng.\n" .
        "- Tổng đài hỗ trợ CSKH 24/7: 1800 1260 | Email: contact@vnpt.vn | Trụ sở: VNPT Tower, 57 Huỳnh Thúc Kháng, Hà Nội.";

    $payload = [
        'system_instruction' => [
            'parts' => [
                ['text' => $systemInstruction]
            ]
        ],
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $userMessage]
                ]
            ]
        ]
    ];

    // 1. Thử gọi mô hình gemini-1.5-flash
    $url1 = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($apiKey);
    $ch1 = curl_init($url1);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch1, CURLOPT_POST, true);
    curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch1, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);

    $res1 = curl_exec($ch1);
    $code1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
    curl_close($ch1);

    if ($res1 && ($code1 === 200 || $code1 === 201)) {
        $json = json_decode($res1, true);
        if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($json['candidates'][0]['content']['parts'][0]['text']);
        }
    }

    // 2. Thử gọi mô hình gemini-2.0-flash
    $url2 = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . urlencode($apiKey);
    $ch2 = curl_init($url2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch2, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);

    $res2 = curl_exec($ch2);
    $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    if ($res2 && ($code2 === 200 || $code2 === 201)) {
        $json2 = json_decode($res2, true);
        if (!empty($json2['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($json2['candidates'][0]['content']['parts'][0]['text']);
        }
    }

    return null;
}

// ── Gọi Gemini AI Trực tiếp ────────────────────────────────
$aiReply = callGeminiAI($message, $geminiApiKey);

if ($aiReply !== null) {
    echo json_encode([
        'reply'   => $aiReply,
        'source'  => 'google_gemini_ai',
        'status'  => 'success'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Dự phòng nếu không kết nối được API Gemini ─────────────
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
} else {
    $reply = "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\nBạn có thể hỏi tôi chi tiết hơn về:\n- ☁️ *Báo giá gói cước VNPT Cloud Enterprise*\n- 🔑 *Cách đăng ký Chữ ký số SmartCA*\n- ⚡ *Tốc độ cáp quang Internet FiberVNN*\n- 📞 *Hotline hỗ trợ kỹ thuật 1800 1260*";
}

echo json_encode([
    'reply'  => $reply,
    'source' => 'local_fallback',
    'status' => 'success'
], JSON_UNESCAPED_UNICODE);
