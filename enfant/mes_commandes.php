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

if (!isset($_SESSION['enfant_id'])) die("Enfant non connecté");
$enfant_id = $_SESSION['enfant_id'];

// Récupérer les commandes de l'enfant
$stmt = $pdo->prepare("
    SELECT * FROM commande 
    WHERE enfant_id = ? 
    ORDER BY date_commande DESC
");
$stmt->execute([$enfant_id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { color: #4a6fa5; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #4a6fa5; color: white; }
        .btn { margin-top: 20px; background: #4a6fa5; color: white; padding: 10px 15px; border: none; border-radius: 4px; text-decoration: none; }
    </style>
</head>
<body>

<h1>Mes Commandes</h1>

<?php if (count($commandes) > 0): ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Total</th>
            <th>Statut</th>
            <th>Voir Détail</th>
        </tr>
        <?php foreach ($commandes as $cmd): ?>
            <tr>
                <td><?= date("d/m/Y H:i", strtotime($cmd['date_commande'])) ?></td>
                <td><?= number_format($cmd['total'], 2) ?> DT</td>
                <td><?= htmlspecialchars($cmd['statut']) ?></td>
                <td><a href="detail_commande.php?id=<?= $cmd['id_commande'] ?>" class="btn">Détail</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Vous n'avez encore passé aucune commande.</p>
<?php endif; ?>


<a href="home2.php" class="btn">🏠 Retour à l'accueil</a>

</body>
</html>
