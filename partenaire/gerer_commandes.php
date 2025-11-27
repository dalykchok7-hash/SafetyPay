<?php
session_start();

// Connexion à la base de données
$host = "localhost";
$dbname = "mon_app";
$user = "root";
$pass = "";
date_default_timezone_set('Africa/Tunis');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}

if (isset($_POST['commande_id']) && isset($_POST['statut'])) {
    $commande_id = (int) $_POST['commande_id'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare("UPDATE commande SET statut = ? WHERE id_commande = ?");
    $stmt->execute([$statut, $commande_id]);

    echo "Statut mis à jour.";
}
?>
