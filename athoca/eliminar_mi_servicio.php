<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /athoca/login.php"); 
    exit;
}
include 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $uid = $_SESSION['usuario_id'];

    // Verificar que el servicio le pertenece al usuario
    $res = $conexion->query("SELECT imagen FROM servicios WHERE id = $id AND usuario_id = $uid");
    if ($row = $res->fetch_assoc()) {
        // Borrar imagen
        if ($row['imagen'] && file_exists("uploads/" . $row['imagen'])) {
            unlink("uploads/" . $row['imagen']);
        }
        // Borrar servicio
        $conexion->query("DELETE FROM servicios WHERE id = $id AND usuario_id = $uid");
    }
}

header("Location: /athoca/index.php");
exit;
?>