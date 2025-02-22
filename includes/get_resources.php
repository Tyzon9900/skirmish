<?php
require 'database.php';
require 'update_resources.php'; // Assurer la mise à jour avant de renvoyer les données

session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Utilisateur non connecté"]));
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT wood, stone, iron, gold, wheat FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$resources = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($resources);
?>
