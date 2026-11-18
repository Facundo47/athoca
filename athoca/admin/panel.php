<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /athoca/login.php");
    exit;
}
include '../includes/header.php';
?>

<h2 class="titulo-seccion">⚙️ Panel de Administrador</h2>

<div class="grid">
    <div class="card"><div class="card-body">
        <h3>👥 Usuarios</h3>
        <p>Ver, editar o eliminar usuarios registrados</p>
        <a class="btn" href="usuarios.php">Administrar</a>
    </div></div>

    <div class="card"><div class="card-body">
        <h3>🛍️ Servicios</h3>
        <p>Gestionar todos los servicios publicados</p>
        <a class="btn" href="servicios.php">Administrar</a>
    </div></div>

    <div class="card"><div class="card-body">
        <h3>📢 Eventos</h3>
        <p>Subir capacitaciones y reuniones</p>
        <a class="btn" href="eventos.php">Administrar</a>
    </div></div>
</div>
<div class="card"><div class="card-body">
    <h3>🔑 Contraseñas</h3>
    <p>Cambiar la contraseña de cualquier usuario</p>
    <a class="btn" href="cambiar_password.php">Administrar</a>
</div></div>

<?php include '../includes/footer.php'; ?>