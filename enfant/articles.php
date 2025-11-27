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

    $partenaireid = $_GET['id'] ; 
    

    $stmtMenu = $pdo->prepare("SELECT * FROM article WHERE partenaire_id = ?");
    $stmtMenu->execute([ $partenaireid]);
    $articles = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Partenaire - SafetyPay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #4a6fa5;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .menu-container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #4a6fa5;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 22px;
        }

        .menu-items {
            display: grid;
            gap: 20px;
        }

        .menu-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .menu-item:last-child {
            border-bottom: none;
        }

        .menu-item-info {
            flex: 1;
        }

        .menu-item h3 {
            color: #4a6fa5;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .menu-item p {
            color: #666;
            font-size: 14px;
        }

        .menu-item .price {
            color: #4a6fa5;
            font-weight: 600;
            font-size: 18px;
            margin-left: 20px;
            align-self: center;
        }

        .no-items {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 16px;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border: 1px solid #4a6fa5;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background-color: #4a6fa5;
            color: white;
        }

        .back-btn i {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .menu-container {
                padding: 20px;
            }
            
            .menu-item {
                flex-direction: column;
            }
            
            .menu-item .price {
                margin-left: 0;
                margin-top: 10px;
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="javascript:history.back()" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <h1>Menu du Partenaire</h1>
    </div>

    <div class="menu-container">
        <h2 class="section-title">Nos articles</h2>
        
        <div class="menu-items">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="menu-item">
                        <div class="menu-item-info">
                        <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>" style="width:100px; height:auto; margin-bottom:10px; border-radius:8px;">
                            <h3><?= htmlspecialchars($article['nom']) ?></h3>
                            <p><?= htmlspecialchars($article['description']) ?></p>
                        </div>
                        <div class="price"><?= htmlspecialchars($article['prix']) ?> DT</div>
                        <form action="ajouter_panier.php" method="post">
                        <input type="hidden" name="article_id" value="<?= $article['id_article'] ?>">
                         <input type="number" name="quantite" value="1" min="1" style="width: 50px;">
                        <button type="submit">Ajouter au panier</button>
                       
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-items">
                    <p>Aucun plat disponible pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>