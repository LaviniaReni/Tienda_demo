<?php
include "includes/db.php";
session_start();

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['nombre'];
            header("Location: index.php");
            exit();
        } else {
            $msg = "❌ Contraseña incorrecta";
            $msg_type = "error";
        }
    } else {
        $msg = "❌ Usuario no encontrado";
        $msg_type = "error";
    }
}
$titulo = "Login";
include __DIR__ . "/includes/head.php";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="page page--login">
  <main class="login">
    <div class="login__card">
      <h1 class="login__title">Iniciar sesión</h1>

      <?php if ($msg): ?>
        <p class="login__msg login__msg--<?php echo $msg_type; ?>">
          <?php echo $msg; ?>
        </p>
      <?php endif; ?>

      <form class="login__form" method="post">
        <label class="login__label" for="email">Correo electrónico</label>
        <input class="login__input" type="email" name="email" id="email" required>

        <label class="login__label" for="password">Contraseña</label>
        <input class="login__input" type="password" name="password" id="password" required>

        <button class="login__btn" type="submit">Entrar</button>

        <p class="login__register">
          ¿No tienes cuenta?
          <a href="register.php" class="login__link">Crear una</a>
        </p>
      </form>
    </div>
  </main>
</body>
</html>
