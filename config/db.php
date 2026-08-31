<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$dbname = 'codeblue_db';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    // Create database if not exists just to be safe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`;");
    
    // Connect to the actual database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Make sure tables exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL UNIQUE,
            room_name VARCHAR(100) NOT NULL,
            floor VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS code_blue_calls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_id INT NOT NULL,
            call_time DATETIME NOT NULL,
            response_time DATETIME NULL,
            status ENUM('pending', 'responded') DEFAULT 'pending',
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
        );
    ");

} catch(PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]));
}
?>
