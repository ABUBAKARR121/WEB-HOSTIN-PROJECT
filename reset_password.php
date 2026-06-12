<?php
require_once 'db.php';
$error = '';
$success = '';

$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Invalid password reset link.");
}

$stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("This reset link is invalid or has expired. Please request a new one.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "❌ Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error = "❌ Password must be at least 8 characters long!";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "❌ Password must contain at least one uppercase letter and one number!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);
        $success = "✅ Password reset successfully! <a href='login.php' style='color:#667eea;'>Click here to login</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TechNova</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #718096;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
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

        .strength-meter {
            margin-top: 8px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }

        @media (max-width: 480px) {
            .card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>🔑 Create New Password</h1>
        <p class="subtitle">For account:
            <?php echo htmlspecialchars($user['email']); ?>
        </p>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success">
                <?php echo $success; ?>
            </div>
        <?php else: ?>

            <form method="POST" id="resetForm">
                <div class="form-group">
                    <label>🔒 New Password</label>
                    <input type="password" name="password" id="password" required placeholder="Min. 8 characters">
                    <div class="strength-meter">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>✓ Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm" required
                        placeholder="Re-enter new password">
                </div>
                <button type="submit">Reset Password →</button>
            </form>

        <?php endif; ?>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm');
        const strengthFill = document.getElementById('strengthFill');

        password.addEventListener('input', function () {
            const val = this.value;
            let strength = 0;

            if (val.length >= 8) strength += 25;
            if (val.match(/[A-Z]/)) strength += 25;
            if (val.match(/[0-9]/)) strength += 25;
            if (val.match(/[^a-zA-Z0-9]/)) strength += 25;

            strengthFill.style.width = strength + '%';

            if (strength < 50) {
                strengthFill.style.background = '#e53e3e';
            } else if (strength < 75) {
                strengthFill.style.background = '#ed8936';
            } else {
                strengthFill.style.background = '#38a169';
            }
        });
    </script>
</body>

</html>