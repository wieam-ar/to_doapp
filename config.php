<?php
$host = "localhost";
$dbname = "todo_app";
$username = "root";
$port = "3306";
$password = "";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch (PDOException $e) {
  die( "Error de Connection: " . $e->getMessage());
}
?>