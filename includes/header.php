<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'database.php'; // Connexion à la BDD

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Erreur : Aucun utilisateur connecté !");
}

$user_id = $_SESSION['user_id'];

// Récupération des ressources du joueur connecté
$query = "SELECT wood, stone, iron, gold, wheat FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$resources = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skirmish Islands</title>
    <link rel="stylesheet" href="../assets/css/resource.css">
</head>
<body>

<div class="resource-bar">
    <div class="resource">
        <img src="../assets/images/bois.png" alt="Bois">
        <span id="wood-count"><?= number_format($resources['wood']) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/pierre.png" alt="Pierre">
        <span id="stone-count"><?= number_format($resources['stone']) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/fer.png" alt="Fer">
        <span id="iron-count"><?= number_format($resources['iron']) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/or.png" alt="Or">
        <span id="gold-count"><?= number_format($resources['gold']) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/ble.png" alt="Blé">
        <span id="wheat-count"><?= number_format($resources['wheat']) ?></span>
    </div>
</div>

<script>
    function updateResources() {
        fetch("../includes/get_resources.php")
        .then(response => response.json())
        .then(data => {
            document.querySelector("#wood-count").textContent = data.wood;
            document.querySelector("#stone-count").textContent = data.stone;
            document.querySelector("#iron-count").textContent = data.iron;
            document.querySelector("#gold-count").textContent = data.gold;
            document.querySelector("#wheat-count").textContent = data.wheat;
        });
    }

    setInterval(updateResources, 3000); // Mise à jour toutes les 3 secondes
</script>

</body>
</html>
