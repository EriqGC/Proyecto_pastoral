<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Ejemplo</title>
    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: modalopen 0.3s;
        }
        
        @keyframes modalopen {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        /* Estilo para el botón de prueba */
        #openModal {
            padding: 10px 20px;
            background-color: #e63946;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        #openModal:hover {
            background-color: #c1121f;
        }
    </style>
</head>
<body>
    <!-- Botón para abrir el modal -->
    <button id="openModal">Abrir Modal</button>
    
    <!-- El modal en sí -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Título del Modal</h2>
            <p>Este es el contenido de tu ventana emergente. Puedes poner aquí formularios, texto, imágenes, etc.</p>
            <div>
                <!-- Ejemplo de contenido -->
                <form>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre"><br><br>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"><br><br>
                    
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Obtener elementos
        const modal = document.getElementById("myModal");
        const btn = document.getElementById("openModal");
        const span = document.getElementsByClassName("close")[0];
        
        // Abrir modal al hacer clic en el botón
        btn.onclick = function() {
            modal.style.display = "block";
        }
        
        // Cerrar modal al hacer clic en la X
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>