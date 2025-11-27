<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=mon_app;charset=utf8", "root", "");

if (!isset($_SESSION['enfant_id'])) {
    die("Enfant non connecté.");
}

$enfant_id = $_SESSION['enfant_id'];

// 1. Récupérer les articles du panier
$stmt = $pdo->prepare("
    SELECT p.article_id, p.quantite, a.prix
    FROM panier p
    JOIN article a ON p.article_id = a.id_article
    WHERE p.enfant_id = ?
");
$stmt->execute([$enfant_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($articles) === 0) {
    die("Votre panier est vide.");
}

// 2. Calculer le total de la commande
$total = 0;
foreach ($articles as $item) {
    $total += $item['prix'] * $item['quantite'];
}

// 3. Insérer la commande
$stmt = $pdo->prepare("INSERT INTO commande (enfant_id, date_commande, total) VALUES (?, NOW(), ?)");
$stmt->execute([$enfant_id, $total]);
$commande_id = $pdo->lastInsertId();

$stmt = $pdo->prepare("UPDATE enfant set solde= solde - ? where id_enfant=?");
$stmt->execute([$total,$enfant_id]);




// 5. Vider le panier
$stmt = $pdo->prepare("DELETE FROM panier WHERE enfant_id = ?");
$stmt->execute([$enfant_id]);

// 6. Rediriger ou message de succès
header("Location: mes_commandes.php");
exit();
?>
