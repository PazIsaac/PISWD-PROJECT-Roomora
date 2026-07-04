<?php
require_once __DIR__ . '/includes/alquileres.php';
require_once __DIR__ . '/includes/sesion.php';
requerirAdmin();

$tituloPagina = 'Registro de departamento';
$paginaActiva = 'registro';
$error = '';
$dbError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $creado = crearAlquiler($_POST);
        if ($creado) {
            header('Location: index.php?creado=1');
            exit;
        }
        $error = 'Seleccioná el tipo de ambiente.';
    } catch (PDOException $e) {
        $dbError = mensajeErrorDb();
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Alta de departamento</h1>
            <?php if ($dbError): ?>
                <p class="msg-error"><?php echo htmlspecialchars($dbError); ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="post" action="Registro.php" class="abm-form">
                <label>
                    Tipo de ambiente
                    <select name="tipo" required>
                        <option value="">Seleccionar…</option>
                        <?php foreach (TIPOS_AMBIENTE as $tipo): ?>
                            <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo (($_POST['tipo'] ?? '') === $tipo) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Precio mensual ($)
                    <input type="number" name="precio" min="0" step="0.01" required value="<?php echo htmlspecialchars($_POST['precio'] ?? ''); ?>">
                </label>
                <label>
                    Cantidad de ambientes
                    <input type="number" name="ambientes" min="0" required value="<?php echo htmlspecialchars($_POST['ambientes'] ?? '1'); ?>">
                </label>
                <label>
                    Metros cuadrados
                    <input type="number" name="metros_cuadrados" min="0" required value="<?php echo htmlspecialchars($_POST['metros_cuadrados'] ?? ''); ?>">
                </label>
                <label class="filter-check-label">
                    <input type="hidden" name="disponible" value="0">
                    <input type="checkbox" name="disponible" value="1" <?php echo !isset($_POST['disponible']) || $_POST['disponible'] === '1' ? 'checked' : ''; ?>>
                    Disponible para alquiler
                </label>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar departamento</button>
                    <a href="index.php" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
