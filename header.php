<?php
session_start();
require('db_connection.php');

// Vérification si l'utilisateur est connecté
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>

<header>
    <h1>RaceConnect</h1>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="events.php">Événements</a></li>
            <li><a href="chat.php">Chat</a></li>
            <li><a href="profile.php">Mon Profil</a></li>
            <li><a href="logout.php">Se déconnecter</a></li>
        </ul>
    </nav>
    
    <?php if ($user): ?>
        <div class="profile-header">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Photo de profil">
            <span><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
    <?php endif; ?>
</header>
