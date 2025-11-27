<?php
session_start();
require 'db.php'; // ta connexion PDO ici
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'];
    $password = $_POST['password'];

    // ADMIN
    if ($identifiant === "admin@example.com" && $password === "admin123") {
        $_SESSION['admin'] = true;
        header("Location: admin/acceuil.php");
        exit;
    }

    $user = null;
    $role = null;

    if (filter_var($identifiant, FILTER_VALIDATE_EMAIL)) {
       // Vérifie d'abord si c'est un parent
        $stmt = $pdo->prepare("SELECT * FROM parents WHERE email = ?");
        $stmt->execute([$identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $role = 'parent';

        // Sinon, vérifie si c'est un partenaire
        if (!$user) {
            $stmt = $pdo->prepare("SELECT * FROM partenaire WHERE email = ?");
            $stmt->execute([$identifiant]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = 'partenaire';

        }
    }else{
       
            // C’est un enfant (numéro de téléphone)
            $stmt = $pdo->prepare("SELECT * FROM enfant WHERE numt = ?");
            $stmt->execute([$identifiant]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = 'enfant';
        
    }
    print_r($user);
    if (password_verify($password, $user['password'])) {
        switch ($role) {
            case 'parent':
                $_SESSION['parent_id'] = $user['id'];
                $_SESSION['f_name'] = $user['f_name'];
                $_SESSION['l_name'] = $user['l_name'];
                header("Location: parent.php");
                break;
            case 'enfant':
                $_SESSION['enfant_id'] = $user['id_enfant'];
                header("Location: enfant/home2.php");
                break;
            case 'partenaire':
                $_SESSION['id_partenaire'] = $user['id_partenaire'];
                header("Location: partenaire/home_p.php");
                break;
        }
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MonSite</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 200%;
            max-width: 500px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
            height: 100px;
                
        }
        
        .logo img {
            max-width: 200px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-group input:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 5px;
        }
        
        .forgot-password a {
            color: #4a90e2;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-button:hover {
            background-color: #3a7bc8;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .signup-link a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .social-login {
            margin-top: 30px;
            text-align: center;
        }
        
        .social-login p {
            color: #777;
            margin-bottom: 15px;
            position: relative;
        }
        
        .social-login p::before,
        .social-login p::after {
            content: "";
            display: inline-block;
            width: 30%;
            height: 1px;
            background-color: #ddd;
            position: absolute;
            top: 50%;
        }
        
        .social-login p::before {
            left: 0;
        }
        
        .social-login p::after {
            right: 0;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
        }
        
        .facebook {
            background-color: #3b5998;
            color: white;
        }
        
        .google {
            background-color: #db4437;
            color: white;
        }
        
        .twitter {
            background-color: #1da1f2;
            color: white;
        }
    </style>
     
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-/...==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <!-- Remplacez par votre logo -->
            <img src="im.png" alt="Logo">
        </div>
        
        <h1>Connexion à votre compte</h1>
        <form  method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text"  name="identifiant" placeholder="Email ou Numéro" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password"  name="password" placeholder="Entrez votre mot de passe" required>
            </div>
            
            
            

              <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
                <div class="forgot-password">
                    <a href="mot_de_passe_oub.html">Mot de passe oublié ?</a>
                </div>
            </div>
            
            <button type="submit" class="login-button">Se connecter</button>
        </form>
        
        <div class="signup-link">
            <p>Pas encore de compte ? <a href="inscription.html">S'inscrire</a></p>
        </div>
        
        <div class="social-login">
            <p>Ou connectez-vous avec</p>
            <div class="social-icons">
                <div class="social-icon facebook">
                    <i class="fab fa-facebook-f"></i>
                </div>
                <div class="social-icon google">
                    <i class="fab fa-google"></i>
                </div>
                <div class="social-icon twitter">
                    <i class="fab fa-twitter"></i>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>