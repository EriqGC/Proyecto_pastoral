<?php
session_start(); 

if($_POST){
    include('../includes/conects.php');
    $link = Conectarse();

    $correo = $_SESSION["correo"];
    $numero = $_POST["numero_user_edit"];
    $no_emergencia = $_POST["noemergencia_user_edit"];
    $talla = $_POST["talla_user_edit"];
    $enfermedades = $_POST["enfermedades_user_edit"];
    

    $query_update = "UPDATE PTL_USUARIOS 
                    SET telefono = '$numero', 
                        no_emergencia = '$no_emergencia', 
                        talla_camisa = '$talla', 
                        enfermedades = '$enfermedades' 
                    WHERE correo = '$correo'";

    $result_update = mysqli_query($link, $query_update);

    if ($result_update) {
        echo "<script>alert('Perfil actualizado');</script>";
        $_SESSION["numero"] = $numero;
        $_SESSION["numero_emergencia"] = $no_emergencia;
        $_SESSION["talla"] = $talla;
        $_SESSION["enfermedades"] = $enfermedades;
        mysqli_close($link);
        header("Location: perfil.php");
        exit();
    } else {
        echo "Error al actualizar: " . mysqli_error($link);
        echo "<br>Consulta: " . $query_update;
        mysqli_close($link);
        exit();
    }
}
?>