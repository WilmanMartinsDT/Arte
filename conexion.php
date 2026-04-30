<?php
$servidor   = "localhost";
$usuario    = "root";
$contrasena = "";           // En XAMPP local siempre está vacío
$base_datos = "arte_db";

$conexion = mysqli_connect($servidor, $usuario, $contrasena, $base_datos);

if (!$conexion) {
    die("❌ Error de conexión: " . mysqli_connect_error());
}
// Si llega aquí, ¡todo bien! No mostramos nada.
?>