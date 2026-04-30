<?php
require_once 'conexion.php';

$stmt = $pdo->query("
    SELECT o.ObraID, o.Titulo, o.AnioCreacion,
           CONCAT(a.Nombre, ' ', a.Apellido) AS Autor,
           e.Nombre AS Estilo
    FROM obras o
    JOIN autores a ON o.AutorID = a.AutorID
    LEFT JOIN estilos e ON o.EstiloID = e.EstiloID
    ORDER BY o.Titulo ASC
");
$obras = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galería de Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">🎨 Galería de Arte</h1>
        <div>
            <a href="nuevaObra.php" class="btn btn-primary me-2">+ Nueva Obra</a>
            <a href="nuevoAutor.php" class="btn btn-secondary">+ Nuevo Autor</a>
        </div>
    </div>

    <?php if (empty($obras)): ?>
        <div class="alert alert-info">No hay obras registradas todavía.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Estilo</th>
                <th>Año</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($obras as $obra): ?>
                <tr>
                    <td><?= htmlspecialchars($obra['Titulo']) ?></td>
                    <td><?= htmlspecialchars($obra['Autor']) ?></td>
                    <td><?= htmlspecialchars($obra['Estilo'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($obra['AnioCreacion'] ?? '—') ?></td>
                    <td class="text-end">
                        <a href="editarObra.php?id=<?= $obra['ObraID'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>