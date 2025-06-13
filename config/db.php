<?php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $name = 'digital_library';
    private $port = '3306';
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name, $this->port);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function checkTables() {
        $result = $this->conn->query("SHOW TABLES LIKE 'books'");
        return $result->num_rows > 0;
    }
}


