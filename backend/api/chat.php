<?php
/**
 * backend/api/chat.php — API Chatbot VNPT Smart AI Trí tuệ Nhân tạo Động Cao cấp (Smart Search + Generative AI Engine)
 */

@set_time_limit(45);
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

// ── 1. Thử kết nối Google Gemini AI API (nếu Key hợp lệ & có Quota) ─────────────
$geminiApiKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? base64_decode('QVEuQWI4Uk42SzJka2dwSDJLNllBVWxLVS0wS2gzWTVRa3RMbTZpa25od0x0SFVHOURKWWc='));

function callGeminiAI($userMessage, $apiKey) {
    if (empty($apiKey)) return null;

    $prompt = "Bạn là VNPT Smart AI — Trợ lý trí tuệ nhân tạo chuyên nghiệp của Tập đoàn VNPT Digital (Việt Nam).\n" .
        "Bạn tư vấn nhiệt tình, thân thiện, súc tích và sử dụng các biểu tượng icon sinh động phù hợp.\n" .
        "Trả lời bằng tiếng Việt: " . $userMessage;

    $payload = ['contents' => [['parts' => [['text' => $prompt]]]]];
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=" . $apiKey;
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    if (function_exists('curl_init')) {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
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

$aiDirectReply = callGeminiAI($message, $geminiApiKey);
if ($aiDirectReply !== null) {
    echo json_encode([
        'reply'  => $aiDirectReply,
        'source' => 'google_gemini_ai',
        'status' => 'success'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── 2. Hàm Tìm kiếm & Tổng hợp Tri thức Động Thời gian thực (Search + Summary Engine)
function fetchLiveDynamicAI($userQuery) {
    $cleanQuery = trim($userQuery);
    if (empty($cleanQuery)) return null;

    // Loại bỏ các từ thừa mở đầu
    $coreKeywords = preg_replace('/^(tổng quát về|thông tin về|cho tôi biết về|là gì|tìm hiểu về|giới thiệu về|hỏi về|chi tiết về|câu hỏi về|tóm tắt về)\s+/ui', '', $cleanQuery);
    $coreKeywords = trim($coreKeywords);

    $queriesToTry = array_unique(array_filter([$coreKeywords, $cleanQuery]));

    foreach ($queriesToTry as $q) {
        // Bước A: Tìm kiếm tiêu đề phù hợp nhất
        $searchUrl = "https://vi.wikipedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($q) . "&format=json&utf8=1";
        $ch = curl_init($searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'VNPT_Smart_AI/2.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resSearch = curl_exec($ch);
        curl_close($ch);

        $bestTitle = null;
        if ($resSearch) {
            $jsonSearch = json_decode($resSearch, true);
            if (!empty($jsonSearch['query']['search'][0]['title'])) {
                $bestTitle = $jsonSearch['query']['search'][0]['title'];
            }
        }

        if (!$bestTitle) {
            $bestTitle = $q;
        }

        // Bước B: Lấy nội dung tóm tắt chi tiết của tiêu đề
        $summaryUrl = "https://vi.wikipedia.org/api/rest_v1/page/summary/" . rawurlencode($bestTitle);
        $ch2 = curl_init($summaryUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch2, CURLOPT_USERAGENT, 'VNPT_Smart_AI/2.0');
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $resSummary = curl_exec($ch2);
        curl_close($ch2);

        if ($resSummary) {
            $jsonSummary = json_decode($resSummary, true);
            if (!empty($jsonSummary['extract'])) {
                $title = $jsonSummary['title'] ?? $bestTitle;
                $extract = trim($jsonSummary['extract']);
                return "🤖 **VNPT Smart AI — Thông tin tổng quát ({$title})**:\n\n{$extract}\n\n👉 *Bạn có cần hỗ trợ thêm thông tin về Hạ tầng Cloud, Chữ ký số hay Cáp quang VNPT không?*";
            }
        }
    }

    return null;
}

// ── 3. Bộ máy Xử lý Ngôn ngữ VNPT Smart AI Engine ──────────────────────────────
function getVNPTResponse($message) {
    $text = mb_strtolower($message, 'UTF-8');

    // Chào hỏi & Hỗ trợ chung
    if (preg_match('/^chào|^hi|^hello|xin chào|bắt đầu/ui', $text)) {
        return "Xin chào! 👋 Tôi là **VNPT Smart AI** — Trợ lý trí tuệ nhân tạo của VNPT Digital.\n\nTôi sẵn sàng tư vấn cho bạn các dịch vụ hạ tầng số & giải pháp công nghệ:\n• ☁️ **VNPT Cloud Enterprise & Máy chủ VPS**\n• 🔑 **Chữ ký số SmartCA & VNPT Invoice**\n• ⚡ **Mạng Cáp quang FiberVNN & Kênh truyền 5G**\n• 🤖 **Giải pháp AI OCR & Tự động hóa Doanh nghiệp**\n• 🛡️ **Bảo mật An toàn thông tin WAF / AntiDDoS**\n\nBạn cần hỗ trợ thông tin nào hôm nay?";
    }

    // Dịch vụ VNPT Cloud
    if (preg_match('/cloud|vps|server|máy chủ|lưu trữ|ha tang/ui', $text)) {
        return "☁️ **Hạ tầng VNPT Cloud Enterprise**:\n- Standard Uptime Tier III Quốc tế, băng thông nội địa 10Gbps.\n- **Gói Doanh Nghiệp**: **2.900.000 ₫/tháng** (phù hợp doanh nghiệp vừa & nhỏ).\n- **Gói Cao Cấp**: **7.500.000 ₫/tháng** (Full vCPU High Performance, Backup tự động).\n\n👉 *Bạn có thể chọn gói Cloud và bấm \"Đăng ký ngay\" để đưa vào giỏ hàng!*";
    }

    // Chữ ký số SmartCA & VNPT Invoice
    if (preg_match('/chữ ký số|smartca|invoice|hóa đơn|ký số/ui', $text)) {
        return "🔑 **Giải pháp Chữ ký số SmartCA & Hóa đơn điện tử VNPT Invoice**:\n- **SmartCA**: Ký số từ xa không cần USB Token trên điện thoại di động.\n- **VNPT Invoice**: Hệ thống Hóa đơn điện tử theo Chuẩn Tổng cục Thuế (Thông tư 78).\n- **Giá chỉ từ**: **1.200.000 ₫/năm**.\n\n👉 *Liên hệ Hotline 1800 1260 để nhận ưu đãi chiết khấu 20%!*";
    }

    // Cáp quang FiberVNN & 5G
    if (preg_match('/cáp quang|fiber|internet|wifi|5g|kênh truyền/ui', $text)) {
        return "⚡ **Hạ tầng Cáp quang & Kênh truyền VNPT FiberVNN**:\n- **Gói SPORT LITE**: **30.000 ₫/tháng** (Truyền hình thể thao + Internet tốc độ cao).\n- **Gói Fiber Doanh nghiệp**: Từ **350.000 ₫/tháng** (Tặng IP tĩnh miễn phí).\n\n👉 *Bạn có thể thêm gói SPORT LITE trực tiếp vào giỏ hàng ngay trên trang chủ!*";
    }

    // AI OCR & Automation
    if (preg_match('/ocr|tự động hóa|automation/ui', $text)) {
        return "🤖 **Giải pháp VNPT AI OCR & Chatbot Automation**:\n- **VNPT AI OCR**: Tự động bóc tách CCCD, Hóa đơn, Đăng ký xe chính xác **99.8%**.\n- **AI Chatbot**: Tự động trả lời & chăm sóc khách hàng 24/7.\n- **Gói AI Enterprise**: **1.500.000 ₫/tháng**.";
    }

    // Thông tin liên hệ
    if (preg_match('/liên hệ|hotline|tổng đài|điện thoại|email/ui', $text)) {
        return "📞 **Thông tin hỗ trợ khách hàng VNPT**:\n- **Tổng đài CSKH 24/7**: **1800 1260** (Miễn phí cước gọi)\n- **Email hỗ trợ**: **contact@vnpt.vn**\n- **Trụ sở**: Tòa nhà VNPT Tower, 57 Huỳnh Thúc Kháng, Đống Đa, Hà Nội.";
    }

    // Tra cứu Tri thức Động Thời gian thực cho MỌI câu hỏi khác (Ví dụ: "cải cách thiên hoàng nhật bản", "Viettel", "Mỹ", "Pháp", "Bóng đá"...)
    $liveReply = fetchLiveDynamicAI($message);
    if ($liveReply !== null) {
        return $liveReply;
    }

    return "Cảm ơn bạn đã nhắn tin cho **VNPT Smart AI**! 🤖\n\nTôi đã nhận được câu hỏi: *\"" . mb_substr($message, 0, 60, 'UTF-8') . "\"*\n\nBạn có thể hỏi tôi về các dịch vụ số VNPT (Cloud, SmartCA, FiberVNN, AI OCR) hoặc tìm hiểu bất kỳ chủ đề nào bạn quan tâm!";
}

$reply = getVNPTResponse($message);

echo json_encode([
    'reply'  => $reply,
    'source' => 'vnpt_live_dynamic_ai_engine',
    'status' => 'success'
], JSON_UNESCAPED_UNICODE);
