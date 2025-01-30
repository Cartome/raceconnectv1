<?php
$host = ''; // Adresse de votre serveur MySQL
$dbname = ''; // Nom de votre base de données
$username = ''; // Votre utilisateur MySQL
$password = ''; // Votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit();
}
?>
