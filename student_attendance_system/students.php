<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

$err = $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $no = trim($_POST['student_no']);
    $name = trim($_POST['name']);
    $class = trim($_POST['class']);
    if ($no && $name) {
        $stmt = $pdo->prepare("INSERT INTO students (student_no,name,class) VALUES (?,?,?)");
        try { 
            $stmt->execute([$no,$name,$class]); 
            $msg = '✅ Student added successfully.'; 
        } catch(Exception $e) { 
            $err = '⚠ Error: '.$e->getMessage(); 
        }
    } else $err = '⚠ Please fill in all required fields.';
}


$stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
$students = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Students</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<style>
  body { background-color: #f8f9fa; }
  .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
  .btn-pink { background-color: #db2777; color: #fff; transition: transform .2s; }
  .btn-pink:hover { background-color: #c026d3; transform: scale(1.05); }
  .btn-outline-pink { border-color: #db2777; color: #db2777; transition: transform .2s; }
  .btn-outline-pink:hover { background-color: #db2777; color: #fff; transform: scale(1.05); }
  .table-hover tbody tr:hover { background-color: rgba(219,39,119,0.1); }
  .alert { border-radius: 10px; }
  .form-control { border-radius: 10px; }
  h3 { color: #db2777; font-weight: 600; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Students</h3>
    <button class="btn btn-pink" data-bs-toggle="collapse" data-bs-target="#addForm">
      ➕ Add Student
    </button>
  </div>

  
  <div id="addForm" class="collapse mb-3">
    <?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif;?>
    <?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif;?>
    <div class="card p-3 mb-3">
      <form method="post" class="row g-2 align-items-center">
        <div class="col-md-3">
          <input name="student_no" placeholder="Student No" class="form-control" required>
        </div>
        <div class="col-md-5">
          <input name="name" placeholder="Full Name" class="form-control" required>
        </div>
        <div class="col-md-2">
          <input name="class" placeholder="Class" class="form-control">
        </div>
        <div class="col-md-2">
          <button name="add_student" class="btn btn-outline-pink w-100">Add</button>
        </div>
      </form>
    </div>
  </div>

 
  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Student No</th>
            <th>Name</th>
            <th>Class</th>
            
            
          </tr>
        </thead>
        <tbody>
          <?php foreach($students as $s): ?>
            <tr>
              <td><?=htmlspecialchars($s['id'])?></td>
              <td><?=htmlspecialchars($s['student_no'])?></td>
              <td><?=htmlspecialchars($s['name'])?></td>
              <td><?=htmlspecialchars($s['class'])?></td>
              <!-- Optional: Action buttons -->
              <!--
              <td>
                <a href="edit_student.php?id=<?=$s['id']?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_student.php?id=<?=$s['id']?>" class="btn btn-sm btn-danger">Delete</a>
              </td>
              -->
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
