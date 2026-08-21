<?php
/**
 * db.php — Kết nối PDO đến database Aiven Cloud MySQL (MERGED)
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

$defaultPass = base64_decode('QVZOU19oc3JpWm9vX212SWp0Q3Jib2FS');

$envHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'db-web-nguyenhoanggiakhang-dabd.a.aivencloud.com';
if ($envHost === 'localhost' || $envHost === '127.0.0.1') {
    $envHost = 'db-web-nguyenhoanggiakhang-dabd.a.aivencloud.com';
}
$envPort = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '11935';
if ($envPort == '3306') $envPort = '11935';

$envName = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'defaultdb';
if ($envName === 'website_vnpt') $envName = 'defaultdb';

$envUser = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'avnadmin';
if ($envUser === 'root' || $envUser === 'vnpt_user') $envUser = 'avnadmin';

$envPass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: $defaultPass);
if (empty($envPass)) $envPass = $defaultPass;

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
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('<div style="font-family:sans-serif;padding:2rem;color:#dc3545;">
        <h3>⚠️ Không thể kết nối cơ sở dữ liệu</h3>
        <p>Vui lòng kiểm tra thông tin kết nối Aiven Cloud MySQL:</p>
        <details><summary>Chi tiết lỗi (debug)</summary><pre>' . htmlspecialchars($e->getMessage()) . '</pre></details>
    </div>');
}
