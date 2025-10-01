<?php
session_start();
require "includes/db.php";

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener órdenes del usuario
$sql = "SELECT * FROM ordenes WHERE usuario_id=? ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$ordenes = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Órdenes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px; }
        .ordenes-card { max-width: 900px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.1);}
        h1 { text-align:center; margin-bottom:20px; color:#111827; }
        table { width:100%; border-collapse: collapse; margin-bottom:20px;}
        th, td { padding:10px; text-align:center; border-bottom:1px solid #ddd; }
        img { width:80px; height:80px; object-fit:cover; border-radius:6px; }
        .btn { padding:8px 16px; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer; margin:2px; transition:0.2s; text-decoration:none; display:inline-block;}
        .btn:hover { background:#2563eb; }
        .total { text-align:right; font-weight:bold; font-size:1.2rem; margin-top:10px; }
    </style>
</head>
<body>
    <div class="ordenes-card">
        <h1>Mis Órdenes</h1>
        <?php if (count($ordenes) === 0): ?>
            <p>No has realizado ninguna orden aún.</p>
        <?php else: ?>
            <?php foreach ($ordenes as $orden): ?>
                <h2>Orden #<?php echo $orden['id']; ?> - <?php echo date("d/m/Y H:i", strtotime($orden['fecha'])); ?></h2>

                <?php
                // Obtener productos de esta orden
                $sql_detalle = "SELECT od.cantidad, p.nombre, p.precio, p.imagen 
                                FROM orden_detalle od
                                JOIN productos p ON od.producto_id = p.id
                                WHERE od.orden_id=?";
                $stmt_detalle = $conn->prepare($sql_detalle);
                $stmt_detalle->bind_param("i", $orden['id']);
                $stmt_detalle->execute();
                $res_detalle = $stmt_detalle->get_result();
                $productos = $res_detalle->fetch_all(MYSQLI_ASSOC);
                $stmt_detalle->close();
                ?>

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
                        <?php $total = 0; ?>
                        <?php foreach ($productos as $p): ?>
                            <?php $subtotal = $p['precio'] * $p['cantidad']; ?>
                            <?php $total += $subtotal; ?>
                            <tr>
                                <td><img src="<?php echo $p['imagen']; ?>" alt="<?php echo $p['nombre']; ?>"></td>
                                <td><?php echo $p['nombre']; ?></td>
                                <td>$<?php echo number_format($p['precio'],2); ?></td>
                                <td><?php echo $p['cantidad']; ?></td>
                                <td>$<?php echo number_format($subtotal,2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total">Total de la Orden: $<?php echo number_format($total,2); ?></div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
        <p style="text-align:center; margin-top:20px;"><a href="index.php" class="btn">Volver al Panel</a></p>
    </div>
</body>
</html>
