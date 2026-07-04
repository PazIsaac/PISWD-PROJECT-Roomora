<?php
require_once __DIR__ . '/includes/sesion.php';

$tituloPagina = 'Registrarse';
$paginaActiva = 'login';
$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nombre === '' || $email === '' || $password === '') {
        $error = 'Completá todos los campos.';
    } elseif ($password !== $password2) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            $usuario = registrarUsuario($nombre, $email, $password, 'cliente');
            if ($usuario) {
                $exito = 'Cuenta creada. Ya podés <a href="login.php">iniciar sesión</a>.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Ya existe una cuenta con ese email.';
            } else {
                $error = 'Error al registrar. Verificá la base de datos.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Crear cuenta</h1>
            <?php if ($error): ?>
                <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($exito): ?>
                <p class="msg-ok"><?php echo $exito; ?></p>
            <?php else: ?>
                <form method="post" action="register.php" class="abm-form">
                    <label>
                        Nombre
                        <input type="text" name="nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </label>
                    <label>
                        Contraseña
                        <input type="password" name="password" required minlength="6">
                    </label>
                    <label>
                        Repetir contraseña
                        <input type="password" name="password2" required minlength="6">
                    </label>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Registrarse</button>
                    </div>
                </form>
                <p class="auth-alt">¿Ya tenés cuenta? <a href="login.php" class="auth-link">Iniciar sesión</a></p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
