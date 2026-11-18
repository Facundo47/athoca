<?php
include 'config/conexion.php';

// Datos del nuevo admin
$nombre = "Administrador ATHOCA";
$email = "admin@athoca.com";
$password = "admin123";  // ← la contraseña que querés
$rol = "admin";

// Convertir la contraseña en hash (como lo hace el registro)
$hash = password_hash($password, PASSWORD_DEFAULT);

// Borrar el admin viejo si existe
$conexion->query("DELETE FROM usuarios WHERE email = '$email'");

// Insertar el nuevo admin
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $hash, $rol);

if ($stmt->execute()) {
    echo "✅ Admin creado correctamente.<br>";
    echo "📧 Email: admin@athoca.com<br>";
    echo "🔒 Contraseña: admin123<br><br>";
    echo "<strong>⚠️ IMPORTANTE: Ahora borrá este archivo (crear_admin.php)</strong>";
} else {
    echo "❌ Error: " . $conexion->error;
}
?>