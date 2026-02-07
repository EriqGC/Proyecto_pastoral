<?php
session_start();
include('../includes/conects.php');
$link = Conectarse();

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['id']) || !isset($_POST['eventName'])) {
    $response['message'] = 'Datos insuficientes';
    echo json_encode($response);
    exit;
}

$userId = $_SESSION['id'];
$eventName = mysqli_real_escape_string($link, $_POST['eventName']);

// Primero obtenemos el ID del evento
$queryEventId = "SELECT id FROM PTL_EVENTOS WHERE nombre = '$eventName'";
$resultEventId = mysqli_query($link, $queryEventId);

if (!$resultEventId || mysqli_num_rows($resultEventId) === 0) {
    $response['message'] = 'Evento no encontrado';
    echo json_encode($response);
    exit;
}

$eventId = mysqli_fetch_assoc($resultEventId)['id'];

// Eliminamos la relación usuario-evento
$queryDelete = "DELETE FROM PTL_USUARIO_EVENTO WHERE personas_id = $userId AND eventos_id = $eventId";

if (mysqli_query($link, $queryDelete)) {
    $response['success'] = true;
    $response['message'] = 'Has salido del evento exitosamente';
} else {
    $response['message'] = 'Error al salir del evento: ' . mysqli_error($link);
}

echo json_encode($response);
?>