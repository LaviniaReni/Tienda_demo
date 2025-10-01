<?php
include '../includes/db.php';

// --- FILTROS ---
$where = [];
$params = [];
$types = '';

if (!empty($_GET['nombre'])) {
    $where[] = "p.nombre LIKE ?";
    $params[] = "%{$_GET['nombre']}%";
    $types .= 's';
}

if (!empty($_GET['precio_min'])) {
    $where[] = "p.precio >= ?";
    $params[] = floatval($_GET['precio_min']); // forzar número
    $types .= 'd';
}

if (!empty($_GET['precio_max'])) {
    $where[] = "p.precio <= ?";
    $params[] = floatval($_GET['precio_max']); // forzar número
    $types .= 'd';
}

if (!empty($_GET['stock_min'])) {
    $where[] = "p.stock >= ?";
    $params[] = intval($_GET['stock_min']);
    $types .= 'i';
}

if (!empty($_GET['stock_max'])) {
    $where[] = "p.stock <= ?";
    $params[] = intval($_GET['stock_max']);
    $types .= 'i';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// --- PAGINACIÓN ---
$productos_por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $productos_por_pagina;

// Contar productos
$sql_count = "SELECT COUNT(*) FROM productos p $whereSql";
$stmt_count = $conn->prepare($sql_count);
if ($params) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$stmt_count->bind_result($total_productos);
$stmt_count->fetch();
$stmt_count->close();

$total_paginas = ceil($total_productos / $productos_por_pagina);

// Si la página es mayor al total, corregimos
if ($pagina > $total_paginas && $total_paginas > 0) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $productos_por_pagina;
}

// Obtener productos
$sql = "SELECT p.id, p.nombre, p.precio, p.stock, p.imagen, c.nombre as categoria
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id
        $whereSql 
        ORDER BY p.id DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if ($params) {
    $bind_params = [...$params, $offset, $productos_por_pagina];
    $stmt->bind_param($types . 'ii', ...$bind_params);
} else {
    $stmt->bind_param('ii', $offset, $productos_por_pagina);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .producto-imagen {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
        }
        .acciones {
            display: flex;
            gap: 5px;
        }
        .acciones form {
            display: inline;
        }
        .paginacion {
            margin-top: 15px;
        }
        .paginacion a {
            padding: 5px 10px;
            border: 1px solid #ccc;
            margin: 0 2px;
            text-decoration: none;
            color: #333;
        }
        .paginacion a.active {
            background: #007bff;
            color: #fff;
        }
        .btn.eliminar {
            background: #dc3545;
            color: #fff;
        }
        .btn.eliminar:hover {
            background: #a71d2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Productos</h2>
        
        <a href="agregar_producto.php" class="btn">➕ Agregar Producto</a>
        <a href="productos.php" class="btn" style="background:#6c757d;">❌ Limpiar filtros</a>
        
        <form method="get" class="filtros">
            <input type="text" name="nombre" placeholder="Nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
            <input type="number" name="precio_min" placeholder="Precio mínimo" value="<?= htmlspecialchars($_GET['precio_min'] ?? '') ?>">
            <input type="number" name="precio_max" placeholder="Precio máximo" value="<?= htmlspecialchars($_GET['precio_max'] ?? '') ?>">
            <input type="number" name="stock_min" placeholder="Stock mínimo" value="<?= htmlspecialchars($_GET['stock_min'] ?? '') ?>">
            <input type="number" name="stock_max" placeholder="Stock máximo" value="<?= htmlspecialchars($_GET['stock_max'] ?? '') ?>">
            <button type="submit" class="btn">🔍 Buscar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    $imgPath = !empty($row['imagen']) && file_exists('../'.$row['imagen']) 
                               ? '../'.htmlspecialchars($row['imagen']) 
                               : '../assets/img/default.png';
                    ?>
                    <tr>
                        <td><img src="<?= $imgPath ?>" class="producto-imagen" alt="Imagen de <?= htmlspecialchars($row['nombre']) ?>"></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['categoria'] ?? 'Sin categoría') ?></td>
                        <td>$<?= number_format($row['precio'], 2, ',', '.') ?></td>
                        <td><?= number_format($row['stock']) ?></td>
                        <td class="acciones">
                            <a href="editar_producto.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn">✏️ Editar</a>
                            <form action="eliminar.php" method="POST" onsubmit="return confirm('¿Seguro de eliminar este producto? Esta acción no se puede deshacer.');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit" class="btn eliminar">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No se encontraron productos</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="paginacion">
            <?php
            if ($total_paginas > 1) {
                $query_string = $_GET;
                $inicio = max(1, $pagina - 2);
                $fin = min($total_paginas, $pagina + 2);

                if ($pagina > 1) {
                    $query_string['pagina'] = $pagina - 1;
                    echo '<a href="?'.http_build_query($query_string).'">⬅️ Anterior</a>';
                }

                for ($i = $inicio; $i <= $fin; $i++) {
                    $query_string['pagina'] = $i;
                    $active = ($i == $pagina) ? 'active' : '';
                    echo '<a href="?'.http_build_query($query_string).'" class="'.$active.'">'.$i.'</a>';
                }

                if ($pagina < $total_paginas) {
                    $query_string['pagina'] = $pagina + 1;
                    echo '<a href="?'.http_build_query($query_string).'">Siguiente ➡️</a>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
