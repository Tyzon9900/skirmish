<?php
session_start();
if (isset($_SESSION["user_id"])) {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
}
header("Location: login.php");
exit();
;
?>
