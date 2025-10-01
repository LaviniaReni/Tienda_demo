<?php
session_start();
include("../includes/db.php"); // $conn es mysqli

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$error = "";
$usuario = "";

// Limitar intentos de login por sesión
if (!isset($_SESSION['login_intentos'])) {
    $_SESSION['login_intentos'] = 0;
}
$MAX_INTENTOS = 5;

// Chequear si ya existen administradores en la base
$tieneAdmins = false;
$res = $conn->query("SELECT COUNT(*) AS total FROM admins");
if ($res) {
    $row = $res->fetch_assoc();
    $tieneAdmins = ($row['total'] > 0);
    $res->free();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($_SESSION['login_intentos'] >= $MAX_INTENTOS) {
        $error = "Demasiados intentos fallidos. Intente más tarde.";
    } else {

        $usuario = htmlspecialchars(trim($_POST["usuario"]), ENT_QUOTES, 'UTF-8');
        $password = trim($_POST["password"]);

        $stmt = $conn->prepare("SELECT id, usuario, password FROM admins WHERE usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin) {
            $stored = $admin["password"];

            if (password_verify($password, $stored)) {
                if (password_needs_rehash($stored, PASSWORD_BCRYPT, ['cost' => 12])) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $u = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    $u->bind_param("si", $newHash, $admin['id']);
                    $u->execute();
                    $u->close();
                }

                session_regenerate_id(true);
                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_usuario"] = $admin["usuario"];
                $_SESSION['login_intentos'] = 0;
                header("Location: index.php");
                exit;
            }

            elseif ($stored === $password) {
                $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $u = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $u->bind_param("si", $newHash, $admin['id']);
                $u->execute();
                $u->close();

                session_regenerate_id(true);
                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_usuario"] = $admin["usuario"];
                $_SESSION['login_intentos'] = 0;
                header("Location: index.php");
                exit;
            }

            else {
                $error = "Usuario o contraseña incorrectos";
                $_SESSION['login_intentos']++;
            }
        } else {
            $error = "Usuario o contraseña incorrectos";
            $_SESSION['login_intentos']++;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; background-color: #f4f4f4; }
        input { display: block; margin: 10px auto; padding: 10px; width: 220px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #007bff; border: none; color: #fff; cursor: pointer; border-radius: 5px; }
        button:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 15px; }
        .mensaje { color: green; margin-bottom: 15px; }
        form { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: inline-block; }
        a { display: block; margin-top: 15px; color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>🔑 Login Administrador</h1>
    
    <?php if (isset($_GET['logout'])): ?>
      <p class="mensaje">Sesión cerrada correctamente ✅</p>
    <?php endif; ?>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    
    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" value="<?= htmlspecialchars($usuario) ?>" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

    <?php if (!$tieneAdmins): ?>
        <a href="register.php">📝 Crear primer administrador</a>
    <?php endif; ?>
</body>
</html>
