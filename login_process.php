<?php
session_start();
require('db_connection.php'); // Inclure le fichier de connexion à la base de données

// Vérification des données du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Recherche de l'utilisateur dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Les informations de connexion sont valides
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Rediriger vers la page du profil ou de l'accueil
        header("Location: profile.php");
        exit();
    } else {
        // Informations de connexion incorrectes
        echo "Email ou mot de passe incorrect.";
    }
}
?>
