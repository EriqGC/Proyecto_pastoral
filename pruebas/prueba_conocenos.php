<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastoral Misionera</title>
    <style>
        /* Variables CSS */
        :root {
            --rojo: #FF0000;
            --rojo-cansado: #EE1B2C;
            --blanco: #F0F0F0;
            --negro: #0F0F0F;
            --gris-claro: #ACB1B5;
            --gris-separador: #E2E2E2;
            --gris-oscuro: #50555B;
        }

        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inria Sans', sans-serif;
            background: white;
            color: var(--negro);
            line-height: 1.6;
        }

        /* Estructura principal */
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            position: relative;
        }

        /* Header */
        header {
            width: 100%;
            height: 88px;
            background: var(--blanco);
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .logo {
            width: 135px;
            height: 34px;
        }

        .nav-menu {
            display: flex;
            gap: 10px;
        }

        .nav-item {
            padding: 10px 20px;
            background: var(--blanco);
            border-bottom-right-radius: 5px;
            border-bottom-left-radius: 5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            text-decoration: underline;
            color: var(--negro);
            font-size: 20px;
            line-height: 21px;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-name {
            font-size: 25px;
            font-weight: 300;
        }

        .user-icon {
            width: 50px;
            height: 50px;
            background: var(--negro);
        }

        .login-btn {
            width: 119px;
            height: 55px;
            background: var(--rojo-cansado);
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            color: var(--blanco);
            font-weight: 700;
            text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            padding: 10px 20px;
        }

        /* Hero Section */
        .hero {
            width: 100%;
            height: 495px;
            position: relative;
            margin-top: 88px;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(2px);
            box-shadow: 4px 4px 4px;
        }

        .hero-title {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            text-align: center;
            color: white;
            font-size: 5vw;
            font-weight: 700;
            text-shadow: 0px 4px 4px rgba(255, 255, 255, 0.25);
        }

        /* Secciones */
        .section {
            width: 100%;
            padding: 60px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 4vw;
            margin-bottom: 40px;
            font-family: 'Inria Sans', sans-serif;
            font-weight: 400;
        }

        .section-subtitle {
            text-align: center;
            font-size: 3.5vw;
            margin-bottom: 40px;
            font-family: 'Jomhuria', cursive;
            font-weight: 400;
        }

        /* Sección Conoce las misiones */
        .about-missions {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            padding: 60px 20px;
        }

        @media (min-width: 768px) {
            .about-missions {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .about-text {
            width: 100%;
            font-size: 18px;
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        @media (min-width: 768px) {
            .about-text {
                width: 40%;
                margin-bottom: 0;
            }
        }

        .about-image {
            width: 100%;
            height: auto;
            background: #D9D9D9;
        }

        @media (min-width: 768px) {
            .about-image {
                width: 50%;
            }
        }

        /* Actividades */
        .activities {
            background: var(--gris-separador);
            padding: 60px 20px;
        }

        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .activity-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .activity-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .activity-content {
            padding: 20px;
            text-align: center;
            font-style: italic;
            font-size: 18px;
        }

        /* Ruta Formativa */
        .formative-route {
            background: var(--gris-separador);
            padding: 60px 20px;
        }

        .route-items {
            display: flex;
            flex-direction: column;
            gap: 60px;
            margin-top: 60px;
        }

        .route-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        @media (min-width: 768px) {
            .route-item {
                flex-direction: row;
                justify-content: space-between;
            }
            
            .route-item:nth-child(even) {
                flex-direction: row-reverse;
            }
        }

        .route-number {
            width: 80px;
            height: 80px;
            background: var(--rojo);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 40px;
            font-weight: 400;
            margin-bottom: 20px;
            box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.25);
        }

        @media (min-width: 768px) {
            .route-number {
                width: 120px;
                height: 120px;
                font-size: 60px;
                margin-bottom: 0;
            }
        }

        .route-details {
            width: 100%;
            text-align: center;
            padding: 20px;
        }

        @media (min-width: 768px) {
            .route-details {
                width: 60%;
                text-align: left;
            }
            
            .route-item:nth-child(even) .route-details {
                text-align: right;
            }
        }

        .route-title {
            font-size: 24px;
            font-style: italic;
            margin-bottom: 10px;
        }

        .route-date {
            font-size: 24px;
            font-style: italic;
            color: var(--rojo-cansado);
        }

        .route-line {
            width: 100%;
            height: 10px;
            background: #4F6360;
            margin: 20px 0;
        }

        @media (min-width: 768px) {
            .route-line {
                position: absolute;
                width: 60%;
                height: 10px;
                top: 50%;
                left: 20%;
                transform: rotate(5deg);
                z-index: 1;
            }
            
            .route-item:nth-child(even) .route-line {
                transform: rotate(-5deg);
            }
        }

        /* Experiencia */
        .experience {
            padding: 60px 20px;
            text-align: center;
            background: white;
        }

        .experience-title {
            font-size: 5vw;
            font-family: 'Jomhuria', cursive;
            font-weight: 400;
            margin-bottom: 40px;
        }

        .experience-circle {
            width: 200px;
            height: 200px;
            background: var(--rojo);
            border-radius: 50%;
            margin: 40px auto;
        }

        .experience-small-circle {
            width: 80px;
            height: 80px;
            background: var(--rojo-cansado);
            border-radius: 50%;
            margin: 20px auto;
        }

        .download-btn {
            display: inline-block;
            padding: 15px 40px;
            border-radius: 50px;
            border: 2px solid black;
            color: black;
            font-size: 30px;
            font-family: 'Jomhuria', cursive;
            font-weight: 400;
            text-decoration: none;
            margin-top: 40px;
        }

        /* Footer */
        footer {
            background: var(--gris-oscuro);
            color: var(--blanco);
            padding: 60px 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .footer-section h3 {
            font-size: 30px;
            font-family: 'Jomhuria', cursive;
            font-weight: 400;
            color: var(--gris-claro);
            margin-bottom: 20px;
        }

        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .footer-contact-icon {
            width: 20px;
            height: 20px;
            background: var(--blanco);
        }

        .footer-link {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            color: var(--blanco);
            text-decoration: none;
        }

        .footer-social-icon {
            width: 20px;
            height: 20px;
            background: var(--blanco);
        }

        .footer-map {
            width: 100%;
            height: 240px;
            margin-top: 20px;
        }

        .footer-copyright {
            text-align: center;
            margin-top: 40px;
            font-size: 16px;
            font-family: 'Jomhuria', cursive;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Hero Section -->
    <?php
        include '../includes/hero.php';
        createHero("Tu fé en acción");
    ?>

    <!-- Conoce las misiones -->
    <section class="about-missions">
        <h2 class="section-title">Conoce las misiones</h2>
        
        <div class="about-text">
            Un llamado personal. Dirigido a cada uno para una entrega de amor con los demás. 
            Es un medio para poder compartir la palabra de Dios y se integra por personas que 
            tienen el deseo de poder compartir y aprender de la fe católica.
        </div>
        
        <img class="about-image" src="https://placehold.co/618x581" alt="Sobre las misiones">
    </section>

    <!-- Actividades a realizar -->
    <section class="activities">
        <h2 class="section-title">Actividades a Realizar</h2>
        <h3 class="section-subtitle">ACTIVIDADES A REALIZAR</h3>
        
        <div class="activities-grid">
            <!-- Actividad 1 -->
            <div class="activity-card">
                <img class="activity-image" src="https://placehold.co/291x179" alt="Actividad 1">
                <div class="activity-content">
                    Actividades y dinámicas con niños
                </div>
            </div>
            
            <!-- Actividad 2 -->
            <div class="activity-card">
                <img class="activity-image" src="https://placehold.co/291x179" alt="Actividad 2">
                <div class="activity-content">
                    Pláticas de formación
                </div>
            </div>
            
            <!-- Actividad 3 -->
            <div class="activity-card">
                <img class="activity-image" src="https://placehold.co/291x179" alt="Actividad 3">
                <div class="activity-content">
                    Celebraciones de la palabra, rosarios, meditación del evangelio, oración en comunidad.
                </div>
            </div>
            
            <!-- Actividad 4 -->
            <div class="activity-card">
                <img class="activity-image" src="https://placehold.co/291x179" alt="Actividad 4">
                <div class="activity-content">
                    Visiteos
                </div>
            </div>
        </div>
    </section>

    <!-- Ruta Formativa -->
    <section class="formative-route">
        <h2 class="section-subtitle">RUTA FORMATIVA</h2>
        
        <div class="route-items">
            <!-- Item 1 -->
            <div class="route-item">
                <div class="route-number">1</div>
                <div class="route-details">
                    <div class="route-title">Encuentro Misionero</div>
                    <div class="route-date">26 de octubre</div>
                    <div class="route-title">10am a 3pm</div>
                </div>
                <div class="route-line"></div>
            </div>
            
            <!-- Item 2 -->
            <div class="route-item">
                <div class="route-number">2</div>
                <div class="route-details">
                    <div class="route-title">Escuela Misionera</div>
                    <div class="route-date">15, 16 y 17 de Noviembre</div>
                </div>
                <div class="route-line"></div>
            </div>
            
            <!-- Item 3 -->
            <div class="route-item">
                <div class="route-number">3</div>
                <div class="route-details">
                    <div class="route-title">Vanguardia</div>
                    <div class="route-date">Noviembre</div>
                </div>
                <div class="route-line"></div>
            </div>
            
            <!-- Item 4 -->
            <div class="route-item">
                <div class="route-number">4</div>
                <div class="route-details">
                    <div class="route-title">Retiro + Misiones</div>
                    <div class="route-date">13 al 19 de Diciembre</div>
                </div>
                <div class="route-line"></div>
            </div>
            
            <!-- Item 5 -->
            <div class="route-item">
                <div class="route-number">5</div>
                <div class="route-details">
                    <div class="route-title">Reencuentro Misionero</div>
                    <div class="route-date">1° Semana de Clases de Enero</div>
                </div>
                <div class="route-line"></div>
            </div>
        </div>
    </section>

    <!-- Experiencia -->
    <section class="experience">
        <h2 class="experience-title">¿TE GUSTARÍA VIVIR <br>ESTA EXPERIENCIA?</h2>
        
        <div class="experience-circle"></div>
        <div class="experience-small-circle"></div>
        
        <a href="#" class="download-btn">Descargar Ruta</a>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>