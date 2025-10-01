<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
include("../includes/db.php");

// 1. Validar y sanitizar el ID de forma segura
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];

    // 2. Eliminar la imagen del servidor de forma segura
    // Usar una sentencia preparada para la consulta SELECT
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prod = $result->fetch_assoc();

    // Comprobar que el producto existe y que tiene una imagen válida antes de borrar
    if ($prod && !empty($prod["imagen"])) {
        $image_path = "../" . $prod["imagen"];
        if (file_exists($image_path) && is_file($image_path)) {
            unlink($image_path);
        }
    }

    // 3. Eliminar el producto de la base de datos de forma segura
    // Usar una sentencia preparada para la consulta DELETE
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

} else {
    // Redirigir si el ID no es válido
    header("Location: productos.php?error=invalid_id");
    exit;
}

header("Location: productos.php");
exit;
?>