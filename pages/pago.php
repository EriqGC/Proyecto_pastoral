<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Pago</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/pago.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria:wght@400&display=swap">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
    <div class="background-image">
        <img src="../assets/img/fondo.png" alt="Imagen de fondo">
    </div>
    
    <div class="main-content">
        <!-- Contenedor de información -->
        <div class="info-card">
            <div class="payment-form">
                <h1 class="payment-title">Proceso de Pago</h1>
                
                <div class="payment-content">
                    <div class="payment-text-container">
                        <p class="payment-text">
                            Este es el proceso de pago para tu inscripción. Aquí puedes encontrar toda la información 
                            relevante sobre los métodos de pago disponibles y los pasos a seguir para completar 
                            tu transacción de manera segura.
                        </p>
                        
                        <p class="payment-text">
                            Por favor, revisa cuidadosamente los detalles de tu compra antes de proceder al pago. 
                            Una vez confirmado, recibirás un correo electrónico con los detalles de tu inscripción 
                            y los accesos correspondientes.
                        </p>
                    </div>
                </div>
                
                <div class="buttons-container">
                    <a href="evento.php?id=<?= htmlspecialchars($_GET['id']) ?>" class="payment-button back-button">Volver Atrás</a>
                </div>
            </div>
        </div>
        <!-- Contenedor de la imagen -->
        <div class="image-card">
            <img src="../assets/img/ejemplo.jpg" alt="Métodos de pago" class="payment-image" id="downloadable-image">
            <button class="download-button" id="download-btn">Descargar Imagen</button>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.getElementById('download-btn').addEventListener('click', function() {
            const image = document.getElementById('downloadable-image');
            const imageUrl = image.src;
            
            // Crear un enlace temporal
            const link = document.createElement('a');
            link.href = imageUrl;
            
            // Obtener el nombre del archivo de la URL
            const fileName = imageUrl.split('/').pop();
            link.download = fileName || 'imagen-descargada.jpg';
            
            // Simular clic en el enlace
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>
</html>