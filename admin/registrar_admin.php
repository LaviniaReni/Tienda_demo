<?php
session_start();
include("../includes/db.php");

// Verificar si está logueado un admin
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = htmlspecialchars(trim($_POST["usuario"]), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST["password"]);
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Evitar usuarios duplicados
    $stmt = $conn->prepare("SELECT id FROM admins WHERE usuario = ? LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $error = "⚠️ Ese usuario ya existe.";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (usuario, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $hash);
        if ($stmt->execute()) {
            $mensaje = "✅ Nuevo administrador creado correctamente.";
        } else {
            $error = "❌ Error al registrar el administrador.";
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Administrador</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; background-color: #f4f4f4; }
        form { background: #fff; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { display: block; margin: 10px auto; padding: 10px; width: 220px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #28a745; border: none; color: #fff; cursor: pointer; border-radius: 5px; }
        button:hover { background: #218838; }
        .error { color: red; margin-bottom: 15px; }
        .mensaje { color: green; margin-bottom: 15px; }
        a { display: block; margin-top: 15px; color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>👤 Registrar nuevo administrador</h1>
    <?php if ($mensaje) echo "<p class='mensaje'>$mensaje</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>

    <a href="index.php">⬅ Volver al panel</a>
</body>
</html>
