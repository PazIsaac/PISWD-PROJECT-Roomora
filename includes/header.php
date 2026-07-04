<?php
require_once __DIR__ . '/sesion.php';

$tituloPagina = $tituloPagina ?? 'Roomora';
$paginaActiva = $paginaActiva ?? 'inicio';
$busquedaTipo = $busquedaTipo ?? ($ubicacionFiltro ?? '');
$usuario = usuarioLogueado();
$esAdmin = $usuario && $usuario['rol'] === 'admin';
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
            <?php if ($esAdmin): ?>
                <a href="Registro.php" class="nav-btn <?php echo $paginaActiva === 'registro' ? 'active' : ''; ?>">Registro</a>
                <a href="departamento.php" class="nav-btn <?php echo $paginaActiva === 'departamento' ? 'active' : ''; ?>">Departamento</a>
            <?php endif; ?>
            <?php if ($usuario): ?>
                <?php if (!$esAdmin): ?>
                    <a href="mis-alquileres.php" class="nav-btn <?php echo $paginaActiva === 'mis-alquileres' ? 'active' : ''; ?>">Mis alquileres</a>
                <?php else: ?>
                    <a href="gestionar-alquileres.php" class="nav-btn <?php echo $paginaActiva === 'gestionar-alquileres' ? 'active' : ''; ?>">Solicitudes</a>
                <?php endif; ?>
                <span class="nav-user"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                <a href="logout.php" class="nav-btn">Salir</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn <?php echo $paginaActiva === 'login' ? 'active' : ''; ?>">Ingresar</a>
            <?php endif; ?>
        </nav>
    </header>
