<?php
session_start();
require 'db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $err = "Passwords do not match.";
    } else {
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$name, $email, $password])) {
            header("Location: index.php");
            exit;
        } else {
            $err = "Registration failed. Try again.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(-45deg, #f7d9f0, #e9c0ff, #ffd6f2, #e8b0ff);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    font-family: 'Segoe UI', sans-serif;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.card {
    border-radius: 25px;
    padding: 40px 30px;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.85);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    animation: slideFadeIn 0.8s forwards;
    opacity: 0;
    transform: translateY(-30px);
}

@keyframes slideFadeIn {
    to { opacity: 1; transform: translateY(0); }
}

h3 { color: #6f2da8; margin-bottom: 30px; font-weight: 600; text-align:center; }

.input-icon {
    position: relative;
}
.input-icon i:first-child {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 1.1rem;
}
.input-icon input {
    padding-left: 35px;
}

.form-control:focus {
    border-color: #ff66b3;
    box-shadow: 0 0 5px rgba(255,102,179,0.5);
}

.btn-pink {
    background-color: #ff66b3;
    color: #fff;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-pink:hover {
    background-color: #e65599;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255,102,179,0.4);
}

.alert { border-radius: 12px; }
.text-center a {
    color: #6f2da8;
    transition: color 0.3s;
}
.text-center a:hover { color: #ff66b3; text-decoration: none; }

.position-relative i.toggle-eye {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.1rem;
    color: #6f2da8;
}
</style>
</head>
<body>

<div class="card col-12 col-md-6">
    <h3>Register</h3>

    <?php if($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3 input-icon form-floating">
            <input type="text" name="name" class="form-control" id="name" placeholder="Name" required>
            <label for="name">Name</label>
            <i class="bi bi-person-fill"></i>
        </div>

        <div class="mb-3 input-icon form-floating">
            <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
            <label for="email">Email</label>
            <i class="bi bi-envelope-fill"></i>
        </div>

        <div class="mb-3 position-relative form-floating">
            <input id="regPassword" type="password" name="password" class="form-control" placeholder="Password" required>
            <label for="regPassword">Password</label>
            <i class="bi bi-eye-slash toggle-eye" id="toggleRegPassword"></i>
        </div>

        <div class="mb-3 position-relative form-floating">
            <input id="confirmPassword" type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            <label for="confirmPassword">Confirm Password</label>
            <i class="bi bi-eye-slash toggle-eye" id="toggleConfirmPassword"></i>
        </div>

        <button class="btn btn-pink w-100">Register</button>
    </form>

    <p class="text-center mt-3 small">
        Already have an account? <a href="index.php">Login</a>
    </p>
</div>

<script>

function toggleEye(toggleId, inputId) {
    const toggle = document.getElementById(toggleId);
    const input = document.getElementById(inputId);
    toggle?.addEventListener('click', function(){
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
}

toggleEye('toggleRegPassword','regPassword');
toggleEye('toggleConfirmPassword','confirmPassword');
</script>

</body>
</html>
