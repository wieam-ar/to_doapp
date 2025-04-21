<?php
require "config.php";
session_start();
if (!isset($_SESSION['users'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['users'];

// POURCENTAGE %
$stmt_completed = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_completed = 1");
$stmt_completed->execute([$user['id']]);
$completed_count = $stmt_completed->fetchColumn();

$stmt_active = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_completed = 0");
$stmt_active->execute([$user['id']]);
$active_count = $stmt_active->fetchColumn();

$total_tasks = $completed_count + $active_count;
$completed_percentage = $total_tasks > 0 ? round(($completed_count / $total_tasks) * 100) : 0;
$active_percentage = 100 - $completed_percentage;

setlocale(LC_TIME, 'fr_FR.UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - To-Do App</title>
  <link rel="stylesheet" href="Dash_style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!--SIDBARE O ADD BUTT + ----------------------------------------------------------------------------------->
  <div class="sidebar">
    <h2>To-Do App</h2>
    <button class="btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">Add Task +</button>
    <ul>
        <li class="active"><a href="dashboard.php" class="text-decoration-none text-dark d-block">Dashboard</a></li>
        <li><a href="Active.php" class="text-decoration-none text-dark d-block">Active</a></li>
        <li><a href="Completed.php" class="text-decoration-none text-dark d-block">Completed</a></li>
    </ul>
  </div>

  <div class="main">
<!-- ----------------------------------------Dash m3a button------------------------------------------------------------------------------------------ -->
    <div class="top-bar">
      <h4 class="Dsh">Dashboard</h4>
      <a href="login.php" class="logout-btn">Sign out
        <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10.3333 2.66667L9.39331 3.60667L11.1133 5.33333H4.33331V6.66667H11.1133L9.39331 8.38667L10.3333 9.33333L13.6666 6M1.66665 1.33333H6.99998V0H1.66665C0.933313 0 0.333313 0.6 0.333313 1.33333V10.6667C0.333313 11.4 0.933313 12 1.66665 12H6.99998V10.6667H1.66665V1.33333Z" fill="white"/>
        </svg>
      </a>
    </div>

    <!--Rectangle Hello withe pic-->
    <div class="greeting-box">
      <div>
        <h1>Hello, Beautiful <?php echo strtoupper($user['username']); ?>!</h1>
        <p>What do you want to do today?</p>
      </div>
      <div class="user">
        <img src="Todo_img.png" alt="User Avatar">
      </div>
    </div>
    <p class="text-end"><?php echo date('l, d F Y'); ?></p>
<!------------------------------------------------------------------------------------------------------------------------------------------------------>
<div class="header-container" style="display: flex; flex-direction: column; align-items: flex-start; position: relative;">
    <!-- BTN DELETE--------------------------------------->
    <form method="POST" style="position: absolute; right: 550px;">
        <button type="submit" name="delete_all" class="text-danger text-decoration-none" style="font-weight: bold; background: none; border: none; cursor: pointer;">
            Delete All
        </button>
    </form>
    <?php
    if (isset($_POST['delete_all'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM tasks");
            $stmt->execute();

            echo "All tasks have been deleted.";
        } catch (PDOException $e) {
            echo "Error deleting tasks: " . $e->getMessage();
        }
    }
    ?>

  <h2>Today's Tasks</h2>
</div>

<br>
<?php
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content">
  <div class="tasks">
    <ul class="task-list">
      <?php foreach ($tasks as $task): ?>
        <li class="<?= $task['is_completed'] ? '' : 'active-task' ?>">
        <input type="checkbox" onchange="updateStatus(this, <?= $task['id'] ?>)" <?= $task['is_completed'] ? 'checked' : '' ?>>
            <?= htmlspecialchars($task['task_name']) ?>
        </li>
        <?php endforeach; ?>
    </ul>
  </div>

  <div class="stats">
    <div class="stat-box">
      <span><?php echo $completed_percentage; ?>%</span>
      <p>Completed tasks</p>
    </div>
    <div class="stat-box active">
      <span><?php echo $active_percentage; ?>%</span>
      <p>Active tasks</p>
    </div>
  </div>
</div>

<!----------------------------------Alert bootstrap------------------------------------------------------------------------------------------------------------------>
  <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="post" action="add_task.php" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="taskName" class="form-label">Task Name</label>
            <input type="text" class="form-control" id="taskName" name="task" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Add Task</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  function updateStatus(checkbox, taskId) {
    const isChecked = checkbox.checked ? 1 : 0;

    fetch('updae_sttatu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&is_completed=${isChecked}`
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); 
        if (isChecked) {
            checkbox.closest('li').classList.add('completed'); 
        } else {
            checkbox.closest('li').classList.remove('completed');
        }
    })
    .catch(error => console.error('Error:', error));
}
    </script>

</body>
</html>
