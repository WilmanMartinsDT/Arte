<?php
require_once 'conexion.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $pais   = trim($_POST['pais'] ?? '');

    if (empty($nombre)) $errores[] = "El nombre del museo es obligatorio.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("INSERT INTO museos (Nombre, Ciudad, Pais) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $ciudad ?: null, $pais ?: null]);
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Museo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php require_once 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">🏛️ Nuevo Museo</h1>
        <a href="index.php" class="btn btn-secondary">← Volver</a>
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
                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad"
                           value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pais" class="form-label">País</label>
                    <input type="text" class="form-control" id="pais" name="pais"
                           value="<?= htmlspecialchars($_POST['pais'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">💾 Guardar museo</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>