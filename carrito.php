<?php
session_start();
require "includes/db.php";

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Manejar acciones de actualizar o eliminar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['actualizar'])) {
        foreach ($_POST['cantidades'] as $producto_id => $cantidad) {
            $cantidad = max(1, intval($cantidad));

            // Verificar stock disponible
            $sql_stock = "SELECT stock FROM productos WHERE id=?";
            $stmt_stock = $conn->prepare($sql_stock);
            $stmt_stock->bind_param("i", $producto_id);
            $stmt_stock->execute();
            $res_stock = $stmt_stock->get_result();
            $prod = $res_stock->fetch_assoc();
            $stmt_stock->close();

            if ($prod && $cantidad > $prod['stock']) {
                $cantidad = $prod['stock']; // limitar al stock disponible
                $_SESSION['error'] = "⚠ La cantidad de {$producto_id} fue ajustada al stock disponible.";
            }

            $stmt = $conn->prepare("UPDATE carrito SET cantidad=? WHERE usuario_id=? AND producto_id=?");
            $stmt->bind_param("iii", $cantidad, $usuario_id, $producto_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['eliminar'])) {
        $producto_id = intval($_POST['producto_id']);
        $stmt = $conn->prepare("DELETE FROM carrito WHERE usuario_id=? AND producto_id=?");
        $stmt->bind_param("ii", $usuario_id, $producto_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener productos del carrito
$sql = "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.imagen 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$productos = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin:0; padding:20px; }
        .carrito-card { max-width: 900px; margin: auto; background: #fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.1);}
        h1 { text-align:center; margin-bottom:20px; color:#111827; }
        table { width:100%; border-collapse: collapse; margin-bottom:20px;}
        th, td { padding:10px; text-align:center; border-bottom:1px solid #ddd; }
        img { width:80px; height:80px; object-fit:cover; border-radius:6px; }
        .btn { padding:8px 16px; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer; margin:2px; transition:0.2s; }
        .btn:hover { background:#2563eb; }
        .total { text-align:right; font-weight:bold; font-size:1.2rem; margin-top:10px; }
        .finalizar { margin-top:15px; display:block; text-align:center; }
        .msg { text-align:center; margin:10px 0; color:#dc2626; font-weight:bold; }
    </style>
</head>
<body>
    <div class="carrito-card">
        <h1>Tu Carrito</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (count($productos) === 0): ?>
            <p>No hay productos en tu carrito.</p>
        <?php else: ?>
            <form method="post">
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach ($productos as $p): ?>
                            <?php $subtotal = $p['precio'] * $p['cantidad']; ?>
                            <?php $total += $subtotal; ?>
                            <tr>
                                <td><img src="<?php echo $p['imagen']; ?>" alt="<?php echo $p['nombre']; ?>"></td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td>$<?php echo number_format($p['precio'],2); ?></td>
                                <td>
                                    <input type="number" name="cantidades[<?php echo $p['producto_id']; ?>]" value="<?php echo $p['cantidad']; ?>" min="1" style="width:60px;">
                                </td>
                                <td>$<?php echo number_format($subtotal,2); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="producto_id" value="<?php echo $p['producto_id']; ?>">
                                        <button type="submit" name="eliminar" class="btn" onclick="return confirm('¿Eliminar producto?');">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total">Total: $<?php echo number_format($total,2); ?></div>
                <div style="text-align:center;">
                    <button type="submit" name="actualizar" class="btn">Actualizar Cantidades</button>
                </div>
            </form>

            <!-- Botón Finalizar Compra -->
            <div class="finalizar">
                <form method="post" action="finalizar_compra.php">
                    <button type="submit" class="btn">Finalizar Compra</button>
                </form>
            </div>
        <?php endif; ?>

        <p style="text-align:center; margin-top:20px;"><a href="productos.php" class="btn">Seguir Comprando</a></p>
    </div>
</body>
</html>
