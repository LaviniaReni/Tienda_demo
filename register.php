<?php
include "includes/db.php";

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["name"]);
    $email  = trim($_POST["email"]);
    $pass1  = $_POST["password"];
    $pass2  = $_POST["password2"];

    if ($pass1 !== $pass2) {
        $msg = "❌ Las contraseñas no coinciden";
        $msg_type = "error";
    } else {
        $hash = password_hash($pass1, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $hash);

        if ($stmt->execute()) {
            $msg = "✅ Registro exitoso, ahora puedes iniciar sesión";
            $msg_type = "success";
        } else {
            $msg = "❌ Error: " . $stmt->error;
            $msg_type = "error";
        }
    }
}
$titulo = "Register";
include __DIR__ . "/includes/head.php";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="page page--register">
  <main class="login">
    <div class="login__card">
      <h1 class="login__title">Crear cuenta</h1>

      <?php if ($msg): ?>
        <p class="login__msg login__msg--<?= $msg_type ?>">
          <?= $msg ?>
        </p>
      <?php endif; ?>

      <form class="login__form" method="post">
        <label class="login__label" for="name">Nombre completo</label>
        <input class="login__input" type="text" name="name" id="name" required>

        <label class="login__label" for="email">Correo electrónico</label>
        <input class="login__input" type="email" name="email" id="email" required>

        <label class="login__label" for="password">Contraseña</label>
        <input class="login__input" type="password" name="password" id="password" required>

        <label class="login__label" for="password2">Repite la contraseña</label>
        <input class="login__input" type="password" name="password2" id="password2" required>

        <button class="login__btn" type="submit">Registrarme</button>

        <p class="login__register">
          ¿Ya tienes cuenta?
          <a href="login.php" class="login__link">Iniciar sesión</a>
        </p>
      </form>
    </div>
  </main>
</body>
</html>
