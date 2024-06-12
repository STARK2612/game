<?php
session_start();
include 'config.php';
include 'csrf.php';

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
        header("Location: ../frontend/dashboard.php");
    } else {
        $error = "Invalid credentials.";
    }
}
$csrf_token = generate_csrf();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container">
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <label for="identifiant">Identifiant:</label>
            <input type="text" id="identifiant" name="identifiant" required>
            <label for="mot_de_passe">Mot de passe:</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
