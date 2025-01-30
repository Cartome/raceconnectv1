<?php
session_start();
require('db_connection.php'); // Inclure le fichier de connexion à la base de données

// Vérification des données du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hachage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Vérification si l'email existe déjà dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // L'email est déjà utilisé
        echo "Cet email est déjà utilisé.";
    } else {
        // Insertion dans la base de données
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            // Rediriger vers la page de connexion après la création du compte
            header("Location: login.html");
            exit();
        } else {
            echo "Une erreur est survenue lors de l'inscription.";
        }
    }
}
?>
