<?php
session_start();
require "includes/db.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$orden_id = intval($_GET['orden_id'] ?? 0);

if ($orden_id <= 0) {
    $_SESSION['error'] = "Orden inválida.";
    header("Location: ordenes.php");
    exit();
}

// Verificar que la orden pertenezca al usuario
$sql = "SELECT * FROM ordenes WHERE id=? AND usuario_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $orden_id, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $_SESSION['error'] = "No tienes acceso a esta orden.";
    header("Location: ordenes.php");
    exit();
}

$orden = $res->fetch_assoc();
$stmt->close();

// Obtener los productos de la orden
$sql_detalle = "SELECT od.cantidad, od.precio, p.nombre, p.imagen 
                FROM orden_detalle od
                JOIN productos p ON od.producto_id = p.id
                WHERE od.orden_id=?";
$stmt = $conn->prepare($sql_detalle);
$stmt->bind_param("i", $orden_id);
$stmt->execute();
$res_detalle = $stmt->get_result();
$productos = $res_detalle->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra realizada</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial; background:#f5f5f5; margin:0; padding:20px;}
        .card { max-width:800px; margin:auto; background:#fff; padding:30px; border-radius:12px; text-align:center; box-shadow:0 6px 18px rgba(0,0,0,0.1);}
        h1 { color:#111827; margin-bottom:20px;}
        table { width:100%; border-collapse:collapse; margin-top:20px;}
        th, td { padding:10px; border-bottom:1px solid #ddd; text-align:center;}
        img { width:70px; height:70px; object-fit:cover; border-radius:6px;}
        .total { font-weight:bold; font-size:1.2rem; margin-top:15px; text-align:right;}
        .btn { display:inline-block; margin:10px; padding:10px 20px; background:#3b82f6; color:#fff; border-radius:6px; text-decoration:none; }
        .btn:hover { background:#2563eb; }
    </style>
</head>
<body>
    <div class="card">
        <h1>✅ ¡Compra realizada con éxito!</h1>
        <p>Tu número de orden es: <strong><?php echo $orden['id']; ?></strong></p>
        <p>Fecha: <?php echo date("d/m/Y H:i", strtotime($orden['fecha'])); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_calc = 0; ?>
                <?php foreach ($productos as $p): ?>
                    <?php $subtotal = $p['precio'] * $p['cantidad']; ?>
                    <?php $total_calc += $subtotal; ?>
                    <tr>
                        <td><img src="<?php echo $p['imagen']; ?>" alt="<?php echo $p['nombre']; ?>"></td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td>$<?php echo number_format($p['precio'], 2); ?></td>
                        <td><?php echo $p['cantidad']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            Total pagado (DB): $<?php echo number_format($orden['total'], 2); ?><br>
            Total calculado: $<?php echo number_format($total_calc, 2); ?>
        </div>

        <a href="productos.php" class="btn">🛒 Seguir comprando</a>
        <a href="ordenes.php" class="btn">📦 Ver mis órdenes</a>
    </div>
</body>
</html>
