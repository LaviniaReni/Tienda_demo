<?php
$mensaje = "";
$mensaje_tipo = ""; // "success", "error" o "warn"

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST["nombre"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $asunto   = trim($_POST["asunto"] ?? "");
    $mensajeU = trim($_POST["mensaje"] ?? "");

    // Validaciones básicas
    if ($nombre === "" || $email === "" || $asunto === "" || $mensajeU === "") {
        $mensaje = "⚠️ Completa todos los campos.";
        $mensaje_tipo = "warn";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "⚠️ El correo no tiene un formato válido.";
        $mensaje_tipo = "warn";
    } else {
        // --- CONFIGURACIÓN DE MAILPIT ---
        ini_set("SMTP", "localhost");
        ini_set("smtp_port", "1025"); // Puerto por defecto de Mailpit en Laragon

        $to      = "admin@localhost"; // receptor ficticio para testing local
        $subject = "Nuevo mensaje de contacto: $asunto";

        $body = "Has recibido un nuevo mensaje desde el formulario de contacto:\r\n\r\n";
        $body .= "Nombre: $nombre\r\n";
        $body .= "Email: $email\r\n";
        $body .= "Asunto: $asunto\r\n";
        $body .= "Mensaje:\r\n$mensajeU\r\n";

        $headers = "From: " . $nombre . " <" . $email . ">";

        if (mail($to, $subject, $body, $headers)) {
            $mensaje = "✅ Tu mensaje fue enviado y capturado por Mailpit.";
            $mensaje_tipo = "success";
        } else {
            $mensaje = "❌ Hubo un error al enviar el correo.";
            $mensaje_tipo = "error";
        }
    }
}

$titulo = "Contacto";
include __DIR__ . "/../includes/head.php";
include __DIR__ . "/../includes/header.php";
?>
<main class="contact">
    <div class="contact__container">
        <h1 class="contact__title">Contacto</h1>

        <?php if ($mensaje): ?>
            <div class="contact__message contact__message--<?= htmlspecialchars($mensaje_tipo, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form class="contact__form" method="post" novalidate>
            <input class="contact__input" type="text" name="nombre" placeholder="Tu nombre" required>
            <input class="contact__input" type="email" name="email" placeholder="Tu correo" required>
            <input class="contact__input" type="text" name="asunto" placeholder="Asunto" required>
            <textarea class="contact__textarea" name="mensaje" rows="5" placeholder="Escribe tu mensaje" required></textarea>
            <button class="contact__button" type="submit">Enviar</button>
        </form>
    </div>
</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
