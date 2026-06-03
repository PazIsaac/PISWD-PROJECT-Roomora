<?php
$tituloPagina = $tituloPagina ?? 'Roomora';
$paginaActiva = $paginaActiva ?? 'inicio';
$busquedaTipo = $busquedaTipo ?? ($ubicacionFiltro ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title><?php echo htmlspecialchars($tituloPagina); ?> — Roomora</title>
</head>
<body>
    <header class="topbar">
        <div class="topbar-left">
            <a href="index.php" class="logo" aria-label="Roomora inicio"></a>
            <form class="topbar-search" method="get" action="index.php" role="search">
                <span class="icon-search" aria-hidden="true">&#128269;</span>
                <input
                    type="search"
                    name="busqueda"
                    placeholder="Buscar tipo: Monoambiente, Duplex..."
                    value="<?php echo htmlspecialchars($busquedaTipo); ?>"
                >
                <?php if (!empty($tipoFiltro ?? '')): ?>
                    <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipoFiltro); ?>">
                <?php endif; ?>
                <?php if (isset($soloDisponibles) && !$soloDisponibles): ?>
                    <input type="hidden" name="todos" value="1">
                <?php endif; ?>
            </form>
        </div>
        <nav class="topbar-nav">
            <a href="index.php" class="nav-btn <?php echo $paginaActiva === 'inicio' ? 'active' : ''; ?>">inicio</a>
            <a href="Registro.php" class="nav-btn <?php echo $paginaActiva === 'registro' ? 'active' : ''; ?>">Registro</a>
            <a href="departamento.php" class="nav-btn <?php echo $paginaActiva === 'departamento' ? 'active' : ''; ?>">Departamento</a>
        </nav>
    </header>
