<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

include("../includes/db.php"); // Tu archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Definir la ruta de destino y el nombre de la imagen
    $upload_dir = "../img/";
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    // 1. Validar el archivo de imagen
    if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
        die("Error al subir el archivo.");
    }
    
    // 2. Validar tipo de archivo (MIME) y tamaño
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $file_info->file($_FILES["imagen"]["tmp_name"]);
    
    if (!in_array($mime_type, $allowed_types)) {
        die("Tipo de archivo no permitido. Solo se aceptan JPEG, PNG, GIF y WebP.");
    }
    
    if ($_FILES["imagen"]["size"] > $max_size) {
        die("El archivo es demasiado grande. El tamaño máximo es de 5 MB.");
    }

    // 3. Generar un nombre de archivo único para evitar colisiones
    $file_extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $new_file_name = uniqid() . '.' . $file_extension;
    $imagen = "img/" . $new_file_name;

    // 4. Mover la imagen al directorio de destino
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $upload_dir . $new_file_name)) {
        // 5. Insertar datos en la base de datos
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen);
        
        if ($stmt->execute()) {
            header("Location: productos.php");
            exit;
        } else {
            die("Error al insertar en la base de datos: " . $stmt->error);
        }
    } else {
        die("Error al guardar la imagen en el servidor.");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { display: flex; flex-direction: column; max-width: 400px; margin: auto; }
        input, textarea, button { margin-bottom: 10px; padding: 10px; }
    </style>
</head>
<body>
    <h1>Agregar Producto</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <textarea name="descripcion" placeholder="Descripción" required></textarea><br>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required><br>
        <input type="number" name="stock" placeholder="Stock" required><br>
        <input type="file" name="imagen" accept="image/*" required><br>
        <button type="submit">Guardar</button>
    </form>
</body>
</html>