<?php
/**
 * backend/api/chat.php — API Chatbot VNPT Smart AI Tích hợp Google Gemini AI (Diagnostic & Fail-Safe)
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
$userProvidedKey = base64_decode('QVEuQWI4Uk42SzJka2dwSDJLNllBVWxLVS0wS2gzWTVRa3RMbTZpa25od0x0SFVHOURKWWc=');
$envKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? '');

$geminiApiKey = (!empty($envKey) && strlen($envKey) > 20) ? trim($envKey) : $userProvidedKey;

function callGeminiAI($userMessage, $apiKey, &$debugInfo = null) {
    if (empty($apiKey)) {
        $debugInfo = "API Key empty";
        return null;
    }

    $prompt = "Bạn là VNPT Smart AI — Trợ lý trí tuệ nhân tạo chuyên nghiệp của Tập đoàn VNPT Digital (Việt Nam).\n" .
        "Bạn tư vấn nhiệt tình, thân thiện, ngắn gọn và sử dụng các biểu tượng icon sinh động phù hợp.\n" .
        "Trả lời câu hỏi người dùng bằng tiếng Việt: " . $userMessage;

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
            curl_setopt($ch, CURLOPT_TIMEOUT, 40);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($res && ($code === 200 || $code === 201)) {
                $json = json_decode($res, true);
                if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                    return trim($json['candidates'][0]['content']['parts'][0]['text']);
                }
            }
            $debugInfo = "cURL Code $code | Err: $curlErr | Res: " . substr((string)$res, 0, 150);
        } catch (Throwable $exCurl) {
            $debugInfo = "cURL Exception: " . $exCurl->getMessage();
        }
    } else {
        $debugInfo = "curl_init missing";
    }

    // 2. Thử dùng stream context
    try {
        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $jsonPayload,
                'timeout' => 40,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ];
        $context = stream_context_create($opts);
        $res = @file_get_contents($url, false, $context);
        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($json['candidates'][0]['content']['parts'][0]['text']);
            }
            $debugInfo .= " | Stream Res: " . substr((string)$res, 0, 150);
        } else {
            $debugInfo .= " | Stream empty";
        }
    } catch (Throwable $exStream) {
        $debugInfo .= " | Stream Exception: " . $exStream->getMessage();
    }

    return null;
}

// ── Gọi Gemini AI Trực tiếp ────────────────────────────────
$debugLog = '';
$aiReply = callGeminiAI($message, $geminiApiKey, $debugLog);

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
    $reply = "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\n*(Ghi chú chẩn đoán kết nối AI: {$debugLog})*";
}

echo json_encode([
    'reply'  => $reply,
    'source' => 'local_fallback',
    'status' => 'success',
    'debug'  => $debugLog
], JSON_UNESCAPED_UNICODE);
