<?php
include('conects.php');
$link = Conectarse();


// Obtener los datos del formulario
$correo = trim($_POST["correo"]);
$contrasena = trim($_POST["contrasena"]);


// Consulta para verificar credenciales y obtener perfil
$query = "SELECT * FROM PTL_USUARIOS WHERE correo = '$correo' AND contrasena = SHA2('$contrasena', 256)";
$result = mysqli_query($link, $query) or die("Error en la consulta: " . mysqli_error($link));

if ($result->num_rows > 0) {
    // Obtener datos del usuario
    
    $usuario = $result->fetch_assoc();
    
    // Iniciar sesión
    session_start();
    $_SESSION["autentificado"] = "SI";
    $_SESSION["id"] = $usuario["id"];
    $_SESSION["Perfil"] = $usuario["puesto"];
    $_SESSION["nombre"] = $usuario["nombre"];
    $_SESSION["correo"] = $usuario["correo"];
    $_SESSION["numero"] = $usuario["telefono"];
    $_SESSION["numero_emergencia"] = $usuario["no_emergencia"];
    $_SESSION["residencia"] = $usuario["residencia_actual"];
    $_SESSION["talla"] = $usuario["talla_camisa"];
    $_SESSION["enfermedades"] = $usuario["enfermedades"];
    $_SESSION["alergias"] = $usuario["alergias"];
    
    header("Location: ../pages/perfil.php");
} else {
    header("Location: ../pages/inicio_sesion.php?errorusuario=1");
}

$link->close();
?>