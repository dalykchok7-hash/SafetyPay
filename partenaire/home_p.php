<?php
session_start();

// Connexion à la base de données
$host = "localhost";
$dbname = "mon_app";
$user = "root";
$pass = "";
date_default_timezone_set('Africa/Tunis');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}

// Vérifie si le partenaire est connecté
if (!isset($_SESSION['id_partenaire'])) {
    echo "❌ Utilisateur non connecté.";
    exit;
}
$partner_id = $_SESSION['id_partenaire'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['desc'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];

    // Traitement image
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $destination = 'uploads/' . $imageName;
        if (move_uploaded_file($imageTmp, $destination)) {
            $imagePath = $imageName;
        }
    }

    // Insertion en base de données
    $stmt = $pdo->prepare("INSERT INTO article (partenaire_id, nom, description, prix, categorie, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$partner_id, $nom, $description, $prix, $categorie, $imagePath]);

    echo "✅ Article ajouté avec succès.<br><br>";
}

// Récupération des articles
$articles = $pdo->prepare("SELECT * FROM article WHERE partenaire_id = ?");
$articles->execute([$partner_id]);
$articlesArray = $articles->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Partenaire - SafetyPay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #4a6fa5;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .card-title i {
            color: #4a6fa5;
            font-size: 1.8rem;
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #4a6fa5;
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
            background-color: white;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px 12px;
        }

        /* Upload d'image */
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            border-color: #4a6fa5;
            background-color: rgba(74, 111, 165, 0.05);
        }

        .file-input-label i {
            font-size: 2rem;
            color: #4a6fa5;
            margin-bottom: 1rem;
        }

        .file-input-text {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .file-input-hint {
            font-size: 0.9rem;
            color: #666;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            display: none;
        }

        /* Boutons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #4a6fa5;
            color: white;
            box-shadow: 0 4px 12px rgba(74, 111, 165, 0.2);
        }

        .btn-primary:hover {
            background-color: #3a5a80;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(74, 111, 165, 0.3);
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }

        /* Liste des articles */
        .articles-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .article-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .article-card:hover {
            transform: translateY(-5px);
        }

        .article-name {
            font-size: 1.2rem;
            color: #4a6fa5;
            margin-bottom: 10px;
        }

        .article-description {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .article-price {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .article-category {
            display: inline-block;
            padding: 4px 10px;
            background-color: #e9ecef;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #333;
            margin-bottom: 15px;
        }

        .article-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        /* Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .nav-links {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-links li {
                margin: 5px;
            }
            
            .solde {
                margin-top: 20px;
            }
        }

        @media (max-width: 768px) {
            .articles-list {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-store-alt" style="font-size: 2rem;"></i>
            <h2>SafetyPay Partenaire</h2>
        </div>
        <ul class="nav-links">
            <li class="active">
                <a href="#">
                    <i class="fas fa-utensils"></i>
                    <span>Gestion du Menu</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-chart-line"></i>
                    <span>Statistiques</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-percentage"></i>
                    <span>Promotions</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
            <li>
                <a href="#" id="open-commandes-modal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Commandes</span>
                </a>
            </li>
        </ul>
        <div class="solde">
            <h4>Statut du compte</h4>
            <h3><i class="fas fa-check-circle"></i> Actif</h3>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="header">
            <h1>Gestion du Menu</h1>
            <div class="user-profile">
                <i class="fas fa-user-circle" style="font-size: 2.5rem; color: #4a6fa5;"></i>
            </div>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($imagePath)): ?>
            <div class="alert alert-success">
                ✅ Article ajouté avec succès.
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-plus-circle"></i>
                Ajouter un nouvel article
            </h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom de l'article</label>
                    <input type="text" id="nom" name="nom" class="form-control" required placeholder="Ex: Burger Spécial">
                </div>

                <div class="form-group">
                    <label for="desc" class="form-label">Description</label>
                    <textarea id="desc" name="desc" class="form-control" required placeholder="Décrivez votre article..."></textarea>
                </div>

                <div class="form-group">
                    <label for="prix" class="form-label">Prix (TND)</label>
                    <input type="number" id="prix" name="prix" class="form-control" step="0.01" min="0" required placeholder="Ex: 12.50">
                </div>

                <div class="form-group">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-control" required>
                        <option value="" disabled selected>Sélectionnez une catégorie</option>
                        <option value="plats">Plats Principaux</option>
                   
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Image de l'article</label>
                    <div class="file-input-container">
                        <input type="file" id="image" name="image" class="file-input" accept="image/*">
                        <label for="image" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="file-input-text">Glissez-déposez votre image ici</span>
                            <span class="file-input-hint">ou cliquez pour sélectionner un fichier</span>
                        </label>
                    </div>
                    <img id="imagePreview" class="image-preview" alt="Aperçu de l'image">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter l'article
                    </button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-list"></i>
                Liste des articles
            </h2>
            
            <?php if (empty($articlesArray)): ?>
                <p style="text-align: center; color: #666;">Aucun article n'a été ajouté pour le moment.</p>
            <?php else: ?>
                <div class="articles-list">
                    <?php foreach ($articlesArray as $article): ?>
                        <div class="article-card">
                            <?php if ($article['image']): ?>
                                <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>" class="article-image">
                            <?php else: ?>
                                <div style="height: 180px; background-color: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                    <i class="fas fa-image" style="font-size: 3rem; color: #ddd;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="article-name"><?= htmlspecialchars($article['nom']) ?></h3>
                            <p class="article-description"><?= htmlspecialchars($article['description']) ?></p>
                            <p class="article-price"><?= htmlspecialchars($article['prix']) ?> TND</p>
                            <span class="article-category">
                                <?php 
                                    $categories = [
                                        'entrees' => 'Entrées',
                                        'plats' => 'Plats Principaux',
                                        'desserts' => 'Desserts',
                                        'boissons' => 'Boissons'
                                    ];
                                    echo htmlspecialchars($categories[$article['categorie']] ?? $article['categorie']);
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="commandes-modal" style="display: none; position: fixed; top: 0; left: 0; 
    width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000;">

    <div style="background: white; padding: 20px; width: 80%; max-width: 600px; 
        margin: 50px auto; border-radius: 10px; position: relative;">
        
        <span id="close-commandes-modal" 
            style="position: absolute; top: 10px; right: 15px; font-size: 24px; 
            cursor: pointer;">&times;</span>
        
        <h2>Mes Commandes</h2>
        <div id="liste-commandes">
            
        </div>
    </div>
</div>
<div id="validation-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000;">
    <div style="background:white; padding:20px; width:80%; max-width:400px; margin:100px auto; border-radius:10px; position:relative;">
        <span id="close-validation-modal" style="position:absolute; top:10px; right:15px; font-size:24px; cursor:pointer;">&times;</span>

        <h3>Changer le statut de la commande</h3>

        <form id="form-statut">
            <input type="hidden" name="commande_id" id="modal-commande-id">
            <label for="statut-select">Nouveau statut :</label>
            <select name="statut" id="statut-select">
                <option value="en-attente">En attente</option>
                <option value="prête">Prête</option>
                <option value="livrée">Livrée</option>
            </select>
            <br><br>
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
        // Gestion de l'aperçu de l'image
        document.getElementById('image').addEventListener('change', function(e) {
            const fileInput = e.target;
            const preview = document.getElementById('imagePreview');
            
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Animation lors de la soumission du formulaire
        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            submitBtn.disabled = true;
        });

        
document.getElementById('open-commandes-modal').addEventListener('click', function(e) {
    e.preventDefault();

    // Affiche la modal
    document.getElementById('commandes-modal').style.display = 'block';

    // Appelle le script PHP pour récupérer les commandes (via fetch)
    fetch('get_commandes.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('liste-commandes');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<p>Aucune commande trouvée.</p>';
                return;
            }

            data.forEach(commande => {
                const div = document.createElement('div');
                div.innerHTML = `
                    <p><strong>Commande #${commande.id_commande}</strong></p>
                    <p>Statut : ${commande.statut}</p>
                    <button class="ouvrir-modal-btn" data-id="${commande.id_commande}">Valider</button>
                    <hr>
                `;
                container.appendChild(div);
            });
        });
});

// Fermeture de la modal
document.getElementById('close-commandes-modal').addEventListener('click', function() {
    document.getElementById('commandes-modal').style.display = 'none';
});


// Ouvrir la modal avec l'ID de la commande
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('ouvrir-modal-btn')) {
        const commandeId = e.target.dataset.id;
        document.getElementById('modal-commande-id').value = commandeId;
        document.getElementById('validation-modal').style.display = 'block';
    }
});

// Fermer la modal
document.getElementById('close-validation-modal').addEventListener('click', function () {
    document.getElementById('validation-modal').style.display = 'none';
});

// Soumettre la mise à jour
document.getElementById('form-statut').addEventListener('submit', function (e) {
    e.preventDefault();

    const commandeId = document.getElementById('modal-commande-id').value;
    const statut = document.getElementById('statut-select').value;

    fetch('gerer_commandes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `commande_id=${commandeId}&statut=${statut}`
    })
    .then(res => res.text())
    .then(data => {
        alert("Commande mise à jour !");
        document.getElementById('validation-modal').style.display = 'none';
        // Option : recharger les commandes
    });
});

    </script>
</body>
</html>
