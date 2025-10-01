<?php
session_start();
require "includes/db.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener productos del carrito
$sql = "SELECT c.producto_id, c.cantidad, p.precio, p.stock 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$productos = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($productos) === 0) {
    header("Location: carrito.php");
    exit();
}

// 🔎 Verificar stock disponible
foreach ($productos as $item) {
    if ($item['cantidad'] > $item['stock']) {
        // No hay stock suficiente
        $_SESSION['error'] = "No hay suficiente stock para el producto ID " . $item['producto_id'];
        header("Location: carrito.php");
        exit();
    }
}

// Calcular total
$total = 0;
foreach ($productos as $item) {
    $total += $item['cantidad'] * $item['precio'];
}

// Crear orden
$sql = "INSERT INTO ordenes (usuario_id, total) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $usuario_id, $total);
$stmt->execute();
$orden_id = $stmt->insert_id;
$stmt->close();

// Insertar detalles y actualizar stock
foreach ($productos as $item) {
    // Insertar detalle
    $sql = "INSERT INTO orden_detalle (orden_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $orden_id, $item['producto_id'], $item['cantidad'], $item['precio']);
    $stmt->execute();
    $stmt->close();

    // Actualizar stock
    $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $item['cantidad'], $item['producto_id']);
    $stmt->execute();
    $stmt->close();
}

// Vaciar carrito
$sql = "DELETE FROM carrito WHERE usuario_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->close();

header("Location: confirmacion.php?orden_id=" . $orden_id);
exit();
