<?php
require_once __DIR__ . '/includes/alquileres.php';
require_once __DIR__ . '/includes/sesion.php';
requerirAdmin();

$tituloPagina = 'Gestionar solicitudes';
$paginaActiva = 'gestionar-alquileres';
$mensaje = '';
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rentaId = (int) ($_POST['renta_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';

        if ($rentaId > 0 && in_array($estado, ['aprobado', 'rechazado'])) {
            if (actualizarEstadoRenta($rentaId, $estado)) {
                $mensaje = 'Solicitud ' . ($estado === 'aprobado' ? 'aprobada' : 'rechazada') . '.';
            } else {
                $error = 'No se pudo actualizar la solicitud.';
            }
        }
    }

    $rentas = todasLasRentas();
} catch (PDOException $e) {
    $dbError = mensajeErrorDb();
    $rentas = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Gestionar solicitudes de alquiler</h1>
            <?php if (!empty($mensaje)): ?>
                <p class="msg-ok"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (!empty($dbError)): ?>
                <p class="msg-error"><?php echo htmlspecialchars($dbError); ?></p>
            <?php endif; ?>
            <?php if (empty($rentas)): ?>
                <p class="empty-msg">No hay solicitudes todavía.</p>
            <?php else: ?>
                <ul class="abm-lista">
                    <?php foreach ($rentas as $r): ?>
                        <li class="abm-item">
                            <div class="abm-item-info">
                                <strong><?php echo htmlspecialchars($r['tipo']); ?> #<?php echo (int) $r['departamento_id']; ?></strong>
                                <span>
                                    <?php echo htmlspecialchars($r['usuario_nombre']); ?> ·
                                    <?php echo htmlspecialchars($r['fecha_inicio']); ?> → <?php echo htmlspecialchars($r['fecha_fin']); ?> ·
                                    <?php echo etiquetaEstadoRenta($r['estado']); ?>
                                </span>
                            </div>
                            <div class="abm-item-actions">
                                <a href="chat.php?renta_id=<?php echo (int) $r['id']; ?>" class="btn-small">Chat</a>
                                <?php if ($r['estado'] === 'pendiente'): ?>
                                    <form method="post" action="gestionar-alquileres.php" class="inline-form">
                                        <input type="hidden" name="renta_id" value="<?php echo (int) $r['id']; ?>">
                                        <input type="hidden" name="estado" value="aprobado">
                                        <button type="submit" class="btn-small" style="background:#4caf50;color:#fff;">Aprobar</button>
                                    </form>
                                    <form method="post" action="gestionar-alquileres.php" class="inline-form">
                                        <input type="hidden" name="renta_id" value="<?php echo (int) $r['id']; ?>">
                                        <input type="hidden" name="estado" value="rechazado">
                                        <button type="submit" class="btn-small btn-danger">Rechazar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
