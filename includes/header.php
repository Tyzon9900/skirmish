<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/database.php'; // Connexion à la BDD

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

// Vérification des ressources
if (!$resources) {
    die("Erreur : Impossible de récupérer les ressources !");
}

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
        <span id="wood-count"><?= number_format($resources['wood'] ?? 0) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/pierre.png" alt="Pierre">
        <span id="stone-count"><?= number_format($resources['stone'] ?? 0) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/fer.png" alt="Fer">
        <span id="iron-count"><?= number_format($resources['iron'] ?? 0) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/or.png" alt="Or">
        <span id="gold-count"><?= number_format($resources['gold'] ?? 0) ?></span>
    </div>
    <div class="resource">
        <img src="../assets/images/ble.png" alt="Blé">
        <span id="wheat-count"><?= number_format($resources['wheat'] ?? 0) ?></span>
    </div>
</div>

<script>
    function updateResources() {
        fetch("../includes/get_resources.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Erreur lors de la mise à jour des ressources :", data.error);
                return;
            }

            document.querySelector("#wood-count").textContent = data.wood;
            document.querySelector("#stone-count").textContent = data.stone;
            document.querySelector("#iron-count").textContent = data.iron;
            document.querySelector("#gold-count").textContent = data.gold;
            document.querySelector("#wheat-count").textContent = data.wheat;
        })
        .catch(error => console.error("Erreur Fetch :", error));
    }

    setInterval(updateResources, 3000); // Mise à jour toutes les 3 secondes
</script>

</body>
</html>
