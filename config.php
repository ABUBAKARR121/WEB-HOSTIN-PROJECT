<?php
// XAMPP Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'technova_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
mysqli_query($conn, $sql);
mysqli_select_db($conn, DB_NAME);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    company VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
)";
mysqli_query($conn, $sql);

// Create activity log table
$sql = "CREATE TABLE IF NOT EXISTS activity_log (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    action VARCHAR(100),
    ip_address VARCHAR(45),
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

// Insert default admin
$check = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$row = mysqli_fetch_assoc($check);

if ($row['count'] == 0) {
    $default_password = password_hash('Admin@2026', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (full_name, email, password, company) 
            VALUES ('Admin User', 'admin@technovasolutions.com', '$default_password', 'TechNova Solutions')";
    mysqli_query($conn, $sql);
}

session_start();
?>