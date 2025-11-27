<?php
session_start();
$host = "localhost";
$dbname = "mon_app";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}

if (isset($_POST['commande_id'])) {
    $id = (int) $_POST['commande_id'];
    $stmt = $pdo->prepare("UPDATE commande SET notifie = 1 WHERE id_commande = ?");
    $stmt->execute([$id]);
}
?>