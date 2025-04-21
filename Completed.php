<?php
require "config.php";
session_start();
if (!isset($_SESSION['users'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['users'];

$query = "SELECT * FROM tasks WHERE user_id = :user_id AND is_completed = 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user['id']]);
$tasks = $stmt->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Completed Tasks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fff;
      color: #231f20;
    }
    .sidebar {
      background-color: #fff;
      border-right: 2px solid #ffd18a;
      width: 220px;
      padding: 20px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
    }
    .sidebar h2 {
      color: #c45d00;
      font-weight: bold;
      font-size: 24px;
      margin-bottom: 30px;
    }
    .sidebar .btn {
      background: #cc6a00;
      color: white;
      border: none;
      width: 100%;
      border-radius: 12px;
      margin-bottom: 20px;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar li {
      padding: 10px 15px;
      margin: 8px 0;
      border-radius: 8px;
    }
    .sidebar .active {
      background-color: #f5ad42;
      color: white;
    }
    .main {
      margin-left: 240px;
      padding: 40px 60px;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
    }
    .task-box {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .task-box input[type="checkbox"] {
      margin-right: 12px;
      transform: scale(1.2);
    }
    .completed-task {
      background-color: #ff9800; 
      color: white;
    }
    .task-detail-box {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.1);
      max-width: 320px;
      margin-left: auto;
    }
    .task-detail-box h5 {
      font-weight: bold;
    }
    .task-detail-box .icons {
      display: flex;
      justify-content: end;
      gap: 12px;
      margin-top: 12px;
    }
    .text-muted {
      font-size: 0.85rem;
      color: gray;
    }
    a.logout-btn {
      background: #b94d00;
      color: white;
      padding: 8px 18px;
      border-radius: 8px;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>To-Do App</h2>
    <button class="btn">Add Task +</button>
    <ul>
      <li><a href="dashboard.php" class="text-decoration-none text-dark">Dashboard</a></li>
      <li><a href="Active.php" class="text-decoration-none text-dark">Active</a></li>
      <li class="active">Completed</li>
    </ul>
  </div>
  <div class="main">
    <div class="top-bar">
      <h4>Completed</h4>
      <a href="login.php" class="logout-btn">Sign out</a>
    </div>
    <p class="text-end"><?php echo strftime('%A, %d %B %Y', time()); ?></p>

    <h5 class="mb-4">Today's Completed Tasks</h5>

    <?php foreach ($tasks as $task): ?>
      <div class="task-box <?php echo $task['is_completed'] ? 'completed-task' : ''; ?>">
        <form action="updae_sttatu.php" method="POST" class="d-flex justify-content-between w-100">
          <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
          <input type="hidden" name="is_completed" value="0">
          <div class="d-flex align-items-center">
            <input type="checkbox" checked onchange="this.form.submit()"> 
            <span class="ms-2"><?php echo htmlspecialchars($task['task_name']); ?></span>
          </div>
          <div class="icons">
            <span><a href="edit_task.php?id=<?php echo $task['id']; ?>">✎</a></span>
            <span><a href="delete_task.php?id=<?php echo $task['id']; ?>">🗑️</a></span>
          </div>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
