<?php
    // Database Configuration
    // $host = 'localhost';
    // $username = 'root';
    // $password = '';
    // $database = 'whitefieldsdb';

    $host = 'sql307.infinityfree.com';
    $username = 'if0_36016256';
    $password = 'IGE6ue9kM2G';
    $database = 'if0_36016256_whitefields';

    // Establish a new MySQLi database connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }