<?php
session_start();
session_regenerate_id(true); // Renforce la sécurité contre le vol de session
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}
?>
