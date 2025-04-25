<?php
    if (!isset($conn)) {
        require_once __DIR__ . '/../vendor/autoload.php'; // adjust path as needed

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host     = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $database = $_ENV['DB_NAME'];

        // Establish a new MySQLi database connection
        // $conn = new mysqli($host, $username, $password, $database);
        $conn = new mysqli('p:' . $host, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        header("X-Using-Persistent-Connection: true");
    }