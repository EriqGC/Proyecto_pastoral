<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="es">

    <head>
        <title>Index</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
        <link rel="stylesheet" href="assets/css/global.css">
        <link rel="stylesheet" href="assets/css/index.css">
    </head>

    <body>
        <div id="navbar">
        <?php
        include 'includes/navbar_index.php';
        ?>
        </div>
        <div id="hero">
        <?php
        include 'includes/hero_index.php';
        createHero("Que Cristo reine aquí y en todo el mundo");
        ?>    
        <div class="white-bg-container">
            <div class="center-tittle"><h1>Conoce las misiones</h1></div>
            <div class="text-left">Un llamado personal. Dirigido a cada uno para una entrega de amor con los demás. 
            Es un medio para poder compartir la palabra de Dios y se integra por personas que 
            tienen el deseo de poder compartir y aprender de la fe católica.</div> 
            <div class="img-left"><img src="assets/img/img-index-1.jpg"></div>
        </div>

        <div class="gray-bg-container">
            <div class="center-tittle"><h1>Actividades a realizar</h1></div>
            <!--<div class="slider-body">-->
                <div class="slider-container swiper">
                    <div class="slider-wrapper">
                        <div class="card-list swiper-wrapper">

                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-1.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>
                            
                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-2.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>

                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-2.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>


                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-3.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>

                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-3.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>

                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-4.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>

                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-5.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>

                            <div class="card-item swiper-slide">
                                <img src="assets/img/actividades-ruleta-index/img-roullet-6.jpg" class="event-image">
                                <h2 class="event-title">Actividades y dinámicas con niños</h2>
                            </div>

                        </div>
                        
                        <div class="swiper-pagination"></div>
                        <!-- If we need navigation buttons -->
                        <div class="swiper-slide-button swiper-button-prev"></div>
                        <div class="swiper-slide-button swiper-button-next"></div>
                    </div>
                </div>
            <!--</div>-->
            
        </div>

        <div  style="overflow:hidden; display: flex;">
            <h2 style="font-size: 70pt; font-family: 'neutraface'; margin-top: 220px; margin-left: 50px">
                ¿Te gustaría vivir esta experiencia?</h2>
            <div style="width: 1100px; height: 800px; background-color: #EE1B2C; border-radius: 50%; transform: translateX(60%) translateY(5%); overflow: hidden;"></div>
            <div style="width: 500px; height: 400px; background-color: #EE1B2C; border-radius: 50%; transform: translateX(-150%)  translateY(120%);z-index: 2; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);"></div>
        </div>

        <div style="display:flex; flex-direction:column; align-items:center; justify-content: center; background-color: #E2E2E2;">
            <img src="assets/img/cheat.jpg" style="margin: 150px; width: 60%;">
            <button style="margin-bottom: 50px; border-radius: 25%; background-color: #E2E2E2; font-size:28pt; padding: 30px 80px; border-width: medium;">Descargar ruta</button>
        </div>

        <?php include 'includes/footer_index.php'; ?> 
        <script type="module">
            import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'

 

        
            const swiper = new Swiper('.slider-wrapper', {
            // Optional parameters
            direction: 'horizontal',
            loop: true,
            grabCursor: true,
            spaceBetween: 30,

            // If we need pagination
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },

            //responsive breakpoints
            breakpoints:{
                0:{
                    slidesPerView: 1
                },
                620:{
                    slidesPerView: 2
                },
                1024:{
                    slidesPerView: 4
                }
            }
            
            });
        </script>
    </body>

</html>