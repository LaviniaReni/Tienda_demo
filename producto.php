<?php
session_start();
require "includes/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$producto_id = intval($_GET['id']);

// Buscar producto en la base
$sql = "SELECT * FROM productos WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $_SESSION['error'] = "Producto no encontrado.";
    header("Location: productos.php");
    exit();
}

$producto = $res->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Detalles</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial; background:#f5f5f5; margin:0; padding:20px;}
        .container { max-width:900px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.1);}
        .producto { display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;}
        .producto img { width:350px; height:350px; object-fit:cover; border-radius:8px; }
        .info { flex:1; min-width:250px; }
        h1 { margin-bottom:10px; }
        .precio { font-size:1.5rem; font-weight:bold; color:#16a34a; margin:15px 0; }
        .stock { color:#555; margin-bottom:15px; }
        .cantidad-input { width:70px; padding:5px; text-align:center; margin-right:10px; }
        .btn { display:inline-block; padding:12px 20px; background:#3b82f6; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer; }
        .btn:hover { background:#2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="producto">
            <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <div class="info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                <div class="precio">$<?php echo number_format($producto['precio'], 2); ?></div>
                <div class="stock">Disponibles: <?php echo $producto['stock']; ?></div>

                <?php if ($producto['stock'] > 0): ?>
                    <form method="post" action="agregar_carrito.php">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" class="cantidad-input" min="1" max="<?php echo $producto['stock']; ?>" value="1" required>
                        <button type="submit" class="btn">🛒 Agregar al carrito</button>
                    </form>
                <?php else: ?>
                    <p style="color:red; font-weight:bold;">❌ Sin stock</p>
                <?php endif; ?>
            </div>
        </div>
        <p style="margin-top:20px;"><a href="productos.php">⬅ Volver a la tienda</a></p>
    </div>
</body>
</html>
