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
if (isset($_GET['partenaire_id'])) {
    $id = (int) $_GET['partenaire_id'];

    $stmt = $pdo->prepare("SELECT * FROM article WHERE partenaire_id = ?");
    $stmt->execute([$id]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($articles);
} else {
    echo json_encode([]);
}
?>