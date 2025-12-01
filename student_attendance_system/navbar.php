<?php

if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Attendance</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
        <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
        <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
      </ul>
      <div class="d-flex align-items-center">
        <?php if($user): ?>
          <span class="me-3 small text-muted">Hi, <?=htmlspecialchars($user['name'])?></span>
          <a href="logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
        <?php else: ?>
          <a href="index.php" class="btn btn-sm btn-primary">Login</a>
        <?php endif;?>
      </div>
    </div>
  </div>
</nav>
