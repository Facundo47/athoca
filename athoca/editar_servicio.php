<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /athoca/login.php");
    exit;
}
include 'config/conexion.php';
include 'includes/header.php';

$msg = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar el servicio
$res = $conexion->query("SELECT * FROM servicios WHERE id = $id");
$servicio = $res->fetch_assoc();

// Verificar que el servicio exista y que seas el dueño (o admin)
if (!$servicio || ($servicio['usuario_id'] != $_SESSION['usuario_id'] && $_SESSION['rol'] !== 'admin')) {
    echo "<div class='mensaje mensaje-error'>❌ No tenés permiso para editar esto o no existe.</div>";
    include 'includes/footer.php';
    exit;
}

// GUARDAR CAMBIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $desc = trim($_POST['descripcion']);
    $precio = trim($_POST['precio']);
    $img = $servicio['imagen']; // Mantener la imagen actual por defecto

    // Si subió una nueva imagen, reemplazamos la vieja
    if (!empty($_FILES['imagen']['name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nueva_img = uniqid() . "." . $ext;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/" . $nueva_img)) {
            // Borrar la imagen vieja si existía
            if ($img && file_exists("uploads/" . $img)) {
                unlink("uploads/" . $img);
            }
            $img = $nueva_img;
        }
    }

    $stmt = $conexion->prepare("UPDATE servicios SET titulo=?, descripcion=?, precio=?, imagen=? WHERE id=?");
    $stmt->bind_param("ssssi", $titulo, $desc, $precio, $img, $id);
    
    if ($stmt->execute()) {
        $msg = "<div class='mensaje mensaje-ok'>✅ Servicio actualizado correctamente</div>";
        // Recargar datos para que se vea la nueva info en el formulario
        $res = $conexion->query("SELECT * FROM servicios WHERE id = $id");
        $servicio = $res->fetch_assoc();
    } else {
        $msg = "<div class='mensaje mensaje-error'>❌ Error al actualizar</div>";
    }
}
?>

<h2 class="titulo-seccion">✏️ Editar Servicio</h2>
<?= $msg ?>

<form method="POST" enctype="multipart/form-data">
    <label>Título *</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($servicio['titulo']) ?>" required>

    <label>Descripción</label>
    <textarea name="descripcion" rows="4"><?= htmlspecialchars($servicio['descripcion']) ?></textarea>

    <label>Precio</label>
    <input type="text" name="precio" value="<?= htmlspecialchars($servicio['precio']) ?>">

    <label>Imagen actual</label>
    <?php if ($servicio['imagen']): ?>
        <img src="/athoca/uploads/<?= $servicio['imagen'] ?>" style="width:150px; border-radius:8px; margin-bottom:10px; display:block;">
    <?php else: ?>
        <p><em>Sin imagen</em></p>
    <?php endif; ?>

    <label>Cambiar imagen (opcional)</label>
    <input type="file" name="imagen" accept="image/*">

    <button type="submit">💾 Guardar Cambios</button>
    <a href="/athoca/index.php" class="btn" style="display:block; text-align:center; margin-top:10px; background:#888;">Cancelar y volver</a>
</form>

<?php include 'includes/footer.php'; ?>