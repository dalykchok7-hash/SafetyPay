<?php
// Connexion à la base
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$f_name = $_POST['firstname'];
$l_name = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO parents(f_name,l_name, email,password) VALUES (?, ?,?,?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$f_name,$l_name, $email, $hashedPassword]);

header('Location: http://localhost/projet/home.html');
}
?>