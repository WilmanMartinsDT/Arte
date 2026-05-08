<?php
require_once 'conexion.php';

$stmt = $pdo->query("
    SELECT m.MuseoID, m.Nombre, m.Ciudad, m.Pais,
           COUNT(om.ObraID) AS TotalObras
    FROM museos m
    LEFT JOIN obras_museos om ON m.MuseoID = om.MuseoID
    GROUP BY m.MuseoID, m.Nombre, m.Ciudad, m.Pais
    ORDER BY m.Nombre ASC
");
$museos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Museos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">🏛️ Museos</h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">← Obras</a>
            <a href="nuevoMuseo.php" class="btn btn-info">+ Nuevo Museo</a>
        </div>
    </div>

    <?php if (empty($museos)): ?>
        <div class="alert alert-info">No hay museos registrados todavía.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Ciudad</th>
                <th>País</th>
                <th class="text-center">Obras</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($museos as $museo): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($museo['Nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($museo['Ciudad'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($museo['Pais'] ?? '—') ?></td>
                    <td class="text-center">
                        <span class="badge bg-info text-dark"><?= $museo['TotalObras'] ?></span>
                    </td>
                    <td class="text-end">
                        <a href="editarMuseo.php?id=<?= $museo['MuseoID'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted"><?= count($museos) ?> museo<?= count($museos) !== 1 ? 's' : '' ?> registrado<?= count($museos) !== 1 ? 's' : '' ?></p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>