<?php
session_start();
require('db_connection.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer tous les utilisateurs sauf l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

// Vérifier si un message a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['receiver_id'])) {
    $message = $_POST['message'];
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['user_id'];

    if (!empty($message)) {
        // Envoi du message
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);

        // Si le message est envoyé à un autre utilisateur, ajouter une notification pour le récepteur
        if ($sender_id != $receiver_id) {
            // Récupérer le nom de l'expéditeur pour la notification
            $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$sender_id]);
            $sender = $stmt->fetch();

            // Ajouter la notification pour l'utilisateur destinataire
            $notification_message = "Vous avez reçu un message de " . $sender['username'];
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
            $stmt->execute([$receiver_id, $notification_message]);
        }

        // Redirection pour afficher le nouveau message
        header('Location: chat.php?receiver_id=' . $receiver_id);
        exit();
    }
}

// Récupérer les notifications non lues pour l'utilisateur actuel
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

// Marquer les notifications comme lues après qu'elles ont été récupérées
if (count($notifications) > 0) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style5.css">
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

<div class="container">
    <!-- Notifications non lues -->
    <?php if (count($notifications) > 0): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Vous avez <?php echo count($notifications); ?> nouvelle(s) notification(s).
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <h1>Liste des utilisateurs</h1>

    <div class="list-group">
        <?php foreach ($users as $user): ?>
            <a href="chat.php?receiver_id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action">
                <?php echo htmlspecialchars($user['username']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (isset($_GET['receiver_id'])): ?>
        <?php
        $receiver_id = $_GET['receiver_id'];
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$receiver_id]);
        $receiver = $stmt->fetch();
        ?>

        <h2>Chat avec <?php echo htmlspecialchars($receiver['username']); ?></h2>

        <div class="chat-box">
            <?php
            $stmt = $pdo->prepare("SELECT m.*, u.username 
                                   FROM messages m 
                                   JOIN users u ON m.sender_id = u.id
                                   WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                                      OR (m.sender_id = ? AND m.receiver_id = ?)
                                   ORDER BY m.created_at ASC");
            $stmt->execute([$_SESSION['user_id'], $receiver_id, $receiver_id, $_SESSION['user_id']]);
            $messages = $stmt->fetchAll();

            foreach ($messages as $msg):
            ?>
                <div class="message <?php echo ($msg['sender_id'] == $_SESSION['user_id']) ? 'sent' : 'received'; ?>">
                    <p><strong><?php echo ($msg['sender_id'] == $_SESSION['user_id']) ? 'Vous' : htmlspecialchars($msg['username']); ?> :</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    <small><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulaire pour envoyer un message -->
        <form action="chat.php?receiver_id=<?php echo $receiver_id; ?>" method="POST">
            <textarea name="message" rows="3" placeholder="Écrivez un message..." required></textarea>
            <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    <?php endif; ?>
</div>
   <script>
                // Actualiser toutes les 10 s
        setInterval(fetchData, 1000);
   </script>
</body>
</html>
