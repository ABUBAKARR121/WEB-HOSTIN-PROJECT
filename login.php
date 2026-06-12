<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];

        $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->execute([$user['id']]);

        $log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'User logged in', ?)");
        $log->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email address or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechNova Admin Portal</title>
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

        .login-wrapper {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .login-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
        }

        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background: #fed7d7;
            color: #c53030;
            border-left: 4px solid #c53030;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #38a169;
        }

        .register-link {
            margin-top: 10px;
        }

        @media (max-width: 480px) {
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>Admin Portal</h1>
                <p>Secure access to TechNova Solutions</p>
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="loginForm">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email" required placeholder="your@email.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" required
                                placeholder="Enter your password">
                            <span class="toggle-password" onclick="togglePassword()">Show</span>
                        </div>
                    </div>
                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>
            <div class="login-footer">
                <a href="forgot_password.php">Forgot Password?</a>
                <span class="register-link"> | <a href="register.php">Create New Account</a></span>
                <br>
                <a href="index.php">Back to Website</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
        }

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>

</html>