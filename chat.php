<?php
require_once __DIR__ . '/includes/alquileres.php';
require_once __DIR__ . '/includes/sesion.php';
requerirLogin();

$tituloPagina = 'Chat';
$paginaActiva = '';
$error = '';

$rentaId = (int) ($_GET['renta_id'] ?? 0);
$renta = $rentaId > 0 ? buscarRentaPorId($rentaId) : null;

if (!$renta) {
    header('Location: index.php');
    exit;
}

$usuario = usuarioLogueado();
$esAdmin = $usuario['rol'] === 'admin';

// Solo el dueño de la renta o el admin pueden ver el chat
if (!$esAdmin && $renta['usuario_id'] !== $usuario['id']) {
    header('Location: index.php');
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['mensaje'] ?? ''))) {
        enviarMensaje($rentaId, $usuario['id'], trim($_POST['mensaje']));
        header('Location: chat.php?renta_id=' . $rentaId);
        exit;
    }

    $mensajes = mensajesDeRenta($rentaId);
} catch (PDOException $e) {
    $error = mensajeErrorDb();
    $mensajes = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Chat · <?php echo htmlspecialchars($renta['tipo']); ?> #<?php echo (int) $renta['departamento_id']; ?></h1>
            <p class="chat-meta">
                <?php echo htmlspecialchars($renta['usuario_nombre']); ?> ·
                <?php echo htmlspecialchars($renta['fecha_inicio']); ?> → <?php echo htmlspecialchars($renta['fecha_fin']); ?> ·
                <?php echo etiquetaEstadoRenta($renta['estado']); ?>
            </p>

            <?php if ($error): ?>
                <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <div class="chat-box" id="chat-box">
                <?php if (empty($mensajes)): ?>
                    <p class="chat-empty">No hay mensajes todavía. Escribí el primero.</p>
                <?php else: ?>
                    <?php foreach ($mensajes as $m): ?>
                        <div class="chat-msg <?php echo $m['usuario_id'] === $usuario['id'] ? 'chat-msg-propia' : 'chat-msg-otra'; ?>">
                            <div class="chat-msg-header">
                                <strong><?php echo htmlspecialchars($m['usuario_nombre']); ?></strong>
                                <span class="chat-msg-time"><?php echo htmlspecialchars($m['created_at']); ?></span>
                            </div>
                            <div class="chat-msg-text"><?php echo nl2br(htmlspecialchars($m['mensaje'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="post" action="chat.php?renta_id=<?php echo (int) $rentaId; ?>" class="chat-form">
                <textarea name="mensaje" rows="2" placeholder="Escribí un mensaje..." required></textarea>
                <button type="submit" class="btn-primary">Enviar</button>
            </form>

            <div class="form-actions" style="margin-top:0.5rem;">
                <?php if ($esAdmin): ?>
                    <a href="gestionar-alquileres.php" class="btn-secondary">Volver a solicitudes</a>
                <?php else: ?>
                    <a href="mis-alquileres.php" class="btn-secondary">Volver a mis alquileres</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
    var chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>