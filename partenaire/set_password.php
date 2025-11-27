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

if (!isset($_GET['token'])) {
    die("Lien invalide.");
}

$token = $_GET['token'];
$stmt = $pdo->prepare("SELECT * FROM partenaire WHERE token = ? AND token_expiration >= NOW()");

$stmt->execute([$token]);
$partenaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$partenaire) {
    die("Lien expiré ou invalide.");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE partenaire SET password = ?, token = NULL, token_expiration = NULL WHERE id_partenaire = ?");
        $update->execute([$hashedPassword, $partenaire['id_partenaire']]);
        $success = "Mot de passe enregistré. Vous pouvez maintenant vous connecter.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6fa5;
            --primary-dark: #3a5a80;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --gray: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .password-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            transform: translateY(-20px);
            opacity: 0;
            animation: fadeInUp 0.5s forwards 0.3s;
        }

        @keyframes fadeInUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .password-header {
            background-color: var(--primary);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .password-header h2 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .password-header p {
            opacity: 0.9;
        }

        .password-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid var(--primary);
        }

        .password-form {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--gray);
            transition: all 0.3s;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            transition: all 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.2);
        }

        .form-control:focus + i {
            color: var(--primary);
        }

        .strength-meter {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-top: 10px;
            overflow: hidden;
            position: relative;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            background-color: var(--danger);
            border-radius: 5px;
            transition: all 0.3s;
        }

        .strength-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 12px;
            color: var(--gray);
        }

        .requirements {
            margin-top: 10px;
            font-size: 14px;
            color: var(--gray);
        }

        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .requirement i {
            margin-right: 8px;
            font-size: 12px;
            transition: all 0.3s;
        }

        .requirement.valid {
            color: var(--success);
        }

        .requirement.valid i {
            color: var(--success);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn:focus:not(:active)::after {
            animation: ripple 0.6s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        .password-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: var(--gray);
        }

        .password-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        /* Animations */
        .shake {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .pulse {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(74, 111, 165, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(74, 111, 165, 0); }
            100% { box-shadow: 0 0 0 0 rgba(74, 111, 165, 0); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .password-container {
                max-width: 100%;
            }
           
            .password-header {
                padding: 20px;
            }
           
            .password-form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="password-container">
        <div class="password-header">
            <h2><i class="fas fa-lock"></i> Changer le mot de passe</h2>
            <p>Créez un nouveau mot de passe sécurisé</p>
        </div>
       
        <form class="password-form" id="passwordForm" method="POST" action="">
           
            <div class="form-group">
                <label for="newPassword">Nouveau mot de passe</label>
                <div class="input-container">
                    <i class="fas fa-key"></i>
                    <input type="password" id="newPassword" name="password"class="form-control" required>
                </div>
               
                <div class="strength-meter">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="strength-labels">
                    <span>Faible</span>
                    <span>Moyen</span>
                    <span>Fort</span>
                </div>
               
                <div class="requirements">
                    <div class="requirement" id="lengthReq">
                        <i class="fas fa-circle"></i>
                        <span>8 caractères minimum</span>
                    </div>
                    <div class="requirement" id="numberReq">
                        <i class="fas fa-circle"></i>
                        <span>Au moins 1 chiffre</span>
                    </div>
                    <div class="requirement" id="specialReq">
                        <i class="fas fa-circle"></i>
                        <span>Au moins 1 caractère spécial</span>
                    </div>
                </div>
            </div>
           
            <div class="form-group">
                <label for="confirmPassword">Confirmer le mot de passe</label>
                <div class="input-container">
                    <i class="fas fa-check-circle"></i>
                    <input type="password" id="confirmPassword" name="confirm"class="form-control" required>
                </div>
                <div id="confirmMessage" style="font-size: 14px; margin-top: 5px;"></div>
            </div>
           
            <button type="submit" class="btn" id="submitBtn">
                 ajouter
            </button>
        </form>
       
        <div class="password-footer">
            <p>Vous avez des problèmes ? <a href="#">Contactez le support</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const strengthBar = document.getElementById('strengthBar');
            const submitBtn = document.getElementById('submitBtn');
            const confirmMessage = document.getElementById('confirmMessage');
           
            // Vérification en temps réel du nouveau mot de passe
            newPassword.addEventListener('input', function() {
                const password = this.value;
                const lengthReq = document.getElementById('lengthReq');
                const numberReq = document.getElementById('numberReq');
                const specialReq = document.getElementById('specialReq');
               
                // Vérifier la longueur
                if (password.length >= 8) {
                    lengthReq.classList.add('valid');
                    lengthReq.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    lengthReq.classList.remove('valid');
                    lengthReq.querySelector('i').className = 'fas fa-circle';
                }
               
                // Vérifier les chiffres
                if (/\d/.test(password)) {
                    numberReq.classList.add('valid');
                    numberReq.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    numberReq.classList.remove('valid');
                    numberReq.querySelector('i').className = 'fas fa-circle';
                }
               
                // Vérifier les caractères spéciaux
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    specialReq.classList.add('valid');
                    specialReq.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    specialReq.classList.remove('valid');
                    specialReq.querySelector('i').className = 'fas fa-circle';
                }
               
                // Calculer la force du mot de passe
                let strength = 0;
                if (password.length >= 8) strength += 1;
                if (/[A-Z]/.test(password)) strength += 1;
                if (/\d/.test(password)) strength += 1;
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
               
                // Mettre à jour la barre de force
                const width = (strength / 4) * 100;
                strengthBar.style.width = width + '%';
               
                if (strength <= 1) {
                    strengthBar.style.backgroundColor = '#dc3545'; // Rouge
                } else if (strength <= 2) {
                    strengthBar.style.backgroundColor = '#fd7e14'; // Orange
                } else {
                    strengthBar.style.backgroundColor = '#28a745'; // Vert
                }
               
                // Vérifier la confirmation si le champ est rempli
                if (confirmPassword.value) {
                    checkPasswordMatch();
                }
            });
           
            // Vérification de la correspondance des mots de passe
            confirmPassword.addEventListener('input', checkPasswordMatch);
           
            function checkPasswordMatch() {
                if (newPassword.value && confirmPassword.value) {
                    if (newPassword.value === confirmPassword.value) {
                        confirmMessage.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> Les mots de passe correspondent';
                        confirmPassword.style.borderColor = '#28a745';
                    } else {
                        confirmMessage.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Les mots de passe ne correspondent pas';
                        confirmPassword.style.borderColor = '#dc3545';
                    }
                } else {
                    confirmMessage.textContent = '';
                    confirmPassword.style.borderColor = '#e9ecef';
                }
            }
           
            // Soumission du formulaire
            document.getElementById('passwordForm').addEventListener('submit', function(e) {
                e.preventDefault();
               
                // Vérification finale
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('shake');
                    setTimeout(() => {
                        confirmPassword.classList.remove('shake');
                    }, 500);
                    return;
                }
               
                // Simulation d'envoi
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
               
                // Animation de succès
                setTimeout(() => {
                    submitBtn.classList.add('pulse');
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> Mot de passe mis à jour !';
                    submitBtn.style.backgroundColor = '#28a745';
                   
                    // Réinitialiser après 3 secondes
                    setTimeout(() => {
                        submitBtn.classList.remove('pulse');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Mettre à jour';
                        submitBtn.style.backgroundColor = '#4a6fa5';
                        this.reset();
                        strengthBar.style.width = '0';
                        confirmMessage.textContent = '';
                       
                        // Réinitialiser les icônes de validation
                        document.querySelectorAll('.requirement').forEach(req => {
                            req.classList.remove('valid');
                            req.querySelector('i').className = 'fas fa-circle';
                        });
                    }, 3000);
                }, 1500);
                document.getElementById('passwordForm').submit();
            });
        });
    </script>
</body>
</html>