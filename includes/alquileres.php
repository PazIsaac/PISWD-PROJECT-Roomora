<?php
// hola
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
