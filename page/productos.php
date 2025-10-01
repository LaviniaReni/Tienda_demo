<?php
session_start();
require __DIR__ . "/../includes/db.php"; // Conexión segura

// =============================
// VERIFICAR LOGIN
// =============================
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// =============================
// CONFIGURACIÓN DE PAGINACIÓN
// =============================
$productosPorPagina = 12;

// Validar número de página
$paginaActual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($paginaActual < 1) $paginaActual = 1;

$offset = ($paginaActual - 1) * $productosPorPagina;

// Forzar enteros para seguridad en SQL
$productosPorPagina = (int)$productosPorPagina;
$offset = (int)$offset;

// Contar total de productos
$totalProductos = $conn->query("SELECT COUNT(*) as total FROM productos")->fetch_assoc()['total'];
$totalPaginas = ceil($totalProductos / $productosPorPagina);

// Redirigir si la página solicitada está fuera de rango
if ($paginaActual > $totalPaginas && $totalPaginas > 0) {
    header("Location: productos.php?page=" . $totalPaginas);
    exit();
}

// Consulta de productos con límite
$sql = "SELECT id, nombre, descripcion, precio, stock, imagen 
        FROM productos 
        LIMIT $productosPorPagina OFFSET $offset";
$res = $conn->query($sql);

// =============================
// INCLUDES GLOBALES
// =============================
$titulo = "Productos";
include __DIR__ . "/../includes/head.php";
include __DIR__ . "/../includes/header.php";
?>
<link rel="stylesheet" href="/css/components/grid_productos.css">
<div class="productos">
  <div class="productos__card">
    <h1 class="productos__title">Productos Disponibles</h1>
    <div class="productos-grid">
      <?php if ($res && $res->num_rows > 0): ?>
          <?php while ($row = $res->fetch_assoc()): ?>
              <article class="producto-card">
                  <!-- Enlace al detalle -->
                  <a href="<?= $base_url; ?>producto.php?id=<?= (int)$row['id']; ?>">
                      <img src="<?= !empty($row['imagen']) ? $base_url . htmlspecialchars($row['imagen']) : $base_url . 'assets/img/no-image.png'; ?>" 
                           alt="<?= htmlspecialchars($row['nombre']); ?>">
                  </a>

                  <!-- Nombre -->
                  <h3>
                      <a href="<?= $base_url; ?>producto.php?id=<?= (int)$row['id']; ?>" class="producto-link">
                          <?= htmlspecialchars($row['nombre']); ?>
                      </a>
                  </h3>

                  <!-- Descripción, precio, stock -->
                  <p><?= htmlspecialchars(mb_strimwidth($row['descripcion'], 0, 60, '...')); ?></p>
                  <p><strong>$<?= number_format($row['precio'], 2); ?></strong></p>
                  <p>Stock: <?= (int)$row['stock']; ?></p>

                  <!-- Botón agregar -->
                  <form method="post" action="<?= $base_url; ?>agregar_carrito.php">
                      <input type="hidden" name="producto_id" value="<?= (int)$row['id']; ?>">
                      <button type="submit" class="btn-agregar" aria-label="Agregar <?= htmlspecialchars($row['nombre']); ?> al carrito">
                          Agregar al carrito
                      </button>
                  </form>
              </article>
          <?php endwhile; ?>
      <?php else: ?>
          <p>No hay productos disponibles.</p>
      <?php endif; ?>
    </div>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
    <div class="paginacion">
      <!-- Botón Anterior -->
      <?php if ($paginaActual > 1): ?>
        <a href="<?= $base_url; ?>page/productos.php?page=<?= $paginaActual - 1; ?>" class="btn-paginacion">« Anterior</a>
      <?php else: ?>
        <span class="btn-paginacion disabled">« Anterior</span>
      <?php endif; ?>

      <!-- Números de página -->
      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="<?= $base_url; ?>page/productos.php?page=<?= $i; ?>" 
           class="btn-paginacion <?= $i == $paginaActual ? 'activo' : '' ?>">
           <?= $i; ?>
        </a>
      <?php endfor; ?>

      <!-- Botón Siguiente -->
      <?php if ($paginaActual < $totalPaginas): ?>
        <a href="<?= $base_url; ?>page/productos.php?page=<?= $paginaActual + 1; ?>" class="btn-paginacion">Siguiente »</a>
      <?php else: ?>
        <span class="btn-paginacion disabled">Siguiente »</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Volver al inicio -->
    <p class="productos__volver">
      <a href="<?= $base_url; ?>index.php" class="productos__link">Volver al inicio</a>
    </p>
  </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
