<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../includes/conects.php');
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== "SI") {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: inicio_sesion.php");
    exit();
}

// Obtener el id del participante
$correo = mysqli_real_escape_string($link, $_SESSION['correo']);
$queryUsuario = "SELECT id FROM PTL_USUARIOS WHERE correo = '$correo'";
$result = mysqli_query($link, $queryUsuario);

if (!$result) {
    echo "Error al obtener el ID del usuario: " . mysqli_error($link);
    exit();
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo "Error: Usuario no encontrado.";
    exit();
}
$usuario_id = $row['id'];

// Obtener datos del formulario
$evento_id = isset($_POST['evento_id']) ? intval($_POST['evento_id']) : 0;
$tipo = 'Participante';

// Verificar si el usuario ya está inscrito
$queryVerificar = "SELECT * FROM PTL_USUARIOS_EVENTOS WHERE persona_id = $usuario_id AND evento_id = $evento_id";
$resultVerificar = mysqli_query($link, $queryVerificar);

if (!$resultVerificar) {
    echo "Error al verificar inscripción: " . mysqli_error($link);
    exit();
}

if (mysqli_num_rows($resultVerificar) > 0) {
    header("Location: evento.php?id=$evento_id&inscripcion=existente");
    exit();
}

// Insertar la inscripción
$tipo = mysqli_real_escape_string($link, $tipo);
$queryInsert = "INSERT INTO PTL_USUARIOS_EVENTOS (persona_id, evento_id, fecha, tipo) 
                VALUES ($usuario_id, $evento_id, NOW(), '$tipo')";

if (mysqli_query($link, $queryInsert)) {
    header("Location: pago.php?id=$evento_id&inscripcion=exitosa");
    exit();
} else {
    echo "Error al insertar inscripción: " . mysqli_error($link);
    exit();
}

// Cerrar la conexión
mysqli_close($link);
?>