<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      text-align: center;
      padding: 50px;
    }
    h1 {
      color: #333;
    }
    .menu {
      margin-top: 30px;
    }
    a {
      display: inline-block;
      margin: 10px;
      padding: 15px 25px;
      background: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: 0.3s;
    }
    a:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <h1>📊 Panel de Administración</h1>
  <p>Bienvenido, <b><?= htmlspecialchars($_SESSION["admin_usuario"]) ?></b>. Selecciona una opción:</p>
  <div class="menu">
    <a href="productos.php">📦 Gestionar Productos</a>
    <a href="ordenes.php">🛒 Ver Órdenes</a>
    <a href="usuarios.php">👤 Gestionar Usuarios</a> <!-- Accede a usuarios.php -->
    <a href="registrar_admin.php">➕ Crear Administrador</a> <!-- Crear admin solo si estás logueado -->
    <a href="../index.php">🏠 Ir a la Tienda</a>
    <a href="logout.php?logout=1">🚪 Cerrar Sesión</a>
  </div>
</body>
</html>
