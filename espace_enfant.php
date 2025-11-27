<?php session_start();
require 'db.php';

$idParent = $_SESSION['parent_id'];

$sql = "SELECT * FROM enfant WHERE parent_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idParent]);
$enfants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
      
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f1f4f9;
    margin: 0;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #333;
}

.container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.child-card {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    padding: 20px;
    width: 300px;
    transition: transform 0.2s ease-in-out;
}

.child-card:hover {
    transform: translateY(-5px);
}

.child-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.child-info h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 20px;
}

.child-body {
    margin-top: 10px;
}

.child-stat {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 13px;
    color: #888;
}

.stat-value {
    font-weight: bold;
    color: #2c3e50;
}

.progress-container {
    margin: 10px 0;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin-bottom: 4px;
}

.progress-bar {
    background-color: #e0e0e0;
    border-radius: 10px;
    height: 8px;
}

.progress-fill {
    height: 8px;
    border-radius: 10px;
    background-color: #3498db;
    width: 0%;
}

.child-actions {
    text-align: center;
    margin-top: 10px;
}

.child-actions a {
    display: inline-block;
    background-color: #27ae60;
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.3s;
}

.child-actions a:hover {
    background-color: #219150;
}
</style>

 
</head>
<body>
<h2>Mes Enfants</h2>
<div class="container">
<?php foreach ($enfants as $enfant): ?>
    <div class="child-card">
        <div class="child-header">
            <div class="child-info">
                <h3><?= htmlspecialchars($enfant['name']) ?></h3>
            </div>
        </div>
        
        <div class="child-body">
            <div class="child-stat">
                <div>
                    <div class="stat-label">Solde actuel</div>
                    <div class="stat-value"><?= number_format($enfant['solde'], 2, ',', ' ') ?>DT</div>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-label">
                    <span>Budget mensuel</span>
                    <span>--% utilisé</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
            </div>
            
            <div class="child-actions">
                <a href="recharger.php? id=<?= $enfant['id_enfant'] ?>">
                    <i class="fas fa-coins"></i> Recharger
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<script>
  
  document.addEventListener("DOMContentLoaded", () => {
    const fills = document.querySelectorAll(".progress-fill");
    fills.forEach(fill => {
        const percent = fill.getAttribute("data-percent");
        fill.style.width = percent + "%";
    });
});


</script>
</body>
</html>