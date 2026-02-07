<?php
include('../includes/conects.php');
session_start();
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Obtener datos del formulario
$tipo = $_POST['tipo'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = ($tipo === 'Noticia') ? $fecha_inicio : $_POST['fecha_fin'];
$costo = ($tipo === 'Noticia') ? 0 : $_POST['costo'];
$categoria = $_POST['categoria'];
$recurrencia = $_POST['recurrencia'];
$supervisor = $_POST['supervisor'];
$creador = $_SESSION['correo'];
$ubicacion = $_POST['origen'] ?? '';
$ubicacion_final = $_POST['destino'] ?? '';
$vehiculo = $_POST['vehiculo'] ?? '';
$asientos = $_POST['asientos'] ?? 0;
$vehiculo_otro = $_POST['vehiculo_otro'] ?? 'Sin especificar';

// obtener datos de transporte
$query = "SELECT id FROM PTL_TRANSPORTE WHERE vehiculo = '$vehiculo'";
$result = mysqli_query($link, $query);

$transporte_id = $result ? mysqli_fetch_assoc($result)['id'] : null;

// Configuración de directorios
$imgEventosDir = '../assets/img/img_eventos/';
$imgNoticiasDir = '../assets/img/img_noticias/';
$docsDir = '../assets/docs/';

// Subir imagen del evento/noticia
$imagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $targetDir = ($tipo === 'Noticia') ? $imgNoticiasDir : $imgEventosDir;
    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $nombreArchivo = uniqid('img_', true) . '.' . $extension;
    $targetFile = $targetDir . $nombreArchivo;
    
    // Validar tipo de imagen
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowedTypes)) {
        die("Error: Solo se permiten imágenes JPG, JPEG, PNG o GIF");
    }
    
    // Mover archivo subido
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
        $imagen = $nombreArchivo;
    } else {
        die("Error al subir la imagen");
    }
}

// Subir carta compromiso (solo para eventos)
$carta_compromiso = '';
if ($tipo === 'Evento' && isset($_FILES['carta_compromiso']) && $_FILES['carta_compromiso']['error'] === UPLOAD_ERR_OK) {
    // Validar que sea PDF
    $extension = strtolower(pathinfo($_FILES['carta_compromiso']['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        die("Error: La carta de compromiso debe ser un archivo PDF");
    }
    
    // Generar nombre único
    $nombreArchivo = uniqid('doc_', true) . '.pdf';
    $targetFile = $docsDir . $nombreArchivo;
    
    // Mover archivo
    if (move_uploaded_file($_FILES['carta_compromiso']['tmp_name'], $targetFile)) {
        $carta_compromiso = $nombreArchivo;
    } else {
        die("Error al subir la carta de compromiso");
    }
}

// Insertar en PTL_EVENTOS con ubicaciones
$query = "INSERT INTO PTL_EVENTOS (tipo, nombre, descripcion, carta_compromiso, costo, fecha_inicio, fecha_fin, creador, categoria, supervisor, imagen, ubicacion, ubicacion_final, recurrencia)
          VALUES ('$tipo', '$nombre', '$descripcion', '$carta_compromiso', $costo, '$fecha_inicio', '$fecha_fin', '$creador', '$categoria', '$supervisor', '$imagen', '$ubicacion', '$ubicacion_final', '$recurrencia')";
$result = mysqli_query($link, $query);
if (!$result) {
    die("Error al insertar evento: " . mysqli_error($link));
}
$evento_id = mysqli_insert_id($link);

// Insertar Itinerario
if (!empty($_POST['hora_inicio'])) {
    foreach ($_POST['hora_inicio'] as $key => $hora_inicio) {
        $hora_fin = $_POST['hora_fin'][$key] ?? $hora_inicio;
        $actividad = $_POST['actividad'][$key] ?? "Actividad programada";
        
        $query = "INSERT INTO PTL_ITINERARIO (hora, actividad) VALUES ('$hora_inicio', '$actividad')";
        $result = mysqli_query($link, $query);
        if (!$result) {
            die("Error al insertar itinerario: " . mysqli_error($link));
        }
        $itinerario_id = mysqli_insert_id($link);

        // Relacionar itinerario con evento
        $query = "INSERT INTO PTL_ITINERARIO_EVENTOS (itinerario_id, evento_id) VALUES ($itinerario_id, $evento_id)";
        $result = mysqli_query($link, $query);
        if (!$result) {
            die("Error al relacionar itinerario con evento: " . mysqli_error($link));
        }
    }
}

// Insertar Transporte si aplica
if ($_POST['viaje'] === 'Si' && !empty($vehiculo)) {
    $query = "INSERT INTO PTL_EVENTO_TRANSPORTE (evento_id, transporte_id,supervisor) VALUES ($evento_id, $transporte_id,'$supervisor')";
    $result = mysqli_query($link, $query);
    if (!$result) {
        die("Error al insertar transporte: " . mysqli_error($link));
    }
    $transporte_id = mysqli_insert_id($link);
}

if($_POST['vehiculo_otro']){
    $query = "INSERT INTO PTL_TRANSPORTE (asientos, vehiculo) VALUES ($asientos, '$vehiculo_otro')";
    $result = mysqli_query($link, $query);
    if (!$result) {
        die("Error al insertar transporte: " . mysqli_error($link));
    }
    $transporte_id = mysqli_insert_id($link);
}
header("Location: ../pages/perfil.php");
?>