<?php
// Configuración de la base de datos
$host = "localhost";  // servidor
$user = "root";       // usuario por defecto en XAMPP
$pass = "";           // contraseña por defecto en XAMPP
$db   = "mi_base";    // nombre de la base que creaste

// Conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar si hay error
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>
