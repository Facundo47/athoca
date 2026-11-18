<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /athoca/login.php"); exit;
}
include '../config/conexion.php';
include '../includes/header.php';

$msg = "";

// ELIMINAR USUARIO
if (isset($_POST['eliminar'])) {
    $id = intval($_POST['id']);
    if ($id == $_SESSION['usuario_id']) {
        $msg = "<div class='mensaje mensaje-error'>❌ No podés eliminarte a vos mismo</div>";
    } else {
        // Borrar también sus servicios (ON DELETE CASCADE ya lo hace, pero por seguridad)
        $conexion->query("DELETE FROM usuarios WHERE id = $id");
        $msg = "<div class='mensaje mensaje-ok'>🗑️ Usuario eliminado</div>";
    }
}

$res = $conexion->query("SELECT * FROM usuarios ORDER BY id DESC");
?>

<h2 class="titulo-seccion">👥 Usuarios registrados</h2>
<?= $msg ?>

<table style="width:100%;background:white;border-collapse:collapse;border-radius:8px;overflow:hidden;">
    <tr style="background:#1b7a3e;color:white;">
        <th style="padding:12px;">ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>WhatsApp</th>
        <th>Acción</th>
    </tr>
    <?php while ($u = $res->fetch_assoc()): ?>
    <tr style="border-bottom:1px solid #eee;">
        <td style="padding:10px;"><?= $u['id'] ?></td>
        <td style="padding:10px;"><?= htmlspecialchars($u['nombre']) ?></td>
        <td style="padding:10px;"><?= htmlspecialchars($u['email']) ?></td>
        <td style="padding:10px;">
            <span style="background:<?= $u['rol']==='admin' ? '#1b7a3e' : '#2ea050' ?>;color:white;padding:3px 8px;border-radius:4px;font-size:12px;">
                <?= $u['rol'] ?>
            </span>
        </td>
        <td style="padding:10px;"><?= htmlspecialchars($u['whatsapp']) ?></td>
        <td style="padding:10px;">
            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                <form method="POST" style="padding:0;box-shadow:none;margin:0;"
                      onsubmit="return confirm('¿Seguro que querés eliminar este usuario? Se borrarán también sus servicios.');">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" name="eliminar"
                            style="background:#c0392b;width:auto;padding:6px 12px;font-size:13px;">
                        🗑️ Eliminar
                    </button>
                </form>
            <?php else: ?>
                <em style="color:#888;">Vos</em>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>