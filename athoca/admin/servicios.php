<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /athoca/login.php"); exit;
}
include '../config/conexion.php';
include '../includes/header.php';

$msg = "";

// ELIMINAR SERVICIO
if (isset($_POST['eliminar'])) {
    $id = intval($_POST['id']);

    // Primero borrar la imagen del servidor (si existe)
    $res = $conexion->query("SELECT imagen FROM servicios WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['imagen'] && file_exists("../uploads/" . $row['imagen'])) {
            unlink("../uploads/" . $row['imagen']);
        }
    }

    // Ahora borrar de la base de datos
    $conexion->query("DELETE FROM servicios WHERE id = $id");
    $msg = "<div class='mensaje mensaje-ok'>🗑️ Servicio eliminado correctamente</div>";
}

$res = $conexion->query("
    SELECT s.*, u.nombre 
    FROM servicios s 
    JOIN usuarios u ON s.usuario_id = u.id 
    ORDER BY s.id DESC
");
?>

<h2 class="titulo-seccion">🛍️ Servicios publicados</h2>
<?= $msg ?>

<?php if ($res->num_rows === 0): ?>
    <p>No hay servicios publicados todavía.</p>
<?php else: ?>
    <div class="grid">
    <?php while ($s = $res->fetch_assoc()): ?>
        <div class="card">
            <?php if ($s['imagen']): ?>
                <img src="/athoca/uploads/<?= $s['imagen'] ?>" alt="">
            <?php endif; ?>
            <div class="card-body">
                <h3><?= htmlspecialchars($s['titulo']) ?></h3>
                <p><?= htmlspecialchars($s['descripcion']) ?></p>
                <p><strong>💵 Precio:</strong> <?= htmlspecialchars($s['precio']) ?></p>
                <p><strong>👤 Vendedor:</strong> <?= htmlspecialchars($s['nombre']) ?></p>

                                <a href="/athoca/editar_servicio.php?id=<?= $s['id'] ?>" 
                   class="btn" style="background:#f39c12; margin-bottom:5px; display:inline-block;">
                    ✏️ Editar
                </a>

                <form method="POST" style="padding:0;box-shadow:none;margin:0;"
                      onsubmit="return confirm('¿Seguro que querés eliminar este servicio?');">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button type="submit" name="eliminar" 
                            style="background:#c0392b;width:auto;padding:8px 15px;">
                        🗑️ Eliminar
                    </button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>