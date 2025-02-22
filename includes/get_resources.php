<?php
session_start();
require 'database.php'; // Vérifie que la connexion à la BDD est correcte

// Vérifie que l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les ressources de l'utilisateur
$query = "SELECT wood, stone, iron, gold, wheat FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$resources = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resources) {
    echo json_encode(["error" => "Aucune ressource trouvée"]);
    exit;
}

// Retourne les ressources en format JSON
header('Content-Type: application/json');
echo json_encode($resources);
?>
