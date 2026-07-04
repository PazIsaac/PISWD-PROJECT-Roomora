<?php
require_once __DIR__ . '/db.php';

function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function usuarioLogueado(): ?array
{
    iniciarSesion();
    return $_SESSION['usuario'] ?? null;
}

function esAdmin(): bool
{
    $u = usuarioLogueado();
    return $u !== null && $u['rol'] === 'admin';
}

function requerirLogin(): void
{
    if (!usuarioLogueado()) {
        header('Location: login.php');
        exit;
    }
}

function requerirAdmin(): void
{
    requerirLogin();
    if (!esAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function login(string $email, string $password): ?array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = :email LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        unset($user['password_hash']);
        iniciarSesion();
        $_SESSION['usuario'] = $user;
        return $user;
    }
    return null;
}

function logout(): void
{
    iniciarSesion();
    $_SESSION = [];
    session_destroy();
}

function registrarUsuario(string $nombre, string $email, string $password, string $rol = 'cliente'): ?array
{
    $pdo = obtenerConexion();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (:nombre, :email, :password_hash, :rol)'
    );
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password_hash' => $hash,
        'rol' => $rol,
    ]);
    return [
        'id' => (int) $pdo->lastInsertId(),
        'nombre' => $nombre,
        'email' => $email,
        'rol' => $rol,
    ];
}
