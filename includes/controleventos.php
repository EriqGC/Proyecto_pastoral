<?php
$link = Conectarse();

// Obtener los datos del formulario
$evento_id = trim($_POST["id"] ?? $_GET["id"] ?? 0);

if ($evento_id > 0) {
    header("Location: ../pages/evento.php?id=".$evento_id);
} else {
    header("Location: ../index.php?evento=vacio");
}

$link->close();
?>