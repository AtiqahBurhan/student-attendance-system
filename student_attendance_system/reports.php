<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }


$start_date = $_POST['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
$end_date   = $_POST['end_date'] ?? date('Y-m-d');


$stmt = $pdo->prepare("SELECT date, 
                              SUM(status='present') AS present,
                              SUM(status='absent') AS absent,
                              SUM(status='late') AS late
                       FROM attendance
                       WHERE date BETWEEN ? AND ?
                       GROUP BY date
                       ORDER BY date ASC");
$stmt->execute([$start_date, $end_date]);
$data = $stmt->fetchAll();


$labels = $present = $absent = $late = [];
foreach($data as $d){
    $labels[] = $d['date'];
    $present[] = (int)$d['present'];
    $absent[]  = (int)$d['absent'];
    $late[]    = (int)$d['late'];
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Attendance Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #f7d9f0, #e9c0ff, #ffd6f2, #e8b0ff);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.card {
    border-radius: 25px;
    padding: 25px;
    margin-bottom: 25px;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    animation: fadeSlideIn 0.8s ease forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeSlideIn {
    to { opacity:1; transform: translateY(0); }
}

h3 { color: #6f2da8; margin-bottom: 20px; }
.btn-pink { background-color: #ff66b3; color: #fff; font-weight: 500; transition: all 0.3s ease; }
.btn-pink:hover { background-color: #e65599; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(255,102,179,0.4); }

.table thead { background-color: #d18cd1; color: #fff; }
.table tbody tr:hover { background-color: #f4e1f7; }
canvas { background-color:#fff; border-radius:15px; padding:15px; }

.badge-present { background-color: #198754; }
.badge-absent  { background-color: #dc3545; }
.badge-late    { background-color: #fd7e14; }

@media (max-width:768px){ canvas{height:250px !important;} }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-5">
    <h3>Attendance Reports</h3>

    
    <div class="card">
        <form method="post" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?=htmlspecialchars($start_date)?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?=htmlspecialchars($end_date)?>">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-pink w-100">Filter</button>
            </div>
        </form>
    </div>

    
    <div class="card">
        <canvas id="trendChart" height="100"></canvas>
    </div>

    
    <div class="card">
        <h5 class="mb-3 text-purple">Summary Table</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $d): ?>
                    <tr>
                        <td><?=htmlspecialchars($d['date'])?></td>
                        <td><span class="badge badge-present"><?=htmlspecialchars($d['present'])?></span></td>
                        <td><span class="badge badge-absent"><?=htmlspecialchars($d['absent'])?></span></td>
                        <td><span class="badge badge-late"><?=htmlspecialchars($d['late'])?></span></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const labels = <?=json_encode($labels)?>;
const chartData = {
    labels: labels,
    datasets: [
        { label:'Present', data: <?=json_encode($present)?>, borderColor:'#198754', backgroundColor:'#198754', fill:false, tension:0.3, pointRadius:6, pointHoverRadius:8 },
        { label:'Absent',  data: <?=json_encode($absent)?>,  borderColor:'#dc3545', backgroundColor:'#dc3545', fill:false, tension:0.3, pointRadius:6, pointHoverRadius:8 },
        { label:'Late',    data: <?=json_encode($late)?>,    borderColor:'#fd7e14', backgroundColor:'#fd7e14', fill:false, tension:0.3, pointRadius:6, pointHoverRadius:8 }
    ]
};

const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive:true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { mode:'index', intersect:false }
        },
        interaction: { mode:'nearest', axis:'x', intersect:false },
        scales: {
            x: { title: { display:true, text:'Date' } },
            y: { title: { display:true, text:'Number of Students' }, beginAtZero:true }
        }
    }
});
</script>

</body>
</html>
