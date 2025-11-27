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

$enfant_id = $_SESSION['enfant_id'];

if ($enfant_id) {
    $stmt = $pdo->prepare("SELECT * FROM commande WHERE enfant_id = ? AND statut = 'prete' AND notifie = 0");
    $stmt->execute([$enfant_id]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($commandes);
} else {
    echo json_encode([]);
}
?>
