<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
require_once '../backend/csrf.php';

// Vérifier l'inactivité
check_inactivity();

// Vérifier si une session est déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'logout') {
    // Messages de débogage
    error_log("Déconnexion de l'utilisateur avec ID: " . $_SESSION['user_id']);

    // Détruire toutes les variables de session
    $_SESSION = array();

    // Si vous voulez détruire complètement la session, effacez également le cookie de session.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Détruire la session
    session_destroy();

    // Messages de débogage
    error_log("Session détruite et redirection vers la page de connexion.");

    // Rediriger vers la page de connexion
    header("Location: index.php");
    exit;
}

// Gestion de la soumission du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    if (!validate_csrf($_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    $identifiant = $_POST['identifiant'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Requête pour vérifier les informations d'identification
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE identifiant = :identifiant");
    $stmt->bindParam(':identifiant', $identifiant);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Définir les variables de session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_firstname'] = $user['prenom'];
        $_SESSION['user_name'] = $user['nom'];

        // Rediriger vers le tableau de bord
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Identifiant ou mot de passe incorrect.";
    }
}

// Charger la couleur de fond configurée
$background_color_file = '../backend/background_color.txt';
$background_color = '#f4f4f4'; // Couleur par défaut
if (file_exists($background_color_file)) {
    $background_color = file_get_contents($background_color_file);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="favicon.ico">
    <style>
        body {
            background-color: <?= htmlspecialchars($background_color) ?>;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <form method="post" action="index.php">
                <h2>Connexion</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                <div class="form-group">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" name="identifiant" id="identifiant" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Connexion</button>
            </form>
        </div>
    </div>
</body>
</html>
