<?php
require_once 'conexion.php';

$buscar = trim($_GET['buscar'] ?? '');
$filtroEstilo = $_GET['estilo'] ?? '';

$estilos = $pdo->query("SELECT EstiloID, Nombre FROM estilos ORDER BY Nombre")->fetchAll();

$sql = "SELECT * FROM vista_obras_completa WHERE 1=1";
$params = [];

if (!empty($buscar)) {
    $sql .= " AND (Titulo LIKE ? OR Autor LIKE ?)";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

if (!empty($filtroEstilo)) {
    $sql .= " AND Estilo = ?";
    $params[] = $filtroEstilo;
}

$sql .= " ORDER BY Titulo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    
    <!-- Buscador y filtro -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="buscar" class="form-control"
                   placeholder="🔍 Buscar por título o autor..."
                   value="<?= htmlspecialchars($buscar) ?>">
        </div>
        <div class="col-md-4">
            <select name="estilo" class="form-select">
                <option value="">— Todos los estilos —</option>
                <?php foreach ($estilos as $e): ?>
                    <option value="<?= htmlspecialchars($e['Nombre']) ?>"
                            <?= ($filtroEstilo === $e['Nombre']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <?php if (!empty($buscar) || !empty($filtroEstilo)): ?>
            <div class="col-md-1">
                <a href="index.php" class="btn btn-outline-secondary w-100">✕</a>
            </div>
        <?php endif; ?>
    </form>

    <!-- Tabla de obras -->
    <?php if (empty($obras)): ?>
        <div class="alert alert-info">No se encontraron obras.</div>
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
        <p class="text-muted">
            <?= count($obras) ?> obra<?= count($obras) !== 1 ? 's' : '' ?> encontrada<?= count($obras) !== 1 ? 's' : '' ?>
        </p>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>