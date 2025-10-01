<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
include("../includes/db.php");

// 📌 Configuración de paginación
$limite = 10;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

// 📌 Contar total de órdenes
$sqlTotal = "SELECT COUNT(*) AS total FROM ordenes";
$resTotal = $conn->query($sqlTotal);
$totalOrdenes = $resTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalOrdenes / $limite);

// 📌 Obtener órdenes con paginación
$sql = "SELECT o.id, o.fecha, o.total, u.nombre AS usuario
        FROM ordenes o
        INNER JOIN usuarios u ON o.usuario_id = u.id
        ORDER BY o.fecha DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limite, $offset);
$stmt->execute();
$ordenes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Órdenes - Panel Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 📌 Importante para responsive -->
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background: #f4f4f4; }
    h1 { margin-bottom: 20px; }
    a { color: #007bff; text-decoration: none; cursor: pointer; }
    a:hover { text-decoration: underline; }
    .paginacion { margin-top: 15px; text-align: center; }
    .paginacion a, .paginacion span { margin: 0 5px; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; }
    .paginacion span { background: #007bff; color: white; border-color: #007bff; }

    /* 📌 Estilos del modal */
    .modal {
      display: none; 
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
      justify-content: center; align-items: center;
      z-index: 1000;
    }
    .modal-contenido {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 800px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
      animation: aparecer 0.3s ease;
      overflow-x: auto;
    }
    @keyframes aparecer {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .cerrar {
      float: right;
      font-size: 18px;
      cursor: pointer;
      color: #ff0000;
    }

    /* 📱 Responsive: tabla a tarjetas */
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr { display: block; }
      thead { display: none; }
      tr { margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; padding: 10px; background: #fafafa; }
      td {
        border: none;
        display: flex;
        justify-content: space-between;
        padding: 8px 5px;
      }
      td::before {
        content: attr(data-label);
        font-weight: bold;
        margin-right: 10px;
        color: #333;
      }
    }
  </style>
</head>
<body>
  <h1>📋 Órdenes</h1>
  <a href="index.php">⬅ Volver al Panel</a>
  <table>
    <thead>
      <tr>
        <th>ID Orden</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($orden = $ordenes->fetch_assoc()): ?>
    <tr>
      <td data-label="ID Orden"><?= htmlspecialchars($orden['id']) ?></td>
      <td data-label="Usuario"><?= htmlspecialchars($orden['usuario']) ?></td>
      <td data-label="Fecha"><?= date("d/m/Y H:i", strtotime($orden['fecha'])) ?></td>
      <td data-label="Total">$<?= htmlspecialchars(number_format($orden['total'], 2)) ?></td>
      <td data-label="Detalle">
        <a onclick="verDetalle(<?= htmlspecialchars($orden['id']) ?>)">👁 Ver Detalle</a>
      </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <!-- 📌 Navegación de paginación -->
  <div class="paginacion">
    <?php if ($pagina > 1): ?>
      <a href="?pagina=1">⏮ Primero</a>
      <a href="?pagina=<?= $pagina - 1 ?>">⬅ Anterior</a>
    <?php endif; ?>

    <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>

    <?php if ($pagina < $totalPaginas): ?>
      <a href="?pagina=<?= $pagina + 1 ?>">Siguiente ➡</a>
      <a href="?pagina=<?= $totalPaginas ?>">Último ⏭</a>
    <?php endif; ?>
  </div>

  <!-- 📌 Modal para mostrar detalle -->
  <div id="modal" class="modal">
    <div class="modal-contenido">
      <span class="cerrar" onclick="cerrarModal()">✖</span>
      <div id="contenido-detalle">
        <p>Cargando...</p>
      </div>
    </div>
  </div>

  <script>
    function verDetalle(idOrden) {
      document.getElementById("modal").style.display = "flex";
      document.getElementById("contenido-detalle").innerHTML = "<p>Cargando...</p>";

      fetch("detalle_orden.php?id=" + idOrden)
        .then(res => res.text())
        .then(data => {
          document.getElementById("contenido-detalle").innerHTML = data;
        })
        .catch(err => {
          document.getElementById("contenido-detalle").innerHTML = "<p>Error al cargar detalle.</p>";
        });
    }

    function cerrarModal() {
      document.getElementById("modal").style.display = "none";
    }
  </script>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
