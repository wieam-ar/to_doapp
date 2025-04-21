<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ToDo App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>To-Do App</h1>
        <p>Start organizing your life day by day</p>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <div class="password-container">
                <input type="password" name="password" placeholder="Password" required>
                <span class="toggle">&#128065;</span>
            </div>
            <button type="submit" name="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>

<?php
require "config.php";
session_start();

if (isset($_POST["submit"])) {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->execute([$username, $password]);
        if ($stmt->rowCount() >= 1) {
            $_SESSION['users'] = $stmt->fetch();
            header('Location: dashboard.php');
            exit();
        } else {
            echo '<div class="alert" id="alert">
                    <span>Login ou mot de passe incorrect!</span>
                    <span onclick="x()"><i class="fa-solid fa-x" style="color: #ffffff;"></i></span>
                  </div>';
        }
    }
}
?>

