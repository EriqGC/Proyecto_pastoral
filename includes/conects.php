<?php //Funcion para conectarese a la base de datos
function Conectarse(){
    if(!($link=mysqli_connect("92.249.45.29", "proydweb_p2025", "Dw3bp202%", "proydweb_p2025", 3306))){
        echo "Fallo al conectar a MySQL";
        exit();
    }
    return $link;
}

?>