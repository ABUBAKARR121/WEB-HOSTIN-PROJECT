<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname']);
        $phone = trim($_POST['phone']);

        $stmt = $pdo->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
        $stmt->execute([$fullname, $phone, $_SESSION['user_id']]);

        $_SESSION['user_name'] = $fullname;
        $message = "✅ Profile updated successfully!";

    } elseif (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if (!password_verify($current, $user['password_hash'])) {
            $error = "❌ Current password is incorrect!";
        } elseif ($new !== $confirm) {
            $error = "❌ New passwords do not match!";
        } elseif (strlen($new) < 8) {
            $error = "❌ Password must be at least 8 characters!";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $_SESSION['user_id']]);
            $message = "✅ Password changed successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>My Profile - TechNova</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f7fafc;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #718096;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        .message {
            padding: 12px;
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

        .avatar-large {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            margin-bottom: 1rem;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            .card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card" style="text-align: center;">
            <div class="avatar-large" style="margin: 0 auto 1rem auto;">
                <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
            </div>
            <h1>
                <?php echo htmlspecialchars($user['fullname']); ?>
            </h1>
            <p class="subtitle">
                <?php echo htmlspecialchars($user['email']); ?> •
                <?php echo ucfirst($user['role']); ?>
            </p>
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
            <h2>✏️ Edit Profile</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small style="color:#718096;">Email cannot be changed</small>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        placeholder="Optional">
                </div>
                <button type="submit" name="update_profile">💾 Save Changes</button>
            </form>
        </div>

        <div class="card">
            <h2>🔑 Change Password</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required minlength="8">
                    <small>Minimum 8 characters, at least one uppercase letter and one number</small>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password">🔄 Update Password</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="dashboard.php" style="color: #667eea;">← Back to Dashboard</a>
        </div>
    </div>
</body>

</html>