<?php 
   /*
    Para hacer uso de este hero solo tienen que agregar el comando
    <?php
        include '../includes/hero.php';
        createHero("El texto que quieres que aparezca");
    ?>
    */
    function createHero($heroText){
        echo    
            '
            <link rel="stylesheet" href="../assets/css/hero.css">
            <div class="banner-container">
                <h1>' . $heroText . '</h1>   
            </div>';
    }
?>
