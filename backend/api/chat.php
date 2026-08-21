<?php
/**
 * backend/api/chat.php — API Chatbot VNPT Smart AI Tích hợp Google Gemini AI Trực tiếp (Mô hình Gemini 3.6 Flash & 2.5 Flash)
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

    // Các mô hình Gemini chuẩn mới nhất của Google
    $models = [
        'gemini-3.6-flash',
        'gemini-2.5-flash'
    ];

    foreach ($models as $m) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $m . ":generateContent?key=" . urlencode($apiKey);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res && ($code === 200 || $code === 201)) {
            $json = json_decode($res, true);
            if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($json['candidates'][0]['content']['parts'][0]['text']);
            }
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
} else {
    $reply = "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\nBạn có thể hỏi tôi chi tiết hơn về các dịch vụ số VNPT Digital!";
}

echo json_encode([
    'reply'  => $reply,
    'source' => 'local_fallback',
    'status' => 'success'
], JSON_UNESCAPED_UNICODE);
