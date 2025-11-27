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

        session_start();
        if (!isset($_SESSION['enfant_id'])) {
            header('Location: login.php'); // ou toute autre page de redirection
            exit();
        }

         $id= $_SESSION['enfant_id'];    
        $sql="select * from enfant where id_enfant=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $enfant = $stmt->fetch(PDO::FETCH_ASSOC);


        // Récupérer la somme totale des recharges de l'enfant
$stmt2 = $pdo->prepare("SELECT SUM(montant) as total_recharge FROM recharge WHERE enfant_id = ?");
$stmt2->execute([$id]);
$resultat = $stmt2->fetch();
$budgetTotal = $resultat['total_recharge'] ?? 0; // si NULL => 0

// Calculer le pourcentage restant
$pourcentage = ($budgetTotal > 0) ? ( $enfant['solde']/ $budgetTotal) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafetyPay - Espace Enfant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset et base */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Barre latérale */
        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #4a6fa5;
            color: white;
            padding: 20px;
            position: fixed;
            display: flex;
            flex-direction: column;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 40px;
            margin-right: 10px;
        }

        .logo h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .nav-links {
            list-style: none;
            flex-grow: 1;
        }

        .nav-links li {
            margin-bottom: 10px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-links a:hover, .nav-links .active a {
            background-color: #3a5a80;
        }

        .nav-links i {
            margin-right: 10px;
            font-size: 18px;
        }

        .solde {
            margin-top: auto;
            padding: 15px;
            background-color: #3a5a80;
            border-radius: 8px;
            text-align: center;
        }

        .solde h4 {
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 400;
        }

        .solde h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        /* Contenu principal */
        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
        }

        .user-profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #4a6fa5;
        }

        /* Cartes */
        .card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .welcome-card h2 {
            margin-bottom: 10px;
            color: #4a6fa5;
        }

        .welcome-card p {
            margin-bottom: 20px;
            color: #666;
        }

        .balance-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .balance {
            font-weight: 600;
            color: #4a6fa5;
        }

        .progress-bar {
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background-color: #4a6fa5;
            border-radius: 5px;
        }

        /* Section partenaires */
        .partners-section {
            margin-bottom: 40px;
        }

        .partners-section h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .partners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }

        .partner-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            position: relative;
        }

        .partner-card:hover {
            transform: translateY(-5px);
        }

        .partner-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .partner-card h3 {
            padding: 15px 15px 5px;
            font-size: 18px;
        }

        .partner-card p {
            padding: 0 15px;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .promo-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff6b6b;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4a6fa5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #3a5a80;
        }

        .see-all {
            display: inline-block;
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .see-all:hover {
            color: #3a5a80;
        }

        /* Page détails */
        .details-header {
            position: relative;
            margin-bottom: 30px;
        }

        .details-header img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 12px;
        }

        .details-title {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: rgba(74, 111, 165, 0.9);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
        }

        .details-title h2 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .details-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .menu-section, .promos-section {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #4a6fa5;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .menu-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .menu-item:last-child {
            border-bottom: none;
        }

        .menu-item h4 {
            font-weight: 500;
        }

        .menu-item p {
            color: #666;
            font-size: 14px;
        }

        .menu-item .price {
            color: #4a6fa5;
            font-weight: 600;
        }

        .promo-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .promo-card h4 {
            color: #4a6fa5;
            margin-bottom: 5px;
        }

        .promo-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .promo-card .validity {
            font-size: 12px;
            color: #888;
        }

        .pay-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4a6fa5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
        }

        .pay-btn:hover {
            background-color: #3a5a80;
        }

        /* Pages listes */
        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            width: 300px;
        }

        .search-bar input {
            flex-grow: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px 0 0 8px;
            outline: none;
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: #4a6fa5;
            color: white;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }

        .filter-options {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-btn {
            padding: 8px 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background-color: #4a6fa5;
            color: white;
            border-color: #4a5a80;
        }

        /* Sections cachées par défaut */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        /* Style pour les résultats de recherche */
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 500;
        }

        .back-btn i {
            margin-right: 5px;
        }
 /* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInBg {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  z-index: 1000;
  opacity: 0;
  transition: opacity 0.3s ease;
  overflow-y: auto;
  padding: 20px;
  box-sizing: border-box;
}

.modal.show {
  opacity: 1;
  animation: fadeInBg 0.3s ease;
}

.modal-content {
  background: white;
  margin: 40px auto;
  max-width: 800px;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  transform: scale(0.95);
  transition: transform 0.3s ease;
  overflow: hidden;
  animation: fadeIn 0.4s ease forwards;
}

.modal.show .modal-content {
  transform: scale(1);
}

.close {
  position: absolute;
  top: 20px;
  right: 25px;
  font-size: 32px;
  color: #777;
  cursor: pointer;
  transition: all 0.2s;
  background: none;
  border: none;
  padding: 5px;
  line-height: 1;
}

.close:hover {
  color: #333;
  transform: rotate(90deg);
}

#modal-title {
  padding: 25px 25px 15px;
  margin: 0;
  color: #2c3e50;
  font-size: 1.8rem;
  border-bottom: 1px solid #eee;
}

#modal-body {
  padding: 20px 25px;
  max-height: 65vh;
  overflow-y: auto;
}

/* Articles style */
.article-card {
  padding: 20px 0;
  border-bottom: 1px solid #f5f5f5;
  transition: all 0.2s;
}

.article-card:last-child {
  border-bottom: none;
}

.article-card:hover {
  background-color: #f9f9f9;
}

.article-title {
  margin: 0 0 10px 0;
  color: #3498db;
  font-size: 1.4rem;
}

.article-desc {
  color: #666;
  margin: 0 0 10px 0;
  line-height: 1.5;
}

.article-price {
  font-weight: bold;
  color: #e74c3c;
  font-size: 1.3rem;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-content {
    margin: 20px auto;
    width: 95%;
  }
  
  #modal-title {
    font-size: 1.5rem;
    padding: 20px 15px 10px;
  }
  
  #modal-body {
    padding: 15px;
  }
}
/* Style pour les images des articles */
.article-image {
  width: 100%;
  max-width: 300px;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  margin: 15px auto;
  display: block;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.article-card {
  padding: 20px;
  border-bottom: 1px solid #f0f0f0;
  position: relative;
}

.article-form {
  margin-top: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.article-form input[type="number"] {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.article-form button {
  padding: 8px 15px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.article-form button:hover {
  background-color: #45a049;
}

/* Responsive Design */
@media (max-width: 600px) {
  .article-image {
    height: 150px;
    max-width: 100%;
  }
  
  .article-card {
    padding: 15px;
  }
  
  .article-form {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .article-form input[type="number"] {
    width: 60px;
  }
}
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            
            <h2>SafetyPay</h2>
        </div>
        <ul class="nav-links">
            <li class="active">
                <a href="#" data-section="accueil">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
            </li>
            
            <li>
                <a href="#" data-section="restaurants">
                    <i class="fas fa-utensils"></i>
                    <span>Restaurants</span>
                </a>
            </li>
            <li>
                <a href="#" data-section="cafes">
                    <i class="fas fa-coffee"></i>
                    <span>Cafés</span>
                </a>
            </li>
            <li>
                <a href="#" data-section="historique">
                    <i class="fas fa-history"></i>
                    <span>Historique</span>
                </a>
            </li>
            <li>
                <a href="#" data-section="reclamation">
                    <i class="fas fa-comment-alt"></i>
                    <span>Réclamation</span>
                </a>
            </li>
            <li>
                <a href="#" data-section="parametres">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </li>
            <li>
        <a href="#" data-section="panier">
            <i class="fas fa-shopping-cart"></i>
            <span>Mon Panier</span>
        </a>
    </li>
    <li>
        <a href="#" data-section="commandes">
            <i class="fas fa-list-alt"></i>
            <span>Mes Commandes</span>
        </a>
    </li>
        </ul>
        <div class="solde">
            <h4>Solde disponible</h4>
            <h3><?= number_format($enfant['solde'] ?? 0, 2) ?></h3>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
              <h1>Bienvenue,<?= $enfant['name'];?></h1>
            <div class="user-profile">
               
            </div>
        </div>

        <div class="content">
            <!-- Section Accueil -->
            <div id="accueil" class="content-section active">
                <div class="card welcome-card">
                    <h2>Découvrez nos partenaires</h2>
                    <p>Utilisez votre portefeuille pour payer dans tous les établissements partenaires SafetyPay.</p>
                    <div class="balance-info">
                        <span>Solde restant ce mois:</span>
                        <span class="balance"><?= number_format($enfant['solde'] ?? 0, 2)
                                                                                                            ?>/<?= $budgetTotal?></span></div>
                    <div class="progress-bar">
                    <div class="progress" style="width: <?= min($pourcentage, 100) ?>%;"></div>                    </div>
                </div>
        
                <?php
                      $sql='SELECT * FROM partenaire ';
                     $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $partenaires=$stmt->fetchAll(PDO::FETCH_ASSOC);
                   
                ?>
                <div class="partners-section">
                    <h2>Restaurants populaires</h2>
                    <div class="partners-grid">
                        <?php 
                    foreach($partenaires as $partenaire):
                        if($partenaire['type']==='restaurant'): ?>
                        <div class="partner-card">
                        <img src="<?= "../admin/images/" . htmlspecialchars($partenaire['photo'])?>" alt="Burger Palace">
                            <h3><?=$partenaire['nom'];?></h3>
                            <p>Spécialités de burgers • €€</p>
                          <a href="#" class="btn view-details" data-id="<?= $partenaire['id_partenaire'] ?>" >  Consulter</a>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <!-- Restaurant 1 -->
                        <div class="partner-card">
                            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Burger Palace">
                            <h3>Burger Palace</h3>
                            <p>Spécialités de burgers • €€</p>
                            <div class="promo-tag">Promo</div>
                            <a href="#" class="btn view-details" data-id="restaurant-1">Consulter</a>
                        </div>
                        
                        <!-- Restaurant 2 -->
                        <div class="partner-card">
                            <img src="https://images.unsplash.com/photo-1590947132387-155cc02f3212?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Pizza Mia">
                            <h3>Pizza Mia</h3>
                            <p>Pizzas artisanales • €€</p>
                            <a href="#" class="btn view-details" data-id="restaurant-2">Consulter</a>
                        </div>
                        
                        <!-- Restaurant 3 -->
                        <div class="partner-card">
                            <img src="https://images.unsplash.com/photo-1583623025817-d180a2221d0a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Sushi Zen">
                            <h3>Sushi Zen</h3>
                            <p>Cuisine japonaise • €€€</p>
                            <div class="promo-tag">Promo</div>
                            <a href="#" class="btn view-details" data-id="restaurant-3">Consulter</a>
                        </div>
                    </div>
                    <a href="#" class="see-all" data-section="restaurants">Voir tous les restaurants →</a>
                </div>

                <div class="partners-section">
                    <h2>Cafés populaires</h2>
                    <div class="partners-grid">
                         <?php 
                         foreach($partenaires as $partenaire):
                          if($partenaire['type']==='café'): ?>
                         <div class="partner-card">
                             <img src="<?= "../admin/images/" . htmlspecialchars($partenaire['photo'])?>" alt="Burger Palace">
                            <h3><?=$partenaire['nom'];?></h3>
                            <a href="articles.php?id=<?php echo $partenaire['id_partenaire']; ?>" class="btn-consulter">  Consulter</a>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <!-- Café 1 -->
                        <div class="partner-card">
                            <img src="https://images.unsplash.com/photo-1498804103079-a6351b050096?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Coffee Time">
                            <h3>Coffee Time</h3>
                            <p>Boissons chaudes et snacks</p>
                            <a href="#" class="btn view-details" data-id="cafe-1">Consulter</a>
                        </div>
                        
                        <!-- Café 2 -->
                        <div class="partner-card">
                            <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Sweet Corner">
                            <h3>Sweet Corner</h3>
                            <p>Pâtisseries maison</p>
                            <div class="promo-tag">Promo</div>
                            <a href="#" class="btn view-details" data-id="cafe-2">Consulter</a>
                        </div>
                        
                        <!-- Café 3 -->
                        <div class="partner-card">
                            <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Milk Shake">
                            <h3>Milk Shake</h3>
                            <p>Milkshakes gourmands</p>
                            <a href="#" class="btn view-details" data-id="cafe-3">Consulter</a>
                        </div>
                    </div>
                    <a href="#" class="see-all" data-section="cafes">Voir tous les cafés →</a>
                </div>
            </div>

            <!-- Section Restaurants -->
            <div id="restaurants" class="content-section">
                <div class="list-header">
                    <h1>Restaurants partenaires</h1>
                    <div class="search-bar">
                        <input type="text" id="search-restaurants" placeholder="Rechercher un restaurant...">
                        <button id="search-resto-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <div class="filter-options">
                    <button class="filter-btn active" data-filter="all">Tous</button>
                    <button class="filter-btn" data-filter="promo">Promos</button>
                    <button class="filter-btn" data-filter="low-price">€</button>
                    <button class="filter-btn" data-filter="mid-price">€€</button>
                    <button class="filter-btn" data-filter="high-price">€€€</button>
                </div>

                <div class="partners-grid" id="restaurants-grid">
                    <!-- Restaurant 1 -->
                    <div class="partner-card" data-name="burger palace" data-price="mid" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Burger Palace">
                        <h3>Burger Palace</h3>
                        <p>Spécialités de burgers • €€</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="restaurant-1">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 2 -->
                    <div class="partner-card" data-name="pizza mia" data-price="mid" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1590947132387-155cc02f3212?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Pizza Mia">
                        <h3>Pizza Mia</h3>
                        <p>Pizzas artisanales • €€</p>
                        <a href="#" class="btn view-details" data-id="restaurant-2">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 3 -->
                    <div class="partner-card" data-name="sushi zen" data-price="high" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1583623025817-d180a2221d0a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Sushi Zen">
                        <h3>Sushi Zen</h3>
                        <p>Cuisine japonaise • €€€</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="restaurant-3">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 4 -->
                    <div class="partner-card" data-name="la crêperie" data-price="low" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1537047902294-62a40c20a6ae?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="La Crêperie">
                        <h3>La Crêperie</h3>
                        <p>Crêpes et galettes • €</p>
                        <a href="#" class="btn view-details" data-id="restaurant-4">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 5 -->
                    <div class="partner-card" data-name="pasta fresca" data-price="mid" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Pasta Fresca">
                        <h3>Pasta Fresca</h3>
                        <p>Pâtes fraîches • €€</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="restaurant-5">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 6 -->
                    <div class="partner-card" data-name="green bowl" data-price="mid" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Green Bowl">
                        <h3>Green Bowl</h3>
                        <p>Cuisine healthy • €€</p>
                        <a href="#" class="btn view-details" data-id="restaurant-6">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 7 -->
                    <div class="partner-card" data-name="taco loco" data-price="low" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1601050690597-df0568f70950?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Taco Loco">
                        <h3>Taco Loco</h3>
                        <p>Tacos et burritos • €</p>
                        <a href="#" class="btn view-details" data-id="restaurant-7">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 8 -->
                    <div class="partner-card" data-name="le bistrot" data-price="mid" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Le Bistrot">
                        <h3>Le Bistrot</h3>
                        <p>Cuisine française • €€</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="restaurant-8">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 9 -->
                    <div class="partner-card" data-name="asian wok" data-price="mid" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Asian Wok">
                        <h3>Asian Wok</h3>
                        <p>Cuisine asiatique • €€</p>
                        <a href="#" class="btn view-details" data-id="restaurant-9">Consulter</a>
                    </div>
                    
                    <!-- Restaurant 10 -->
                    <div class="partner-card" data-name="the grill" data-price="high" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="The Grill">
                        <h3>The Grill</h3>
                        <p>Viandes et grillades • €€€</p>
                        <a href="#" class="btn view-details" data-id="restaurant-10">Consulter</a>
                    </div>
                </div>
            </div>

            <!-- Section Cafés -->
            <div id="cafes" class="content-section">
                <div class="list-header">
                    <h1>Cafés partenaires</h1>
                    <div class="search-bar">
                        <input type="text" id="search-cafes" placeholder="Rechercher un café...">
                        <button id="search-cafe-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <div class="filter-options">
                    <button class="filter-btn active" data-filter="all">Tous</button>
                    <button class="filter-btn" data-filter="promo">Promos</button>
                </div>

                <div class="partners-grid" id="cafes-grid">
                    <!-- Café 1 -->
                    <div class="partner-card" data-name="coffee time" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1498804103079-a6351b050096?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Coffee Time">
                        <h3>Coffee Time</h3>
                        <p>Boissons chaudes et snacks</p>
                        <a href="#" class="btn view-details" data-id="cafe-1">Consulter</a>
                    </div>
                    
                    <!-- Café 2 -->
                    <div class="partner-card" data-name="sweet corner" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Sweet Corner">
                        <h3>Sweet Corner</h3>
                        <p>Pâtisseries maison</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="cafe-2">Consulter</a>
                    </div>
                    
                    <!-- Café 3 -->
                    <div class="partner-card" data-name="milk shake" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Milk Shake">
                        <h3>Milk Shake</h3>
                        <p>Milkshakes gourmands</p>
                        <a href="#" class="btn view-details" data-id="cafe-3">Consulter</a>
                    </div>
                    
                    <!-- Café 4 -->
                    <div class="partner-card" data-name="juice bar" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1603569283847-aa295f0d016a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Juice Bar">
                        <h3>Juice Bar</h3>
                        <p>Jus frais</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="cafe-4">Consulter</a>
                    </div>
                    
                    <!-- Café 5 -->
                    <div class="partner-card" data-name="donut world" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Donut World">
                        <h3>Donut World</h3>
                        <p>Donuts</p>
                        <a href="#" class="btn view-details" data-id="cafe-5">Consulter</a>
                    </div>
                    
                    <!-- Café 6 -->
                    <div class="partner-card" data-name="ice cream" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Ice Cream">
                        <h3>Ice Cream</h3>
                        <p>Glaces artisanales</p>
                        <a href="#" class="btn view-details" data-id="cafe-6">Consulter</a>
                    </div>
                    
                    <!-- Café 7 -->
                    <div class="partner-card" data-name="bubble tea" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1536599424071-0b215a388ba7?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Bubble Tea">
                        <h3>Bubble Tea</h3>
                        <p>Thés à bulles</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="cafe-7">Consulter</a>
                    </div>
                    
                    <!-- Café 8 -->
                    <div class="partner-card" data-name="choco latte" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1572383672419-ab35444a6934?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Choco Latte">
                        <h3>Choco Latte</h3>
                        <p>Chocolats chauds</p>
                        <a href="#" class="btn view-details" data-id="cafe-8">Consulter</a>
                    </div>
                    
                    <!-- Café 9 -->
                    <div class="partner-card" data-name="tea garden" data-promo="false">
                        <img src="https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Tea Garden">
                        <h3>Tea Garden</h3>
                        <p>Thés du monde</p>
                        <a href="#" class="btn view-details" data-id="cafe-9">Consulter</a>
                    </div>
                    
                    <!-- Café 10 -->
                    <div class="partner-card" data-name="smoothie spot" data-promo="true">
                        <img src="https://images.unsplash.com/photo-1505576399279-565b52d4ac71?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Smoothie Spot">
                        <h3>Smoothie Spot</h3>
                        <p>Smoothies frais</p>
                        <div class="promo-tag">Promo</div>
                        <a href="#" class="btn view-details" data-id="cafe-10">Consulter</a>
                    </div>
                </div>
            </div>

            <!-- Section Historique -->
            <div id="historique" class="content-section">
                <h1>Historique des transactions</h1>
                <div class="card">
                    <p>Voici l'historique de vos dernières transactions :</p>
                    <ul style="margin-top: 20px; list-style: none;">
                        <li style="padding: 10px; border-bottom: 1px solid #eee;">Burger Palace - 8,50 € - 12/05/2023</li>
                        <li style="padding: 10px; border-bottom: 1px solid #eee;">Coffee Time - 3,20 € - 10/05/2023</li>
                        <li style="padding: 10px; border-bottom: 1px solid #eee;">Pizza Mia - 12,00 € - 08/05/2023</li>
                        <li style="padding: 10px;">Sweet Corner - 4,50 € - 05/05/2023</li>
                    </ul>
                </div>
            </div>

            <!-- Section Réclamation -->
            <div id="reclamation" class="content-section">
                <h1>Formulaire de réclamation</h1>
                <div class="card">
                    <form style="display: grid; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Sujet</label>
                            <input type="text" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Message</label>
                            <textarea style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 100px;"></textarea>
                        </div>
                        <button type="submit" style="background-color: #4a6fa5; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer;">Envoyer</button>
                    </form>
                </div>
            </div>

            <!-- Section Paramètres -->
            <div id="parametres" class="content-section">
                <h1>Paramètres du compte</h1>
                <div class="card">
                    <form style="display: grid; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Nom</label>
                            <input type="text" value="Lucas" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Prénom</label>
                            <input type="text" value="Dupont" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Email</label>
                            <input type="email" value="lucas.dupont@example.com" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Mot de passe</label>
                            <input type="password" placeholder="••••••••" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px;">Limite de dépense mensuelle</label>
                            <input type="number" value="60" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <button type="submit" style="background-color: #4a6fa5; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer;">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>

            <!-- Section Détails Restaurant (cachée par défaut) -->
            <div id="restaurant-details" class="content-section">
                <a href="#" class="back-btn" data-section="restaurants"><i class="fas fa-arrow-left"></i> Retour </a>
                
                <div class="details-header">
                    <img id="details-image" src="" alt="">
                    <div class="details-title">
                        <h2 id="details-name"></h2>
                        <p id="details-description"></p>
                    </div>
                </div>

                <div class="details-content">
                    <div class="menu-section">
                        <h3 class="section-title">Menu</h3>
                        <div id="menu-items"></div>
                    </div>

                    <div class="promos-section">
                        <h3 class="section-title">Promotions</h3>
                        <div id="promo-items"></div>
                        <a href="#" class="pay-btn">Payer avec SafetyPay</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

   <div id="articles-modal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 id="modal-title">Menu</h2>
    <div id="modal-body">
      <!-- Les articles seront affichés ici -->
    </div>
  </div>
</div>

<div id="toast-notification" style="
    display: none;
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #323232;
    color: #fff;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    font-size: 16px;
    z-index: 9999;
    transition: opacity 0.3s ease;
"></div>
<div id="section-panier" class="section-content" style="display: none;">
    <!-- Contenu du panier sera injecté ici -->
</div>

<div id="section-commandes" class="section-content" style="display: none;">
    <!-- Contenu des commandes sera injecté ici -->
</div>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
  const modal = document.getElementById("articles-modal");
  const modalBody = document.getElementById("modal-body");
  const modalTitle = document.getElementById("modal-title");
  const closeBtn = document.querySelector(".close");

  // Fonctions de gestion de la modal
  const modalManager = {
    open: () => {
      modal.style.display = "block";
      setTimeout(() => modal.classList.add('show'), 10);
    },
    close: () => {
      modal.classList.remove('show');
      setTimeout(() => {
        modal.style.display = "none";
        modalBody.innerHTML = "";
      }, 300);
    }
  };

  // Événements
  closeBtn.addEventListener("click", modalManager.close);
  window.addEventListener("click", (e) => e.target === modal && modalManager.close());

  // Gestion des clics sur les boutons
  document.querySelectorAll(".view-details").forEach(btn => {
    btn.addEventListener("click", async function(e) {
      e.preventDefault();
      
      const partenaireId = this.dataset.id;
      const partenaireName = this.dataset.name || "Menu";
      
      modalTitle.textContent = partenaireName;

      try {
        const response = await fetch(`get_articles.php?partenaire_id=${partenaireId}`);
        const data = await response.json();
        
      modalBody.innerHTML = data.length > 0 
  ? data.map(article => `
      <div class="article-card">
        <h3 class="article-title">${article.nom}</h3>
        ${article.description ? `<p class="article-desc">${article.description}</p>` : ''}
        <p class="article-price">${article.prix} DT</p>
        
        ${article.image ? `
          <img class="article-image" src="../partenaire/uploads/${article.image}" 
               alt="${article.nom}"
               onerror="this.style.display='none'">
        ` : ''}
        
        <form class="article-form" action="ajouter_panier.php" method="post">
          <input type="hidden" name="article_id" value="${article.id_article}">
          <input type="number" name="quantite" value="1" min="1">
          <button type="submit">Ajouter au panier</button>
        </form>
      </div>
    `).join('')
  : "<p style='text-align:center;color:#777;padding:20px;'>Aucun article disponible</p>";
        
        modalManager.open();
      } catch (err) {
        console.error("Erreur:", err);
        modalBody.innerHTML = `
          <div style="text-align:center;padding:40px 20px;color:#e74c3c;">
            <p>Une erreur est survenue lors du chargement des articles</p>
            <button onclick="location.reload()" style="margin-top:15px;padding:8px 20px;background:#3498db;color:white;border:none;border-radius:4px;cursor:pointer;">
              Réessayer
            </button>
          </div>
        `;
        modalManager.open();
      }
    });
  });
});
        // Navigation entre les sections
        document.querySelectorAll('.nav-links a, .see-all').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-section');
                
                // Masquer toutes les sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Afficher la section correspondante
                document.getElementById(sectionId).classList.add('active');
                
                // Mettre à jour le menu actif
                document.querySelectorAll('.nav-links li').forEach(item => {
                    item.classList.remove('active');
                });
                
                if (sectionId !== 'restaurant-details') {
                    document.querySelector(`.nav-links a[data-section="${sectionId}"]`).parentElement.classList.add('active');
                }
            });
        });

        // Affichage des détails d'un partenaire
        document.querySelectorAll('.view-details').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const partnerId = this.getAttribute('data-id');
                const partnerData = partnersData[partnerId];
                
                if (!partnerData) return;
                
                // Mettre à jour les détails
                document.getElementById('details-image').src = partnerData.image;
                document.getElementById('details-image').alt = partnerData.name;
                document.getElementById('details-name').textContent = partnerData.name;
                document.getElementById('details-description').textContent = partnerData.description;
                
                // Générer le menu
                const menuItemsContainer = document.getElementById('menu-items');
                menuItemsContainer.innerHTML = '';
                
                partnerData.menu.forEach(item => {
                    const menuItem = document.createElement('div');
                    menuItem.className = 'menu-item';
                    menuItem.innerHTML = `
                        <div>
                            <h4>${item.name}</h4>
                            <p>${item.description}</p>
                        </div>
                        <div class="price">${item.price}</div>
                    `;
                    menuItemsContainer.appendChild(menuItem);
                });
                
                // Générer les promos
                const promoItemsContainer = document.getElementById('promo-items');
                promoItemsContainer.innerHTML = '';
                
                if (partnerData.promos.length === 0) {
                    promoItemsContainer.innerHTML = '<p>Aucune promotion disponible pour le moment.</p>';
                } else {
                    partnerData.promos.forEach(promo => {
                        const promoCard = document.createElement('div');
                        promoCard.className = 'promo-card';
                        promoCard.innerHTML = `
                            <h4>${promo.title}</h4>
                            <p>${promo.description}</p>
                            <p class="validity">${promo.validity}</p>
                        `;
                        promoItemsContainer.appendChild(promoCard);
                    });
                }
                
                // Mettre à jour le bouton retour
                const backBtn = document.querySelector('.back-btn');
                backBtn.setAttribute('data-section', partnerId.startsWith('restaurant') ? 'restaurants' : 'cafes');
                
                // Masquer toutes les sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Afficher la section détails
                document.getElementById('restaurant-details').classList.add('active');
            });
        });

        // Filtrage des restaurants
        document.querySelectorAll('#restaurants .filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Active/désactive le bouton
                document.querySelectorAll('#restaurants .filter-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                const cards = document.querySelectorAll('#restaurants-grid .partner-card');
                
                cards.forEach(card => {
                    const hasPromo = card.getAttribute('data-promo') === 'true';
                    const priceRange = card.getAttribute('data-price');
                    
                    if (filter === 'all' || 
                        (filter === 'promo' && hasPromo) ||
                        (filter === 'low-price' && priceRange === 'low') ||
                        (filter === 'mid-price' && priceRange === 'mid') ||
                        (filter === 'high-price' && priceRange === 'high')) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Filtrage des cafés
        document.querySelectorAll('#cafes .filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Active/désactive le bouton
                document.querySelectorAll('#cafes .filter-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                const cards = document.querySelectorAll('#cafes-grid .partner-card');
                
                cards.forEach(card => {
                    const hasPromo = card.getAttribute('data-promo') === 'true';
                    
                    if (filter === 'all' || (filter === 'promo' && hasPromo)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Recherche de restaurants
        document.getElementById('search-resto-btn').addEventListener('click', function() {
            const searchTerm = document.getElementById('search-restaurants').value.toLowerCase();
            const cards = document.querySelectorAll('#restaurants-grid .partner-card');
            let hasResults = false;
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(searchTerm)) {
                    card.style.display = 'block';
                    hasResults = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (!hasResults) {
                // Afficher un message si aucun résultat
                // (à implémenter)
            }
        });

        // Recherche de cafés
        document.getElementById('search-cafe-btn').addEventListener('click', function() {
            const searchTerm = document.getElementById('search-cafes').value.toLowerCase();
            const cards = document.querySelectorAll('#cafes-grid .partner-card');
            let hasResults = false;
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(searchTerm)) {
                    card.style.display = 'block';
                    hasResults = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (!hasResults) {
                // Afficher un message si aucun résultat
                // (à implémenter)
            }
        });
        // Fonction de navigation principale
function navigateTo(sectionId) {
    function navigateTo(sectionId) {
    // ... (conservez le code existant)
    
    // Ajoutez cette condition pour l'accueil
    if (sectionId === 'accueil') {
        document.getElementById('restaurant-details').classList.remove('active');
    }
}
    // 1. Gestion des sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(sectionId).classList.add('active');
    
    // 2. Gestion du menu actif
    document.querySelectorAll('.nav-links li').forEach(item => {
        item.classList.remove('active');
    });
    if (sectionId !== 'restaurant-details') {
        const navItem = document.querySelector(`.nav-links a[data-section="${sectionId}"]`);
        if (navItem) navItem.parentElement.classList.add('active');
    }
    
    // 3. Mise à jour de l'URL
    history.pushState({section: sectionId}, '', `#${sectionId}`);
}

// Gestion du bouton retour navigateur
window.addEventListener('popstate', function(e) {
    if (e.state?.section) {
        navigateTo(e.state.section);
    }
});

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        navigateTo(window.location.hash.substring(1));
    } else {
        navigateTo('accueil');
    }
});

// Adaptation des liens existants
document.querySelectorAll('[data-section]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        navigateTo(this.getAttribute('data-section'));
    });
});
function showToast(message) {
    const toast = document.getElementById('toast-notification');
    toast.textContent = message;
    toast.style.display = 'block';
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.style.display = 'none', 300);
    }, 5000); // disparait après 5 secondes
}
setInterval(() => {
    fetch('verifier_commande.php')
        .then(res => res.json())
        .then(data => {
            data.forEach(commande => {
                showToast(`Ta commande n°${commande.id_commande} est prête !`);

                // Après notification, on signale au serveur que l'enfant a été notifié
                fetch('marquer_notifie.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `commande_id=${commande.id_commande}`
                });
            });
        });
}, 10000); // toutes les 10 secondes
document.querySelectorAll('[data-section]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        // Cacher toutes les sections
        document.querySelectorAll('.section-content').forEach(section => {
            section.style.display = 'none';
        });

        // Afficher la section ciblée
        const target = this.getAttribute('data-section');
        const sectionToShow = document.getElementById(`section-${target}`);
        if (sectionToShow) {
            sectionToShow.style.display = 'block';

            // Si c’est le panier ou les commandes, charger les données
            if (target === 'panier') {
                fetchPanier();
            } else if (target === 'commandes') {
                fetchCommandes();
            }
        }
    });
});
function fetchPanier() {
    fetch('get_panier.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('section-panier');
            const articles = data.articles;
            const total = data.total;

            container.innerHTML = '<h3>🛒 Mon Panier</h3>';

            if (articles.length === 0) {
                container.innerHTML += '<p>Votre panier est vide.</p>';
                return;
            }

            const table = document.createElement('table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${articles.map(article => `
                        <tr>
                            <td>${article.nom}</td>
                            <td>${article.prix.toFixed(2)} DT</td>
                            <td>${article.quantite}</td>
                            <td>${(article.total).toFixed(2)} DT</td>
                        </tr>
                    `).join('')}
                </tbody>
            `;

            container.appendChild(table);

            const totalDiv = document.createElement('div');
            totalDiv.classList.add('total-section');
            totalDiv.innerHTML = `
                <strong>Total à payer :</strong>
                <span class="total-amount">${total.toFixed(2)} DT</span>
            `;
            container.appendChild(totalDiv);
        });
}

function fetchCommandes() {
    fetch('get_commandes.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('section-commandes');
            container.innerHTML = '<h3>Mes Commandes</h3>';
            if (data.length === 0) {
                container.innerHTML += '<p>Aucune commande.</p>';
                return;
            }
            data.forEach(commande => {
                container.innerHTML += `
                    <div>
                        <strong>Commande #${commande.id_commande}</strong><br>
                        Statut : ${commande.statut} - Total : ${commande.total} €
                        <hr>
                    </div>
                `;
            });
        });
}

    </script>
</body>
</html>