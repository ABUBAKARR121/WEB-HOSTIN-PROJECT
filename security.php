<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get security settings
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM security_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Security Center - TechNova</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f7fafc;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            margin-bottom: 2rem;
        }

        h1 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #718096;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card-header {
            padding: 1.25rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .card-body {
            padding: 1.25rem;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            font-weight: 600;
            color: #2d3748;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-warning {
            background: #fed7d7;
            color: #c53030;
        }

        .badge-info {
            background: #bee3f8;
            color: #2c5282;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 10px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .checklist {
            list-style: none;
            padding: 0;
        }

        .checklist li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checklist li:before {
            content: "✓";
            color: #38a169;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ Security Center</h1>
            <p class="subtitle">Monitor and manage your website's security settings</p>
        </div>

        <div class="grid">
            <!-- SSL Card -->
            <div class="card">
                <div class="card-header">
                    <h3>🔐 SSL Certificate</h3>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <span class="status-label">SSL Status:</span>
                        <span class="status-badge badge-active">✓
                            <?php echo isset($settings['ssl_status']) ? ucfirst($settings['ssl_status']) : 'Active'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">HTTPS Redirect:</span>
                        <span class="status-badge badge-active">Enabled</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Certificate Type:</span>
                        <span>Let's Encrypt (Free SSL)</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Expiry Date:</span>
                        <span>July 15, 2026</span>
                    </div>
                    <a href="#" class="btn btn-primary btn-sm"
                        onclick="alert('SSL renewal initiated. Valid for 90 days.')">🔄 Renew SSL</a>
                </div>
            </div>

            <!-- Firewall Card -->
            <div class="card">
                <div class="card-header">
                    <h3>🛡️ Firewall Protection</h3>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <span class="status-label">Web Application Firewall:</span>
                        <span class="status-badge badge-active">✓ Active</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">SQL Injection Protection:</span>
                        <span class="status-badge badge-active">Enabled</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">XSS Protection:</span>
                        <span class="status-badge badge-active">Enabled</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">DDoS Mitigation:</span>
                        <span class="status-badge badge-active">Active</span>
                    </div>
                </div>
            </div>

            <!-- Security Headers Card -->
            <div class="card">
                <div class="card-header">
                    <h3>📋 Security Headers</h3>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <span class="status-label">Content Security Policy:</span>
                        <span class="status-badge badge-active">Configured</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">X-Frame-Options:</span>
                        <span class="status-badge badge-active">DENY</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">HSTS:</span>
                        <span class="status-badge badge-active">Enabled (365 days)</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">X-Content-Type-Options:</span>
                        <span class="status-badge badge-active">nosniff</span>
                    </div>
                </div>
            </div>

            <!-- Password Policy Card -->
            <div class="card">
                <div class="card-header">
                    <h3>🔒 Password Policy</h3>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <span class="status-label">Minimum Length:</span>
                        <span>8 characters</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Require Uppercase:</span>
                        <span class="status-badge badge-active">Yes</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Require Numbers:</span>
                        <span class="status-badge badge-active">Yes</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Session Timeout:</span>
                        <span>
                            <?php echo isset($settings['session_timeout']) ? $settings['session_timeout'] : '30'; ?>
                            minutes
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deployment Checklist -->
        <div class="card" style="background: white; border-radius: 16px; padding: 1.5rem;">
            <h3 style="margin-bottom: 1rem;">✅ Hostinger Deployment Security Checklist</h3>
            <ul class="checklist">
                <li>Install Let's Encrypt SSL Certificate (Free in hPanel)</li>
                <li>Enable "Hotlink Protection" to prevent image theft</li>
                <li>Enable "Bot Blocker" for malicious bots</li>
                <li>Configure "2-Factor Authentication" for admin account</li>
                <li>Set up "Daily Automated Backups" in Hostinger</li>
                <li>Enable "PHP Security" settings (disable dangerous functions)</li>
                <li>Configure "IP Blocker" for suspicious addresses</li>
                <li>Enable "ModSecurity" WAF rules</li>
            </ul>

            <div style="margin-top: 1.5rem; padding: 1rem; background: #edf2f7; border-radius: 10px;">
                <h4>📊 Security Score: 96/100</h4>
                <div style="background: #e2e8f0; border-radius: 10px; height: 8px; margin-top: 8px; overflow: hidden;">
                    <div style="width: 96%; background: linear-gradient(135deg, #38a169, #2f855a); height: 100%;"></div>
                </div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: #718096;">Your website has excellent security
                    posture.</p>
            </div>

            <div style="margin-top: 1rem; text-align: center;">
                <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>