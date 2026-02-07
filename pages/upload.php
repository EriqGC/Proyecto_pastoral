<?php
session_start();

// Verificar permisos
if ($_SESSION["Perfil"] != "Coordinador" && $_SESSION["Perfil"] != "Administrador") {
    http_response_code(403);
    die("No tienes permisos para realizar esta acción");
}

// Directorios de destino
$uploadDirs = [
    'images' => '../uploads/images/',
    'documents' => '../uploads/documents/'
];

// Validar el tipo de archivo
$allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
$allowedDocumentTypes = ['application/pdf'];

try {
    if (!isset($_FILES['file']) || !isset($_POST['folder'])) {
        throw new Exception('Datos incompletos');
    }

    $file = $_FILES['file'];
    $folder = $_POST['folder'];

    // Validar carpeta de destino
    if (!array_key_exists($folder, $uploadDirs)) {
        throw new Exception('Carpeta de destino no válida');
    }

    $targetDir = $uploadDirs[$folder];
    
    // Crear directorio si no existe
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Validar tipo de archivo según la carpeta
    if ($folder === 'images' && !in_array($file['type'], $allowedImageTypes)) {
        throw new Exception('Tipo de imagen no permitido');
    }

    if ($folder === 'documents' && !in_array($file['type'], $allowedDocumentTypes)) {
        throw new Exception('Solo se permiten archivos PDF');
    }

    // Validar tamaño del archivo (ejemplo: 5MB máximo)
    if ($file['size'] > 5242880) {
        throw new Exception('El archivo es demasiado grande (máximo 5MB)');
    }

    // Generar nombre único para el archivo
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExt;
    $targetPath = $targetDir . $fileName;

    // Mover el archivo subido
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode([
            'success' => true,
            'fileName' => $fileName,
            'originalName' => $file['name']
        ]);
    } else {
        throw new Exception('Error al mover el archivo subido');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>