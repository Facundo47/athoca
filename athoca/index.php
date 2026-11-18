<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/conexion.php';
include 'includes/header.php';

// Traer eventos próximos
$eventos = $conexion->query("SELECT * FROM eventos WHERE fecha >= CURDATE() ORDER BY fecha ASC");

// Armar consulta de servicios según búsqueda
$buscar = $_GET['buscar'] ?? '';
if ($buscar) {
    $buscar_escapado = $conexion->real_escape_string($buscar);
    $sql = "SELECT s.*, u.nombre, u.whatsapp, u.facebook, u.instagram
            FROM servicios s
            JOIN usuarios u ON s.usuario_id = u.id
            WHERE s.titulo LIKE '%$buscar_escapado%' 
               OR s.descripcion LIKE '%$buscar_escapado%'
            ORDER BY s.fecha_publicacion DESC";
    $servicios = $conexion->query($sql);
} else {
    $servicios = $conexion->query("
        SELECT s.*, u.nombre, u.whatsapp, u.facebook, u.instagram
        FROM servicios s
        JOIN usuarios u ON s.usuario_id = u.id
        ORDER BY s.fecha_publicacion DESC
    ");
}
?>

<!-- ===== EVENTOS ===== -->
<h2 class="titulo-seccion">📢 Capacitaciones y Reuniones</h2>

<?php if ($eventos->num_rows === 0): ?>
    <p>No hay eventos programados por el momento.</p>
<?php else: ?>
    <div class="grid">
        <?php while ($e = $eventos->fetch_assoc()): ?>
            <div class="card">
                <!-- Imagen del evento -->
                <?php if ($e['imagen']): ?>
                    <img src="/athoca/uploads/<?= $e['imagen'] ?>" alt="">
                <?php endif; ?>
                
                <div class="card-body">
                    <h3><?= htmlspecialchars($e['titulo']) ?></h3>
                    <p><?= htmlspecialchars($e['descripcion']) ?></p>
                    <p><strong>📅 Fecha:</strong> <?= $e['fecha'] ?> <?= $e['hora'] ?></p>
                    <p><strong>📍 Lugar:</strong> <?= htmlspecialchars($e['lugar']) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>


<!-- ===== SERVICIOS ===== -->
<h2 class="titulo-seccion">🛍️ Servicios Ofrecidos</h2>

<!-- 🔍 BUSCADOR -->
<form method="GET" style="max-width:100%;padding:15px;display:flex;gap:10px;margin-bottom:20px;">
    <input type="text" name="buscar" placeholder="🔍 Buscar servicio..." 
           value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>"
           style="flex:1;">
    <button type="submit" style="width:auto;padding:10px 20px;">Buscar</button>
</form>

<?php if ($servicios->num_rows === 0): ?>
    <p>No se encontraron servicios<?= $buscar ? " para \"" . htmlspecialchars($buscar) . "\"" : "" ?>.</p>
<?php else: ?>
    <div class="grid">
        <?php while ($s = $servicios->fetch_assoc()): ?>
            <div class="card">
                <!-- Imagen del servicio -->
                <?php if ($s['imagen']): ?>
                    <img src="/athoca/uploads/<?= $s['imagen'] ?>" alt="">
                <?php endif; ?>
                
                <div class="card-body">
                    <h3><?= htmlspecialchars($s['titulo']) ?></h3>
                    <p><?= htmlspecialchars($s['descripcion']) ?></p>
                    <p><strong>💵 Precio:</strong> <?= htmlspecialchars($s['precio']) ?></p>
                    <p><strong>👤 Vendedor:</strong> <?= htmlspecialchars($s['nombre']) ?></p>

                    <!-- Redes Sociales -->
                    <div style="margin-bottom: 10px;">
                        <?php if ($s['whatsapp']): ?>
                            <a class="btn" target="_blank" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $s['whatsapp']) ?>">WhatsApp</a>
                        <?php endif; ?>
                        <?php if ($s['facebook']): ?>
                            <a class="btn btn-azul" target="_blank" href="<?= $s['facebook'] ?>">Facebook</a>
                        <?php endif; ?>
                        <?php if ($s['instagram']): ?>
                            <a class="btn btn-rosa" target="_blank" href="<?= $s['instagram'] ?>">Instagram</a>
                        <?php endif; ?>
                    </div>

                    <!-- BOTONES DE EDITAR Y ELIMINAR (Solo para el dueño o Admin) -->
                    <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['usuario_id'] == $s['usuario_id'] || $_SESSION['rol'] === 'admin')): ?>
                        <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px;">
                            
                            <!-- Botón Editar -->
                            <a href="/athoca/editar_servicio.php?id=<?= $s['id'] ?>" 
                               class="btn" 
                               style="background:#f39c12; width:100%; text-align:center; padding: 10px 0; font-weight: bold; box-sizing: border-box; border-radius: 5px; color: white; text-decoration: none; margin: 0;">
                                ✏️ Editar
                            </a>

                            <!-- Botón Eliminar -->
                            <form method="POST" action="/athoca/eliminar_mi_servicio.php" 
                                  style="padding:0; box-shadow:none; margin:0; width: 100%;"
                                  onsubmit="return confirm('¿Seguro que querés eliminar este servicio?');">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" 
                                        style="background:#c0392b; width:100%; text-align:center; padding: 10px 0; font-weight: bold; border: none; border-radius: 5px; color: white; cursor: pointer; font-size: 15px;">
                                    🗑️ Eliminar
                                </button>
                            </form>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
<?php if (isset($_SESSION['usuario_id'])): ?>
    <div style="background: #e8f5e9; border-left: 5px solid #1b7a3e; padding: 15px; margin-bottom: 20px; border-radius: 5px; color: #1b7a3e;">
        <strong>👋 ¡Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>!</strong> 
        <?= $_SESSION['rol'] === 'admin' ? 'Tenés acceso total al sistema.' : 'Recordá que podés subir y gestionar tus servicios.' ?>
    </div>
<?php endif; ?>