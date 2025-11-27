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

// Récupération de l'ID de l'article à supprimer du panier
$id_panier = $_POST['id_panier'] ?? null;

if ($enfant_id && $id_panier) {
    // Supprimer l'article du panier seulement pour l'enfant connecté
    $stmt = $pdo->prepare("DELETE FROM panier WHERE id_panier = ? AND enfant_id = ?");
    $stmt->execute([$id_panier, $enfant_id]);

    // Redirection vers le panier
    header("Location: panier.php");
    exit;
} else {
    echo "Erreur : Impossible de supprimer l'article.";
}
?>