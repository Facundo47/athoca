<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /athoca/login.php"); exit;
}
include '../config/conexion.php';
include '../includes/header.php';

$msg = "";
$editando = null;

// ✅ ELIMINAR EVENTO
if (isset($_POST['eliminar'])) {
    $id = intval($_POST['id']);
    
    // Borrar imagen física si existe
    $res = $conexion->query("SELECT imagen FROM eventos WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['imagen'] && file_exists("../uploads/" . $row['imagen'])) {
            unlink("../uploads/" . $row['imagen']);
        }
    }
    
    $conexion->query("DELETE FROM eventos WHERE id = $id");
    $msg = "<div class='mensaje mensaje-ok'>🗑️ Evento eliminado correctamente</div>";
}

// ✅ EDITAR EVENTO
if (isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $t = trim($_POST['titulo']); 
    $d = trim($_POST['descripcion']);
    $f = $_POST['fecha']; 
    $h = $_POST['hora']; 
    $l = trim($_POST['lugar']);
    
    // Lógica de imagen
    $img = $_POST['imagen_actual']; 
    if (!empty($_FILES['imagen']['name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nueva_img = uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/" . $nueva_img)) {
            if ($img && file_exists("../uploads/" . $img)) {
                unlink("../uploads/" . $img); // Borrar vieja
            }
            $img = $nueva_img;
        }
    }

    $stmt = $conexion->prepare("UPDATE eventos SET titulo=?, descripcion=?, fecha=?, hora=?, lugar=?, imagen=? WHERE id=?");
    $stmt->bind_param("ssssssi", $t,$d,$f,$h,$l,$img,$id);
    
    if ($stmt->execute()) {
        $msg = "<div class='mensaje mensaje-ok'>✅ Evento actualizado correctamente</div>";
    } else {
        $msg = "<div class='mensaje mensaje-error'>❌ Error al actualizar</div>";
    }
}

// ✅ CREAR EVENTO
if (isset($_POST['crear'])) {
    $t = trim($_POST['titulo']); 
    $d = trim($_POST['descripcion']);
    $f = $_POST['fecha']; 
    $h = $_POST['hora']; 
    $l = trim($_POST['lugar']);
    $img = null;

    if (!empty($_FILES['imagen']['name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $img = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/" . $img);
    }

    $stmt = $conexion->prepare("INSERT INTO eventos (titulo,descripcion,fecha,hora,lugar,imagen) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $t,$d,$f,$h,$l,$img);
    $stmt->execute();
    $msg = "<div class='mensaje mensaje-ok'>✅ Evento creado</div>";
}

// CARGAR DATOS SI SE VA A EDITAR
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conexion->query("SELECT * FROM eventos WHERE id = $id");
    $editando = $res->fetch_assoc();
}

$eventos = $conexion->query("SELECT * FROM eventos ORDER BY fecha DESC");
?>

<h2 class="titulo-seccion">📢 Gestionar Eventos</h2>
<?= $msg ?>

<!-- FORMULARIO PARA CREAR O EDITAR (AHORA CON ENCTYPE) -->
<form method="POST" enctype="multipart/form-data">
    <h3 style="color:#1b7a3e;margin-bottom:10px;">
        <?= $editando ? "✏️ Editando evento: " . htmlspecialchars($editando['titulo']) : "➕ Crear nuevo evento" ?>
    </h3>
    
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $editando['id'] ?>">
        <input type="hidden" name="imagen_actual" value="<?= $editando['imagen'] ?>">
    <?php endif; ?>

    <label>Título *</label>
    <input type="text" name="titulo" required value="<?= $editando ? htmlspecialchars($editando['titulo']) : '' ?>">

    <label>Descripción</label>
    <textarea name="descripcion" rows="3"><?= $editando ? htmlspecialchars($editando['descripcion']) : '' ?></textarea>

    <label>Fecha *</label>
    <input type="date" name="fecha" required value="<?= $editando ? $editando['fecha'] : '' ?>">

    <label>Hora</label>
    <input type="time" name="hora" value="<?= $editando ? $editando['hora'] : '' ?>">

    <label>Lugar</label>
    <input type="text" name="lugar" value="<?= $editando ? htmlspecialchars($editando['lugar']) : '' ?>">

    <!-- CAMPO DE IMAGEN -->
    <label>Imagen del evento</label>
    <?php if ($editando && $editando['imagen']): ?>
        <img src="/athoca/uploads/<?= $editando['imagen'] ?>" style="width:100px; border-radius:8px; margin-bottom:10px; display:block;">
    <?php endif; ?>
    <input type="file" name="imagen" accept="image/*">

    <?php if ($editando): ?>
        <button type="submit" name="editar">💾 Guardar Cambios</button>
        <a href="eventos.php" class="btn" style="display:block; text-align:center; margin-top:10px; background:#888;">Cancelar Edición</a>
    <?php else: ?>
        <button type="submit" name="crear">Guardar Evento</button>
    <?php endif; ?>
</form>

<!-- LISTADO CON BOTONES -->
<h3 class="titulo-seccion">📋 Eventos cargados</h3>

<?php if ($eventos->num_rows === 0): ?>
    <p>No hay eventos cargados todavía.</p>
<?php else: ?>
    <div class="grid">
        <?php while ($e = $eventos->fetch_assoc()): ?>
            <div class="card">
                <!-- MOSTRAR IMAGEN EN EL PANEL -->
                <?php if ($e['imagen']): ?>
                    <img src="/athoca/uploads/<?= $e['imagen'] ?>" alt="">
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= htmlspecialchars($e['titulo']) ?></h3>
                    <p><?= htmlspecialchars($e['descripcion']) ?></p>
                    <p><strong>📅</strong> <?= $e['fecha'] ?> <?= $e['hora'] ?></p>
                    <p><strong>📍</strong> <?= htmlspecialchars($e['lugar']) ?></p>

                    <a href="eventos.php?editar=<?= $e['id'] ?>" 
                       class="btn" style="background:#f39c12; margin-bottom:5px; display:inline-block;">
                        ✏️ Editar
                    </a>

                    <form method="POST" 
                          style="padding:0;box-shadow:none;margin:0;display:inline-block;"
                          onsubmit="return confirm('¿Seguro que querés eliminar este evento?');">
                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                        <button type="submit" name="eliminar"
                                style="background:#c0392b;color:white;width:auto;padding:8px 15px;border:none;border-radius:5px;cursor:pointer;">
                            🗑️ Eliminar
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>