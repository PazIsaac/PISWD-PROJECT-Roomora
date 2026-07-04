<?php
require_once __DIR__ . '/includes/sesion.php';

$tituloPagina = 'Iniciar sesión';
$paginaActiva = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completá todos los campos.';
    } else {
        try {
            $usuario = login($email, $password);
            if ($usuario) {
                header('Location: index.php');
                exit;
            }
            $error = 'Email o contraseña incorrectos.';
        } catch (PDOException $e) {
            $error = 'Error de conexión. Verificá la base de datos.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout layout-single">
    <main class="content form-page">
        <div class="form-card">
            <h1>Iniciar sesión</h1>
            <?php if ($error): ?>
                <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="post" action="login.php" class="abm-form">
                <label>
                    Email
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </label>
                <label>
                    Contraseña
                    <input type="password" name="password" required>
                </label>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Ingresar</button>
                </div>
            </form>
            <p class="auth-alt">¿No tenés cuenta? <a href="register.php" class="auth-link">Registrarse</a></p>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<!-- a -->