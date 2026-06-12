<?php
// install.php - Run this ONCE to setup everything
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS technova_solutions");
    $pdo->exec("USE technova_solutions");

    // Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        role ENUM('admin', 'manager') DEFAULT 'manager',
        reset_token VARCHAR(255) DEFAULT NULL,
        reset_expires DATETIME DEFAULT NULL,
        last_login DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Backups Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS backups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        filepath VARCHAR(500) NOT NULL,
        size VARCHAR(50),
        type ENUM('database', 'full') DEFAULT 'database',
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Activity Logs Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Security Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS security_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Insert default security settings
    $pdo->exec("INSERT IGNORE INTO security_settings (setting_key, setting_value) VALUES 
        ('ssl_status', 'active'),
        ('firewall_enabled', 'true'),
        ('backup_frequency', 'daily'),
        ('session_timeout', '30')");

    // Create Admin Account
    $admin_email = 'admin@technova.com';
    $admin_password = 'TechNova@2026';
    $admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    $fullname = 'System Administrator';

    $stmt = $pdo->prepare("INSERT IGNORE INTO users (fullname, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute([$fullname, $admin_email, $admin_hash]);

    // Create backup directory
    if (!is_dir('backups')) {
        mkdir('backups', 0777, true);
    }

    // Success message with CSS/JS internal
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation Complete - TechNova</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            
            .container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 500px;
                width: 100%;
                padding: 40px;
                animation: fadeIn 0.6s ease-out;
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .success-icon {
                text-align: center;
                font-size: 80px;
                margin-bottom: 20px;
            }
            
            h1 {
                color: #2c3e50;
                text-align: center;
                margin-bottom: 10px;
            }
            
            .subtitle {
                text-align: center;
                color: #7f8c8d;
                margin-bottom: 30px;
            }
            
            .credentials {
                background: #f8f9fa;
                border-radius: 12px;
                padding: 20px;
                margin: 20px 0;
                border-left: 4px solid #27ae60;
            }
            
            .cred-item {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #e0e0e0;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .cred-item:last-child {
                border-bottom: none;
            }
            
            .cred-label {
                font-weight: bold;
                color: #2c3e50;
            }
            
            .cred-value {
                color: #667eea;
                font-family: monospace;
                font-size: 14px;
                word-break: break-all;
            }
            
            .warning {
                background: #fff3cd;
                border: 1px solid #ffc107;
                border-radius: 8px;
                padding: 12px;
                margin: 20px 0;
                color: #856404;
                font-size: 14px;
            }
            
            .btn {
                display: block;
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-align: center;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
                margin-top: 20px;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            
            .feature-list {
                margin: 20px 0;
            }
            
            .feature-list li {
                padding: 8px 0;
                color: #555;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-icon">✅</div>
            <h1>Installation Complete!</h1>
            <p class="subtitle">TechNova Solutions is ready to go</p>
            
            <div class="credentials">
                <div class="cred-item">
                    <span class="cred-label">📧 Admin Email:</span>
                    <span class="cred-value">admin@technova.com</span>
                </div>
                <div class="cred-item">
                    <span class="cred-label">🔑 Admin Password:</span>
                    <span class="cred-value">TechNova@2026</span>
                </div>
            </div>
            
            <div class="warning">
                ⚠️ Please save these credentials securely. This message will only appear once.
            </div>
            
            <ul class="feature-list">
                <li>✓ Database created successfully</li>
                <li>✓ Admin account configured</li>
                <li>✓ Backup system ready</li>
                <li>✓ Security settings initialized</li>
            </ul>
            
            <a href="login.php" class="btn">🔐 Proceed to Login →</a>
        </div>
        
        <script>
            // Store credentials temporarily for demo (not for production)
            sessionStorage.setItem("admin_email", "admin@technova.com");
            console.log("Installation completed at: " + new Date().toLocaleString());
        </script>
    </body>
    </html>';

} catch (PDOException $e) {
    echo '<!DOCTYPE html>
    <html>
    <head><title>Installation Error</title>
    <style>
        body { font-family: Arial; background: #f8d7da; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .error-box { background: white; padding: 30px; border-radius: 10px; border-left: 4px solid #dc3545; max-width: 500px; }
        .error-box h2 { color: #721c24; }
        .error-box p { color: #721c24; }
    </style>
    </head>
    <body>
    <div class="error-box">
        <h2>❌ Installation Failed</h2>
        <p>' . $e->getMessage() . '</p>
        <p>Make sure XAMPP/WAMP is running and MySQL is started.</p>
    </div>
    </body></html>';
}
?>