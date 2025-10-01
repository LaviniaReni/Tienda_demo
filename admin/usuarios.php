<?php
session_start();
require "../includes/db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Eliminar administrador (solo si no es el mismo que está logueado)
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    if ($id !== $_SESSION["admin_id"]) {
        $sql = "DELETE FROM admins WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: usuarios.php");
    exit;
}

// Obtener lista de administradores
$sql = "SELECT id, usuario, creado FROM admins ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Administradores</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 30px;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 80%;
      margin: 0 auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background: #007bff;
      color: #fff;
    }
    a {
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 14px;
    }
    .btn-eliminar {
      background: #dc3545;
      color: #fff;
    }
    .btn-eliminar:hover {
      background: #a71d2a;
    }
    .btn-volver {
      display: inline-block;
      margin: 20px auto;
      background: #6c757d;
      color: #fff;
    }
    .btn-volver:hover {
      background: #565e64;
    }
  </style>
</head>
<body>
  <h1>👤 Administradores Registrados</h1>
  <table>
    <tr>
      <th>ID</th>
      <th>Usuario</th>
      <th>Creado en</th>
      <th>Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row["id"] ?></td>
        <td><?= htmlspecialchars($row["usuario"]) ?></td>
        <td><?= $row["creado"] ?></td>
        <td>
          <?php if ($row["id"] != $_SESSION["admin_id"]): ?>
            <a href="usuarios.php?eliminar=<?= $row["id"] ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que quieres eliminar este administrador?')">Eliminar</a>
          <?php else: ?>
            <em>(Tú)</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <div style="text-align: center;">
    <a href="index.php" class="btn-volver">⬅ Volver al Panel</a>
  </div>
</body>
</html>
