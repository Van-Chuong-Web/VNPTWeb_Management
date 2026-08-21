<?php
class Database
{
   private $host;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function __construct()
    {
        // Ưu tiên đọc từ biến môi trường (production), nếu không có thì dùng mặc định (localhost)
       $this->host = $_ENV['DB_HOST'] ?? 'localhost';
    $this->user = $_ENV['DB_USER'] ?? 'root';
    $this->pass = $_ENV['DB_PASSWORD'] ?? ''; 
    $this->dbname = $_ENV['DB_NAME'] ?? 'website_vnpt';

    $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        $this->conn->set_charset("utf8mb4");
    }

    public function getConnection()
    {
        return $this->conn;
    }

    private function normalizeArgs($types, $params)
    {
        if (is_array($types)) {
            $params = $types;
            $types = "";
        }
        if (empty($types) && !empty($params)) {
            $types = "";
            foreach ($params as $p) {
                if (is_int($p)) $types .= "i";
                elseif (is_float($p) || is_double($p)) $types .= "d";
                else $types .= "s";
            }
        }
        return [$types, (array)$params];
    }

    public function select($sql, $types = "", $params = [])
    {
        list($types, $params) = $this->normalizeArgs($types, $params);
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi prepare: " . $this->conn->error);
        }

        if ($types !== "" && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $data;
    }

    public function execute($sql, $types = "", $params = [])
    {
        list($types, $params) = $this->normalizeArgs($types, $params);
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi prepare: " . $this->conn->error);
        }

        if ($types !== "" && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();
        if (!$success) {
            throw new Exception("Lỗi truy vấn: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    }

    public function insert($sql, $types = "", $params = [])
    {
        list($types, $params) = $this->normalizeArgs($types, $params);
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi prepare: " . $this->conn->error);
        }

        if ($types !== "" && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();
        if (!$success) {
            throw new Exception("Lỗi truy vấn insert: " . $stmt->error);
        }

        $insertId = $this->conn->insert_id;
        $stmt->close();
        return $insertId;
    }

    public function count($sql, $types = "", $params = [])
    {
        list($types, $params) = $this->normalizeArgs($types, $params);
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi prepare: " . $this->conn->error);
        }

        if ($types !== "" && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_row() : [0];
        $stmt->close();
        return (int) ($row[0] ?? 0);
    }

    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>