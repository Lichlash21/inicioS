<?php
session_start();
$host = 'localhost';
$dbname = 'inicio_de_sesion';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['username'] = $username;
            header('Location: welcome.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } elseif (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        try {
            $stmt->execute([$username, $password]);
            $success = 'Usuario registrado exitosamente. Ahora puedes iniciar sesión.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'El nombre de usuario ya está en uso.';
            } else {
                $error = 'Error al registrar usuario: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Inicio de Sesión / Registro</h1>
        <?php 
        if (isset($error)) echo "<p class='error'>$error</p>";
        if (isset($success)) echo "<p class='success'>$success</p>";
        ?>
        <div class="form-wrapper">
            <div class="form-section">
                <h2>Iniciar Sesión</h2>
                <form method="POST">
                    <input type="text" name="username" placeholder="Usuario" required>
                    <input type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit" name="login">Iniciar Sesión</button>
                </form>
            </div>
            <div class="form-section">
                <h2>Crear Perfil</h2>
                <form method="POST">
                    <input type="text" name="username" placeholder="Usuario" required>
                    <input type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit" name="register">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
