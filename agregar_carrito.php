<?php
session_start();
require "includes/db.php";

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!isset($_SESSION['usuario_id'])) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "error" => "No logueado"]);
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}

if (isset($_POST['producto_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $producto_id = intval($_POST['producto_id']);
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
    if ($cantidad < 1) $cantidad = 1;

    // Verificar stock
    $stmt_stock = $conn->prepare("SELECT stock FROM productos WHERE id=?");
    $stmt_stock->bind_param("i", $producto_id);
    $stmt_stock->execute();
    $res_stock = $stmt_stock->get_result();
    $producto = $res_stock->fetch_assoc();
    $stmt_stock->close();

    if (!$producto || $producto['stock'] < $cantidad) {
        $msg = "❌ Producto no disponible o stock insuficiente";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "error" => $msg]);
            exit();
        } else {
            $_SESSION['error'] = $msg;
            header("Location: productos.php");
            exit();
        }
    }

    // Insertar/actualizar carrito
    $stmt = $conn->prepare("SELECT 1 FROM carrito WHERE usuario_id=? AND producto_id=?");
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $stmt2 = $conn->prepare("UPDATE carrito SET cantidad = cantidad + ? WHERE usuario_id=? AND producto_id=?");
        $stmt2->bind_param("iii", $cantidad, $usuario_id, $producto_id);
        $stmt2->execute();
        $stmt2->close();
    } else {
        $stmt3 = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)");
        $stmt3->bind_param("iii", $usuario_id, $producto_id, $cantidad);
        $stmt3->execute();
        $stmt3->close();
    }
    $stmt->close();

    // Obtener nuevo total
    $stmt4 = $conn->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id=?");
    $stmt4->bind_param("i", $usuario_id);
    $stmt4->execute();
    $res = $stmt4->get_result();
    $data = $res->fetch_assoc();
    $count = $data['total'] ?? 0;
    $stmt4->close();

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "count" => (int)$count]);
        exit();
    }
}

header("Location: productos.php");
exit();
