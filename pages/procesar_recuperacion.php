<?php
include('../includes/conects.php');
// Cargar PHPMailer manualmente desde la carpeta includes
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$step = isset($_POST['step']) ? $_POST['step'] : null;

$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Paso 1: Verificar email
if ($step == 1) {
    // Verificar si el email existe
    $email = trim($_POST["correo"]);
    $query = "SELECT id FROM PTL_USUARIOS WHERE correo = '$email'";
    
    error_log("Consulta SQL: " . $query);
    $result = mysqli_query($link, $query);
    $num_rows = mysqli_num_rows($result);
    error_log("Filas encontradas: " . $num_rows);
    
    if ($num_rows == 0) {
        error_log("email no encontrado: " . $email);
        header("Location: recuperar_contrasena.php?error=email");
        exit();
    }

    // Generar código de 6 dígitos
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['recovery_code'] = $codigo;
    $_SESSION['recovery_email'] = $email;

    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP de Hotmail/Outlook
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'enrique_agc@hotmail.com';
        $mail->Password = 'Enrique09'; // Usa una contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom('enrique_agc@hotmail.com', 'Sistema Pastoral');
        $mail->addAddress($email);
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Código de recuperación de contraseña - Pastoral';
        $mail->Body = "
            <h1>Código de recuperación</h1>
            <p>Hemos recibido una solicitud para restablecer tu contraseña en el sistema Pastoral.</p>
            <p>Tu código de verificación es: <strong>$codigo</strong></p>
            <p>Este código es válido por 15 minutos.</p>
            <p>Si no solicitaste este cambio, por favor ignora este mensaje.</p>
            <hr>
            <p><small>Sistema Pastoral - Desarrollo Web 2025</small></p>
        ";
        $mail->AltBody = "Tu código de verificación es: $codigo\n\nSi no solicitaste este cambio, ignora este mensaje.";
        
        $mail->send();
        header("Location: recuperar_contrasena.php?step=2&email=" . urlencode($email));
    } catch (Exception $e) {
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        header("Location: recuperar_contrasena.php?error=email&message=Error_al_enviar_el_código");
    }
    exit();
}

// Paso 2: Verificar código
if ($step == 2 && isset($_SESSION['recovery_code'])) {
    $codigo_usuario = trim($_POST['codigo']);
    $email = $_SESSION['recovery_email'];

    if ($codigo_usuario != $_SESSION['recovery_code']) {
        header("Location: recuperar_contrasena.php?step=2&email=" . urlencode($email) . "&error=codigo");
        exit();
    }

    // Código correcto, avanzar al paso 3
    header("Location: recuperar_contrasena.php?step=3&email=" . urlencode($email));
    exit();
}

// Paso 3: Cambiar contraseña
if ($step == 3 && isset($_SESSION['recovery_email'])) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = $_SESSION['recovery_email'];

    if ($password != $confirm_password) {
        header("Location: recuperar_contrasena.php?step=3&email=" . urlencode($email) . "&error=password");
        exit();
    }

    // Actualizar contraseña en la base de datos
    $hashed_password = hash('sha256', $password);
    $update_query = "UPDATE PTL_USUARIOS SET contrasena = SHA2('$password', 256) WHERE correo = '$email'";
    mysqli_query($link, $update_query);

    // Enviar email de confirmación
    $mail = new PHPMailer(true);
    
    try {
        // Configuración SMTP (igual que antes)
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'enrique_agc@hotmail.com';
        $mail->Password = 'Enrique09';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom('enrique_agc@hotmail.com', 'Sistema Pastoral');
        $mail->addAddress($email);
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Contraseña actualizada - Pastoral';
        $mail->Body = "
            <h1>Contraseña actualizada</h1>
            <p>Tu contraseña en el sistema Pastoral ha sido cambiada exitosamente.</p>
            <p>Si no realizaste este cambio, por favor contacta al administrador del sistema inmediatamente.</p>
            <hr>
            <p><small>Sistema Pastoral - Desarrollo Web 2025</small></p>
        ";
        $mail->AltBody = "Tu contraseña ha sido actualizada. Si no realizaste este cambio, contacta al administrador.";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar el correo de confirmación: " . $mail->ErrorInfo);
    }

    // Limpiar sesión y redirigir
    unset($_SESSION['recovery_code']);
    unset($_SESSION['recovery_email']);
    header("Location: inicio_sesion.php?password_changed=1");
    exit();
}

// Redirección por si acceden directamente al archivo
header("Location: recuperar_contrasena.php");
exit();