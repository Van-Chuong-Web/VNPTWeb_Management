<?php
/**
 * db.php — Smart Multi-Candidate PDO Connection to MySQL (website_vnpt)
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

$aivenPass = base64_decode('QVZOU19oc3JpWm9vX212SWp0Q3Jib2FS');

$candidates = [
    [
        'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost',
        'port' => $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306',
        'name' => $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'website_vnpt',
        'user' => $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root',
        'pass' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '')
    ],
    [
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'website_vnpt',
        'user' => 'vnpt_user',
        'pass' => 'MatKhauBaoMat123@#$'
    ],
    [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'website_vnpt',
        'user' => 'root',
        'pass' => ''
    ],
    [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'website_vnpt',
        'user' => 'vnpt_user',
        'pass' => 'MatKhauBaoMat123@#$'
    ],
    [
        'host' => 'db-web-nguyenhoanggiakhang-dabd.a.aivencloud.com',
        'port' => '11935',
        'name' => 'defaultdb',
        'user' => 'avnadmin',
        'pass' => $aivenPass
    ]
];

$pdo = null;
$lastError = null;

foreach ($candidates as $c) {
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['name']);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ];
        $pdo = new PDO($dsn, $c['user'], $c['pass'], $options);
        if (!defined('DB_HOST')) define('DB_HOST', $c['host']);
        if (!defined('DB_PORT')) define('DB_PORT', $c['port']);
        if (!defined('DB_NAME')) define('DB_NAME', $c['name']);
        if (!defined('DB_USER')) define('DB_USER', $c['user']);
        if (!defined('DB_PASS')) define('DB_PASS', $c['pass']);
        if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
        break;
    } catch (PDOException $e) {
        $lastError = $e->getMessage();
    }
}

if (!$pdo) {
    http_response_code(500);
    die('<div style="font-family:sans-serif;padding:2rem;color:#dc3545;">
        <h3>⚠️ Không thể kết nối cơ sở dữ liệu</h3>
        <p>Không thể kết nối đến MySQL server trên localhost hoặc cloud.</p>
        <details><summary>Chi tiết lỗi (debug)</summary><pre>' . htmlspecialchars($lastError) . '</pre></details>
    </div>');
}
