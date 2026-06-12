<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Create backup directory
$backup_dir = 'backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// Create backup
if (isset($_GET['create'])) {
    $filename = 'technova_backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backup_dir . $filename;

    // Create database dump
    $command = "mysqldump --user=root --host=localhost --password= technova_solutions > " . escapeshellarg($filepath);
    exec($command, $output, $return_var);

    if (file_exists($filepath) && filesize($filepath) > 0) {
        $size = round(filesize($filepath) / 1024, 2) . ' KB';
        $stmt = $pdo->prepare("INSERT INTO backups (filename, filepath, size, type, created_by) VALUES (?, ?, ?, 'database', ?)");
        $stmt->execute([$filename, $filepath, $size, $_SESSION['user_id']]);

        // Log activity
        $log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'Created backup: $filename', ?)");
        $log->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);

        $message = "✅ Backup created successfully: $filename ($size)";
    } else {
        $error = "❌ Backup creation failed. Make sure mysqldump is installed.";
    }
}

// Restore backup
if (isset($_GET['restore'])) {
    $id = $_GET['restore'];
    $stmt = $pdo->prepare("SELECT * FROM backups WHERE id = ?");
    $stmt->execute([$id]);
    $backup = $stmt->fetch();

    if ($backup && file_exists($backup['filepath'])) {
        $command = "mysql --user=root --host=localhost --password= technova_solutions < " . escapeshellarg($backup['filepath']);
        exec($command);

        $log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'Restored backup: " . $backup['filename'] . "', ?)");
        $log->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);

        $message = "✅ Database restored successfully from: " . $backup['filename'];
    } else {
        $error = "❌ Backup file not found!";
    }
}

// Delete backup
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT * FROM backups WHERE id = ?");
    $stmt->execute([$id]);
    $backup = $stmt->fetch();

    if ($backup) {
        if (file_exists($backup['filepath'])) {
            unlink($backup['filepath']);
        }
        $stmt = $pdo->prepare("DELETE FROM backups WHERE id = ?");
        $stmt->execute([$id]);
        $message = "🗑️ Backup deleted: " . $backup['filename'];
    }
}

// Get all backups
$backups = $pdo->query("SELECT b.*, u.fullname FROM backups b LEFT JOIN users u ON b.created_by = u.id ORDER BY b.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Backup Manager - TechNova</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        h1 {
            color: #2d3748;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #718096;
            color: white;
        }

        .btn-danger {
            background: #e53e3e;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .message {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #38a169;
        }

        .error {
            background: #fed7d7;
            color: #c53030;
            border-left: 4px solid #c53030;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f7fafc;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-db {
            background: #bee3f8;
            color: #2c5282;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .info-box {
            background: #edf2f7;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            th,
            td {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 1rem;
                display: block;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
            }

            td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: none;
            }

            td:before {
                content: attr(data-label);
                font-weight: bold;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>💾 Backup & Recovery System</h1>
            <div>
                <a href="?create=1" class="btn btn-primary" onclick="return confirm('Create a new database backup?')">➕
                    Create Backup</a>
                <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">📋 Available Backups</h3>

            <?php if (count($backups) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td data-label="ID">#
                                    <?php echo $backup['id']; ?>
                                </td>
                                <td data-label="Filename">
                                    <?php echo htmlspecialchars($backup['filename']); ?>
                                </td>
                                <td data-label="Size">
                                    <?php echo $backup['size']; ?>
                                </td>
                                <td data-label="Created By">
                                    <?php echo htmlspecialchars($backup['fullname'] ?? 'Unknown'); ?>
                                </td>
                                <td data-label="Date">
                                    <?php echo date('M d, Y H:i', strtotime($backup['created_at'])); ?>
                                </td>
                                <td data-label="Actions">
                                    <div class="action-buttons">
                                        <a href="?restore=<?php echo $backup['id']; ?>" class="btn btn-primary btn-sm"
                                            onclick="return confirm('Restore this backup? This will overwrite your current database.')">↩️
                                            Restore</a>
                                        <a href="?delete=<?php echo $backup['id']; ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this backup permanently?')">🗑️ Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #718096; text-align: center; padding: 2rem;">No backups found. Click "Create Backup" to get
                    started.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">📖 Backup Strategy Guide</h3>
            <div class="info-box">
                <p><strong>🔄 Manual Backups:</strong> Click "Create Backup" to manually backup your database at any
                    time.</p>
                <p><strong>🤖 Automated Backups (Setup Instructions):</strong> For production servers, set up a cron
                    job:</p>
                <code
                    style="background: #2d3748; color: #a0aec0; padding: 8px; display: block; margin: 10px 0; border-radius: 5px;">
                    0 2 * * * wget -O /dev/null http://localhost/technova/backup.php?create=1
                </code>
                <p><strong>📊 Recovery Strategy:</strong> Three backup retention policy - keep last 3 backups minimum.
                </p>
                <p><strong>🔐 Security:</strong> Backups are stored locally. For production, sync to cloud storage (AWS
                    S3, Google Drive).</p>
            </div>
        </div>
    </div>
</body>

</html>