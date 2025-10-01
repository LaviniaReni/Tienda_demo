<?php
// =========================
// 1. Detectar protocolo
// =========================
$protocol = "http://";
if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
    $protocol = "https://";
}

// =========================
// 2. Detectar host
// =========================
$rawHost = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';

if (filter_var($rawHost, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
    $host = $rawHost; // dominio válido
} elseif (filter_var($rawHost, FILTER_VALIDATE_IP)) {
    $host = $rawHost; // IP válida
} else {
    $host = '127.0.0.1'; // fallback seguro
}

// =========================
// 3. Agregar puerto si no es estándar
// =========================
$port = $_SERVER['SERVER_PORT'] ?? null;
if ($port && !in_array($port, [80, 443]) && strpos($host, ":$port") === false) {
    $host .= ':' . $port;
}

// =========================
// 4. Detectar carpeta raíz del proyecto
// =========================
// Para localhost (127.0.0.1) o cualquier IP local agregamos la carpeta raíz
$projectFolder = basename(dirname(__DIR__)); // ej: "loginapp"

// Si es dominio real o VirtualHost (ej: loginapp.test o midominio.com) → la app está en raíz
if (!filter_var($host, FILTER_VALIDATE_IP) && $host !== '127.0.0.1') {
    $projectFolder = '';
}

// =========================
// 5. Construir URL base
// =========================
$base_url = $protocol . $host . ($projectFolder ? '/' . $projectFolder : '') . '/';

// =========================
// 6. Definir constante global
// =========================
if (!defined("BASE_URL")) {
    define("BASE_URL", $base_url);
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <meta name="description" content="LoginApp - Tu tienda online para comprar productos al mejor precio.">
    <meta name="author" content="LoginApp">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo $base_url; ?>assets/favicon.ico" type="image/png">

    <!-- Título dinámico -->
    <title><?php echo isset($pageTitle) ? $pageTitle . " | LoginApp" : "LoginApp"; ?></title>

    <!-- Estilos principales -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>css/layout/header.css">
     <!-- Scripts -->
    <script src="<?= BASE_URL; ?>js/header.js" defer></script>
</head>
