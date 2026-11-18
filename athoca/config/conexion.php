<?php
$host = "localhost";
$usuario = "root";
$password = "";  // En XAMPP por defecto va vacío
$base_datos = "athoca_db";

$conexion = new mysqli($host, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>