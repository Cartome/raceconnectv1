<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Connexion à la base de données
require('db_connection.php');

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs du formulaire
    $name = $_POST['name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $type = $_POST['type'];
    $user_id = $_SESSION['user_id'];  // L'ID de l'utilisateur connecté

    // Vérifier si toutes les données sont envoyées
    if (empty($name) || empty($description) || empty($event_date) || empty($type)) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    // Insertion des données dans la table `events`
    $stmt = $pdo->prepare("INSERT INTO events (user_id, name, event_date, type, description) 
                           VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $name, $event_date, $type, $description])) {
        // Si l'événement est créé avec succès, redirection vers la page des événements
        header("Location: events.php");
        exit();
    } else {
        echo "Erreur lors de la création de l'événement.";
        exit;
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
    <link rel="stylesheet" href="style4.css">
    <link rel="icon" type="img/google.jpg" href="img/google.jpg"> <!-- Favicon -->

</head>
<body>
    <header>
        <h1>Crée ton événement</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="create_event.php">Créer un événement</a></li>
                <li><a href="profile.php">Mon Profil</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <section class="create-event">
        <h2>Créer un événement</h2>
        <form action="create_event.php" method="POST">
            <label for="name">Nom de l'événement</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Information supplémentaire</label>
            <textarea id="description" name="description" required></textarea>

            <label for="event_date">Date de l'événement</label>
            <input type="datetime-local" id="event_date" name="event_date" required>

            <label for="type">Type d'événement</label>
            <select id="type" name="type" required onchange="toggleOtherField()">
                <option value="" disabled selected>Choisissez un type</option>
                <option value="gp-f1">GP F1</option>
                <option value="gp-moto">GP Moto</option>
                <option value="rallye">Rallye</option>
                <option value="rasso">Rasso</option>
                <option value="auto">Auto</option>
                <option value="moto">Moto</option>
                <option value="other">Autre</option>
            </select>

            <script>
    function toggleOtherField() {
        const typeSelect = document.getElementById('type');
        const otherTypeContainer = document.getElementById('other-type-container');
        if (typeSelect.value === 'other') {
            otherTypeContainer.style.display = 'block';
            document.getElementById('other-type').required = true; // Rendre le champ requis
        } else {
            otherTypeContainer.style.display = 'none';
            document.getElementById('other-type').required = false; // Retirer l'obligation
        }
    }
</script>


            <div id="other-type-container" style="display: none; margin-top: 10px;">
                <label for="other-type">Veuillez préciser :</label>
                <input type="text" id="other-type" name="other-type" placeholder="Type d'événement">
            </div>
            <button type="submit">Créer l'événement</button>
        </form>
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
