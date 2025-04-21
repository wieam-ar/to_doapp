<?php
session_start();
require "config.php";

if (!isset($_SESSION['users'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['users'];
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = trim($_POST['task']);

    if (!empty($task)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task_name) VALUES (?, ?)");
        $stmt->execute([$user_id, $task]);
    }
}

header("Location: dashboard.php");
exit();
?>
