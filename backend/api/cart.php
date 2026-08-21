<?php
/**
 * backend/api/cart.php — API Giỏ hàng lưu theo PHP Session & CSDL MySQL
 */

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';

try {
    $rawInput = json_decode(file_get_contents('php://input'), true);
    $input = !empty($rawInput) ? array_merge($_POST, $rawInput) : $_POST;

    if ($action === 'add' || (!empty($input) && isset($input['name']))) {
        $id    = trim($input['id'] ?? $input['code'] ?? 'sp_' . time());
        $name  = trim($input['name'] ?? $input['ten_san_pham'] ?? 'Dịch vụ VNPT');
        $price = floatval($input['price'] ?? $input['gia'] ?? 0);
        $icon  = trim($input['icon'] ?? 'package');
        $color = trim($input['color'] ?? '#0066CC');

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $foundIndex = -1;
        foreach ($_SESSION['cart'] as $idx => $item) {
            if (($item['id'] ?? '') === $id || strtolower($item['name'] ?? '') === strtolower($name)) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex >= 0) {
            $_SESSION['cart'][$foundIndex]['qty'] = 1;
        } else {
            $_SESSION['cart'][] = [
                'id'    => $id,
                'name'  => $name,
                'price' => $price,
                'qty'   => 1,
                'icon'  => $icon,
                'color' => $color
            ];
        }

        echo json_encode([
            'status'  => 'success',
            'message' => 'Đã thêm dịch vụ vào giỏ hàng!',
            'items'   => array_values($_SESSION['cart'])
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'remove' || isset($_GET['remove_id'])) {
        $removeId = $_GET['remove_id'] ?? $input['id'] ?? '';
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], function($item) use ($removeId) {
            return ($item['id'] ?? '') !== $removeId && strtolower($item['name'] ?? '') !== strtolower($removeId);
        }));

        echo json_encode([
            'status'  => 'success',
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'items'   => array_values($_SESSION['cart'])
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        echo json_encode([
            'status'  => 'success',
            'message' => 'Đã xóa toàn bộ giỏ hàng',
            'items'   => []
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Default: GET cart items
    echo json_encode([
        'status' => 'success',
        'items'  => array_values($_SESSION['cart'])
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
