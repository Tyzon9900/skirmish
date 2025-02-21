<?php
require_once 'includes/database.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        header("Location: pages/accueil.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Skirmish</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <?php if ($error): ?>
            <p class="error" id="error-message"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p>
            <a href="register.php">Créer un compte</a> | <a href="forgot_password.php">Mot de passe oublié ?</a>
        </p>
    </div>
    <script>
        // Supprimer le message d'erreur après 3 secondes
        setTimeout(() => {
            let errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>
