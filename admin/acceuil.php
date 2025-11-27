<?php
session_start();

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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // selon votre structure
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['emailp'];
    $type = $_POST['type'];
    $adresse = $_POST['adresse'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    // Utilise toujours des slashs `/` pour les chemins dans PHP (même sous Windows)
    move_uploaded_file($tmp, "C:\\wamp64\\www\\projet\\admin\\images\\".$image);
    $token = bin2hex(random_bytes(16)); // 32 caractères
    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));


    // Insertion dans la base
    $stmt = $pdo->prepare("INSERT INTO partenaire (nom, type, adresse, photo , token,email,token_expiration) VALUES (?, ?, ?, ?, ?,?,?)");
    $stmt->execute([$nom, $type, $adresse, $image, $token,$email,$expiration]);
    echo "Token expiration enregistrée : $expiration";


 

    $link = "http://localhost/projet/partenaire/set_password.php?token=$token";
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // ou votre serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'kchokmedali1@gmail.com'; // votre email
        $mail->Password = 'amzbujjfhcwslpwe'; // mot de passe d'application (non votre vrai mot de passe)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('kchokmedali1@gmail.com', 'Safety_pay');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Création de mot de passe - Partenaire';
        $mail->Body = "Bonjour,<br><br>Veuillez cliquer sur ce lien pour définir votre mot de passe : <a href='$link'>$link</a><br><br>Merci.";

        $mail->send();
        echo "✅ Partenaire ajouté et email envoyé avec succès.";
        header("Location: acceuil.php");
    } catch (Exception $e) {
        echo "Erreur lors de l’envoi de l’email : {$mail->ErrorInfo}";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --dark: #1b263b;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --gray: #adb5bd;
            --light-gray: #e9ecef;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .admin-link {
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .admin-link:hover {
            background-color: var(--secondary);
            color: white;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }

        .hero-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: white;
            color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Partners Section */
        .section {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            color: var(--dark);
        }

        .partners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .partner-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .partner-card:hover {
            transform: translateY(-5px);
        }

        .partner-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .partner-info {
            padding: 1.5rem;
        }

        .partner-name {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .partner-type {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background-color: var(--primary);
            color: white;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        .partner-address {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* Footer */
        .footer {
            background-color: var(--dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Section Admin - Version améliorée */
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .admin-title {
            font-size: 1.8rem;
            color: var(--dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .admin-title i {
            color: var(--primary);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.2rem;
            background-color: var(--light-gray);
            color: var(--dark);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: 1px solid var(--gray);
        }

        .back-btn:hover {
            background-color: var(--gray);
            color: white;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--light-gray);
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: var(--dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .card-title i {
            color: var(--primary);
            font-size: 1.8rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            background-color: white;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
        }

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
            padding: 3rem 2rem;
            border: 2px dashed var(--gray);
            border-radius: 12px;
            background-color: var(--light);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            border-color: var(--primary);
            background-color: rgba(67, 97, 238, 0.05);
        }

        .file-input-label i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .file-input-label span {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .file-input-label .hint {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .file-name {
            margin-top: 0.8rem;
            font-size: 0.95rem;
            color: var(--dark);
            font-weight: 500;
        }

        .btn-admin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            padding: 1rem 2rem;
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.3);
        }

        .preview-container {
            margin-top: 2rem;
            text-align: center;
            display: none;
        }

        .image-preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .additional-options {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--light-gray);
        }

        .options-title {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-weight: 600;
        }

        .switch-container {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin-right: 1rem;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .switch-label {
            font-weight: 500;
            color: var(--dark);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 1.5rem;
            }
            
            .card {
                padding: 1.8rem;
            }

            .admin-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .admin-title {
                font-size: 1.5rem;
            }

            .back-btn {
                width: 100%;
                justify-content: center;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">
                <i class="fas fa-handshake"></i>
                Espace_Admin
            </a>
            <div class="nav-links">
                <a href="#" class="nav-link">Accueil</a>
                <a href="#" class="nav-link">Partenaires</a>
                <a href="#" class="nav-link">À propos</a>
                <a href="#" class="nav-link">Contact</a>
                <a href="#admin" class="nav-link admin-link">
                    <i class="fas fa-user-shield"></i>
                    Espace Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <h1 class="hero-title">Découvrez nos partenaires exceptionnels</h1>
            <p class="hero-subtitle">Trouvez les meilleurs restaurants, cafés et établissements près de chez vous</p>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Explorer maintenant
            </a>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="section">
        <h2 class="section-title">Nos Partenaires</h2>
        <div class="partners-grid">
            <!-- Partenaire 1 -->
            <div class="partner-card">
                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Restaurant" class="partner-image">
                <div class="partner-info">
                    <h3 class="partner-name">Burger Palace</h3>
                    <span class="partner-type">Fast-food</span>
                    <p class="partner-address">15 Rue de la Paix, 75002 Paris</p>
                </div>
            </div>

            <!-- Partenaire 2 -->
            <div class="partner-card">
                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Café" class="partner-image">
                <div class="partner-info">
                    <h3 class="partner-name">Café des Arts</h3>
                    <span class="partner-type">Café</span>
                    <p class="partner-address">22 Avenue Montaigne, 75008 Paris</p>
                </div>
            </div>

            <!-- Partenaire 3 -->
            <div class="partner-card">
                <img src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Restaurant" class="partner-image">
                <div class="partner-info">
                    <h3 class="partner-name">La Bella Italia</h3>
                    <span class="partner-type">Restaurant</span>
                    <p class="partner-address">5 Rue du Faubourg Saint-Honoré, 75008 Paris</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Section - Version améliorée -->
    <section id="admin" class="section" style="background-color: #f5f7fa;">
        <div class="admin-container">
            <div class="admin-header">
                <h1 class="admin-title">
                    <i class="fas fa-plus-circle"></i>
                    Nouveau Partenaire
                </h1>
                <a href="#" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>

            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-store-alt"></i>
                    Informations du partenaire
                </h2>
                
                <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nom" class="form-label" >Nom du partenaire</label>
                    <input type="text" id="nom" name="nom" class="form-control" required placeholder="Ex: Burger Palace">
                </div>
                <div class="form-group">
                    <label  class="form-label" >Email:</label>
                    <input type="email"  name="emailp" class="form-control" >
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">Type d'établissement</label>
                    <select id="type" name="type" class="form-control form-select">
                        <option value="restaurant">Restaurant</option>
                        <option value="cafe">Café</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" id="adresse" name="adresse" class="form-control" required placeholder="Ex: 15 Rue de la Paix, 75002 Paris">
                </div>

                <div class="form-group">
                    <label class="form-label">Image du partenaire</label>
                    <div class="file-input-container">
                        <input type="file" id="image" name="image" class="file-input" accept="image/*" required>
                        <label for="image" class="file-input-label" id="fileLabel">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Cliquez pour télécharger une image</span>
                            <span>ou glissez-déposez votre fichier</span>
                            <span class="file-name" id="fileName">Aucun fichier sélectionné</span>
                        </label>
                    </div>
                    <div class="preview-container">
                        <img id="imagePreview" class="image-preview" alt="Aperçu de l'image">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Enregistrer le partenaire
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2023 Partenaires+. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Gestion améliorée de l'upload d'image
        document.getElementById('image').addEventListener('change', function(e) {
            const fileInput = e.target;
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('previewContainer');
            
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                
                // Vérification de la taille du fichier (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux (max. 5MB)');
                    fileInput.value = '';
                    fileName.textContent = 'Format recommandé : PNG ou JPG (max. 5MB)';
                    previewContainer.style.display = 'none';
                    return;
                }
                
                // Vérification du type de fichier
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Seuls les fichiers JPG, PNG ou GIF sont acceptés');
                    fileInput.value = '';
                    fileName.textContent = 'Format recommandé : PNG ou JPG (max. 5MB)';
                    previewContainer.style.display = 'none';
                    return;
                }
                
                // Afficher le nom du fichier
                fileName.textContent = `Fichier sélectionné : ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                
                // Afficher l'aperçu de l'image
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewContainer.style.display = 'block';
                    imagePreview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = 'Format recommandé : PNG ou JPG (max. 5MB)';
                previewContainer.style.display = 'none';
            }
        });

        // Gestion améliorée du drag and drop
        const fileLabel = document.getElementById('fileLabel');
        
        fileLabel.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileLabel.style.borderColor = '#4361ee';
            fileLabel.style.backgroundColor = 'rgba(67, 97, 238, 0.1)';
            fileLabel.style.boxShadow = '0 0 0 2px rgba(67, 97, 238, 0.2)';
        });

        fileLabel.addEventListener('dragleave', () => {
            fileLabel.style.borderColor = 'var(--gray)';
            fileLabel.style.backgroundColor = 'var(--light)';
            fileLabel.style.boxShadow = 'none';
        });

        fileLabel.addEventListener('drop', (e) => {
            e.preventDefault();
            fileLabel.style.borderColor = 'var(--gray)';
            fileLabel.style.backgroundColor = 'var(--light)';
            fileLabel.style.boxShadow = 'none';
            
            if (e.dataTransfer.files.length) {
                document.getElementById('image').files = e.dataTransfer.files;
                const event = new Event('change');
                document.getElementById('image').dispatchEvent(event);
            }
        });

        // Animation lors de la soumission du formulaire
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement en cours...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
