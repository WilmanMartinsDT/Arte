<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg mb-4" style="background-color:#2c2c2c;">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="index.php">🎨 Galería de Arte</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon" style="filter:invert(1)"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item">
                    <a href="index.php"
                       class="btn btn-sm <?= $paginaActual === 'index.php' ? 'btn-light' : 'btn-outline-light' ?>">
                        🖼️ Obras
                    </a>
                </li>
                <li class="nav-item">
                    <a href="estilos.php"
                       class="btn btn-sm <?= $paginaActual === 'estilos.php' ? 'btn-light' : 'btn-outline-light' ?>">
                        🎨 Estilos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="museos.php"
                       class="btn btn-sm <?= $paginaActual === 'museos.php' ? 'btn-light' : 'btn-outline-light' ?>">
                        🏛️ Museos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="nuevoAutor.php"
                       class="btn btn-sm <?= $paginaActual === 'nuevoAutor.php' ? 'btn-light' : 'btn-outline-light' ?>">
                        👤 Autores
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>