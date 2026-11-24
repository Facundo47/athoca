
<?php

$host = getenv("DB_HOST");
$usuario = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$base_datos = getenv("DB_NAME");
$puerto = 3306;

$conexion = new mysqli(
    $host,
    $usuario,
    $password,
    $base_datos,
    $puerto
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

?>
