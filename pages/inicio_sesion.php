<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../assets/css/inicio_sesion.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria:wght@400&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</head>
<body>
    <div class="login-container">
        <div class="background-image">
            <img src="../assets/img/fondo.png" alt="Imagen de fondo">
        </div>
        
        <a href="../index.php" class="logo-link">
            <img src="../assets/img/logo_upaep.png" alt="Logo UPAEP" class="logo">
        </a>
        <form action="../includes/control.php" method="POST">
            <div class="login-form">
                <h1 class="login-title">Acceder</h1>
                <?php 
                    if($_GET)
                    {
                        if($_GET['errorusuario'] == 1)
                        {
                            echo '<p class="error-message">Correo o contraseña incorrectos</p>';
                        }
                    }
                ?>   
                <div class="input-group">
                    <input type="email" name="correo" placeholder="correo electrónico" class="form-input">
                </div>

                <div class="input-group">
                    <input type="password" name="contrasena" placeholder="Contraseña" class="form-input">
                </div>
                
                <a href="recuperar_contrasena.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
                
                <input type="submit" class="login-button" value="Ingresar">
                
                <div class="register-link">
                    <span>¿No tiene una cuenta?</span>
                    <a href="registro_sesion.php" class="register-text">Registrarse</a>
                </div>
            </div>
        </form>
        
    </div>
    
    <?php include '../includes/footer.php'; ?>
    

</body>
</html>