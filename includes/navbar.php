<?php
// navbar.php
session_start();
// Verifica si la sesión está iniciada y si el usuario tiene el perfil adecuado
$is_index = (basename($_SERVER['PHP_SELF']) == 'index.php');
$base_path = $is_index ? '' : '../';
$current_page = basename($_SERVER['PHP_SELF']);

$nav_items = [
    'anuncios.php' => 'Anuncios',
    'index.php' => 'Conócenos',
    'calendario.php' => 'Calendario',
    'dudas.php' => 'Dudas'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/navbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="<?= $base_path ?>index.php" class="logo">
                <picture>
                    <!-- Versión móvil (menos de 500px) -->
                    <source media="(max-width: 500px)" 
                            srcset="<?= $base_path ?>assets/img/logo_upaep_peque.png">
                    <!-- Versión escritorio -->
                    <img src="<?= $base_path ?>assets/img/logo_upaep.png" 
                        alt="Logo UPAEP"
                        class="logo-img">
                </picture>
            </a>
            
            <ul class="nav-links">
                <?php foreach ($nav_items as $page => $title): ?>
                <li>
                    <a href="<?= $base_path . ($page == 'index.php' ? '' : 'pages/') . $page ?>" 
                       class="<?= ($current_page == $page) ? 'active' : '' ?>">
                       <?= $title ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="auth">
            <?php if (isset($_SESSION["autentificado"]) && $_SESSION["autentificado"] === "SI"): ?>
                <?php switch ($_SESSION["Perfil"] ?? ''): 
                    case "Administrador":
                    case "Coordinador":
                    case "Supervisor":
                    case "Participante": ?>
                        <a href="mensajes.php">
                            <img class="btn_perfil" src="<?= $base_path ?>assets/img/correo.png" alt="mensajes">
                        </a>
                        <a class="apodo" href="perfil.php">
                            <span class="nombre-perfil"><?= $_SESSION["nombre"] ?></span>
                            <img class="btn_perfil" src="<?= $base_path ?>assets/img/perfil.png" alt="perfil">
                        </a>
                        <a href="logout.php">
                            <img class="btn_perfil" src="<?= $base_path ?>assets/img/logout.png" alt="cerrar sesión">
                        </a>
                        <?php break; ?>
                    <?php default: ?>
                        <!-- Sin acciones para perfiles desconocidos -->
                <?php endswitch; ?>
            <?php else: ?>
                <a href="<?= $base_path ?>pages/registro_sesion.php" class="register-button">Registrarse</a>
                <a href="<?= $base_path ?>pages/inicio_sesion.php" class="login-button">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
        
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </nav>
    
    <div class="background-opaque"></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hamburger = document.querySelector(".hamburger");
            const bg = document.querySelector(".background-opaque");
            const navBar = document.querySelector(".nav-links");
            const shadow = document.querySelector(".navbar");

            // Toggle menu
            hamburger.addEventListener('click', () => {
                navBar.classList.toggle("active");
                bg.classList.toggle("active");
                shadow.classList.toggle("active");
            });

            // Cerrar menu al hacer clic fuera
            bg.addEventListener('click', () => {
                navBar.classList.remove("active");
                bg.classList.remove("active");
                shadow.classList.remove("active");
            });

            // Cerrar menu al seleccionar enlace
            document.querySelectorAll('.nav-links a').forEach(link => {
                link.addEventListener('click', () => {
                    navBar.classList.remove("active");
                    bg.classList.remove("active");
                    shadow.classList.remove("active");
                });
            });
        });
    </script>
</body>
</html>