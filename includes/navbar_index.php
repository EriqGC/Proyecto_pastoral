<?php
$current_page = basename($_SERVER['PHP_SELF']);
$nav_items = [
    'anuncios.php' => 'Anuncios',
    'index.php' => 'Conócenos',
    'calendario.php' => 'Calendario',
    'dudas.php' => 'Dudas'
];
?>

<?php
/*
Para hacer uso de este footer solo tienen que agregar el comando
<?php include 'includes/footer.php'; ?>

Esto es en caso de tener subcarpetas
class="<?= (strpos($current_page, 'anuncios') !== false) ? 'active' : '' ?>"
*/
echo '
    <link rel="stylesheet" href="assets/css/navbar.css">
    <nav class="navbar">
        <div class="navbar-left">
            <a href="index.php" class="logo">
                <img src="assets/img/logo_upaep.png" alt="Logo UPAEP">
            </a>
            <ul class="nav-links">'?>
                <?php foreach ($nav_items as $page => $title): ?>
                <li>
                    <?php if ($page == 'index.php') { ?>
                        <a href="<?= $page ?>" class="<?= ($current_page == $page) ? 'active' : '' ?>">
                            <?= $title ?>
                        </a>
                    <?php } else { ?>
                    <a href="pages/<?= $page ?>" 
                       class="<?= ($current_page == $page) ? 'active' : '' ?>">
                       <?= $title ?>
                    </a>
                    <?php } ?>
                </li>
                <?php endforeach; ?>
                <?php echo '
            </ul>
        </div>
        <div class="auth">
        ';?>
        <?php
        if (isset($_SESSION["autentificado"]) && $_SESSION["autentificado"] === "SI") {
            switch ($_SESSION["Perfil"] ?? '') {
                case "Administrador":
                    echo '
                        <a href="#">
                            <img class="btn_perfil" src="assets/img/correo.png" alt="mensajes">
                         </a>
                        <a class="apodo" href="pages/perfil.php">
                            <span class="nombre-perfil">' . $_SESSION["nombre"] . '</span>
                            <img class="btn_perfil" src="assets/img/perfil.png" alt="perfil">
                        </a>
                        <a href="logout.php">
                            <img class="btn_perfil" src="assets/img/logout.png" alt="cerrar sesión">
                        </a>
                    ';
                    break;
                case "Coordinador":
                    echo '
                        <a href="#">
                            <img class="btn_perfil" src="assets/img/correo.png" alt="mensajes">
                         </a>
                        <a class="apodo" href="pages/perfil.php">
                            <span class="nombre-perfil">' . $_SESSION["nombre"] . '</span>
                            <img class="btn_perfil" src="assets/img/perfil.png" alt="perfil">
                        </a>
                        <a href="logout.php">
                            <img class="btn_perfil" src="assets/img/logout.png" alt="cerrar sesión">
                        </a>
                    ';
                    break;
                case "Supervisor":
                    echo '
                        <a href="#">
                            <img class="btn_perfil" src="assets/img/correo.png" alt="mensajes">
                         </a>
                        <a class="apodo" href="pages/perfil.php">
                            <span class="nombre-perfil">' . $_SESSION["nombre"] . '</span>
                            <img class="btn_perfil" src="assets/img/perfil.png" alt="perfil">
                        </a>
                        <a href="logout.php">
                            <img class="btn_perfil" src="assets/img/logout.png" alt="cerrar sesión">
                        </a>
                    ';
                    break;
                case "Participante":
                    echo '
                        <a href="#">
                            <img class="btn_perfil" src="assets/img/correo.png" alt="mensajes">
                         </a>
                        <a class="apodo" href="pages/perfil.php">
                            <span class="nombre-perfil">' . $_SESSION["nombre"] . '</span>
                            <img class="btn_perfil" src="assets/img/perfil.png" alt="perfil">
                        </a>
<<<<<<< HEAD
                        <a href=" pages/logout.php">
=======
                        <a href="pages/logout.php">
>>>>>>> f15f49e3cf467484be995a56a4aa124aedc544e8
                            <img class="btn_perfil" src="assets/img/logout.png" alt="cerrar sesión">
                        </a>
                    ';
                    break;
                default:
                    
                    break;
            }
        }
        else
            {
                echo '
                    <a href="pages/registro_sesion.php" class="register-button">Registrarse</a>
                    <a href="pages/inicio_sesion.php" class="login-button">Iniciar Sesión</a>
                    ';
            }
        ?>
        <?php 
        echo '
        </div>
    </nav>';
?>