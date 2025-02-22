<?php
session_start();
require '../includes/database.php'; // Connexion à la BDD
include '../includes/header.php'; 
// Récupération des bâtiments depuis la BDD
try {
    $query = "SELECT * FROM buildings ORDER BY id ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des bâtiments.");
}

// Récupération des ressources du joueur
$user_id = $_SESSION['user_id'] ?? 1;
$query = "SELECT wood, stone, iron, gold FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$resources = $stmt->fetch(PDO::FETCH_ASSOC);

// Gestion de l'amélioration d'un bâtiment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upgrade_building'])) {
    $building_id = $_POST['building_id'];
    
    // Récupération des informations du bâtiment
    $query = "SELECT * FROM buildings WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$building_id]);
    $building = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($building) {
        // Calcul des nouveaux coûts et du temps d'amélioration
        $new_level = $building['level'] + 1;
        $cost_wood = 250 * $new_level;
        $cost_stone = 250 * $new_level;
        $cost_iron = 250 * $new_level;
        $cost_gold = 250 * $new_level;
        $upgrade_time = 10 * $new_level; // 10 sec par niveau (test)
        
        // Vérification des ressources disponibles
        if ($resources['wood'] >= $cost_wood && $resources['stone'] >= $cost_stone && $resources['iron'] >= $cost_iron && $resources['gold'] >= $cost_gold) {
            // Déduction des ressources
            $query = "UPDATE users SET wood = wood - ?, stone = stone - ?, iron = iron - ?, gold = gold - ? WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$cost_wood, $cost_stone, $cost_iron, $cost_gold, $user_id]);

            // Mise à jour du bâtiment avec un timer
            $query = "UPDATE buildings SET level = ?, construction_time = ? WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$new_level, time() + $upgrade_time, $building_id]);
            
            header("Location: construction.php");
            exit();
        } else {
            echo "<script>alert('Ressources insuffisantes pour améliorer ce bâtiment.');</script>";
        }
    }
}
?>

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
        <nav class="sidebar">
            <ul>
                <li><a href="accueil.php">Vue générale</a></li>
                <li><a href="construction.php" class="active">Construction</a></li>
                <li><a href="../logout.php" class="logout-btn">Déconnexion</a></li>
            </ul>
        </nav>

        <div class="content">
            <h1>Gestion de la Construction</h1>
            <div class="construction-container">
                <?php foreach ($buildings as $building) : ?>
                <div class="building-row">
                    <div class="building-left">
                        <img src="../assets/images/<?= htmlspecialchars($building['image']) ?>" alt="<?= htmlspecialchars($building['name']) ?>">
                        <div class="building-info">
                            <h2><?= htmlspecialchars($building['name']) ?></h2>
                            <p><strong>Niveau :</strong> <?= htmlspecialchars($building['level']) ?></p>
                        </div>
                    </div>

                    <div class="building-center">
                        <p><?= htmlspecialchars($building['description']) ?></p>
                    </div>

                    <div class="building-right">
                        <form method="POST">
                            <input type="hidden" name="building_id" value="<?= $building['id'] ?>">
                            <button type="submit" name="upgrade_building" class="upgrade-button">Améliorer</button>
                        </form>
                        <p><strong>Temps :</strong> <?= htmlspecialchars($building['construction_time']) ?> sec</p>
                        <p>Coût : 250 + Niveau * 250 de chaque ressource</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
