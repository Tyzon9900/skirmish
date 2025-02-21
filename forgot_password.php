<?php
require_once 'includes/database.php';
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require_once 'includes/load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();


$success = "";
$error = "";

if (isset($_SESSION["reset_attempts_time"]) && time() - $_SESSION["reset_attempts_time"] < 300) { // 5 min
    die("Trop de tentatives ! Réessayez plus tard.");
}

// Empêcher le spam (5 tentatives max)
if (!isset($_SESSION["reset_attempts"])) {
    $_SESSION["reset_attempts"] = 0;
}

if ($_SESSION["reset_attempts"] > 5) {
    $error = "Trop de tentatives ! Réessayez dans 5 minutes.";
    $_SESSION["reset_attempts_time"] = time();    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION["reset_attempts"]++;

    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Générer un token sécurisé
            $token = bin2hex(random_bytes(50));

            // Enregistrer le token dans la base de données
            $expire_time = date("Y-m-d H:i:s", strtotime("+30 minutes")); // Expiration dans 30 min
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expire = ? WHERE email = ?");
            $stmt->execute([$token, $expire_time, $email]);            

            // Configurer PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = getenv('EMAIL_USERNAME');
                $mail->Password = getenv('EMAIL_PASSWORD');                
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Expéditeur & Destinataire
                $mail->setFrom('tyzon9900@gmail.com', 'Skirmish');
                $mail->addAddress($email);

                // Contenu de l'email
                $reset_link = "http://localhost/skirmish/reset_password.php?token=$token";
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe - Skirmish';
                $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : 
                               <a href='$reset_link'>Réinitialiser mon mot de passe</a>";

                // Envoyer l'email
                $mail->send();

                // Message de succès et redirection rapide
                $success = "Un email de réinitialisation a été envoyé. Redirection...";
                echo "<script>
                        setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                      </script>";
            } catch (Exception $e) {
                $error = "L'envoi de l'email a échoué : " . $mail->ErrorInfo;
            }
        } else {
            $error = "Aucun compte trouvé avec cet email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Skirmish</title>
    <link rel="stylesheet" href="assets/css/forgot_password.css">
</head>
<body>
    <div class="container">
        <h2>Mot de passe oublié</h2>
        <?php if ($error): ?>
            <p class="error" id="error-message"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Votre email" required>
            <button type="submit">Envoyer</button>
        </form>
        <p>
            <a href="login.php">Retour à la connexion</a>
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
