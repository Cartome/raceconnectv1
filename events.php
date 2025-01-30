<?php
session_start();

// Connexion à la base de données
require('db_connection.php');

// Récupérer les événements avec les noms des utilisateurs
$stmt = $pdo->prepare("SELECT e.*, u.username FROM events e JOIN users u ON e.user_id = u.id ORDER BY e.created_at DESC");
$stmt->execute();
$events = $stmt->fetchAll();

// Gérer le commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_POST['event_id'])) {
    $comment = $_POST['comment'];
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (event_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$event_id, $user_id, $comment]);
    }
}

// Gérer le like
if (isset($_POST['like']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur a déjà liké cet événement
    $like_stmt = $pdo->prepare("SELECT id FROM likes WHERE event_id = ? AND user_id = ?");
    $like_stmt->execute([$event_id, $user_id]);

    if ($like_stmt->rowCount() == 0) {
        // Ajouter le like
        $stmt = $pdo->prepare("INSERT INTO likes (event_id, user_id) VALUES (?, ?)");
        $stmt->execute([$event_id, $user_id]);
    } else {
        // Supprimer le like
        $stmt = $pdo->prepare("DELETE FROM likes WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$event_id, $user_id]);
    }
}

// Gérer la suppression de commentaire
if (isset($_POST['delete_comment']) && isset($_POST['comment_id']) && isset($_POST['event_id'])) {
    $comment_id = $_POST['comment_id'];
    $event_id = $_POST['event_id'];
    
    // Vérifier si l'utilisateur est le créateur de l'événement
    $stmt = $pdo->prepare("SELECT user_id FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event_creator = $stmt->fetchColumn();

    if ($_SESSION['user_id'] == $event_creator) {
        // Supprimer le commentaire si l'utilisateur est le créateur de l'événement
        $delete_stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $delete_stmt->execute([$comment_id]);
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
    <title>RaceConnect-Événements</title>
    <link rel="stylesheet" href="style3.css">
    <link rel="icon" type="img/google.jpg" href="img/google.jpg"> <!-- Favicon -->

</head>
<body>

<header>
    <h1>Événements de RaceConnect</h1>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="create_event.php">Créer un événement</a></li>
            <li><a href="profile.php">Mon Profil</a></li>
            <li><a href="logout.php">Se déconnecter</a></li>
        </ul>
    </nav>
</header>

<div class="events-section">
    <h2>Nos Événements</h2>

    <?php foreach ($events as $event): ?>
    <div class="event-card">
        <h3><?php echo htmlspecialchars($event['name']); ?></h3>
        <div class="event-info">
            <p><strong>Type:</strong> <?php echo htmlspecialchars($event['type']); ?></p>
            <p><strong>Date de l'événement:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
            <p><strong>Posté par:</strong> <?php echo htmlspecialchars($event['username']); ?></p> <!-- Nom de l'utilisateur -->
            <p><strong>Posté le:</strong> <?php echo date('d/m/Y H:i', strtotime($event['created_at'])); ?></p>
        </div>
        <div class="event-description">
            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
        </div>

        <!-- Likes -->
        <?php
        $like_stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE event_id = ?");
        $like_stmt->execute([$event['id']]);
        $like_count = $like_stmt->fetchColumn();

        $user_liked_stmt = $pdo->prepare("SELECT id FROM likes WHERE event_id = ? AND user_id = ?");
        $user_liked_stmt->execute([$event['id'], $_SESSION['user_id']]);
        $user_liked = $user_liked_stmt->rowCount() > 0;
        ?>
        <div class="event-footer">
            <form action="events.php" method="POST" style="display:inline;">
                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                <button type="submit" name="like">
                    <?php echo $user_liked ? 'Ne plus aimer' : 'Aimer'; ?>
                </button>
            </form>
            <p><strong>Likes:</strong> <?php echo $like_count; ?></p>
            
            <!-- Lien vers la page des détails -->
            <a href="event_details.php?id=<?php echo $event['id']; ?>">Voir plus</a>

            <!-- Vérification si l'utilisateur est le créateur de l'événement -->
            <?php if ($_SESSION['user_id'] == $event['user_id']): ?>
                <!-- Formulaire de suppression -->
                <form action="delete_event.php" method="POST" style="display:inline;">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <button type="submit" class="delete-button">Supprimer</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Commentaires -->
        <div class="comments-section">
            <h4>Commentaires :</h4>
            <?php
            $comment_stmt = $pdo->prepare("SELECT c.id, c.comment, u.username, c.created_at FROM comments c JOIN users u ON c.user_id = u.id WHERE c.event_id = ? ORDER BY c.created_at DESC");
            $comment_stmt->execute([$event['id']]);
            $comments = $comment_stmt->fetchAll();

            foreach ($comments as $comment):
            ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> - <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>

                    <!-- Formulaire de suppression de commentaire (visible seulement pour le créateur de l'événement) -->
                    <?php if ($_SESSION['user_id'] == $event['user_id']): ?>
                        <form action="events.php" method="POST" style="display:inline;">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <button type="submit" name="delete_comment" class="delete-button">Supprimer ce commentaire</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Ajouter un commentaire -->
            <form action="events.php" method="POST">
                <textarea name="comment" rows="4" placeholder="Ajouter un commentaire" required></textarea>
                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                <button type="submit">Commenter</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<footer>
    <p>&copy; 2025 Événements de Voitures et Motos</p>
    <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
</footer>

</body>
</html>
