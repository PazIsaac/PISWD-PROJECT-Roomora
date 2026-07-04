<?php
require_once __DIR__ . '/includes/alquileres.php';

$tituloPagina = 'Alquileres';
$paginaActiva = 'inicio';
$busquedaTipo = $_GET['busqueda'] ?? '';
$tipoFiltro = $_GET['tipo'] ?? '';
$soloDisponibles = !isset($_GET['todos']) || $_GET['todos'] !== '1';
$ubicacionFiltro = $busquedaTipo;

$dbError = null;
try {
    $alquileresFiltrados = cargarAlquileres($busquedaTipo, $tipoFiltro, $soloDisponibles);
} catch (PDOException $e) {
    $dbError = mensajeErrorDb();
    $alquileresFiltrados = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout">
    <aside class="sidebar">
        <form class="sidebar-form" method="get" action="index.php" id="filtros">
            <section class="filter-block">
                <label class="filter-label serif" for="busqueda">Buscar tipo:</label>
                <div class="filter-input-wrap">
                    <span class="icon-search" aria-hidden="true">&#128269;</span>
                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="<?php echo htmlspecialchars($busquedaTipo); ?>"
                        placeholder="Monoambiente, Duplex..."
                    >
                    <?php if ($busquedaTipo !== ''): ?>
                        <a href="index.php<?php
                            $qs = [];
                            if ($tipoFiltro) $qs[] = 'tipo=' . urlencode($tipoFiltro);
                            if (!$soloDisponibles) $qs[] = 'todos=1';
                            echo $qs ? '?' . implode('&', $qs) : '';
                        ?>" class="clear-btn" title="Limpiar búsqueda">&times;</a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="filter-block">
                <label class="filter-label" for="tipo">Tipo de Ambiente:</label>
                <select id="tipo" name="tipo" class="filter-select">
                    <option value="">Todos</option>
                    <?php foreach (TIPOS_AMBIENTE as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo strcasecmp($tipoFiltro, $tipo) === 0 ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </section>

            <section class="filter-block filter-check">
                <label class="filter-check-label">
                    <input type="checkbox" name="solo_disp" value="1" <?php echo $soloDisponibles ? 'checked' : ''; ?>
                           onchange="document.getElementById('todos').value = this.checked ? '0' : '1'; document.getElementById('filtros').submit();">
                    Solo disponibles
                </label>
                <input type="hidden" name="todos" id="todos" value="<?php echo $soloDisponibles ? '0' : '1'; ?>">
            </section>

            <button type="submit" class="btn-filter">Aplicar filtros</button>
        </form>
    </aside>

    <main class="content">
        <?php if ($dbError): ?>
            <p class="msg-error"><?php echo htmlspecialchars($dbError); ?></p>
        <?php endif; ?>
        <?php if (!empty($_GET['creado'])): ?>
            <p class="msg-ok banner-ok">Departamento registrado correctamente.</p>
        <?php endif; ?>
        <?php if (empty($alquileresFiltrados)): ?>
            <p class="empty-msg">No hay departamentos que coincidan con los filtros.</p>
        <?php else: ?>
            <div class="grid-alquileres">
                <?php foreach ($alquileresFiltrados as $dep): ?>
                    <article class="card-alquiler <?php echo $dep['disponible'] ? '' : 'card-no-disponible'; ?>">
                        <div class="card-imagen"></div>
                        <div class="card-info">
                            <h3><?php echo htmlspecialchars($dep['tipo']); ?> #<?php echo (int) $dep['id']; ?></h3>
                            <p class="card-meta">
                                <?php echo (int) $dep['ambientes']; ?> amb. ·
                                <?php echo (int) $dep['metros_cuadrados']; ?> m² ·
                                <?php echo etiquetaDisponible($dep['disponible']); ?>
                            </p>
                            <p class="card-precio"><?php echo formatearPrecio($dep['precio']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
document.getElementById('tipo').addEventListener('change', function () {
    document.getElementById('filtros').submit();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
