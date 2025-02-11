<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Connexion à la base de données
require('db_connection.php');

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer l'historique des événements de l'utilisateur
$eventStmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY created_at DESC");
$eventStmt->execute([$_SESSION['user_id']]);
$events = $eventStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <title>RaceConnect Profil de <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="style2.css">
    <link rel="icon" type="img/google.jpg" href="img/google.jpg"> <!-- Favicon -->

</head>

<body>
    <header>
        <h1>RaceConnect</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="events.php">Événements</a></li>
                <li><a href="chat.php">chat</a></li>
                <li><a href="profile.php">Mon Profil</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <section class="profile">
        <h2>Votre profil</h2>
        <p><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
 
 <!-- Affichage de la photo de profil -->
<div class="profile-picture">
    <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'default-avatar.png'; ?>" 
         alt="Photo de profil" width="150" height="150">
</div>

<!-- Formulaire d'upload de photo de profil -->
<form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
    <label for="profile_picture">Changer la photo de profil :</label>
    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
    <button type="submit">Mettre à jour</button>
</form>

 
        <div class="event-history">
            <h3>Historique de vos événements</h3>
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <?php
                    // Récupérer le nom de l'utilisateur créant l'événement
                    $creatorStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                    $creatorStmt->execute([$event['user_id']]);
                    $creator = $creatorStmt->fetch();
                    ?>
                    <div class="event">
                        <p><strong>Nom de l'événement :</strong> <?php echo htmlspecialchars($event['name']); ?></p>
                        <p><strong>Créé par :</strong> <?php echo htmlspecialchars($creator['username']); ?></p>
                        <p><strong>Type :</strong> <?php echo htmlspecialchars($event['type']); ?></p>
                        <p><strong>Date :</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                        <p><strong>Description :</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                        <p><strong>Créé le :</strong> <?php echo htmlspecialchars($event['created_at']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Vous n'avez pas encore créé d'événements.</p>
            <?php endif; ?>
        </div>
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
