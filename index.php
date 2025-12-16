<?php
// Cargar el archivo JSON con los productos
$productos_json = file_get_contents('productos.json');
$productos = json_decode($productos_json, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($productos)) {
    die("Error: No se pudo cargar el catálogo de productos.");
}

// Extraer categorías únicas
$categorias = [];
foreach ($productos as $p) {
    $cat = trim($p['categoria'] ?? '');
    if ($cat !== '' && !in_array($cat, $categorias)) {
        $categorias[] = $cat;
    }
}
sort($categorias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Cabecera -->
<header class="bg-primary text-white py-3">
    <div class="container">
        <h1 class="h3">Mi Tienda Online</h1>
        <p class="mb-0">Productos de calidad al mejor precio</p>
    </div>
</header>

<!-- Contenido principal -->
<main class="container mt-4 mb-5">
    <!-- Barra de búsqueda -->
    <div class="mb-4">
        <input type="text" 
               id="searchInput" 
               class="form-control" 
               placeholder="Buscar por nombre o descripción...">
    </div>

    <!-- Filtros por categoría -->
    <?php if (!empty($categorias)): ?>
    <div class="mb-3">
        <button class="btn btn-outline-secondary btn-sm me-2 category-filter active" data-category="todos">
            Todas
        </button>
        <?php foreach ($categorias as $cat): ?>
            <button class="btn btn-outline-secondary btn-sm me-2 category-filter"
                    data-category="<?= htmlspecialchars(strtolower($cat)) ?>">
                <?= htmlspecialchars(ucfirst($cat)) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Contador de productos visibles -->
    <div id="productCounter" class="mb-3 text-muted">
        Productos encontrados: <span id="count">0</span>
    </div>

    <!-- Productos -->
    <div id="productContainer" class="row">
        <?php foreach ($productos as $producto): ?>
            <?php
            $nombre = $producto['nombre'] ?? 'Sin nombre';
            $precio = $producto['precio'] ?? '0.00';
            $foto = $producto['foto'] ?? '';
            $descripcion = $producto['descripcion'] ?? '';
            $categoria = strtolower($producto['categoria'] ?? '');
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 product-item"
                 data-nombre="<?= htmlspecialchars(strtolower($nombre)) ?>"
                 data-descripcion="<?= htmlspecialchars(strtolower($descripcion)) ?>"
                 data-categoria="<?= htmlspecialchars($categoria) ?>">
                <div class="card product-card h-100 shadow-sm">
                    <?php if ($foto): ?>
                        <img src="img/<?= htmlspecialchars($foto) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($nombre) ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted"><?= ucfirst(htmlspecialchars($producto['categoria'] ?? 'Sin categoría')) ?></small>
                        <h6 class="card-title"><?= htmlspecialchars($nombre) ?></h6>
                        <p class="card-text"><?= htmlspecialchars($descripcion) ?></p>
                        <p class="card-text mt-auto">
                            <strong>$<?= number_format((float)$precio, 2, ',', '.') ?></strong>
                        </p>
                        <!-- Botón WhatsApp -->
                        <a href="https://wa.me/5355348649?text=Hola,%20quiero%20comprar:%20<?= urlencode($nombre) ?>%20(%24<?= $precio ?>)" 
                           target="_blank" 
                           class="btn btn-success btn-sm mt-2">
                            Comprar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Mensaje si no hay resultados -->
    <div id="noResults" class="text-center text-muted py-4" style="display: none;">
        No se encontraron productos que coincidan con tu búsqueda o categoría.
    </div>
</main>

<!-- Pie de página -->
<footer class="bg-dark text-light text-center py-3 mt-auto">
    <div class="container">
        <p class="mb-0">&copy; 2025 Mi Tienda Online. Todos los derechos reservados.</p>
    </div>
</footer>

<!-- JavaScript -->
<script>
function filtrarProductos() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const categoriaActiva = document.querySelector('.category-filter.active').getAttribute('data-category');
    const items = document.querySelectorAll('.product-item');
    let visibleCount = 0;

    items.forEach(item => {
        const nombre = item.getAttribute('data-nombre') || '';
        const descripcion = item.getAttribute('data-descripcion') || '';
        const categoria = item.getAttribute('data-categoria') || '';

        const coincideBusqueda = (searchTerm === '' || nombre.includes(searchTerm) || descripcion.includes(searchTerm));
        const coincideCategoria = (categoriaActiva === 'todos' || categoria === categoriaActiva);

        if (coincideBusqueda && coincideCategoria) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Actualizar contador
    document.getElementById('count').textContent = visibleCount;

    // Mostrar/ocultar mensaje
    document.getElementById('noResults').style.display = (visibleCount === 0) ? 'block' : 'none';
}

// Eventos
document.getElementById('searchInput').addEventListener('input', filtrarProductos);

document.querySelectorAll('.category-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        filtrarProductos();
    });
});

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', filtrarProductos);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
