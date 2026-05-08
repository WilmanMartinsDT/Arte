<?php
require_once 'conexion.php';

$stmt = $pdo->query("
    SELECT a.AutorID, a.Nombre, a.Apellido, a.Nacionalidad, a.Genero,
           COUNT(o.ObraID) AS TotalObras
    FROM autores a
    LEFT JOIN obras o ON a.AutorID = o.AutorID
    GROUP BY a.AutorID, a.Nombre, a.Apellido, a.Nacionalidad, a.Genero
    ORDER BY a.Apellido, a.Nombre ASC
");
$autores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">👤 Autores</h1>
        <a href="nuevoAutor.php" class="btn btn-secondary">+ Nuevo Autor</a>
    </div>

    <?php if (empty($autores)): ?>
        <div class="alert alert-info">No hay autores registrados todavía.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Nacionalidad</th>
                <th>Género</th>
                <th class="text-center">Obras</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($autores as $autor): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($autor['Apellido'].', '.$autor['Nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($autor['Nacionalidad'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($autor['Genero'] ?? '—') ?></td>
                    <td class="text-center">
                        <span class="badge bg-secondary"><?= $autor['TotalObras'] ?></span>
                    </td>
                    <td class="text-end">
                        <a href="editarAutor.php?id=<?= $autor['AutorID'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted"><?= count($autores) ?> autor<?= count($autores) !== 1 ? 'es' : '' ?> registrado<?= count($autores) !== 1 ? 's' : '' ?></p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>