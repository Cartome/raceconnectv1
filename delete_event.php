<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Connexion à la base de données
require('db_connection.php');

// Vérifier si l'ID de l'événement est passé via la méthode POST
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Vérifier si l'événement appartient à l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT user_id FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if ($event && $event['user_id'] == $_SESSION['user_id']) {
        // Supprimer l'événement de la base de données
        $deleteStmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $deleteStmt->execute([$event_id]);

        // Rediriger vers la page des événements après la suppression
        header("Location: events.php?message=Événement supprimé avec succès");
        exit();
    } else {
        echo "Vous n'avez pas la permission de supprimer cet événement.";
    }
} else {
    echo "Aucun événement sélectionné.";
}
?>
