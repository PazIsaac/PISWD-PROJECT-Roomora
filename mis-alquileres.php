<?php
require_once __DIR__ . '/includes/alquileres.php';
require_once __DIR__ . '/includes/sesion.php';
requerirLogin();

$tituloPagina = 'Mis alquileres';
$paginaActiva = 'mis-alquileres';
$usuario = usuarioLogueado();

$rentas = [];
try {
    $rentas = rentasDeUsuario($usuario['id']);
} catch (PDOException $e) {
    $dbError = mensajeErrorDb();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Mis solicitudes de alquiler</h1>
            <?php if (!empty($dbError)): ?>
                <p class="msg-error"><?php echo htmlspecialchars($dbError); ?></p>
            <?php endif; ?>
            <?php if (empty($rentas)): ?>
                <p class="empty-msg">No solicitaste ningún alquiler todavía. <a href="index.php" class="auth-link">Ver departamentos</a>.</p>
            <?php else: ?>
                <ul class="abm-lista">
                    <?php foreach ($rentas as $r): ?>
                        <li class="abm-item">
                            <div class="abm-item-info">
                                <strong><?php echo htmlspecialchars($r['tipo']); ?> #<?php echo (int) $r['departamento_id']; ?></strong>
                                <span>
                                    <?php echo htmlspecialchars($r['fecha_inicio']); ?> → <?php echo htmlspecialchars($r['fecha_fin']); ?> ·
                                    <?php echo etiquetaEstadoRenta($r['estado']); ?>
                                </span>
                            </div>
                            <div class="abm-item-actions">
                                <a href="chat.php?renta_id=<?php echo (int) $r['id']; ?>" class="btn-small">Chat</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<!-- a -->
