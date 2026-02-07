<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../assets/css/inicio_sesion.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria:wght@400&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="login-container">
        <div class="background-image">
            <img src="../assets/img/fondo.png" alt="Imagen de fondo">
        </div>
        
        <a href="../index.php" class="logo-link">
            <img src="../assets/img/logo_upaep.png" alt="Logo UPAEP" class="logo">
        </a>
        
        <?php
        // Mostrar el formulario correspondiente según el paso
        $step = isset($_GET['step']) ? $_GET['step'] : 1;
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        $error = isset($_GET['error']) ? $_GET['error'] : '';
        
        switch($step) {
            case 1: // Paso 1: Solicitar correo
        ?>
                <form action="procesar_recuperacion.php" method="POST">
                    <div class="login-form">
                        <h1 class="login-title">Recuperar Contraseña</h1>
                        
                        <?php if($error == 'email'): ?>
                            <p class="error-message">El correo no está registrado</p>
                        <?php endif; ?>
                        
                        <div class="input-group">
                            <input type="email" name="correo" id="correo" placeholder="Correo electrónico" class="form-input" required>
                        </div>
                        
                        <input type="hidden" name="step" value="1">
                        <input type="submit" class="login-button" value="Continuar">
                        
                        <div class="register-link">
                            <span>¿Recordaste tu contraseña?</span>
                            <a href="inicio_sesion.php" class="register-text">Iniciar Sesión</a>
                        </div>
                    </div>
                </form>
        <?php
                break;
                
            case 2: // Paso 2: Verificar código
        ?>
                <form action="procesar_recuperacion.php" method="POST">
                    <div class="login-form">
                        <h1 class="login-title">Verificar Código</h1>
                        
                        <?php if($error == 'codigo'): ?>
                            <p class="error-message">Código incorrecto</p>
                        <?php endif; ?>
                        
                        <p class="payment-text">Hemos enviado un código de verificación a <?php echo htmlspecialchars($email); ?></p>
                        
                        <div class="input-group">
                            <input type="text" name="codigo" placeholder="Código de 6 dígitos" class="form-input" required maxlength="6">
                        </div>
                        
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <input type="hidden" name="step" value="2">
                        <input type="submit" class="login-button" value="Verificar">
                    </div>
                </form>
        <?php
                break;
                
            case 3: // Paso 3: Nueva contraseña
        ?>
                <form action="procesar_recuperacion.php" method="POST" id="passwordForm">
                    <div class="login-form">
                        <h1 class="login-title">Nueva Contraseña</h1>
                        
                        <?php if($error == 'password'): ?>
                            <p class="error-message">Las contraseñas no coinciden</p>
                        <?php endif; ?>
                        
                        <div class="input-group">
                            <input type="password" name="password" id="password" placeholder="Nueva contraseña" class="form-input" required>
                        </div>
                        
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar contraseña" class="form-input" required>
                            <span id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">👁️</span>
                        </div>
                        
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <input type="hidden" name="step" value="3">
                        <input type="submit" class="login-button" value="Cambiar Contraseña">
                    </div>
                </form>
                
                <script>
                    // Mostrar/ocultar contraseña
                    const togglePassword = document.getElementById('togglePassword');
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirm_password');
                    
                    togglePassword.addEventListener('click', function() {
                        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                        password.setAttribute('type', type);
                        confirmPassword.setAttribute('type', type);
                    });
                    
                    // Validar que las contraseñas coincidan
                    document.getElementById('passwordForm').addEventListener('submit', function(e) {
                        if(password.value !== confirmPassword.value) {
                            e.preventDefault();
                            alert('Las contraseñas no coinciden');
                        }
                    });
                </script>
        <?php
                break;
        }
        ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>