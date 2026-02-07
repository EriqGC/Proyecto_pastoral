<?php
include('../includes/conects.php');
session_start();
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Obtener ID del evento a modificar
$evento_id = isset($_POST['evento_id']) ? intval($_POST['evento_id']) : 0;
if ($evento_id <= 0) {
    die("Error: ID de evento inválido");
}

// Obtener datos del formulario
$tipo = $_POST['tipo'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = ($tipo === 'Noticia') ? $fecha_inicio : $_POST['fecha_fin'];
$costo = ($tipo === 'Noticia') ? 0 : $_POST['costo'];
$categoria = $_POST['categoria'];
$supervisor = $_POST['supervisor'];
$ubicacion = $_POST['origen'] ?? '';
$ubicacion_final = $_POST['destino'] ?? '';
$vehiculo = mysqli_real_escape_string($link, $_POST['vehiculo'] ?? '');
$asientos = intval($_POST['asientos'] ?? 0);

if ($_POST['vehiculo'] === 'otro' && !empty($_POST['vehiculo_otro'])) {
    $vehiculo = $_POST['vehiculo_otro'];
}

// Configuración de directorios
$imgEventosDir = '../assets/img/img_eventos/';
$imgNoticiasDir = '../assets/img/img_noticias/';
$docsDir = '../assets/docs/';

// Obtener información actual del evento para manejar archivos
$query = "SELECT imagen, carta_compromiso, tipo FROM PTL_EVENTOS WHERE id = $evento_id";
$result = mysqli_query($link, $query);
$evento_actual = mysqli_fetch_assoc($result);

// Manejo de la imagen
$imagen = $evento_actual['imagen'];
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    // Eliminar imagen anterior si existe
    if (!empty($imagen)) {
        $oldImagePath = ($evento_actual['tipo'] === 'Noticia') ? $imgNoticiasDir . $imagen : $imgEventosDir . $imagen;
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
    }
    
    // Subir nueva imagen
    $targetDir = ($tipo === 'Noticia') ? $imgNoticiasDir : $imgEventosDir;
    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $nombreArchivo = uniqid('img_', true) . $extension;
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

// Manejo de la carta compromiso (solo para eventos)
$carta_compromiso = $evento_actual['carta_compromiso'];
if ($tipo === 'Evento' && isset($_FILES['carta_compromiso']) && $_FILES['carta_compromiso']['error'] === UPLOAD_ERR_OK) {
    // Eliminar carta anterior si existe
    if (!empty($carta_compromiso)) {
        $oldDocPath = $docsDir . $carta_compromiso;
        if (file_exists($oldDocPath)) {
            unlink($oldDocPath);
        }
    }
    
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

// Actualizar el evento en la base de datos
$query = "UPDATE PTL_EVENTOS SET 
          tipo = '$tipo', 
          nombre = '$nombre', 
          descripcion = '$descripcion', 
          carta_compromiso = '$carta_compromiso', 
          costo = $costo, 
          fecha_inicio = '$fecha_inicio', 
          fecha_fin = '$fecha_fin', 
          categoria = '$categoria', 
          supervisor = '$supervisor', 
          imagen = '$imagen', 
          ubicacion = '$ubicacion', 
          ubicacion_final = '$ubicacion_final'
          WHERE id = $evento_id";
$result = mysqli_query($link, $query);
if (!$result) {
    die("Error al actualizar evento: " . mysqli_error($link));
}

// Manejar itinerario
if (!empty($_POST['hora_inicio'])) {
    // Obtener itinerarios existentes para este evento
    $query = "SELECT i.id, i.hora, i.actividad 
              FROM PTL_ITINERARIO i
              JOIN PTL_ITINERARIO_EVENTOS ie ON i.id = ie.itinerario_id
              WHERE ie.evento_id = $evento_id
              ORDER BY i.id";
    $result = mysqli_query($link, $query);
    $existing_itineraries = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $existing_itineraries[] = $row;
    }

    // Procesar cada actividad del formulario
    foreach ($_POST['hora_inicio'] as $key => $hora_inicio) {
        $actividad = $_POST['actividad'][$key] ?? "Actividad programada";
        
        // Si existe un itinerario en esta posición, lo actualizamos
        if (isset($existing_itineraries[$key])) {
            $itinerario_id = $existing_itineraries[$key]['id'];
            $query = "UPDATE PTL_ITINERARIO SET 
                      hora = '$hora_inicio', 
                      actividad = '$actividad' 
                      WHERE id = $itinerario_id";
            mysqli_query($link, $query);
        } else {
            // Si no existe, insertamos nuevo
            $query = "INSERT INTO PTL_ITINERARIO (hora, actividad) VALUES ('$hora_inicio', '$actividad')";
            mysqli_query($link, $query);
            $itinerario_id = mysqli_insert_id($link);
            
            // Crear relación con el evento
            $query = "INSERT INTO PTL_ITINERARIO_EVENTOS (itinerario_id, evento_id) VALUES ($itinerario_id, $evento_id)";
            mysqli_query($link, $query);
        }
    }

    // Eliminar itinerarios sobrantes (si el formulario tiene menos actividades que las existentes)
    $submitted_count = count($_POST['hora_inicio']);
    if (count($existing_itineraries) > $submitted_count) {
        for ($i = $submitted_count; $i < count($existing_itineraries); $i++) {
            $itinerario_id = $existing_itineraries[$i]['id'];
            
            // Primero eliminamos la relación
            $query = "DELETE FROM PTL_ITINERARIO_EVENTOS WHERE itinerario_id = $itinerario_id";
            mysqli_query($link, $query);
            
            // Luego eliminamos el itinerario
            $query = "DELETE FROM PTL_ITINERARIO WHERE id = $itinerario_id";
            mysqli_query($link, $query);
        }
    }
} else {
    // Si no se enviaron actividades, eliminamos todas las existentes para este evento
    $query = "SELECT i.id FROM PTL_ITINERARIO i
              JOIN PTL_ITINERARIO_EVENTOS ie ON i.id = ie.itinerario_id
              WHERE ie.evento_id = $evento_id";
    $result = mysqli_query($link, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $itinerario_id = $row['id'];
        
        // Eliminar relación primero
        $query = "DELETE FROM PTL_ITINERARIO_EVENTOS WHERE itinerario_id = $itinerario_id";
        mysqli_query($link, $query);
        
        // Eliminar itinerario
        $query = "DELETE FROM PTL_ITINERARIO WHERE id = $itinerario_id";
        mysqli_query($link, $query);
    }
}

// Manejar transporte
if ($_POST['viaje'] === 'Si' && !empty($vehiculo)) {
    // Verificar si el vehículo existe o insertar uno nuevo
    if ($_POST['vehiculo'] === 'otro' && !empty($_POST['vehiculo_otro'])) {
        $vehiculo_otro = mysqli_real_escape_string($link, $_POST['vehiculo_otro']);
        $query = "INSERT INTO PTL_TRANSPORTE (vehiculo, asientos) VALUES ('$vehiculo_otro', $asientos)";
        mysqli_query($link, $query);
        $transporte_id = mysqli_insert_id($link);
    } else {
        $query = "SELECT id FROM PTL_TRANSPORTE WHERE vehiculo = '$vehiculo' LIMIT 1";
        $result = mysqli_query($link, $query);
        $transporte_id = $result ? mysqli_fetch_assoc($result)['id'] : null;
    }

    if ($transporte_id) {
        // Verificar si ya existe transporte para este evento
        $query = "SELECT * FROM PTL_EVENTO_TRANSPORTE WHERE evento_id = $evento_id LIMIT 1";
        $result = mysqli_query($link, $query);
        
        if (mysqli_num_rows($result) > 0) {
            // Actualizar transporte existente
            $query = "UPDATE PTL_EVENTO_TRANSPORTE SET transporte_id = $transporte_id WHERE evento_id = $evento_id";
        } else {
            // Insertar nuevo transporte
            $query = "INSERT INTO PTL_EVENTO_TRANSPORTE (evento_id, transporte_id) VALUES ($evento_id, $transporte_id)";
        }
        mysqli_query($link, $query);
    }
} else {
    // Eliminar relación de transporte si existe
    $query = "DELETE FROM PTL_EVENTO_TRANSPORTE WHERE evento_id = $evento_id";
    mysqli_query($link, $query);
}

// Redirección
$pagina_origen = $_SERVER['HTTP_REFERER'] ?? '../pages/perfil.php';
$pagina_origen = filter_var($pagina_origen, FILTER_SANITIZE_URL);
header("Location: $pagina_origen?success=evento_actualizado");
exit();
?>