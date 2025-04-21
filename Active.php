<?php
require "config.php";
session_start();
if (!isset($_SESSION['users'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['users'];

$query = "SELECT * FROM tasks WHERE user_id = :user_id AND is_completed = 0";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user['id']]);
$tasks = $stmt->fetchAll();

setlocale(LC_TIME, 'fr_FR.UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Active Tasks</title>
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
      position: relative;
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
      cursor: pointer;
    }
    .task-box input[type="checkbox"] {
      margin-right: 12px;
      transform: scale(1.2);
    }
    .task-detail-box {
      position: absolute;
      right: 40px;
      top: 180px;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.1);
      max-width: 320px;
      display: none;
      transition: all 0.3s ease-in-out;
      z-index: 1000;
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
      <li class="active">Active</li>
      <li><a href="Completed.php" class="text-decoration-none text-dark">Completed</a></li>
    </ul>
  </div>
  <div class="main">
    <div class="top-bar">
      <h4>Active</h4>
      <a href="login.php" class="logout-btn">Sign out</a>
    </div>
    <p class="text-end"><?php echo strftime('%A, %d %B %Y', time()); ?></p>

    <h5 class="mb-4">Today’s Active Tasks</h5>

    <?php foreach ($tasks as $task): ?>
      <div class="task-box"
           onmouseenter="showDetails('<?php echo htmlspecialchars($task['task_name']); ?>',
                                     '<?php echo htmlspecialchars($task['description']); ?>',
                                     '<?php echo $task['id']; ?>')"
           onmouseleave="hideDetails()">
        <div>
          <input type="checkbox" class="task-status" data-task-id="<?php echo $task['id']; ?>" <?php echo $task['is_completed'] ? 'checked' : ''; ?>>
          <?php echo htmlspecialchars($task['task_name']); ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="task-detail-box" id="task-detail">
      <h5 id="detail-title">Task Name</h5>
      <p class="text-muted" id="detail-time">Added just now</p>
      <p><strong>Description</strong></p>
      <p id="detail-description">Description goes here</p>
      <div class="icons">
        <a href="#" id="edit-link">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.25 5.75H11C11.1375 5.75 11.25 5.6375 11.25 5.5V5.75ZM11.25 5.75H20.75V5.5C20.75 5.6375 20.8625 5.75 21 5.75H20.75V8H23V5.5C23 4.39688 22.1031 3.5 21 3.5H11C9.89687 3.5 9 4.39688 9 5.5V8H11.25V5.75ZM27 8H5C4.44687 8 4 8.44688 4 9V10C4 10.1375 4.1125 10.25 4.25 10.25H6.1375L6.90938 26.5938C6.95938 27.6594 7.84063 28.5 8.90625 28.5H23.0938C24.1625 28.5 25.0406 27.6625 25.0906 26.5938L25.8625 10.25H27.75C27.8875 10.25 28 10.1375 28 10V9C28 8.44688 27.5531 8 27 8ZM22.8531 26.25H9.14688L8.39062 10.25H23.6094L22.8531 26.25Z" fill="#EDB046"/>
            </svg>
        </a>
        <a href="#" id="delete-link">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 26H30V28H2V26ZM25.4 9C26.2 8.2 26.2 7 25.4 6.2L21.8 2.6C21 1.8 19.8 1.8 19 2.6L4 17.6V24H10.4L25.4 9ZM20.4 4L24 7.6L21 10.6L17.4 7L20.4 4ZM6 22V18.4L16 8.4L19.6 12L9.6 22H6Z" fill="#EDB046"/>
            </svg>
        </a>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.task-status');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                const isChecked = this.checked ? 1 : 0;

                fetch('updae_sttatu.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `task_id=${taskId}&is_completed=${isChecked}`
                })
                .then(response => {
                    if (response.ok) {
                        console.log('Statut de la tâche mis à jour');
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Erreur:', error));
            });
        });
    });

    function showDetails(name, description, id) {
      const detailBox = document.getElementById('task-detail');
      detailBox.style.display = 'block';

      document.getElementById('detail-title').innerText = name;
      document.getElementById('detail-description').innerText = description;
      document.getElementById('detail-time').innerText = "Now";

      document.getElementById('edit-link').href = `edit_task.php?id=${id}`;
      document.getElementById('delete-link').href = `delete_task.php?id=${id}`;
    }

    function hideDetails() {
      const detailBox = document.getElementById('task-detail');
      detailBox.style.display = 'none';
    }
  </script>
</body>
</html>
