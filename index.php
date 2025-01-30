<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RaceConnect</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Font Awesome --> 
    <link rel="icon" type="img/google.jpg" href="img/google.jpg"> <!-- Favicon -->

</head>

<body>
    <header>
        <nav>
            <h1>RaceConnect</h1>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="events.php">Événements</a></li>
                    <li><a href="login.php">Se Connecter</a></li>
                    <li><a href="register.php">Créer un Compte</a></li>
                <?php else: ?>
                    <li><a href="profile.php">Mon Profil</a></li>
                    <li><a href="create_event.php">Créer un événement</a></li>
                    <li><a href="events.php">Événements</a></li>
                    <li><a href="chat.php">chat</a></li>
                    <li><a href="logout.php">Se Déconnecter</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Section Présentation -->
    <section class="presentation">
        <div class="container">
            <h2>Bienvenue sur RaceConnect !</h2>
            <p>Rejoignez notre communauté pour découvrir et partager des événements passionnants autour des voitures et des motos : Grand Prix, rassemblements, rallyes et plus encore !</p>
            <div class="extra-info">
            <p>Notre plateforme est dédiée à rassembler les passionnés d'automobile et de moto, que vous soyez pilote, fan, ou simple curieux.</p>
            </div>
        </div>
    </section>

    <!-- Section Carousel -->
    <section class="carousel-section">
        <div class="container">
            <div id="eventCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="/img/11.jpg" class="d-block w-100" alt="Événement voiture 1">
                    </div>
                    <div class="carousel-item">
                        <img src="/img/2.jpg" class="d-block w-100" alt="Événement voiture 3">
                    </div>
                    <div class="carousel-item">
                        <img src="/img/3.png" class="d-block w-100" alt="Événement voiture 3">
                    </div>
                    <div class="carousel-item">
                        <img src="/img/4.jpg" class="d-block w-100" alt="Événement voiture 3">
                    </div>
                    <div class="carousel-item">
                        <img src="/img/5.jpg" class="d-block w-100" alt="Événement voiture 3">
                    </div>
                    <div class="carousel-item">
                        <img src="/img/6.jpg" class="d-block w-100" alt="Événement voiture 3">
                    </div>
                    <div class="carousel-item">
                        <img src="/img/8.jpg" class="d-block w-100" alt="Événement voiture 3">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#eventCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Précédent</span>
                </a>
                <a class="carousel-control-next" href="#eventCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Suivant</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Section Appels à l'Action -->
    <section class="cta">
        <div class="container">
            <h3>Rejoignez-nous dès aujourd'hui!</h3>
            <p>Créez votre compte pour participer et publier vos propres événements!</p>
            <a href="register.php" class="btn">Créer un compte</a>
            <a href="login.php" class="btn">Se connecter</a>
        </div>
    </section>

    <!-- Section Informations -->
    <section class="info-section">
        <div class="container">
            <h3>Découvrez nos services</h3>
            <div class="info-cards">
                <div class="card">
                    <img src="/img/rdvamie.png" alt="Service 1" class="card-image">
                    <h4>Rencontré des amies</h4>
                    <p>Tu es sur le point de nouer des relations avec de réelles passionnait automobile.</p>
                </div>
                <div class="card">
                    <img src="/img/rasso.jpg" alt="Service 2" class="card-image">
                    <h4>Fais connaître tes rassemblements</h4>
                    <p>Partage tes bons plans dans ta région et rencontre des personnes intéressées pour y aller avec toi.</p>
                </div>
                <div class="card">
                    <img src="/img/th10.jpg" alt="Service 3" class="card-image">
                    <h4>Les GP</h4>
                    <p>Débusque tes amis pour t'accompagner aux GP Spa, Monza, las Vegas.</p>
                </div>
            </div>
            <div class="contact-button">
                <a href="contact.php" class="btn">Nous Contacter</a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Événements de Voitures et Motos</p>
        <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>

    <!-- Script Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    
</body>

</html>
