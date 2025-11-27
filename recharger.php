<?php
session_start();
require 'db.php';


$idEnfant = $_GET['id'];

// Vérifie que cet enfant appartient au parent connecté
$sql = "SELECT * FROM enfant WHERE id_enfant = ? AND parent_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idEnfant, $_SESSION['parent_id']]);
$enfant = $stmt->fetch();

if (!$enfant) {
    echo "Enfant introuvable ou non autorisé.";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = floatval($_POST['montant']);

    if ($montant > 0) {
        // Mise à jour du solde
        $update = $pdo->prepare("UPDATE enfant SET solde = solde + ? WHERE id_enfant = ?");
        $update->execute([$montant, $idEnfant]);

        $stmt1 = $pdo->prepare("INSERT INTO recharge (enfant_id, montant, date_recharge) VALUES (?, ?, NOW())");
        $stmt1->execute([$idEnfant, $montant]);

        header("Location: parent.php?");
        exit;
    } else {
        $error = "Montant invalide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recharger <?= htmlspecialchars($enfant['name']) ?></title>
    <style>
       body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(120deg, #f4f7fa, #e3f0ff);
      padding: 40px;
    }

    .payment-box {
      max-width: 450px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #2c3e50;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 8px;
    }

    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .row {
      display: flex;
      gap: 10px;
    }

    .row .form-group {
      flex: 1;
    }

    button {
      background-color: #27ae60;
      color: white;
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #219150;
    }

    .card-icons {
      text-align: center;
      margin-bottom: 15px;
    }

    .card-icons img {
      width: 45px;
      margin: 0 5px;
      opacity: 0.8;
    }

    .error {
      background: #ffe0e0;
      color: #c0392b;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
    }
    </style>
</head>
<body>
<div class="payment-box">
    <h2>Recharge pour : <?= htmlspecialchars($enfant['name']) ?></h2>

    <?php if (isset($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <div class="card-icons">
      <img src="https://img.icons8.com/color/48/visa.png" alt="Visa">
      <img src="https://img.icons8.com/color/48/mastercard-logo.png" alt="MasterCard">
      <img src="https://img.icons8.com/color/48/amex.png" alt="Amex">
    </div>

    <form method="post">
      <div class="form-group">
        <label for="name">Nom sur la carte</label>
        <input type="text" id="name" name="name" required>
      </div>

      <div class="form-group">
        <label for="card_number">Numéro de carte</label>
        <input type="text" id="card_number" name="card_number" maxlength="16" required>
      </div>

      <div class="row">
        <div class="form-group">
          <label for="expiry">Expiration</label>
          <input type="text" id="expiry" name="expiry" placeholder="MM/AA" maxlength="5" required>
        </div>
        <div class="form-group">
          <label for="cvv">CVV</label>
          <input type="text" id="cvv" name="cvv" maxlength="4" required>
        </div>
      </div>

      <div class="form-group">
        <label for="montant">Montant à recharger (DT)</label>
        <input type="number" id="montant" name="montant" step="0.01" required>
      </div>

      <button type="submit">Recharger le compte</button>
    </form>
  </div>
</body>
</html>
