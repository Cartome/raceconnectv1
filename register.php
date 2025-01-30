<?php
session_start();

// Connexion à la base de données
require('db_connection.php');

// Variable d'erreur pour les messages
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUsername = $stmt->fetch();

    if ($existingUsername) {
        // Si le nom d'utilisateur existe déjà
        $error_message = "Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.";
    } else {
        // Vérification si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            // Si l'email est déjà utilisé, on redirige vers la page de connexion
            $_SESSION['error_message'] = "Cet email est déjà associé à un compte. Veuillez vous connecter.";
            header("Location: login.php");
            exit();
        } else {
            // Si le nom d'utilisateur et l'email sont disponibles, on hache le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer l'utilisateur dans la base de données
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            // Rediriger vers la page de connexion avec un message de succès
            $_SESSION['success_message'] = "Votre compte a été créé avec succès. Veuillez vous connecter.";
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <title>RaceConnect</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="icon" type="img/google.jpg" href="img/google.jpg"> <!-- Favicon -->

</head>
<body>

<header>
    <h1>Créer un Compte</h1>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="login.php">Se Connecter</a></li>
        </ul>
    </nav>
</header>

<section class="container">
    <form action="register.php" method="POST">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>

        <label for="email">Adresse Email</label>
        <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Créer un compte</button>
    </form>

    <?php
    if (!empty($error_message)) {
        echo "<p style='color: red;'>" . htmlspecialchars($error_message) . "</p>";
    }
    ?>
</section>

<footer>
    <p>&copy; 2025 Événements de Voitures et Motos</p>
    <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
</footer>

</body>
</html>
