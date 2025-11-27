<?php
$host = "localhost";
$dbname = "mon_app";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}



    $stmt = $pdo->query("SELECT * FROM commande ORDER BY date_commande DESC");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($commandes);
?>
