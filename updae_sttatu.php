<?php
require "config.php";
session_start();
if (!isset($_SESSION['users'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['task_id']) && isset($_POST['is_completed'])) {
    $task_id = $_POST['task_id'];
    $is_completed = $_POST['is_completed'];

    $query = "UPDATE tasks SET is_completed = :is_completed WHERE id = :task_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'is_completed' => $is_completed,
        'task_id' => $task_id,
        'user_id' => $_SESSION['users']['id']
    ]);

    if ($is_completed == 1) {
        header("Location: Completed.php");
    } else {
        header("Location: Active.php");
    }
    exit();
}
?>
