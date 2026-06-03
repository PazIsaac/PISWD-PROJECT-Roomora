<?php
require_once __DIR__ . '/includes/alquileres.php';

$tituloPagina = 'Gestionar departamentos';
$paginaActiva = 'departamento';
$mensaje = '';
$error = '';
$editando = null;
$dbError = null;
$alquileres = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);

        if ($accion === 'eliminar' && $id > 0) {
            if (eliminarAlquiler($id)) {
                $mensaje = 'Departamento eliminado correctamente.';
            } else {
                $error = 'No se pudo eliminar el departamento.';
            }
        } elseif ($accion === 'actualizar' && $id > 0) {
            if (actualizarAlquiler($id, $_POST)) {
                $mensaje = 'Departamento actualizado.';
            } else {
                $error = 'No se pudo actualizar. Revisá los datos.';
            }
        }
    }

    if (isset($_GET['editar'])) {
        $editando = buscarPorId((int) $_GET['editar']);
    }

    $alquileres = cargarAlquileres(null, null, false);
} catch (PDOException $e) {
    $dbError = mensajeErrorDb();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <?php if ($mensaje): ?>
            <p class="msg-ok"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <?php if ($dbError): ?>
            <p class="msg-error"><?php echo htmlspecialchars($dbError); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($editando): ?>
            <div class="form-card">
                <h1>Modificar departamento #<?php echo (int) $editando['id']; ?></h1>
                <form method="post" action="departamento.php" class="abm-form">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?php echo (int) $editando['id']; ?>">
                    <label>
                        Tipo de ambiente
                        <select name="tipo" required>
                            <?php foreach (TIPOS_AMBIENTE as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo strcasecmp($editando['tipo'], $tipo) === 0 ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Precio mensual ($)
                        <input type="number" name="precio" min="0" step="0.01" required value="<?php echo htmlspecialchars((string) $editando['precio']); ?>">
                    </label>
                    <label>
                        Ambientes
                        <input type="number" name="ambientes" min="0" required value="<?php echo (int) $editando['ambientes']; ?>">
                    </label>
                    <label>
                        Metros cuadrados
                        <input type="number" name="metros_cuadrados" min="0" required value="<?php echo (int) $editando['metros_cuadrados']; ?>">
                    </label>
                    <label class="filter-check-label">
                        <input type="hidden" name="disponible" value="0">
                        <input type="checkbox" name="disponible" value="1" <?php echo $editando['disponible'] ? 'checked' : ''; ?>>
                        Disponible para alquiler
                    </label>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Guardar cambios</button>
                        <a href="departamento.php" class="btn-secondary">Volver al listado</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="form-card listado-abm">
                <h1>Modificar o eliminar departamentos</h1>
                <?php if (empty($alquileres)): ?>
                    <p class="empty-msg">No hay departamentos cargados. <a href="Registro.php">Registrar uno</a>.</p>
                <?php else: ?>
                    <ul class="abm-lista">
                        <?php foreach ($alquileres as $dep): ?>
                            <li class="abm-item">
                                <div class="abm-item-info">
                                    <strong><?php echo htmlspecialchars($dep['tipo']); ?> #<?php echo (int) $dep['id']; ?></strong>
                                    <span>
                                        <?php echo (int) $dep['ambientes']; ?> amb. ·
                                        <?php echo (int) $dep['metros_cuadrados']; ?> m² ·
                                        <?php echo etiquetaDisponible($dep['disponible']); ?> ·
                                        <?php echo formatearPrecio($dep['precio']); ?>
                                    </span>
                                </div>
                                <div class="abm-item-actions">
                                    <a href="departamento.php?editar=<?php echo (int) $dep['id']; ?>" class="btn-small">Editar</a>
                                    <form method="post" action="departamento.php" class="inline-form" onsubmit="return confirm('¿Eliminar este departamento?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo (int) $dep['id']; ?>">
                                        <button type="submit" class="btn-small btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
