<?php
session_start();
include('conects.php');

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para hacer preguntas']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$evento_id = intval($data['evento_id']);
$pregunta = trim($data['pregunta']);

if (empty($pregunta)) {
    echo json_encode(['success' => false, 'message' => 'La pregunta no puede estar vacía']);
    exit;
}

// Insertar la nueva pregunta
$link = Conectarse();
$query = "INSERT INTO PTL_preguntas_frecuentes (pregunta, respuesta) VALUES (?, 'Pendiente de respuesta')";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, 's', $pregunta);
mysqli_stmt_execute($stmt);
$pregunta_id = mysqli_insert_id($link);

// Relacionar la pregunta con el evento
$queryRelacion = "INSERT INTO PTL_preguntas_frecuentes_evento (pgs_frecuentes_id, eventos_id) VALUES (?, ?)";
$stmtRelacion = mysqli_prepare($link, $queryRelacion);
mysqli_stmt_bind_param($stmtRelacion, 'ii', $pregunta_id, $evento_id);
mysqli_stmt_execute($stmtRelacion);

echo json_encode(['success' => true]);
?>