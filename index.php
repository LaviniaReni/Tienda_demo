<?php
// index.php
include("includes/db.php");

// Definir título de página (opcional)
$pageTitle = "Inicio - LoginApp";

// Guardar el contenido en un buffer
ob_start();

// ==================== CONTENIDO DE LA PÁGINA ====================
?>

<h1>Productos disponibles</h1>

<div class="grid-productos">
    <?php
    $sql = "SELECT id, nombre, descripcion, precio, imagen FROM productos ORDER BY creado DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()): ?>
            <div class="producto-card">
                <?php if (!empty($row['imagen'])): ?>
                    <img src="<?php echo htmlspecialchars($row['imagen']); ?>" 
                         alt="<?php echo htmlspecialchars($row['nombre']); ?>" 
                         class="producto-img">
                <?php else: ?>
                    <img src="img/no-image.png" alt="Sin imagen" class="producto-img">
                <?php endif; ?>

                <h2 class="producto-nombre"><?php echo htmlspecialchars($row['nombre']); ?></h2>
                <p class="producto-desc"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p class="producto-precio">$<?php echo number_format($row['precio'], 2); ?></p>

                <a href="carrito.php?add=<?php echo $row['id']; ?>" class="btn-agregar">
                    Agregar al carrito
                </a>
            </div>
        <?php endwhile;
    else: ?>
        <p>No hay productos disponibles.</p>
    <?php endif; ?>
</div>

<?php
// ==================== FIN DEL CONTENIDO ====================

// Pasar contenido al layout
$content = ob_get_clean();
include("layout.php");
