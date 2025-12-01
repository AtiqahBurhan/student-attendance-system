<?php
session_start();
require 'db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pw    = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $pw === $user['password']) {
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        $err = "Invalid email or password.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
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
    animation: slideFadeIn 1s ease forwards;
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


.form-floating input:focus + label,
.form-floating input:not(:placeholder-shown) + label {
    transform: translateY(-50%) scale(0.85);
    color: #6f2da8;
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
.text-center a:hover {
    color: #ff66b3;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="card col-12 col-md-5">
    <h3>Login</h3>

    <?php if($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3 input-icon form-floating">
            <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
            <label for="email">Email</label>
            <i class="bi bi-envelope-fill"></i>
        </div>

        <div class="mb-3 input-icon form-floating position-relative">
            <input id="password" type="password" name="password" class="form-control" placeholder="Password" required>
            <label for="password">Password</label>
            <i class="bi bi-lock-fill"></i>
            <i class="bi bi-eye-slash" id="togglePassword"
               style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
        </div>

        <button type="submit" class="btn btn-pink w-100">Login</button>
    </form>

    <p class="text-center mt-3 small">
        No account? <a href="register.php">Register</a>
    </p>
</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const password = document.getElementById('password');
togglePassword?.addEventListener('click', function () {
    const type = password.type === 'password' ? 'text' : 'password';
    password.type = type;
    this.classList.toggle('bi-eye');
    this.classList.toggle('bi-eye-slash');
});
</script>

</body>
</html>
