<?php
session_start();
include 'config/conexion.php';
include 'includes/header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($u = $res->fetch_assoc()) {
        if (password_verify($pass, $u['password'])) {
            $_SESSION['usuario_id'] = $u['id'];
            $_SESSION['nombre'] = $u['nombre'];
            $_SESSION['rol'] = $u['rol'];
            header("Location: /athoca/index.php");
            exit;
        }
    }
    $msg = "<div class='mensaje mensaje-error'>❌ Email o contraseña incorrectos</div>";
}
?>

<h2 class="titulo-seccion">🔐 Iniciar Sesión</h2>
<?= $msg ?>

<form method="POST">
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Contraseña</label>
    <input type="password" name="password" required>
    <button type="submit">Ingresar</button>
</form>

<?php include 'includes/footer.php'; ?>