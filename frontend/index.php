<?php
session_start();
require_once '../backend/config.php';
require_once '../backend/csrf.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    $identifiant = $_POST['identifiant'];
    $password = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE identifiant = :identifiant");
    $stmt->bindParam(':identifiant', $identifiant);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
        $_SESSION['last_activity'] = time();
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Identifiant ou mot de passe incorrect.";
    }
}

$csrf_token = generate_csrf();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire d'armes - Connexion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Connexion</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="post" action="index.php">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="form-group">
                                <label for="identifiant">Identifiant:</label>
                                <input type="text" id="identifiant" name="identifiant" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="mot_de_passe">Mot de passe:</label>
                                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Connexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
