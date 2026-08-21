<?php
class Database
{
    private $pdo;

    public function __construct()
    {
        // Đọc file .env nếu có
        $envFile = __DIR__ . '/../../.env';
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

        $host   = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
        $port   = (int)($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);
        $dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'website_vnpt';
        $user   = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
        $pass   = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            try {
                // Fallback sang vnpt_user nếu root bị giới hạn
                $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", 'vnpt_user', 'MatKhauBaoMat123@#$', $options);
            } catch (PDOException $e2) {
                throw new Exception("Lỗi kết nối CSDL website_vnpt: " . $e2->getMessage());
            }
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function select($sql, $types = "", $params = [])
    {
        if (is_array($types)) {
            $params = $types;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(is_array($params) ? $params : []);
        return $stmt->fetchAll();
    }

    public function insert($sql, $types = "", $params = [])
    {
        if (is_array($types)) {
            $params = $types;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(is_array($params) ? $params : []);
        return $this->pdo->lastInsertId();
    }

    public function update($sql, $types = "", $params = [])
    {
        if (is_array($types)) {
            $params = $types;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(is_array($params) ? $params : []);
        return $stmt->rowCount();
    }

    public function delete($sql, $types = "", $params = [])
    {
        return $this->update($sql, $types, $params);
    }
}