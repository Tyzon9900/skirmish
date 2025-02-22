<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["success" => false, "message" => "Utilisateur non connecté"]));
}

$user_id = $_SESSION['user_id'];
$building_id = $_POST['building_id'] ?? null;

if (!$building_id) {
    die(json_encode(["success" => false, "message" => "ID du bâtiment manquant"]));
}

// Récupération des infos du bâtiment
$query = "SELECT * FROM buildings WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$building_id]);
$building = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$building) {
    die(json_encode(["success" => false, "message" => "Bâtiment introuvable"]));
}

$level = $building['level'];
$cost_wood = 250 + ($level * 250);
$cost_stone = 250 + ($level * 250);
$cost_iron = 250 + ($level * 250);
$cost_gold = 250 + ($level * 250);

// Vérification des ressources
$query = "SELECT wood, stone, iron, gold FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['wood'] < $cost_wood || $user['stone'] < $cost_stone || $user['iron'] < $cost_iron || $user['gold'] < $cost_gold) {
    die(json_encode(["success" => false, "message" => "Pas assez de ressources"]));
}

// Déduction des ressources et mise à jour du niveau
$query = "UPDATE users SET wood = wood - ?, stone = stone - ?, iron = iron - ?, gold = gold - ? WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$cost_wood, $cost_stone, $cost_iron, $cost_gold, $user_id]);

$query = "UPDATE buildings SET level = level + 1 WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$building_id]);

echo json_encode(["success" => true]);
