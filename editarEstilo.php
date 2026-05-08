<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$errores = [];

$stmt = $pdo->prepare("SELECT * FROM estilos WHERE EstiloID = ?");
$stmt->execute([$id]);
$estilo = $stmt->fetch();
if (!$estilo) { header("Location: index.php"); exit; }

if (isset($_POST['borrar'])) {
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM obras WHERE EstiloID = ?");
    $stmtCheck->execute([$id]);
    if ($stmtCheck->fetchColumn() > 0) {
        $errores[] = "No se puede borrar: tiene obras asociadas.";
    } else {
        $pdo->prepare("DELETE FROM estilos WHERE EstiloID = ?")->execute([$id]);
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['borrar'])) {
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (empty($nombre)) $errores[] = "El nombre del estilo es obligatorio.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("UPDATE estilos SET Nombre=?, Descripcion=? WHERE EstiloID=?");
        $stmt->execute([$nombre, $descripcion ?: null, $id]);
        header("Location: index.php");
        exit;
    }
    $estilo['Nombre'] = $nombre;
    $estilo['Descripcion'] = $descripcion;
}

$stmtObras = $pdo->prepare("
    SELECT o.ObraID, o.Titulo, o.AnioCreacion,
           CONCAT(a.Nombre, ' ', a.Apellido) AS Autor
    FROM obras o
    JOIN autores a ON o.AutorID = a.AutorID
    WHERE o.EstiloID = ?
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
    <title>Editar Estilo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">✏️ Editar Estilo</h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">← Volver</a>
            <form method="POST" class="d-inline"
                  onsubmit="return confirm('¿Seguro que quieres borrar este estilo?')">
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
                       value="<?= htmlspecialchars($estilo['Nombre']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($estilo['Descripcion'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
        </form>
    </div>

    <h2 class="mb-3">Obras con este estilo</h2>
    <?php if (empty($obras)): ?>
        <div class="alert alert-info">No hay obras con este estilo.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr><th>Título</th><th>Autor</th><th>Año</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($obras as $obra): ?>
                <tr>
                    <td><?= htmlspecialchars($obra['Titulo']) ?></td>
                    <td><?= htmlspecialchars($obra['Autor']) ?></td>
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