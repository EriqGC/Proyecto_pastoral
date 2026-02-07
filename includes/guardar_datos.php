<?php
if ($_POST) {
    $nombre_u = $_POST["nombre_alumno"];
    $apellido_p = $_POST["apellido_p"];
    $apellido_m = $_POST["apellido_m"];
    $mes = $_POST["mes_n"];
    $dia = $_POST["dia_n"];
    $anio = $_POST["anio_n"];
    $timestamp = mktime(
        0, 
        0, 
        0, 
        $mes,
        $dia,
        $anio
    );
    $fecha_n = date("Y-m-d", $timestamp);
    $sexo = $_POST["sexo"];

    $enfermedades = $_POST["enfermedades"] ?? "";
    $alergias = $_POST["alergias"] ?? "";
    $tratamiento = $_POST["tratamiento"] ?? "";
    $num_emergencias = $_POST["numero_emergencias"];

    $habilidades = $_POST['habilidades'];
    $playera = $_POST['playera'];

    $residencia_origen = $_POST['residencia_o'];
    $residencia_actual = $_POST['residencia_a'];
    $mensaje = $_POST['mensaje'];

    $idupaep = $_POST['idupaep'] ?? null;
    $matricula = $_POST['matricula'] ?? null;
    $licenciatura = $_POST['licenciatura'] ?? "";
    $institucion = "Algo";

    $id_invitado = "";
    $no_misiones = 0;
    $puesto = "Participante";
    $no_practicas_misioneras = 0;
    $imagen = "";

    $correo = $_POST['correo'];
    $numero = $_POST['numero_tel'];
    $contrasena = $_POST['contrasena'];
}

include('../includes/conects.php');
$link = Conectarse();

// Corrección: Falta un paréntesis al final de la consulta SQL
$result = mysqli_query($link, "INSERT INTO PTL_USUARIOS (
    nombre, 
    apellido_p, 
    apellido_m, 
    sexo, 
    fecha_nacimiento, 
    correo, 
    telefono, 
    lugar_nacimiento, 
    residencia_actual,
    enfermedades, 
    alergias, 
    tratamiento, 
    no_emergencia, 
    id_upaep, 
    matricula_upaep, 
    institucion_educativa, 
    licenciatura, 
    id_invitado_upaep, 
    no_misiones_realizadas,
    puesto, 
    talla_camisa, 
    no_practias_misioneras, 
    identifica_pregunta, 
    imagen,
    contrasena
) VALUES('$nombre_u', '$apellido_p', '$apellido_m', '$sexo', '$fecha_n', '$correo', '$numero', '$residencia_origen', '$residencia_actual',
'$enfermedades', '$alergias', '$tratamiento', '$num_emergencias', '$idupaep', '$matricula', '$institucion', '$licenciatura', '$id_invitado', '$no_misiones',
'$puesto', '$playera', '$no_practicas_misioneras', '$mensaje', '$imagen', SHA2('$contrasena', 256)
);");

// Iniciar sesión
session_start();
$_SESSION["autentificado"] = "SI";
$_SESSION["Perfil"] = $puesto;
$_SESSION["nombre"] = $nombre_u;
$_SESSION["correo"] = $correo;
$_SESSION["numero"] = $numero;
$_SESSION["numero_emergencia"] = $num_emergencias;
$_SESSION["residencia"] = $residencia_actual;
$_SESSION["talla"] = $playera;
$_SESSION["enfermedades"] = $enfermedades;
$_SESSION["alergias"] = $alergias;

mysqli_close($link);
header("Location: perfil.php");
exit();
?>
