<?php
session_start();

// Connexion à la base de données
require('db_connection.php');

// Récupérer les événements avec les noms des utilisateurs
$stmt = $pdo->prepare("SELECT e.*, u.username FROM events e LEFT JOIN users u ON e.user_id = u.id ORDER BY e.created_at DESC");
$stmt->execute();
$events = $stmt->fetchAll();

// Ajouter un événement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['type']) && isset($_POST['event_date'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $event_date = $_POST['event_date'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO events (name, description, type, event_date, user_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $description, $type, $event_date, $user_id]);
    
    header("Location: events.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>RaceConnect - Événements</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>

<header>
    <h1>Événements de RaceConnect</h1>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Mon Profil</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="login.php">Se connecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<div class="container mt-4">
    <?php if (isset($_SESSION['user_id'])): ?>
    <section class="create-event">
        <h2>Créer un événement</h2>
        <form action="events.php" method="POST">
            <label for="name">Nom de l'événement :</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Information supplémentaire :</label>
            <textarea id="description" name="description" required></textarea>

            <label for="event_date">Date de l'événement :</label>
            <input type="datetime-local" id="event_date" name="event_date" required>

            <label for="type">Type d'événement :</label>
            <select id="type" name="type" required>
                <option value="" disabled selected>Choisissez un type</option>
                <option value="gp-f1">GP F1</option>
                <option value="gp-moto">GP Moto</option>
                <option value="rallye">Rallye</option>
                <option value="rasso">Rasso</option>
                <option value="auto">Auto</option>
                <option value="moto">Moto</option>
                <option value="other">Autre</option>
            </select>
            <button type="submit" class="btn btn-primary">Publier</button>
        </form>
    </section>
    <?php endif; ?>

    <h2>Fil des Événements :</h2>

    <?php foreach ($events as $event): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"> <?php echo htmlspecialchars($event['name']); ?> </h5>
            <p class="card-text"> <?php echo nl2br(htmlspecialchars($event['description'])); ?> </p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($event['type']); ?></p>
            <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?></p>
            <p class="text-muted">Posté par <?php echo $event['username'] ? htmlspecialchars($event['username']) : "Anonyme"; ?> le <?php echo date('d/m/Y H:i', strtotime($event['created_at'])); ?></p>
            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-info">Voir plus</a>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $event['user_id']): ?>
                <form action="delete_event.php" method="POST" style="display:inline;">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">Supprimer</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<footer>
    <p>&copy; 2025 RaceConnect</p>
</footer>

</body>
</html>