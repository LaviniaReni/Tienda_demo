<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
include("../includes/db.php");

// 1. Obtener producto actual de forma segura
// Usar una sentencia preparada para evitar la inyección SQL.
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID de producto no válido.");
}

$id = $_GET["id"];
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    // 2. Validación y manejo seguro de la imagen
    if (!empty($_FILES["imagen"]["name"])) {
        $upload_dir = "../img/";
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
            die("Error al subir el archivo.");
        }
        
        $file_info = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $file_info->file($_FILES["imagen"]["tmp_name"]);
        
        if (!in_array($mime_type, $allowed_types)) {
            die("Tipo de archivo no permitido.");
        }
        
        if ($_FILES["imagen"]["size"] > $max_size) {
            die("El archivo es demasiado grande.");
        }

        $file_extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $file_extension;
        $imagen = "img/" . $new_file_name;
        
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $upload_dir . $new_file_name);
    } else {
        $imagen = $producto["imagen"];
    }

    // 3. Sentencia preparada para la actualización de datos
    $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen=? WHERE id=?");
    $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $imagen, $id);
    $stmt->execute();

    header("Location: productos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { display: flex; flex-direction: column; max-width: 400px; margin: auto; }
        input, textarea, button { margin-bottom: 10px; padding: 10px; }
    </style>
</head>
<body>
  <h1>Editar Producto</h1>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br>
    <textarea name="descripcion" required><?= htmlspecialchars($producto['descripcion']) ?></textarea><br>
    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio']) ?>" required><br>
    <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock']) ?>" required><br>
    <p>Imagen actual:</p>
    <img src="../<?= htmlspecialchars($producto['imagen']) ?>" width="100" alt="Imagen del producto"><br>
    <input type="file" name="imagen" accept="image/*"><br>
    <button type="submit">Actualizar</button>
  </form>
</body>
</html>