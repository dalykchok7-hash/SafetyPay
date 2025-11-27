<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=mon_app;charset=utf8", "root", "");

// Enfant connecté
if (!isset($_SESSION['enfant_id'])) die("Enfant non connecté");
$enfant_id = $_SESSION['enfant_id'];

// Récupération des articles dans le panier
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
foreach ($panier as $item) {
    $total += $item['prix'] * $item['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --danger-color: #f72585;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7ff;
            color: var(--dark-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }
        
        .cart-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #d1146a;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #3ab0d6;
            transform: translateY(-2px);
        }
        
        .btn-home {
            background-color: var(--dark-color);
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-home:hover {
            background-color: #343a40;
            transform: translateY(-2px);
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .total-amount {
            color: var(--primary-color);
            margin-left: 10px;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .empty-cart p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="container">
        <h1>🛒 Mon Panier</h1>
        
        <?php if (count($panier) > 0): ?>
        <div class="cart-container">
            <table>
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($panier as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nom']) ?></td>
                        <td><?= number_format($item['prix'], 2) ?> DT</td>
                        <td>
                            <form action="modifier_quantite.php" method="post" class="quantity-form">
                                <input type="hidden" name="id_panier" value="<?= $item['id_panier'] ?>">
                                <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="1" class="quantity-input">
                                <button type="submit" class="btn btn-primary" style="padding: 8px 12px;">✔</button>
                            </form>
                        </td>
                        <td><?= number_format($item['prix'] * $item['quantite'], 2) ?> DT</td>
                        <td>
                            <form action="supprimer_article.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                <input type="hidden" name="id_panier" value="<?= $item['id_panier'] ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total-section">
                <span>Total à payer :</span>
                <span class="total-amount"><?= number_format($total, 2) ?> DT</span>
            </div>
            
            <div class="action-buttons">
                <a href="home2.php" class="btn btn-home">🏠 Retour à l'accueil</a>
                <form action="valider_commande.php" method="post">
                    <button type="submit" class="btn btn-success">Valider la commande</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-cart">
            <p>Votre panier est vide</p>
            <a href="home2.php" class="btn btn-home">🏠 Retour à l'accueil</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
</body>
</html>