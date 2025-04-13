<?php
    // Database Configuration
    // $host = 'localhost';
    // $username = 'root';
    // $password = '';
    // $database = 'whitefieldsdb';
    
    $host = '153.92.15.45';
    $username = 'u659352537_whitefields';
    $password = 'Wfdc2021';
    $database = 'u659352537_whitefields';

    // $host = 'sql307.infinityfree.com';
    // $username = 'if0_36016256';
    // $password = 'IGE6ue9kM2G';
    // $database = 'if0_36016256_whitefields';

    // Establish a new MySQLi database connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }