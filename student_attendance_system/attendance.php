<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }


$date = $_POST['date'] ?? date('Y-m-d');


$students = $pdo->query("SELECT * FROM students ORDER BY name")->fetchAll();


$existing_attendance = [];
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE date = ?");
$stmt->execute([$date]);
foreach($stmt->fetchAll() as $row){
    $existing_attendance[$row['student_id']] = $row['status'];
}

$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    $statuses = $_POST['status'] ?? [];
    $pdo->beginTransaction();
    try {
        foreach ($statuses as $student_id => $status) {
            $stmt = $pdo->prepare("INSERT INTO attendance (student_id,date,status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)");
            $stmt->execute([$student_id, $date, $status]);
        }
        $pdo->commit();
        $msg = 'Attendance saved for ' . htmlspecialchars($date);

        
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE date = ?");
        $stmt->execute([$date]);
        $existing_attendance = [];
        foreach($stmt->fetchAll() as $row){
            $existing_attendance[$row['student_id']] = $row['status'];
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $err = 'Save failed: ' . $e->getMessage();
    }
}


$filter_date = $_POST['filter_date'] ?? '';
$query = "SELECT a.*, s.student_no, s.name FROM attendance a JOIN students s ON a.student_id=s.id";
$params = [];
if($filter_date) { $query .= " WHERE a.date = ?"; $params[] = $filter_date; }
$query .= " ORDER BY a.date DESC, s.name LIMIT 100";
$list = $pdo->prepare($query);
$list->execute($params);
$att_list = $list->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Take Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<style>
body {
    background: linear-gradient(135deg, #f7d9f0, #e9c0ff, #ffd6f2, #e8b0ff);
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
    padding: 30px;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.85);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    animation: fadeSlideIn 0.8s ease forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeSlideIn {
    to { opacity:1; transform: translateY(0); }
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

.text-purple { color: #6f2da8; }

.table thead {
    background-color: #d18cd1;
    color: #fff;
}

.table tbody tr:hover { background-color: #f4e1f7; }

.form-select { max-width: 150px; }

.badge-present { background-color: #198754; }
.badge-absent { background-color: #dc3545; }
.badge-late { background-color: #fd7e14; }

.alert { border-radius: 12px; }

@media (max-width: 768px) {
    .form-select { max-width: 100%; }
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-5">
    
    <div class="card mb-4">
        <h3 class="mb-3 text-purple">Take Attendance</h3>
        <?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif;?>
        <?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif;?>

        <form method="post">
            <div class="mb-4 row align-items-center">
                <label class="col-auto col-form-label fw-semibold">Date</label>
                <div class="col-auto">
                    <input type="date" name="date" class="form-control" value="<?=htmlspecialchars($date)?>" onchange="this.form.submit()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s): 
                            $sel_status = $existing_attendance[$s['id']] ?? 'present';
                            $badge_class = $sel_status=='present'?'badge-present':($sel_status=='absent'?'badge-absent':'badge-late');
                        ?>
                        <tr>
                            <td><?=htmlspecialchars($s['student_no'])?></td>
                            <td class="text-start"><?=htmlspecialchars($s['name'])?></td>
                            <td>
                                <select name="status[<?= $s['id'] ?>]" class="form-select">
                                    <option value="present" <?= $sel_status=='present'?'selected':'' ?>>Present</option>
                                    <option value="absent" <?= $sel_status=='absent'?'selected':'' ?>>Absent</option>
                                    <option value="late" <?= $sel_status=='late'?'selected':'' ?>>Late</option>
                                </select>
                                <span class="badge <?=$badge_class?> mt-1"><?=ucfirst($sel_status)?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button name="save_attendance" class="btn btn-pink">Save Attendance</button>
            </div>
        </form>
    </div>

    
    <div class="card">
        <h5 class="mb-3 text-purple">Recent Attendance Records</h5>
        <form method="post" class="row g-3 mb-3">
            <div class="col-auto">
                <input type="date" name="filter_date" class="form-control" value="<?=htmlspecialchars($filter_date)?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-secondary" type="submit">Filter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($att_list as $a): 
                        $status_class = $a['status']=='present'?'badge-present':($a['status']=='absent'?'badge-absent':'badge-late');
                    ?>
                    <tr>
                        <td><?=htmlspecialchars($a['date'])?></td>
                        <td class="text-start"><?=htmlspecialchars($a['student_no'].' - '.$a['name'])?></td>
                        <td><span class="badge <?=$status_class?>"><?=ucfirst($a['status'])?></span></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
