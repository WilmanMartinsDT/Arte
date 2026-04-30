<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$errores = [];

$stmt = $pdo->prepare("SELECT * FROM autores WHERE AutorID = ?");
$stmt->execute([$id]);
$autor = $stmt->fetch();
if (!$autor) { header("Location: index.php"); exit; }

if (isset($_POST['borrar'])) {
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM obras WHERE AutorID = ?");
    $stmtCheck->execute([$id]);
    if ($stmtCheck->fetchColumn() > 0) {
        $errores[] = "No se puede borrar: este autor tiene obras asociadas. Bórralas primero.";
    } else {
        $pdo->prepare("DELETE FROM autores WHERE AutorID = ?")->execute([$id]);
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['borrar'])) {
    $nombre          = trim($_POST['nombre'] ?? '');
    $apellido        = trim($_POST['apellido'] ?? '');
    $genero          = $_POST['genero'] ?? '';
    $fechaNacimiento = $_POST['fechaNacimiento'] ?: null;
    $nacionalidad    = trim($_POST['nacionalidad'] ?? '');

    if (empty($nombre))   $errores[] = "El nombre es obligatorio.";
    if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
    if (empty($genero))   $errores[] = "El género es obligatorio.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("UPDATE autores SET Nombre=?, Apellido=?, Genero=?, FechaNacimiento=?, Nacionalidad=? WHERE AutorID=?");
        $stmt->execute([$nombre, $apellido, $genero, $fechaNacimiento, $nacionalidad, $id]);
        header("Location: index.php");
        exit;
    }

    $autor['Nombre']          = $nombre;
    $autor['Apellido']        = $apellido;
    $autor['Genero']          = $genero;
    $autor['FechaNacimiento'] = $fechaNacimiento;
    $autor['Nacionalidad']    = $nacionalidad;
}

$stmtObras = $pdo->prepare("
    SELECT o.ObraID, o.Titulo, o.AnioCreacion, e.Nombre AS Estilo
    FROM obras o
    LEFT JOIN estilos e ON o.EstiloID = e.EstiloID
    WHERE o.AutorID = ?
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
    <title>Editar Autor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">✏️ Editar Autor</h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">← Volver</a>
            <form method="POST" class="d-inline"
                  onsubmit="return confirm('¿Seguro que quieres borrar este autor?')">
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
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?= htmlspecialchars($autor['Nombre']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="apellido" name="apellido"
                           value="<?= htmlspecialchars($autor['Apellido']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="genero" class="form-label">Género <span class="text-danger">*</span></label>
                <select class="form-select" id="genero" name="genero" required>
                    <option value="">— Selecciona —</option>
                    <option value="Masculino" <?= ($autor['Genero'] === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                    <option value="Femenino"  <?= ($autor['Genero'] === 'Femenino')  ? 'selected' : '' ?>>Femenino</option>
                    <option value="Otro"      <?= ($autor['Genero'] === 'Otro')      ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento"
                       value="<?= htmlspecialchars($autor['FechaNacimiento'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="nacionalidad" class="form-label">Nacionalidad</label>
                <input type="text" class="form-control" id="nacionalidad" name="nacionalidad"
                       value="<?= htmlspecialchars($autor['Nacionalidad'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
        </form>
    </div>

    <h2 class="mb-3">Obras de <?= htmlspecialchars($autor['Nombre'] . ' ' . $autor['Apellido']) ?></h2>
    <?php if (empty($obras)): ?>
        <div class="alert alert-info">Este autor no tiene obras registradas.</div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Título</th>
                <th>Estilo</th>
                <th>Año</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($obras as $obra): ?>
                <tr>
                    <td><?= htmlspecialchars($obra['Titulo']) ?></td>
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