<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Utilisateur non connecté"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération des niveaux des bâtiments producteurs (Carrière, Scierie, Ferme)
$query = "SELECT name, level FROM buildings WHERE name IN ('Carrière', 'Scierie', 'Ferme')";
$stmt = $pdo->prepare($query);
$stmt->execute();
$buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Définition des gains par niveau
$gains = [
    "Carrière" => 10,  // Production de pierre
    "Scierie" => 10,   // Production de bois
    "Ferme" => 10      // Production de blé
];

$wood_gain = 0;
$stone_gain = 0;
$wheat_gain = 0;

foreach ($buildings as $building) {
    if ($building['name'] == "Carrière") {
        $stone_gain = $building['level'] * $gains["Carrière"];
    }
    if ($building['name'] == "Scierie") {
        $wood_gain = $building['level'] * $gains["Scierie"];
    }
    if ($building['name'] == "Ferme") {
        $wheat_gain = $building['level'] * $gains["Ferme"];
    }
}

// Mise à jour des ressources
$query = "UPDATE users SET wood = wood + ?, stone = stone + ?, wheat = wheat + ? WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$wood_gain, $stone_gain, $wheat_gain, $user_id]);

echo json_encode(["status" => "ok"]);
?>
