<?php
require 'database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Utilisateur non connecté"]));
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['building_id'])) {
    die(json_encode(["error" => "ID du bâtiment manquant"]));
}

$building_id = intval($_POST['building_id']);

// Récupération des infos actuelles du bâtiment
$query = "SELECT * FROM buildings WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$building_id]);
$building = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$building) {
    die(json_encode(["error" => "Bâtiment introuvable"]));
}

// Calcul du coût et du temps pour le niveau suivant
$next_level = $building['level'] + 1;
$base_cost = 250;
$cost_multiplier = 1.5;

$new_cost_wood = round($base_cost * pow($cost_multiplier, $next_level - 1));
$new_cost_stone = round($base_cost * pow($cost_multiplier, $next_level - 1));
$new_cost_iron = round($base_cost * pow($cost_multiplier, $next_level - 1));
$new_cost_gold = round($base_cost * pow($cost_multiplier, $next_level - 1));
$new_time = round(10 * pow(1.5, $next_level - 1)); // Temps d'amélioration

// Vérification des ressources
$query = "SELECT wood, stone, iron, gold FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$user_resources = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    $user_resources['wood'] < $new_cost_wood ||
    $user_resources['stone'] < $new_cost_stone ||
    $user_resources['iron'] < $new_cost_iron ||
    $user_resources['gold'] < $new_cost_gold
) {
    die(json_encode(["error" => "Ressources insuffisantes"]));
}

// Déduction des ressources et mise à jour du bâtiment
$query = "UPDATE users SET wood = wood - ?, stone = stone - ?, iron = iron - ?, gold = gold - ? WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$new_cost_wood, $new_cost_stone, $new_cost_iron, $new_cost_gold, $user_id]);

$query = "UPDATE buildings SET level = ?, construction_time = ? WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$next_level, $new_time, $building_id]);

echo json_encode(["success" => "Bâtiment amélioré au niveau $next_level"]);
