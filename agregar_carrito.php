<?php
session_start();
require "includes/db.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Agregar producto al carrito
if (isset($_POST['producto_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $producto_id = intval($_POST['producto_id']);
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

    if ($cantidad < 1) {
        $cantidad = 1; // seguridad
    }

    // Verificar stock antes de insertar/actualizar
    $sql_stock = "SELECT stock FROM productos WHERE id=?";
    $stmt_stock = $conn->prepare($sql_stock);
    $stmt_stock->bind_param("i", $producto_id);
    $stmt_stock->execute();
    $res_stock = $stmt_stock->get_result();
    $producto = $res_stock->fetch_assoc();
    $stmt_stock->close();

    if (!$producto || $producto['stock'] <= 0) {
        $_SESSION['error'] = "❌ El producto no está disponible.";
        header("Location: productos.php");
        exit();
    }

    // Comprobar si ya está en el carrito
    $sql = "SELECT * FROM carrito WHERE usuario_id=? AND producto_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // Aumentar cantidad existente
        $sql_update = "UPDATE carrito 
                       SET cantidad = cantidad + ? 
                       WHERE usuario_id=? AND producto_id=?";
        $stmt2 = $conn->prepare($sql_update);
        $stmt2->bind_param("iii", $cantidad, $usuario_id, $producto_id);
        $stmt2->execute();
        $stmt2->close();
    } else {
        // Insertar nuevo producto
        $sql_insert = "INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)";
        $stmt3 = $conn->prepare($sql_insert);
        $stmt3->bind_param("iii", $usuario_id, $producto_id, $cantidad);
        $stmt3->execute();
        $stmt3->close();
    }

    $stmt->close();
}

// Redirigir a productos
header("Location: productos.php");
exit();
?>
