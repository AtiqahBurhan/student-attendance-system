<?php

session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }


$stmt = $pdo->query("SELECT COUNT(*) as total_students FROM students");
$total_students = $stmt->fetch()['total_students'] ?? 0;


$stmt = $pdo->query("SELECT class, COUNT(*) as cnt FROM students GROUP BY class");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT status, COUNT(*) as cnt FROM attendance WHERE date = ? GROUP BY status");
$stmt->execute([$today]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = ['present'=>0,'absent'=>0,'late'=>0];
foreach($rows as $r) $counts[$r['status']] = (int)$r['cnt'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>



  body { background-color: #ffd6ffff; }
  .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer; transition: transform .2s; }
  .card:hover { transform: translateY(-5px); }
  .btn-outline-pink { border-color: #db2777; color: #db2777; }
  .btn-outline-pink:hover { background-color: #db2777; color: #fff; }
  .stat-number { font-size: 2.5rem; font-weight: bold; color: #db2777; }
  .chart-card h6 { font-weight: 600; color: #4b5563; }
  .badge-status { font-size: 0.9rem; padding: 0.5em 0.8em; border-radius: 0.5rem; cursor:pointer; transition: transform .2s; }
  .badge-status:hover { transform: scale(1.1); }
  .badge-present { background-color: #db2777; color: #fff; }
  .badge-absent { background-color: #6f42c1; color: #fff; }
  .badge-late { background-color: #ff9f43; color: #fff; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <h2 class="mb-4 text-center text-pink fw-bold">📊 Dashboard</h2>

  <div class="row g-4">
    
    <div class="col-md-4">
      <div class="card text-center p-4" data-bs-toggle="modal" data-bs-target="#studentModal">
        <h6 class="text-secondary mb-2">Total Students</h6>
        <div class="stat-number"><?= $total_students ?></div>
        <small class="text-muted">Click to see class breakdown</small>
      </div>
    </div>

    
    <div class="col-md-8">
      <div class="card p-3 chart-card">
        <h6>Attendance Today (<?= htmlspecialchars($today) ?>)</h6>
        <canvas id="todayChart" height="150"></canvas>
        <div class="d-flex justify-content-around mt-3">
          <span class="badge-status badge-present" onclick="showAttendance('present')">
            Present: <?= $counts['present'] ?>
          </span>
          <span class="badge-status badge-absent" onclick="showAttendance('absent')">
            Absent: <?= $counts['absent'] ?>
          </span>
          <span class="badge-status badge-late" onclick="showAttendance('late')">
            Late: <?= $counts['late'] ?>
          </span>
        </div>
      </div>
    </div>
  </div>

 
  <div class="mt-5 text-center">
    <a href="students.php" class="btn btn-outline-pink m-2">Manage Students</a>
    <a href="attendance.php" class="btn btn-outline-pink m-2">Take Attendance</a>
    <a href="reports.php" class="btn btn-outline-pink m-2">Reports</a>
  </div>
</div>


<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Student Breakdown by Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <?php foreach($classes as $c): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Class <?= htmlspecialchars($c['class']) ?>
              <span class="badge bg-primary rounded-pill"><?= $c['cnt'] ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attendanceModalTitle">Attendance List</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="attendanceModalBody">
       
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

const ctx = document.getElementById('todayChart').getContext('2d');
const data = {
  labels: ['Present','Absent','Late'],
  datasets: [{
    label: 'Count',
    data: [<?= $counts['present']?>, <?= $counts['absent']?>, <?= $counts['late']?>],
    backgroundColor: ['#db2777','#6f42c1','#ff9f43'],
    borderWidth: 1
  }]
};
new Chart(ctx, {
  type: 'doughnut',
  data: data,
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom', labels: { padding: 15 } },
      tooltip: { callbacks: { label: function(ctx) { return ctx.label + ': ' + ctx.raw + ' student(s)'; } } }
    },
    onClick: (evt, item) => {
      if(item.length){
        const index = item[0].index;
        const status = ['present','absent','late'][index];
        showAttendance(status);
      }
    }
  }
});


function showAttendance(status){
  const modalTitle = document.getElementById('attendanceModalTitle');
  const modalBody = document.getElementById('attendanceModalBody');
  modalTitle.textContent = 'Attendance: ' + status.charAt(0).toUpperCase() + status.slice(1);

  
  fetch(`attendance_list.php?status=${status}&date=<?= $today ?>`)
    .then(res => res.text())
    .then(html => { modalBody.innerHTML = html; });

  const attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));
  attendanceModal.show();
}
</script>

</body>
</html>
