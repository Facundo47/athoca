<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ATHOCA - Servicios</title>
    <link rel="stylesheet" href="/athoca/css/estilo.css">
</head>
<body>
<header>
    <h1>🌿 ATHOCA</h1>
    <nav>
        <a href="/athoca/index.php">Inicio</a>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="/athoca/subir_servicio.php">Subir Servicio</a>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <a href="/athoca/admin/panel.php">Panel Admin</a>
            <?php endif; ?>
            <a href="/athoca/logout.php">Salir (<?= $_SESSION['nombre'] ?>)</a>
        <?php else: ?>
            <a href="/athoca/login.php">Ingresar</a>
            <a href="/athoca/registro.php">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>
<div class="contenedor">