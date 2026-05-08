<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$errores = [];
$autores = $pdo->query("SELECT AutorID, CONCAT(Nombre, ' ', Apellido) AS NombreCompleto FROM autores ORDER BY Apellido, Nombre")->fetchAll();
$estilos = $pdo->query("SELECT EstiloID, Nombre FROM estilos ORDER BY Nombre")->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM obras WHERE ObraID = ?");
$stmt->execute([$id]);
$obra = $stmt->fetch();
if (!$obra) { header("Location: index.php"); exit; }

if (isset($_POST['borrar'])) {
    $pdo->prepare("DELETE FROM obras_museos WHERE ObraID = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM obras WHERE ObraID = ?")->execute([$id]);
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $autorId     = $_POST['autorId'] ?? '';
    $estiloId    = $_POST['estiloId'] ?: null;
    $anio        = $_POST['anio'] ?: null;
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (empty($titulo))  $errores[] = "El título es obligatorio.";
    if (empty($autorId)) $errores[] = "El autor es obligatorio.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("UPDATE obras SET AutorID=?, EstiloID=?, Titulo=?, AnioCreacion=?, Descripcion=? WHERE ObraID=?");
        $stmt->execute([$autorId, $estiloId, $titulo, $anio, $descripcion, $id]);
        header("Location: index.php");
        exit;
    }

    $obra['Titulo']       = $titulo;
    $obra['AutorID']      = $autorId;
    $obra['EstiloID']     = $estiloId;
    $obra['AnioCreacion'] = $anio;
    $obra['Descripcion']  = $descripcion;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Obra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">✏️ Editar Obra</h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">← Volver</a>
            <form method="POST" class="d-inline"
                  onsubmit="return confirm('¿Seguro que quieres borrar esta obra?')">
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

    <div class="card p-4">
        <form method="POST">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="titulo" name="titulo"
                       value="<?= htmlspecialchars($obra['Titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="autorId" class="form-label">Autor <span class="text-danger">*</span></label>
                <div class="input-group">
                    <select class="form-select" id="autorId" name="autorId" required>
                        <option value="">— Selecciona un autor —</option>
                        <?php foreach ($autores as $autor): ?>
                            <option value="<?= $autor['AutorID'] ?>"
                                <?= ($obra['AutorID'] == $autor['AutorID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($autor['NombreCompleto']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="nuevoAutor.php" class="btn btn-outline-secondary">+ Nuevo autor</a>
                </div>
            </div>

            <div class="mb-3">
                <label for="estiloId" class="form-label">Estilo</label>
                <select class="form-select" id="estiloId" name="estiloId">
                    <option value="">— Sin estilo —</option>
                    <?php foreach ($estilos as $estilo): ?>
                        <option value="<?= $estilo['EstiloID'] ?>"
                            <?= ($obra['EstiloID'] == $estilo['EstiloID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($estilo['Nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="anio" class="form-label">Año de creación</label>
                <input type="number" class="form-control" id="anio" name="anio"
                       min="1" max="2025" value="<?= htmlspecialchars($obra['AnioCreacion'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($obra['Descripcion'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>