<?php
require_once 'includes/database.php';

$error = "";
$success = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Vérification si l'email ou le pseudo existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        if ($existingUser["email"] === $email) {
            $error = "Cet email est déjà utilisé.";
        } elseif ($existingUser["username"] === $username) {
            $error = "Ce nom d'utilisateur est déjà pris.";
        }
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule et 1 chiffre.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $success = "Inscription réussie ! Redirection...";
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
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/register.css">
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
        <h2>Inscription</h2>
        <?php if ($error): ?>
            <p class="error" id="error-message"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success" id="success-message"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>

            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            <small id="password-rules" class="password-rules">8 caractères minimum, 1 majuscule, 1 chiffre</small>

            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
            <small id="password-match" class="password-rules"></small>

            <button type="submit" id="submit-btn" disabled>S'inscrire</button>
            <a href="login.php">Retour à la connexion</a>
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

        // Supprimer le message d'erreur après 3 secondes
        setTimeout(() => {
            let errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 3000);

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
