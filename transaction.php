<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
   <style>
/* Style général */
.table-container {
    padding: 30px;
    max-width: 1000px;
    margin: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

thead {
    background-color: #2c3e50;
    color: white;
}

th, td {
    padding: 16px;
    text-align: left;
    font-size: 15px;
    border-bottom: 1px solid #f0f0f0;
}

tbody tr:hover {
    background-color: #f7faff;
    cursor: pointer;
}

td.amount {
    color: #27ae60;
    font-weight: bold;
}

td.date {
    font-style: italic;
    color: #555;
}

.no-data {
    text-align: center;
    padding: 40px;
    font-size: 16px;
    color: #888;
}
</style>

    </style>

</head>
<body>
    <?php
    session_start();
    $idParent = $_SESSION['parent_id'];
    require 'db.php';
$sql = "
    SELECT recharge.*, enfant.name 
    FROM recharge 
    JOIN enfant  ON recharge.enfant_id = enfant.id_enfant
    WHERE enfant.parent_id = ?
    ORDER BY recharge.date_recharge DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idParent]);
$recharges = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Dernières Recharges</h1>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Enfant</th>
        <th>Montant</th>
        <th>Date de recharge</th>
    </tr>
    <?php foreach ($recharges as $recharge): ?>
    <tr>
        <td><?= htmlspecialchars($recharge['name']) ?></td>
        <td><?= number_format($recharge['montant'], 2, ',', ' ') ?> DT</td>
        <td><?= date('d/m/Y H:i', strtotime($recharge['date_recharge'])) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>