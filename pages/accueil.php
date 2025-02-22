<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../assets/css/accueil.css">
    <link rel="stylesheet" href="assets/css/resource.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
    <div class="container">
        <!-- Menu latéral -->
        <nav class="sidebar">
            <ul>
                <li><a href="accueil.php" class="active">Vue générale</a></li>
                <li><a href="construction.php">Construction</a></li>
                <li><a href="../logout.php" class="logout-btn">Déconnexion</a></li>
            </ul>
        </nav>

        <!-- Contenu principal -->
        <main class="content">
            <h1>Bienvenue sur Skirmish Islands</h1>
            <p>Préparez-vous à bâtir votre empire et à affronter vos ennemis.</p>
        </main>
    </div>
</body>
</html>
