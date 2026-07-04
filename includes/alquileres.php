<?php
require_once __DIR__ . '/db.php';

define('TABLA_DEPARTAMENTOS', 'departamentos');

define('TIPOS_AMBIENTE', [
    'Monoambiente',
    'Duplex',
    'Departamento',
    'PH',
    'Loft',
    'Casa',
]);

function filaDepartamento(array $fila): array
{
    return [
        'id' => (int) $fila['id'],
        'tipo' => $fila['tipo'] ?? '',
        'precio' => (float) $fila['precio'],
        'ambientes' => (int) ($fila['ambientes'] ?? 0),
        'metros_cuadrados' => (int) ($fila['metros_cuadrados'] ?? 0),
        'disponible' => (int) ($fila['disponible'] ?? 0),
    ];
}

function cargarAlquileres(?string $busquedaTipo = null, ?string $tipo = null, ?bool $soloDisponibles = null): array
{
    $pdo = obtenerConexion();
    $sql = 'SELECT id, tipo, precio, ambientes, metros_cuadrados, disponible
            FROM ' . TABLA_DEPARTAMENTOS . ' WHERE 1=1';
    $params = [];

    $busquedaTipo = trim((string) $busquedaTipo);
    $tipo = trim((string) $tipo);

    if ($busquedaTipo !== '') {
        $sql .= ' AND tipo LIKE :busqueda';
        $params['busqueda'] = '%' . $busquedaTipo . '%';
    }
    if ($tipo !== '') {
        $sql .= ' AND tipo = :tipo';
        $params['tipo'] = $tipo;
    }
    if ($soloDisponibles === true) {
        $sql .= ' AND disponible = 1';
    }

    $sql .= ' ORDER BY id ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return array_map('filaDepartamento', $stmt->fetchAll());
}

function buscarPorId(int $id): ?array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT id, tipo, precio, ambientes, metros_cuadrados, disponible
         FROM ' . TABLA_DEPARTAMENTOS . ' WHERE id = :id LIMIT 1'
    );
    $stmt->execute(['id' => $id]);
    $fila = $stmt->fetch();

    return $fila ? filaDepartamento($fila) : null;
}

function normalizarDisponible($valor): int
{
    return !empty($valor) && $valor !== '0' ? 1 : 0;
}

function crearAlquiler(array $datos): ?array
{
    $tipo = trim($datos['tipo'] ?? '');
    if ($tipo === '') {
        return null;
    }

    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'INSERT INTO ' . TABLA_DEPARTAMENTOS . ' (tipo, precio, ambientes, metros_cuadrados, disponible)
         VALUES (:tipo, :precio, :ambientes, :metros_cuadrados, :disponible)'
    );
    $stmt->execute([
        'tipo' => $tipo,
        'precio' => (float) ($datos['precio'] ?? 0),
        'ambientes' => (int) ($datos['ambientes'] ?? 0),
        'metros_cuadrados' => (int) ($datos['metros_cuadrados'] ?? 0),
        'disponible' => normalizarDisponible($datos['disponible'] ?? 1),
    ]);

    return buscarPorId((int) $pdo->lastInsertId());
}

function actualizarAlquiler(int $id, array $datos): bool
{
    $existente = buscarPorId($id);
    if (!$existente) {
        return false;
    }

    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'UPDATE ' . TABLA_DEPARTAMENTOS . ' SET
            tipo = :tipo,
            precio = :precio,
            ambientes = :ambientes,
            metros_cuadrados = :metros_cuadrados,
            disponible = :disponible
         WHERE id = :id'
    );

    return $stmt->execute([
        'id' => $id,
        'tipo' => trim($datos['tipo'] ?? $existente['tipo']),
        'precio' => (float) ($datos['precio'] ?? $existente['precio']),
        'ambientes' => (int) ($datos['ambientes'] ?? $existente['ambientes']),
        'metros_cuadrados' => (int) ($datos['metros_cuadrados'] ?? $existente['metros_cuadrados']),
        'disponible' => normalizarDisponible($datos['disponible'] ?? $existente['disponible']),
    ]);
}

function eliminarAlquiler(int $id): bool
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('DELETE FROM ' . TABLA_DEPARTAMENTOS . ' WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}

function formatearPrecio(float $precio): string
{
    return '$' . number_format($precio, 0, ',', '.');
}

function etiquetaDisponible(int $disponible): string
{
    return $disponible ? 'Disponible' : 'No disponible';
}

function mensajeErrorDb(): string
{
    return 'No se pudo conectar a MySQL. Importá sql/departamentos.sql en phpMyAdmin y configurá includes/config.local.php (base: proyecto de callamullo).';
}

/* ──────────── Rentas (solicitudes de alquiler) ──────────── */

function solicitarRenta(int $departamentoId, int $usuarioId, string $fechaInicio, string $fechaFin): ?array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'INSERT INTO rentas (departamento_id, usuario_id, fecha_inicio, fecha_fin, estado)
         VALUES (:departamento_id, :usuario_id, :fecha_inicio, :fecha_fin, \'pendiente\')'
    );
    $stmt->execute([
        'departamento_id' => $departamentoId,
        'usuario_id' => $usuarioId,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFin,
    ]);
    return [
        'id' => (int) $pdo->lastInsertId(),
        'departamento_id' => $departamentoId,
        'usuario_id' => $usuarioId,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFin,
        'estado' => 'pendiente',
    ];
}

function rentasDeUsuario(int $usuarioId): array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT r.id, r.departamento_id, r.usuario_id, r.fecha_inicio, r.fecha_fin, r.estado, r.created_at,
                d.tipo, d.precio, d.ambientes, d.metros_cuadrados, d.disponible
         FROM rentas r
         JOIN departamentos d ON d.id = r.departamento_id
         WHERE r.usuario_id = :usuario_id
         ORDER BY r.created_at DESC'
    );
    $stmt->execute(['usuario_id' => $usuarioId]);
    $rows = $stmt->fetchAll();
    return array_map(function ($r) {
        $r['id'] = (int) $r['id'];
        $r['departamento_id'] = (int) $r['departamento_id'];
        $r['usuario_id'] = (int) $r['usuario_id'];
        $r['precio'] = (float) $r['precio'];
        $r['ambientes'] = (int) $r['ambientes'];
        $r['metros_cuadrados'] = (int) $r['metros_cuadrados'];
        $r['disponible'] = (int) $r['disponible'];
        return $r;
    }, $rows);
}

function todasLasRentas(): array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->query(
        'SELECT r.id, r.departamento_id, r.usuario_id, r.fecha_inicio, r.fecha_fin, r.estado, r.created_at,
                d.tipo, d.precio, d.ambientes, d.metros_cuadrados, d.disponible,
                u.nombre AS usuario_nombre, u.email AS usuario_email
         FROM rentas r
         JOIN departamentos d ON d.id = r.departamento_id
         JOIN usuarios u ON u.id = r.usuario_id
         ORDER BY r.created_at DESC'
    );
    $rows = $stmt->fetchAll();
    return array_map(function ($r) {
        $r['id'] = (int) $r['id'];
        $r['departamento_id'] = (int) $r['departamento_id'];
        $r['usuario_id'] = (int) $r['usuario_id'];
        $r['precio'] = (float) $r['precio'];
        $r['ambientes'] = (int) $r['ambientes'];
        $r['metros_cuadrados'] = (int) $r['metros_cuadrados'];
        $r['disponible'] = (int) $r['disponible'];
        return $r;
    }, $rows);
}

function actualizarEstadoRenta(int $rentaId, string $estado): bool
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('UPDATE rentas SET estado = :estado WHERE id = :id');
    $stmt->execute(['estado' => $estado, 'id' => $rentaId]);
    return $stmt->rowCount() > 0;
}

function buscarRentaPorId(int $rentaId): ?array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT r.id, r.departamento_id, r.usuario_id, r.fecha_inicio, r.fecha_fin, r.estado, r.created_at,
                d.tipo, d.precio, d.ambientes, d.metros_cuadrados, d.disponible,
                u.nombre AS usuario_nombre, u.email AS usuario_email
         FROM rentas r
         JOIN departamentos d ON d.id = r.departamento_id
         JOIN usuarios u ON u.id = r.usuario_id
         WHERE r.id = :id LIMIT 1'
    );
    $stmt->execute(['id' => $rentaId]);
    $r = $stmt->fetch();
    if (!$r) return null;
    $r['id'] = (int) $r['id'];
    $r['departamento_id'] = (int) $r['departamento_id'];
    $r['usuario_id'] = (int) $r['usuario_id'];
    $r['precio'] = (float) $r['precio'];
    $r['ambientes'] = (int) $r['ambientes'];
    $r['metros_cuadrados'] = (int) $r['metros_cuadrados'];
    $r['disponible'] = (int) $r['disponible'];
    return $r;
}

function etiquetaEstadoRenta(string $estado): string
{
    return match ($estado) {
        'pendiente' => '<span class="status-badge status-pendiente">Pendiente</span>',
        'aprobado' => '<span class="status-badge status-aprobado">Aprobado</span>',
        'rechazado' => '<span class="status-badge status-rechazado">Rechazado</span>',
        'cancelado' => '<span class="status-badge status-cancelado">Cancelado</span>',
        default => htmlspecialchars($estado),
    };
}

/* ──────────── Chat / Mensajes ──────────── */

function enviarMensaje(int $rentaId, int $usuarioId, string $mensaje): ?array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'INSERT INTO mensajes (renta_id, usuario_id, mensaje) VALUES (:renta_id, :usuario_id, :mensaje)'
    );
    $stmt->execute([
        'renta_id' => $rentaId,
        'usuario_id' => $usuarioId,
        'mensaje' => $mensaje,
    ]);
    return [
        'id' => (int) $pdo->lastInsertId(),
        'renta_id' => $rentaId,
        'usuario_id' => $usuarioId,
        'mensaje' => $mensaje,
    ];
}

function mensajesDeRenta(int $rentaId): array
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT m.id, m.renta_id, m.usuario_id, m.mensaje, m.created_at,
                u.nombre AS usuario_nombre, u.rol AS usuario_rol
         FROM mensajes m
         JOIN usuarios u ON u.id = m.usuario_id
         WHERE m.renta_id = :renta_id
         ORDER BY m.created_at ASC'
    );
    $stmt->execute(['renta_id' => $rentaId]);
    $rows = $stmt->fetchAll();
    return array_map(function ($m) {
        $m['id'] = (int) $m['id'];
        $m['renta_id'] = (int) $m['renta_id'];
        $m['usuario_id'] = (int) $m['usuario_id'];
        return $m;
    }, $rows);
}
