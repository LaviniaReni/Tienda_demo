<?php
session_start();
require "includes/db.php";

header('Content-Type: application/json');

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["count" => 0]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

$count = $data['total'] ?? 0;

echo json_encode(["count" => (int)$count]);
