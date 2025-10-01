<?php
session_start();
include("../includes/db.php"); // $conn es mysqli

$error = "";
$exito = "";

// Si ya está logueado, redirigir
if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = filter_var(trim($_POST["usuario"]), FILTER_SANITIZE_STRING);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);

    // Validaciones básicas
    if (empty($usuario) || empty($password) || empty($password2)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $password2) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT id FROM admins WHERE usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "El usuario ya existe.";
        } else {
            // Hashear contraseña
            $hash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);

            // Insertar usuario
            $stmt = $conn->prepare("INSERT INTO admins (usuario, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $usuario, $hash);
            if ($stmt->execute()) {
                $exito = "Administrador registrado correctamente ✅";
            } else {
                $error = "Error al registrar. Intente nuevamente.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Administrador</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; background-color: #f4f4f4; }
        input { display: block; margin: 10px auto; padding: 10px; width: 220px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #28a745; border: none; color: #fff; cursor: pointer; border-radius: 5px; }
        button:hover { background: #218838; }
        .error { color: red; margin-bottom: 15px; }
        .exito { color: green; margin-bottom: 15px; }
        form { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: inline-block; }
        a { display: block; margin-top: 10px; color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>📝 Registro de Administrador</h1>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($exito)) echo "<p class='exito'>$exito</p>"; ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="password2" placeholder="Repetir contraseña" required>
        <button type="submit">Registrarme</button>
    </form>

    <a href="login.php">← Volver al login</a>
</body>
</html>
