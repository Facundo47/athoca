<?php
include 'config/conexion.php';
include 'includes/header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telefono = trim($_POST['telefono']);
    $whatsapp = trim($_POST['whatsapp']);
    $facebook = trim($_POST['facebook']);
    $instagram = trim($_POST['instagram']);

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre,email,password,telefono,whatsapp,facebook,instagram) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss", $nombre,$email,$pass,$telefono,$whatsapp,$facebook,$instagram);

    if ($stmt->execute()) {
        $msg = "<div class='mensaje mensaje-ok'>✅ Registrado. Ya podés iniciar sesión.</div>";
    } else {
        $msg = "<div class='mensaje mensaje-error'>❌ Error: email ya registrado.</div>";
    }
}
?>

<h2 class="titulo-seccion">📝 Registro de Vendedores</h2>
<?= $msg ?>

<form method="POST">
    <label>Nombre completo *</label>
    <input type="text" name="nombre" required>

    <label>Email *</label>
    <input type="email" name="email" required>

    <label>Contraseña *</label>
    <input type="password" name="password" required>

    <label>Teléfono</label>
    <input type="text" name="telefono">

    <label>WhatsApp (con código país, ej: 3834123456)</label>
    <input type="text" name="whatsapp">

    <label>Link de Facebook</label>
    <input type="url" name="facebook" placeholder="https://facebook.com/tuperfil">

    <label>Link de Instagram</label>
    <input type="url" name="instagram" placeholder="https://instagram.com/tuperfil">

    <button type="submit">Registrarme</button>
</form>

<?php include 'includes/footer.php'; ?>