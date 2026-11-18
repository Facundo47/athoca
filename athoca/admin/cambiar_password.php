<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /athoca/login.php"); exit;
}
include '../config/conexion.php';
include '../includes/header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['usuario_id']);
    $nueva = $_POST['nueva_password'];

    if (strlen($nueva) < 4) {
        $msg = "<div class='mensaje mensaje-error'>❌ Mínimo 4 caracteres</div>";
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("UPDATE usuarios SET password=? WHERE id=?");
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();
        $msg = "<div class='mensaje mensaje-ok'>✅ Contraseña actualizada</div>";
    }
}

$usuarios = $conexion->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY nombre");
?>

<h2 class="titulo-seccion">🔑 Cambiar Contraseña de Usuarios</h2>
<?= $msg ?>

<form method="POST">
    <label>Usuario</label>
    <select name="usuario_id" required>
        <option value="">-- Elegir usuario --</option>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>">
                <?= htmlspecialchars($u['nombre']) ?> (<?= $u['email'] ?>) - <?= $u['rol'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Nueva contraseña</label>
    <input type="text" name="nueva_password" required minlength="4" 
           placeholder="Escribí la nueva contraseña">

    <button type="submit">🔑 Cambiar contraseña</button>
</form>

<?php include '../includes/footer.php'; ?>