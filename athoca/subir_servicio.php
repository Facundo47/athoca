<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /athoca/login.php");
    exit;
}
include 'config/conexion.php';
include 'includes/header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $desc = trim($_POST['descripcion']);
    $precio = trim($_POST['precio']);
    $uid = $_SESSION['usuario_id'];
    $img = null;

    if (!empty($_FILES['imagen']['name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $img = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/" . $img);
    }

    $stmt = $conexion->prepare("INSERT INTO servicios (usuario_id,titulo,descripcion,precio,imagen) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss", $uid, $titulo, $desc, $precio, $img);

    if ($stmt->execute()) {
        $msg = "<div class='mensaje mensaje-ok'>✅ Servicio publicado</div>";
    } else {
        $msg = "<div class='mensaje mensaje-error'>❌ Error al publicar</div>";
    }
}
?>

<h2 class="titulo-seccion">📤 Subir Servicio</h2>
<?= $msg ?>

<form method="POST" enctype="multipart/form-data">
    <label>Título *</label>
    <input type="text" name="titulo" required>

    <label>Descripción</label>
    <textarea name="descripcion" rows="4"></textarea>

    <label>Precio</label>
    <input type="text" name="precio" placeholder="$5000">

    <label>Imagen</label>
    <input type="file" name="imagen" accept="image/*">

    <button type="submit">Publicar</button>
</form>

<?php include 'includes/footer.php'; ?>