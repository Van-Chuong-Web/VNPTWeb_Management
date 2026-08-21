<?php
/**
 * db.php — Kết nối PDO đến CSDL MySQL website_vnpt (MERGED)
 */

// Đọc file .env nếu có
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, "\"' \t");
            if (!isset($_ENV[$name])) $_ENV[$name] = $value;
        }
    }
}

$envHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$envPort = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$envName = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'website_vnpt';
$envUser = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
$envPass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');

if (!defined('DB_HOST')) define('DB_HOST',    $envHost);
if (!defined('DB_PORT')) define('DB_PORT',    $envPort);
if (!defined('DB_NAME')) define('DB_NAME',    $envName);
if (!defined('DB_USER')) define('DB_USER',    $envUser);
if (!defined('DB_PASS')) define('DB_PASS',    $envPass);
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// ── Tạo kết nối PDO ───────────────────────────────────────────────────────
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
);

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Thử fallback sang vnpt_user nếu root lỗi
    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET),
            'vnpt_user',
            'MatKhauBaoMat123@#$',
            $options
        );
    } catch (PDOException $e2) {
        http_response_code(500);
        die('<div style="font-family:sans-serif;padding:2rem;color:#dc3545;">
            <h3>⚠️ Không thể kết nối cơ sở dữ liệu website_vnpt</h3>
            <p>Vui lòng kiểm tra MySQL server và database website_vnpt trên máy chủ.</p>
            <details><summary>Chi tiết lỗi (debug)</summary><pre>' . htmlspecialchars($e2->getMessage()) . '</pre></details>
        </div>');
    }
}
