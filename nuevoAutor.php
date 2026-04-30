<?php
require_once 'conexion.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre          = trim($_POST['nombre'] ?? '');
    $apellido        = trim($_POST['apellido'] ?? '');
    $genero          = $_POST['genero'] ?? '';
    $fechaNacimiento = $_POST['fechaNacimiento'] ?: null;
    $nacionalidad    = trim($_POST['nacionalidad'] ?? '');

    if (empty($nombre))   $errores[] = "El nombre es obligatorio.";
    if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
    if (empty($genero))   $errores[] = "El género es obligatorio.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("INSERT INTO autores (Nombre, Apellido, Genero, FechaNacimiento, Nacionalidad) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellido, $genero, $fechaNacimiento, $nacionalidad]);
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
    <title>Nuevo Autor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-principal">👤 Nuevo Autor</h1>
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
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="apellido" name="apellido"
                           value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="genero" class="form-label">Género <span class="text-danger">*</span></label>
                <select class="form-select" id="genero" name="genero" required>
                    <option value="">— Selecciona —</option>
                    <option value="Masculino" <?= (($_POST['genero'] ?? '') === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                    <option value="Femenino"  <?= (($_POST['genero'] ?? '') === 'Femenino')  ? 'selected' : '' ?>>Femenino</option>
                    <option value="Otro"      <?= (($_POST['genero'] ?? '') === 'Otro')      ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento"
                       value="<?= htmlspecialchars($_POST['fechaNacimiento'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="nacionalidad" class="form-label">Nacionalidad</label>
                <input type="text" class="form-control" id="nacionalidad" name="nacionalidad"
                       value="<?= htmlspecialchars($_POST['nacionalidad'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">💾 Guardar autor</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>