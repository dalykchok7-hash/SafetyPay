<?php
session_start();
header('Content-Type: application/json');

$pdo = new PDO("mysql:host=localhost;dbname=mon_app;charset=utf8", "root", "");

// Vérifie que l'enfant est connecté
if (!isset($_SESSION['enfant_id'])) {
    echo json_encode(['articles' => [], 'total' => 0]);
    exit;
}

$enfant_id = $_SESSION['enfant_id'];

$sql = "
    SELECT p.id_panier, a.nom, a.prix, p.quantite, a.id_article
    FROM panier p
    JOIN article a ON p.article_id = a.id_article
    WHERE p.enfant_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$enfant_id]);
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total
$total = 0;
foreach ($panier as &$item) {
    $item['total'] = $item['prix'] * $item['quantite'];
    $total += $item['total'];
}

echo json_encode([
    'articles' => $panier,
    'total' => $total
]);
?>