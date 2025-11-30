<?php
session_start();
session_unset();
session_destroy();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Logging Out...</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #f7d9f0, #e9c0ff);
    font-family: 'Segoe UI', sans-serif;
}

.logout-card {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px 30px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    animation: fadeIn 0.8s ease forwards;
    position: relative;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    to { transform: translateY(-100px); opacity: 0; }
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    color: #ff66b3;
    margin-top: 20px;
    animation: rotate 1s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

h3 { color: #6f2da8; font-weight: 600; }
p { color: #555; }
.countdown {
    font-size: 1.2rem;
    margin-top: 10px;
    font-weight: 500;
    color: #ff66b3;
}
</style>
</head>
<body>

<div class="logout-card" id="logoutCard">
    <i class="bi bi-box-arrow-right" style="font-size:2rem; color:#ff66b3;"></i>
    <h3>Logging Out...</h3>
    <p>You will be redirected shortly</p>
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div class="countdown" id="countdown">3</div>
</div>

<script>
let countdown = 3;
const countdownEl = document.getElementById('countdown');
const logoutCard = document.getElementById('logoutCard');

const interval = setInterval(() => {
    countdown--;
    if(countdown > 0){
        countdownEl.textContent = countdown;
    } else {
        clearInterval(interval);
        
        logoutCard.style.animation = 'slideUp 0.8s forwards';
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 800);
    }
}, 1000);
</script>

</body>
</html>
