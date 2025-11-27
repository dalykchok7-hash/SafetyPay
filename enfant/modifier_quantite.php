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

$enfant_id = $_SESSION['enfant_id'] ?? null;
$id_panier = $_POST['id_panier'] ?? null;
$quantite = $_POST['quantite'] ?? null;

if ($enfant_id && $id_panier && $quantite > 0) {
    $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE id_panier = ? AND enfant_id = ?");
    $stmt->execute([$quantite, $id_panier, $enfant_id]);
}

header("Location: panier.php");
exit;
?>