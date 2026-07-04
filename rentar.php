<?php
require_once __DIR__ . '/includes/alquileres.php';
require_once __DIR__ . '/includes/sesion.php';
requerirLogin();

$tituloPagina = 'Solicitar alquiler';
$paginaActiva = '';
$error = '';
$exito = '';
$departamento = null;

$depId = (int) ($_GET['id'] ?? 0);
if ($depId > 0) {
    $departamento = buscarPorId($depId);
}

if (!$departamento) {
    header('Location: index.php');
    exit;
}

$usuario = usuarioLogueado();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? '';

    if ($fechaInicio === '' || $fechaFin === '') {
        $error = 'Completá las fechas.';
    } elseif ($fechaFin <= $fechaInicio) {
        $error = 'La fecha de fin debe ser posterior a la de inicio.';
    } else {
        try {
            $renta = solicitarRenta($departamento['id'], $usuario['id'], $fechaInicio, $fechaFin);
            if ($renta) {
                $exito = 'Solicitud enviada. Podés ver el estado en <a href="mis-alquileres.php">Mis alquileres</a>.';
            }
        } catch (PDOException $e) {
            $error = 'Error al enviar la solicitud: ' . htmlspecialchars($e->getMessage());
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Solicitar alquiler</h1>

            <div class="renta-dep-info">
                <strong><?php echo htmlspecialchars($departamento['tipo']); ?> #<?php echo (int) $departamento['id']; ?></strong>
                <span><?php echo (int) $departamento['ambientes']; ?> amb. · <?php echo (int) $departamento['metros_cuadrados']; ?> m² · <?php echo formatearPrecio($departamento['precio']); ?></span>
            </div>

            <?php if ($error): ?>
                <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($exito): ?>
                <p class="msg-ok"><?php echo $exito; ?></p>
            <?php else: ?>
                <form method="post" action="rentar.php?id=<?php echo (int) $departamento['id']; ?>" class="abm-form">
                    <label>
                        Fecha de inicio
                        <input type="date" name="fecha_inicio" required value="<?php echo htmlspecialchars($_POST['fecha_inicio'] ?? ''); ?>">
                    </label>
                    <label>
                        Fecha de fin
                        <input type="date" name="fecha_fin" required value="<?php echo htmlspecialchars($_POST['fecha_fin'] ?? ''); ?>">
                    </label>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Enviar solicitud</button>
                        <a href="index.php" class="btn-secondary">Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
