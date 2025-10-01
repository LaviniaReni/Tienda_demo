<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    exit("No autorizado");
}
include("../includes/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("ID inválido");
}

$idOrden = intval($_GET['id']);

// 📌 Obtener datos de la orden
$sql = "SELECT o.id, o.fecha, o.total, u.nombre AS usuario
        FROM ordenes o
        INNER JOIN usuarios u ON o.usuario_id = u.id
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idOrden);
$stmt->execute();
$orden = $stmt->get_result()->fetch_assoc();

if (!$orden) {
    exit("Orden no encontrada.");
}

// 📌 Obtener productos de la orden (tabla orden_detalle ✅)
$sqlItems = "SELECT p.nombre, d.cantidad, d.precio AS precio_unitario
             FROM orden_detalle d
             INNER JOIN productos p ON d.producto_id = p.id
             WHERE d.orden_id = ?";
$stmt2 = $conn->prepare($sqlItems);
$stmt2->bind_param("i", $idOrden);
$stmt2->execute();
$items = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; }
    h2 { margin-top: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f4f4f4; }

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
  <h2>🧾 Detalle de la Orden #<?= htmlspecialchars($orden['id']) ?></h2>
  <p><b>Cliente:</b> <?= htmlspecialchars($orden['usuario']) ?></p>
  <p><b>Fecha:</b> <?= date("d/m/Y H:i", strtotime($orden['fecha'])) ?></p>
  <p><b>Total:</b> $<?= number_format($orden['total'], 2) ?></p>

  <h3>📦 Productos</h3>
  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
    <?php 
    $totalOrden = 0;
    while ($item = $items->fetch_assoc()): 
        $subtotal = $item['cantidad'] * $item['precio_unitario'];
        $totalOrden += $subtotal;
    ?>
      <tr>
        <td data-label="Producto"><?= htmlspecialchars($item['nombre']) ?></td>
        <td data-label="Cantidad"><?= htmlspecialchars($item['cantidad']) ?></td>
        <td data-label="Precio Unitario">$<?= number_format($item['precio_unitario'], 2) ?></td>
        <td data-label="Subtotal">$<?= number_format($subtotal, 2) ?></td>
      </tr>
    <?php endwhile; ?>
      <tr>
        <td colspan="3" style="text-align:right"><strong>Total</strong></td>
        <td><strong>$<?= number_format($totalOrden, 2) ?></strong></td>
      </tr>
    </tbody>
  </table>
</body>
</html>
<?php
$stmt->close();
$stmt2->close();
$conn->close();
?>
