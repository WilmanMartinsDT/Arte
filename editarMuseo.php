<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$errores = [];

$stmt = $pdo->prepare("SELECT * FROM museos WHERE MuseoID = ?");
$stmt->execute([$id]);
$museo = $stmt->fetch();
if (!$museo) { header("Location: index.php"); exit; }

if (isset($_POST['borrar'])) {
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM obras_museos WHERE MuseoID = ?");
    $stmtCheck->execute([$id]);
    if ($stmtCheck->fetchColumn() > 0) {
        $errores[] = "No se puede borrar: tiene obras asociadas.";
    } else {
        $pdo->prepare("DELETE FROM museos WHERE MuseoID = ?")->execute([$id]);
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['borrar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $pais   = trim($_POST['pais'] ?? '');

    if (empty($nombre)) $errores[] = "El nombre del museo es obligatorio.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("UPDATE museos SET Nombre=?, Ciudad=?, Pais=? WHERE MuseoID=?");
        $stmt->execute([$nombre, $ciudad ?: null, $pais ?: null, $id]);
        header("Location: index.php");
        exit;
    }
    $museo['Nombre'] = $nombre;
    $museo['Ciudad'] = $ciudad;
    $museo['Pais']   = $pais;
}

$stmtObras = $pdo->prepare("
    SELECT o.ObraID, o.Titulo, o.AnioCreacion,
           CONCAT(a.Nombre, ' ', a.Apellido) AS Autor,
           om.FechaEntrada, om.FechaSalida
    FROM obras_museos om
    JOIN obras o ON om.ObraID = o.ObraID
    JOIN autores a ON o.AutorID = a.AutorID
    WHERE om.MuseoID = ?
    ORDER BY o.Titulo ASC
");
$stmtObras->execute([$id]);
$obras = $stmtObras->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Museo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">✏️ Editar Museo</h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">← Volver</a>
            <form method="POST" class="d-inline"
                  onsubmit="return confirm('¿Seguro que quieres borrar este museo?')">
                <button type="submit" name="borrar" class="btn btn-danger">🗑️ Borrar</button>
            </form>
        </div>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errores as $e): ?>
                <p class="mb-0"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card p-4 mb-4">
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($museo['Nombre']) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad"
                           value="<?= htmlspecialchars($museo['Ciudad'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pais" class="form-label">País</label>
                    <input type="text" class="form-control" id="pais" name="pais"
                           value="<?= htmlspecialchars($museo['Pais'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
        </form>
    </div>

    <h2 class="mb-3">Obras en este museo</h2>
    <?php if (empty($obras)): ?>
        <div class="alert alert-info">Este museo no tiene obras registradas.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Título</th><th>Autor</th><th>Año</th>
                <th>Fecha entrada</th><th>Fecha salida</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($obras as $obra): ?>
                <tr>
                    <td><?= htmlspecialchars($obra['Titulo']) ?></td>
                    <td><?= htmlspecialchars($obra['Autor']) ?></td>
                    <td><?= htmlspecialchars($obra['AnioCreacion'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($obra['FechaEntrada'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($obra['FechaSalida'] ?? '—') ?></td>
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