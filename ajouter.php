<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['parent_id'])) {
    echo "❌ Erreur : vous devez être connecté en tant que parent.";
    exit;
}else{
    $idParent = $_SESSION['parent_id'];
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prenom = $_POST['firstname'];
    $age = $_POST['age'];
    $num=$_POST['numtel'];
    $psw=$_POST['mdp'];
    $mdpHash = password_hash($psw, PASSWORD_DEFAULT);
    // Vérifier si l'email existe déjà pour un enfant
$checkSql = "SELECT * FROM enfant WHERE numt = ?";
$stmt = $pdo->prepare($checkSql);
$stmt->execute([$num]);

if ($stmt->rowCount() > 0) {
    echo "❌ Erreur : Cet email d'enfant est déjà enregistré.";
} else {
    $sql = "INSERT INTO enfant (parent_id, name, age,numt,password) VALUES (?, ?, ?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idParent, $prenom, $age,$num,$mdpHash]);
    header('Location: parent.php');
}
       
    } 
?>
