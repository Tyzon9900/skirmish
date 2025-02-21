<?php
require_once 'includes/database.php';

$error = "";
$success = "";

// Vérifier si un token est fourni et valide
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Token invalide ou expiré.");
}

$token = $_GET['token'];

// Vérifier si le token existe en base de données
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

// Vérifier si le token a expiré
if (!$user || empty($user["reset_token_expire"]) || strtotime($user["reset_token_expire"]) < time()) {
    header("Location: forgot_password.php?error=expired");
    exit();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($new_password === $confirm_password && strlen($new_password) >= 8 &&
        preg_match('/[A-Z]/', $new_password) && preg_match('/\d/', $new_password)) {

        // Hasher le nouveau mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe et supprimer le token
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expire = NULL WHERE reset_token = ?");
        $stmt->execute([$hashed_password, $token]);

        $success = "Votre mot de passe a été réinitialisé. Redirection...";
        echo "<script>
                setTimeout(() => { window.location.href = 'login.php'; }, 3000);
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="assets/css/forgot_password.css">
    <style>
        .password-rules {
            color: red;
            font-size: 0.9em;
        }
        .valid {
            color: green !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Réinitialisation du mot de passe</h2>
        <?php if ($success): ?>
            <p class="success" id="success-message"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <input type="password" id="password" name="password" placeholder="Nouveau mot de passe" required>
            <small id="password-rules" class="password-rules">8 caractères minimum, 1 majuscule, 1 chiffre</small>

            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
            <small id="password-match" class="password-rules"></small>

            <button type="submit" id="submit-btn" disabled>Réinitialiser</button>
        </form>
    </div>
    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordRules = document.getElementById('password-rules');
        const passwordMatch = document.getElementById('password-match');
        const submitBtn = document.getElementById('submit-btn');

        function validatePasswords() {
            let isValid = password.value.length >= 8 && /[A-Z]/.test(password.value) && /\d/.test(password.value);

            if (isValid) {
                passwordRules.classList.add("valid");
            } else {
                passwordRules.classList.remove("valid");
            }

            if (password.value !== confirmPassword.value) {
                passwordMatch.textContent = "Les mots de passe ne correspondent pas.";
                passwordMatch.style.color = "red";
                submitBtn.disabled = true;
            } else {
                passwordMatch.textContent = "";
            }

            if (isValid && password.value === confirmPassword.value) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        password.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);

        // Supprimer le message de succès après 3 secondes
        setTimeout(() => {
            let successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>
