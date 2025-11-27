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

// Récupérer les données du formulaire
$article_id = isset($_POST['article_id']) ? (int) $_POST['article_id'] : 0;
$quantite = isset($_POST['quantite']) ? (int) $_POST['quantite'] : 1;

// ⚠️ Assurez-vous que l'enfant est connecté
$enfant_id = $_SESSION['enfant_id'] ?? null;

if ($enfant_id && $article_id > 0) {
    // Vérifie si cet article est déjà dans le panier
    $stmt = $pdo->prepare("SELECT * FROM panier WHERE enfant_id = ? AND article_id = ?");
    $stmt->execute([$enfant_id, $article_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Met à jour la quantité
        $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE enfant_id = ? AND article_id = ?");
        $stmt->execute([$quantite, $enfant_id, $article_id]);
    } else {
        // Ajoute un nouvel article dans le panier
        $stmt = $pdo->prepare("INSERT INTO panier (enfant_id, article_id, quantite) VALUES (?, ?, ?)");
        $stmt->execute([$enfant_id, $article_id, $quantite]);
    }

    // Rediriger vers la page du menu ou panier
    header("Location: panier.php");
    exit();
} else {
    echo "Erreur : données manquantes.";
}
?>
