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
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone'] ?? '');

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one number";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email address already registered. Please login instead.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'manager')");
            $stmt->execute([$fullname, $email, $password_hash, $phone]);

            $success = "Account created successfully! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TechNova Solutions</title>
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

        .register-wrapper {
            width: 100%;
            max-width: 550px;
        }

        .register-card {
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

        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .register-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .register-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-criteria {
            background: #f7fafc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .password-criteria h4 {
            font-size: 0.85rem;
            color: #4a5568;
            margin-bottom: 10px;
        }

        .criteria-list {
            list-style: none;
            padding: 0;
        }

        .criteria-list li {
            font-size: 0.8rem;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .criteria-list li.valid {
            color: #38a169;
        }

        .criteria-list li.invalid {
            color: #e53e3e;
        }

        .criteria-icon {
            font-size: 1rem;
            width: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-register {
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
            margin-top: 0.5rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .register-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .register-footer a {
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

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .register-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-wrapper">
        <div class="register-card">
            <div class="register-header">
                <h1>Create Account</h1>
                <p>Join TechNova Solutions today</p>
            </div>
            <div class="register-body">
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

                <form method="POST" id="registerForm">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" required placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="your@email.com">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" name="password" id="password" required placeholder="Type your password here"
                            autocomplete="off">

                        <div class="password-criteria">
                            <h4>Password Requirements</h4>
                            <ul class="criteria-list" id="criteriaList">
                                <li id="lengthCriteria">
                                    <span class="criteria-icon" id="lengthIcon">⬜</span>
                                    <span>At least 8 characters long</span>
                                </li>
                                <li id="uppercaseCriteria">
                                    <span class="criteria-icon" id="uppercaseIcon">⬜</span>
                                    <span>At least one uppercase letter A to Z</span>
                                </li>
                                <li id="lowercaseCriteria">
                                    <span class="criteria-icon" id="lowercaseIcon">⬜</span>
                                    <span>At least one lowercase letter a to z</span>
                                </li>
                                <li id="numberCriteria">
                                    <span class="criteria-icon" id="numberIcon">⬜</span>
                                    <span>At least one number 0 to 9</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="text" name="confirm_password" id="confirm" required
                            placeholder="Re-type your password here" autocomplete="off">
                        <div id="matchMessage" style="font-size: 0.75rem; margin-top: 5px;"></div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number (Optional)</label>
                        <input type="tel" name="phone" placeholder="+1234567890">
                    </div>

                    <button type="submit" class="btn-register">Create Account</button>
                </form>
            </div>
            <div class="register-footer">
                Already have an account? <a href="login.php">Login here</a>
                <br>
                <a href="index.php">Back to Website</a>
            </div>
        </div>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm');
        const matchMessage = document.getElementById('matchMessage');

        const lengthIcon = document.getElementById('lengthIcon');
        const uppercaseIcon = document.getElementById('uppercaseIcon');
        const lowercaseIcon = document.getElementById('lowercaseIcon');
        const numberIcon = document.getElementById('numberIcon');

        const lengthCriteria = document.getElementById('lengthCriteria');
        const uppercaseCriteria = document.getElementById('uppercaseCriteria');
        const lowercaseCriteria = document.getElementById('lowercaseCriteria');
        const numberCriteria = document.getElementById('numberCriteria');

        function validatePassword() {
            const val = password.value;

            let lengthValid = false;
            let uppercaseValid = false;
            let lowercaseValid = false;
            let numberValid = false;

            if (val.length >= 8) {
                lengthValid = true;
                lengthIcon.innerHTML = '✅';
                lengthCriteria.classList.add('valid');
                lengthCriteria.classList.remove('invalid');
            } else {
                lengthIcon.innerHTML = '❌';
                lengthCriteria.classList.add('invalid');
                lengthCriteria.classList.remove('valid');
            }

            if (/[A-Z]/.test(val)) {
                uppercaseValid = true;
                uppercaseIcon.innerHTML = '✅';
                uppercaseCriteria.classList.add('valid');
                uppercaseCriteria.classList.remove('invalid');
            } else {
                uppercaseIcon.innerHTML = '❌';
                uppercaseCriteria.classList.add('invalid');
                uppercaseCriteria.classList.remove('valid');
            }

            if (/[a-z]/.test(val)) {
                lowercaseValid = true;
                lowercaseIcon.innerHTML = '✅';
                lowercaseCriteria.classList.add('valid');
                lowercaseCriteria.classList.remove('invalid');
            } else {
                lowercaseIcon.innerHTML = '❌';
                lowercaseCriteria.classList.add('invalid');
                lowercaseCriteria.classList.remove('valid');
            }

            if (/[0-9]/.test(val)) {
                numberValid = true;
                numberIcon.innerHTML = '✅';
                numberCriteria.classList.add('valid');
                numberCriteria.classList.remove('invalid');
            } else {
                numberIcon.innerHTML = '❌';
                numberCriteria.classList.add('invalid');
                numberCriteria.classList.remove('invalid');
            }

            return lengthValid && uppercaseValid && lowercaseValid && numberValid;
        }

        function checkPasswordsMatch() {
            const pass = password.value;
            const conf = confirm.value;

            if (conf.length === 0) {
                matchMessage.innerHTML = '';
                matchMessage.style.color = '';
            } else if (pass === conf) {
                matchMessage.innerHTML = '✅ Passwords match';
                matchMessage.style.color = '#38a169';
            } else {
                matchMessage.innerHTML = '❌ Passwords do not match';
                matchMessage.style.color = '#e53e3e';
            }
        }

        password.addEventListener('input', function () {
            validatePassword();
            checkPasswordsMatch();
        });

        confirm.addEventListener('input', function () {
            checkPasswordsMatch();
        });

        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const isValid = validatePassword();
            const pass = password.value;
            const conf = confirm.value;

            if (!isValid) {
                e.preventDefault();
                alert('Please meet all password requirements before submitting');
            } else if (pass !== conf) {
                e.preventDefault();
                alert('Passwords do not match');
            }
        });
    </script>
</body>

</html>