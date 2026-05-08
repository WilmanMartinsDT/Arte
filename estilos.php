<?php
require_once 'conexion.php';

$stmt = $pdo->query("
    SELECT e.EstiloID, e.Nombre, e.Descripcion,
           COUNT(o.ObraID) AS TotalObras
    FROM estilos e
    LEFT JOIN obras o ON e.EstiloID = o.EstiloID
    GROUP BY e.EstiloID, e.Nombre, e.Descripcion
    ORDER BY e.Nombre ASC
");
$estilos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estilos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">🖼️ Estilos artísticos</h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">← Obras</a>
            <a href="nuevoEstilo.php" class="btn btn-success">+ Nuevo Estilo</a>
        </div>
    </div>

    <?php if (empty($estilos)): ?>
        <div class="alert alert-info">No hay estilos registrados todavía.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th class="text-center">Obras</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($estilos as $estilo): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($estilo['Nombre']) ?></strong></td>
                    <td class="text-muted"><?= htmlspecialchars($estilo['Descripcion'] ?? '—') ?></td>
                    <td class="text-center">
                        <span class="badge bg-primary"><?= $estilo['TotalObras'] ?></span>
                    </td>
                    <td class="text-end">
                        <a href="editarEstilo.php?id=<?= $estilo['EstiloID'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted"><?= count($estilos) ?> estilo<?= count($estilos) !== 1 ? 's' : '' ?> registrado<?= count($estilos) !== 1 ? 's' : '' ?></p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>