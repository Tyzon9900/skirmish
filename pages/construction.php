<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la Construction</title>
    <link rel="stylesheet" href="../assets/css/construction.css">
</head>
<body>

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
    <div class="content">
        <h1>Gestion de la Construction</h1>
        <div class="construction-container">
            <?php
            $buildings = [
                ['id' => 1, 'name' => 'Carrière', 'image' => '../assets/images/carriere.png', 'description' => 'Produit de la pierre essentielle à la construction.'],
                ['id' => 2, 'name' => 'Scierie', 'image' => '../assets/images/scierie.png', 'description' => 'Produit du bois pour bâtir des structures.'],
                ['id' => 3, 'name' => 'Ferme', 'image' => '../assets/images/ferme.png', 'description' => 'Augmente la production de nourriture pour les troupes.'],
                ['id' => 4, 'name' => 'Temple', 'image' => '../assets/images/temple.png', 'description' => 'Améliore la ferveur religieuse de votre peuple.'],
                ['id' => 5, 'name' => 'Université', 'image' => '../assets/images/universite.png', 'description' => 'Permet d’effectuer des recherches pour progresser.'],
                ['id' => 6, 'name' => 'Caserne', 'image' => '../assets/images/caserne.png', 'description' => 'Entraîne vos soldats pour défendre la cité.'],
                ['id' => 7, 'name' => 'Forge', 'image' => '../assets/images/forge.png', 'description' => 'Fabrique des armes et armures.'],
                ['id' => 8, 'name' => 'Muraille', 'image' => '../assets/images/muraille.png', 'description' => 'Augmente la défense de votre cité.'],
                ['id' => 9, 'name' => 'Port', 'image' => '../assets/images/port.png', 'description' => 'Permet de commercer et construire des navires.'],
            ];
            ?>

            <?php foreach ($buildings as $building) : ?>
                <div class="building">
                    <img src="<?= htmlspecialchars($building['image']) ?>" alt="<?= htmlspecialchars($building['name']) ?>">
                    <div class="building-info">
                        <h2><?= htmlspecialchars($building['name']) ?> - Niveau 1</h2>
                        <p><?= htmlspecialchars($building['description']) ?></p>
                        <button class="upgrade-button">Améliorer</button>
                        <p>Temps de construction : 1h 30min</p>
                        <p>Coût : 100 Bois, 50 Pierre</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>
