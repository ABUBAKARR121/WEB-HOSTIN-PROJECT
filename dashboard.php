<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM backups");
$total_backups = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) = CURDATE()");
$today_activity = $stmt->fetch()['total'];

// Get recent activity
$recent_activity = $pdo->query("SELECT al.*, u.fullname FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Dashboard - TechNova Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f7fafc;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            transition: all 0.3s;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            opacity: 0.7;
            margin-top: 5px;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-nav a.active {
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-left: 4px solid white;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 1rem;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .welcome-text h3 {
            color: #2d3748;
        }

        .welcome-text p {
            color: #718096;
            font-size: 0.9rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #2d3748;
        }

        .stat-card .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .quick-btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: transform 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-btn:hover {
            transform: translateY(-2px);
        }

        /* Activity Table */
        .activity-table {
            width: 100%;
            overflow-x: auto;
        }

        .activity-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .activity-table th,
        .activity-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .activity-table th {
            background: #f7fafc;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #667eea;
                color: white;
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                font-size: 1.5rem;
                cursor: pointer;
                z-index: 1001;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-admin {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-manager {
            background: #bee3f8;
            color: #2c5282;
        }
    </style>
</head>

<body>
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>🚀 TechNova</h2>
            <p>Admin Panel v2.0</p>
        </div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="security.php">🔒 Security Center</a>
            <a href="backup.php">💾 Backup Manager</a>
            <a href="profile.php">👤 My Profile</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-text">
                <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                <p>Here's what's happening with your hosting environment today.</p>
            </div>
            <div class="user-info">
                <div class="avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💾</div>
                <div class="stat-value"><?php echo $total_backups; ?></div>
                <div class="stat-label">Backups Created</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo $today_activity; ?></div>
                <div class="stat-label">Today's Activity</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🟢</div>
                <div class="stat-value">99.9%</div>
                <div class="stat-label">Server Uptime</div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">⚡ Quick Actions</div>
            <div class="quick-actions">
                <a href="backup.php?create=1" class="quick-btn">💾 Create Backup</a>
                <a href="security.php" class="quick-btn">🔒 Security Check</a>
                <a href="profile.php" class="quick-btn">👤 Edit Profile</a>
            </div>
        </div>

        <div class="card">
            <div class="card-title">📋 Recent Activity</div>
            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activity as $activity): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['fullname'] ?? 'System'); ?></td>
                                <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                <td><?php echo $activity['ip_address']; ?></td>
                                <td><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-title">ℹ️ System Information</div>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>SSL Status:</strong> <span style="color: green;">✓ Active (HTTPS)</span></p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Auto-refresh activity every 30 seconds
        setInterval(function () {
            location.reload();
        }, 30000);
    </script>
</body>

</html>