<?php
// layout.php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Variable que cada página debe definir antes de incluir layout.php
$pageTitle = $pageTitle ?? "LoginApp";
$pageStyles = $pageStyles ?? [];
$pageScripts = $pageScripts ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<?php include __DIR__ . "/includes/head.php"; ?>
<body>

  <?php include __DIR__ . "/includes/header.php"; ?>

  <main class="container">
    <?php 
    // Aquí se inyecta el contenido de cada página
    if (isset($content)) {
        echo $content;
    } else {
        echo "<p>Error: no se ha definido contenido para esta página.</p>";
    }
    ?>
  </main>

  <?php include __DIR__ . "/includes/footer.php"; ?>

  <!-- Estilos específicos de la página -->
  <?php foreach ($pageStyles as $css): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
  <?php endforeach; ?>

  <!-- Scripts específicos de la página -->
  <?php foreach ($pageScripts as $js): ?>
    <script src="<?php echo htmlspecialchars($js); ?>" defer></script>
  <?php endforeach; ?>
</body>
</html>
