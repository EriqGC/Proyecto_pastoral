<?php
//include('../includes/conects.php');
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Obtener supervisores desde la base de datos
$supervisores = [];
$query = "SELECT nombre, apellido_p, apellido_m, correo FROM PTL_USUARIOS WHERE puesto IN ('Supervisor', 'Coordinador', 'Administrador')";
$result = mysqli_query($link, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $supervisores[] = [
        'nombre_completo' => $row['nombre'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m'],
        'correo' => $row['correo']
    ];
}
mysqli_free_result($result);

// Obtener vehículos desde la base de datos
$vehiculos = [];
$queryVehiculos = "SELECT DISTINCT vehiculo FROM PTL_TRANSPORTE WHERE vehiculo IS NOT NULL";
$resultVehiculos = mysqli_query($link, $queryVehiculos);
while ($row = mysqli_fetch_assoc($resultVehiculos)) {
    $vehiculos[] = $row['vehiculo'];
}
mysqli_free_result($resultVehiculos);
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    /* Estilos para el modal */
    .ptl-event-modal {
        display: none;
        position: fixed;
        z-index: 100000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .ptl-modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        width: 80%;
        max-width: 900px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        animation: ptl-modalopen 0.3s;
        height: 500px;
        overflow: auto;
    }

    @keyframes ptl-modalopen {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ptl-close-modal {
        color: var(--color-gris);
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .ptl-close-modal:hover {
        color: var(--color-gris-oscuro);
    }

    .ptl-modal-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    /* Tabs */
    .ptl-tab-container {
        display: flex;
        border-bottom: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .ptl-tab {
        padding: 10px 20px;
        cursor: pointer;
        background: var(--color-blanco);
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
        transition: all 0.3s;
    }

    .ptl-tab.active {
        background: var(--color-rojo);
        color: var(--color-blanco);
    }

    .ptl-tab:hover:not(.active) {
        background: var(--color-gris-claro);
    }

    .ptl-tab-content {
        display: none;
        padding: 15px 0;
    }

    .ptl-tab-content.active {
        display: block;
    }

    /* Formulario */
    .ptl-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .ptl-form-column {
        display: flex;
        flex-direction: column;
    }

    .ptl-image-column {
        display: flex;
        flex-direction: column;
        align-items: left;
    }

    .ptl-form-label {
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--color-negro);
        text-align: left;
    }

    .ptl-form-input,
    .ptl-form-select,
    .ptl-form-textarea {
        padding: 10px;
        border: 1px solid var(--color-gris-claro);
        border-radius: 4px;
        margin-bottom: 15px;
        width: 100%;
        box-sizing: border-box;
        font-size: 14px;
    }

    .ptl-form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .ptl-form-select {
        appearance: none;
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23333" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px top 50%;
        background-size: 16px;
    }

    .ptl-preview-image {
        max-width: 100%;
        height: auto;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: none;
    }

    /* Itinerario */
    .ptl-itinerario-container {
        margin-top: 15px;
    }

    .ptl-hora-group {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
        justify-content: center;
    }

    .ptl-hora-group input[type="time"] {
        width: auto;
    }

    /* Botones */
    .ptl-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .ptl-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .ptl-btn-primary {
        background-color: var(--color-rojo);
        color: white;
    }

    .ptl-btn-primary:hover {
        background-color: var(--color-rojo-presionado);
    }

    .ptl-btn-secondary {
        background-color: var(--color-gris);
        color: white;
    }

    .ptl-btn-secondary:hover {
        background-color: var(--color-gris-oscuro);
    }

    .ptl-btn-add {
        background-color: #28a745;
        color: white;
        padding: 8px 15px;
    }

    .ptl-btn-remove {
        background-color: var(--color-rojo);
        color: white;
        padding: 8px 15px;
    }

    /* Mapa */
    .ptl-map-container {
        height: 300px;
        width: 100%;
        background: #f8f9fa;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    #map {
        height: 100%;
        width: 100%;
    }

    .ptl-map-loader {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ptl-modal-content {
            width: 90%;
            margin: 10% auto;
            padding: 15px;
        }

        .ptl-form-grid {
            grid-template-columns: 1fr;
        }

        .ptl-tab-container {
            flex-wrap: wrap;
        }

        .ptl-tab {
            margin-bottom: 5px;
        }

        .ptl-hora-group {
            flex-wrap: wrap;
        }

        .ptl-modal-actions {
            flex-direction: column;
        }

        .ptl-btn {
            width: 100%;
        }
    }
</style>

<div id="crearEventoModal" class="ptl-event-modal">
    <div class="ptl-modal-content">
        <span class="ptl-close-modal">&times;</span>
        <h2 class="ptl-modal-title">Crear Nuevo Evento</h2>
        <div class="ptl-tab-container">
            <div class="ptl-tab active" data-tab="detallesEvento">Detalles</div>
            <div class="ptl-tab" data-tab="itinerario">Itinerario</div>
            <div class="ptl-tab" data-tab="ubicacion">Ubicación</div>
        </div>
        <form id="formCrearEvento" action="guardar_evento.php" method="POST" enctype="multipart/form-data">
            <p>*Todos los campos son obligatorios, en caso de no serlo no podra rellenarlo.</p>
            <!-- Detalles -->
            <div id="detallesEvento" class="ptl-tab-content active">
                <div class="ptl-form-grid">
                    <div class="ptl-form-column">
                        <label class="ptl-form-label">Tipo:</label>
                        <select class="ptl-form-select" name="tipo" id="tipoEvento" required>
                            <option value="Evento">Evento</option>
                            <option value="Noticia">Noticia</option>
                        </select>

                        <label class="ptl-form-label">Nombre del Evento:</label>
                        <input class="ptl-form-input" type="text" name="nombre" required>

                        <label class="ptl-form-label">Costo:</label>
                        <input class="ptl-form-input" type="number" name="costo" id="costoInput" required>

                        <label class="ptl-form-label">Recurrencia:</label>
                        <select class="ptl-form-select" name="recurrencia" id="recurenciaEvento" required>
                            <option value="Unico" <?= ($evento['recurrencia'] ?? '') === 'unico' ? 'selected' : '' ?>>Unico</option>
                            <option value="Semanal" <?= ($evento['recurrencia'] ?? '') === 'semanal' ? 'selected' : '' ?>>Semanal</option>
                            <option value="Mensual" <?= ($evento['recurrencia'] ?? '') === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                            <option value="Anual" <?= ($evento['recurrencia'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual</option>
                        </select>

                        <label class="ptl-form-label">Fecha Inicio:</label>
                        <input class="ptl-form-input" type="date" name="fecha_inicio" required>

                        <label class="ptl-form-label">Fecha Fin:</label>
                        <input class="ptl-form-input" type="date" name="fecha_fin" required>

                        <label class="ptl-form-label">Descripción:</label>
                        <textarea class="ptl-form-textarea" name="descripcion" required></textarea>
                    </div>
                    <div class="ptl-image-column">
                        <label class="ptl-form-label">Supervisor:</label>
                        <select class="ptl-form-select" name="supervisor" id="supervisor" required>
                            <?php foreach ($supervisores as $sup): ?>
                                <option value="<?= htmlspecialchars($sup['correo']) ?>" <?= ($evento['supervisor'] ?? '') === $sup['correo'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sup['nombre_completo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="ptl-form-label">Categoría:</label>
                        <select class="ptl-form-select" name="categoria" required>
                            <option value="Práctica Misionera">Práctica Misionera</option>
                            <option value="Misión">Misión</option>
                            <option value="Otro">Otro</option>
                        </select>

                        <label class="ptl-form-label">Carta Compromiso:</label>
                        <input class="ptl-form-input" type="file" name="carta_compromiso" id="cartaInput" accept=".pdf">

                        <label class="ptl-form-label">Imagen del Evento:</label>
                        <input class="ptl-form-input" type="file" name="imagen" accept="image/*" required>
                        <img id="previewImagen" class="ptl-preview-image" src="#" alt="Vista previa">
                    </div>
                </div>
            </div>

            <!-- Itinerario -->
            <div id="itinerario" class="ptl-tab-content" id="itinerario">
                <div class="ptl-itinerario-container">
                    <div class="ptl-hora-group">
                        <label class="ptl-form-label">Hora Inicio:</label>
                        <input class="ptl-form-input" type="time" name="hora_inicio[]" required>
                        <label class="ptl-form-label">Actividad:</label>
                        <input class="ptl-form-input" type="text" name="actividad" required>
                    </div>
                    <div id="ptl-hora-groups-container"></div>
                    <div class="ptl-hora-group">
                        <button type="button" class="ptl-btn ptl-btn-add agregar-hora">+ Agregar hora</button>
                    </div>
                    <div class="ptl-hora-group">
                        <label class="ptl-form-label">Hora fin:</label>
                        <input class="ptl-form-input" type="time" name="hora_inicio[]" required>
                        <label class="ptl-form-label">Actividad:</label>
                        <input class="ptl-form-input" type="text" name="actividad" required>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div id="ubicacion" class="ptl-tab-content">
                <div class="ptl-form-grid">
                    <div class="ptl-form-column">
                        <label class="ptl-form-label">¿Requiere Viaje?</label>
                        <select class="ptl-form-select" name="viaje" id="viajeSelect" required>
                            <option value="Si">Sí</option>
                            <option value="No" selected>No</option>
                        </select>

                        <label class="ptl-form-label">Ubicación Inicial:</label>
                        <input class="ptl-form-input" type="text" id="origen" name="origen" placeholder="Ej: Ciudad de México">

                        <label class="ptl-form-label">Ubicación Destino:</label>
                        <input class="ptl-form-input" type="text" id="destino" name="destino" placeholder="Ej: Guadalajara">

                        <label class="ptl-form-label">Vehículo:</label>
                        <select class="ptl-form-select" name="vehiculo" id="vehiculoSelect">
                            <option value="">Seleccione un vehículo</option>
                            <?php foreach ($vehiculos as $vehiculo): ?>
                                <option value="<?= htmlspecialchars($vehiculo) ?>"><?= htmlspecialchars($vehiculo) ?></option>
                            <?php endforeach; ?>
                            <option value="otro">Otro (especificar)</option>
                        </select>
                        <input class="ptl-form-input" type="text" name="vehiculo_otro" id="vehiculoOtroInput" style="display: none; margin-top: 5px;" placeholder="Especificar vehículo">
                        <input class="ptl-form-input" type="number" name="asientos" id="asientosInput" style="display: none; margin-top: 5px;" placeholder="Especificar asientos">
                    </div>
                    <div class="ptl-form-column">
                        <div class="ptl-map-container">
                            <div id="map"></div>
                            <div id="loader" class="ptl-map-loader">Calculando ruta...</div>
                        </div>
                    </div>
                </div>
                <div class="ptl-modal-actions">
                    <button type="button" class="ptl-btn ptl-btn-secondary cancelar-modal">Cancelar</button>
                    <button type="submit" class="ptl-btn ptl-btn-primary">Guardar Evento</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function initMap() {
        // Verificar si el mapa ya está inicializado
        if (window.mapInitialized) return;

        const map = L.map('map', {
            preferCanvas: true,
            zoomControl: false,
            attributionControl: false
        }).setView([19.4326, -99.1332], 7);

        // Capa base mejorada
        L.tileLayer('https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Controles del mapa
        L.control.zoom({
            position: 'topright'
        }).addTo(map);
        L.control.attribution({
            position: 'bottomright'
        }).addTo(map);

        window.map = map; // Guardar referencia global
        window.mapInitialized = true;
    }

    // Inicializar el mapa cuando se muestre el modal
    document.getElementById('crearEventoModal').addEventListener('shown', function() {
        initMap();
        // Forzar redimensionamiento del mapa
        setTimeout(() => window.map.invalidateSize(), 100);
    });
    // Inicializar mapa con OpenStreetMap optimizado
    const map = L.map('map', {
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false
    }).setView([19.4326, -99.1332], 7);

    // Capa base mejorada
    L.tileLayer('https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Controles del mapa
    L.control.zoom({
        position: 'topright'
    }).addTo(map);
    L.control.attribution({
        position: 'bottomright'
    }).addTo(map);

    let startMarker, endMarker, routeLine;
    const geocodeCache = new Map();
    let currentController = null;

    // Función mejorada de geocodificación con cache y timeout
    async function geocode(query) {
        const cleanQuery = query.trim().toLowerCase();

        if (geocodeCache.has(cleanQuery)) {
            return geocodeCache.get(cleanQuery);
        }

        try {
            const controller = new AbortController();
            currentController = controller;
            const timeoutId = setTimeout(() => controller.abort(), 5000);

            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(cleanQuery)}&format=json&addressdetails=1&limit=3&countrycodes=mx`, {
                    signal: controller.signal
                }
            );

            clearTimeout(timeoutId);

            if (!response.ok) throw new Error('Error en geocodificación');

            const data = await response.json();
            geocodeCache.set(cleanQuery, data);
            return data;
        } catch (error) {
            geocodeCache.set(cleanQuery, []);
            return [];
        } finally {
            currentController = null;
        }
    }

    // Función de validación mejorada
    function isValidCoordinate(coords) {
        return coords &&
            Math.abs(coords[0]) <= 90 &&
            Math.abs(coords[1]) <= 180 &&
            !isNaN(coords[0]) &&
            !isNaN(coords[1]);
    }

    // Función principal mejorada
    async function calcularRuta() {
        try {
            // Cancelar solicitud anterior
            if (currentController) currentController.abort();

            const origen = document.getElementById('origen').value.trim();
            const destino = document.getElementById('destino').value.trim();

            // Validación básica de entrada
            if (!origen || !destino || origen.length < 4 || destino.length < 4) {
                return;
            }

            // Mostrar loader
            document.getElementById('loader').style.display = 'block';

            // Geocodificación paralela
            const [startResults, endResults] = await Promise.all([
                geocode(origen),
                geocode(destino)
            ]);

            // Validar resultados
            if (!startResults.length || !endResults.length) {
                throw new Error('No se encontraron las ubicaciones');
            }

            const startCoords = [
                parseFloat(startResults[0].lat),
                parseFloat(startResults[0].lon)
            ];

            const endCoords = [
                parseFloat(endResults[0].lat),
                parseFloat(endResults[0].lon)
            ];

            if (!isValidCoordinate(startCoords)) throw new Error('Coordenadas iniciales inválidas');
            if (!isValidCoordinate(endCoords)) throw new Error('Coordenadas finales inválidas');

            // Calcular ruta
            const controller = new AbortController();
            currentController = controller;
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            const response = await fetch(
                `https://router.project-osrm.org/route/v1/driving/${startCoords[1]},${startCoords[0]};${endCoords[1]},${endCoords[0]}?overview=full`, {
                    signal: controller.signal
                }
            );

            clearTimeout(timeoutId);
            currentController = null;

            if (!response.ok) throw new Error('Error en el servidor de rutas');

            const data = await response.json();

            if (data.code !== 'Ok' || !data.routes?.[0]) {
                throw new Error('No hay ruta disponible entre estos puntos');
            }

            // Actualizar mapa
            if (routeLine) map.removeLayer(routeLine);
            const coords = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
            routeLine = L.polyline(coords, {
                color: '#3388ff',
                weight: 4,
                opacity: 0.7
            }).addTo(map);

            // Actualizar marcadores
            if (startMarker) map.removeLayer(startMarker);
            if (endMarker) map.removeLayer(endMarker);

            startMarker = L.marker(startCoords, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map).bindPopup('Origen');

            endMarker = L.marker(endCoords, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map).bindPopup('Destino');

            // Ajustar vista
            map.fitBounds(routeLine.getBounds(), {
                padding: [50, 50],
                maxZoom: 12
            });

        } catch (error) {
            if (error.name !== 'AbortError') {
                showError(error.message);
            }
        } finally {
            document.getElementById('loader').style.display = 'none';
            currentController = null;
        }
    }

    // Debounce mejorado
    function createDebouncer(delay = 1500) {
        let timeout;
        return (callback) => {
            clearTimeout(timeout);
            timeout = setTimeout(callback, delay);
        };
    }

    const debouncer = createDebouncer();

    // Event listeners optimizados
    ['origen', 'destino'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => {
            debouncer(() => calcularRuta());
        });
    });

    // Función de manejo de errores
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.style.position = 'fixed';
        errorDiv.style.top = '20px';
        errorDiv.style.left = '50%';
        errorDiv.style.transform = 'translateX(-50%)';
        errorDiv.style.backgroundColor = '#e74c3c';
        errorDiv.style.color = 'white';
        errorDiv.style.padding = '10px 20px';
        errorDiv.style.borderRadius = '4px';
        errorDiv.style.zIndex = '2000';
        errorDiv.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 5000);
    }

    // Funcionalidad de pestañas
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.ptl-tab');
        const tabContents = document.querySelectorAll('.ptl-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remover clases activas
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Activar pestaña y contenido
                this.classList.add('active');
                const targetTab = document.getElementById(this.dataset.tab);
                targetTab.classList.add('active');
            });
        });

        // Mostrar/ocultar campos según tipo
        const tipoEvento = document.getElementById('tipoEvento');
        tipoEvento.addEventListener('change', function() {
            const isNoticia = this.value === 'Noticia';

            // Deshabilitar campos específicos
            document.getElementById('cartaInput').disabled = isNoticia;
            document.getElementById('costoInput').disabled = isNoticia;
            document.getElementById('supervisor').disabled = isNoticia;
            document.getElementById('fecha_fin').disabled = isNoticia;
            document.getElementById('recurenciaEvento').disabled = isNoticia;

            // Deshabilitar campos de Ubicación/Viaje
            document.getElementById('viajeSelect').disabled = isNoticia;
            document.getElementById('destino').disabled = isNoticia;
            document.getElementById('vehiculoSelect').disabled = isNoticia;

            // Obtener las pestañas y sus contenidos
            const tabItinerario = document.querySelector('.ptl-tab[data-tab="itinerario"]');
            const tabUbicacion = document.querySelector('.ptl-tab[data-tab="ubicacion"]');
            const contentItinerario = document.getElementById('itinerario');
            const contentUbicacion = document.getElementById('ubicacion');

            if (isNoticia) {
                // Deshabilitar las pestañas (no se pueden hacer clic)
                tabItinerario.classList.add('disabled');
                tabUbicacion.classList.add('disabled');

                // Ocultar los contenidos
                contentItinerario.style.display = 'none';
                contentUbicacion.style.display = 'none';

                // Si estaba activa una pestaña deshabilitada, volver a "Detalles"
                const activeTab = document.querySelector('.ptl-tab.active');
                if (activeTab.dataset.tab === 'itinerario' || activeTab.dataset.tab === 'ubicacion') {
                    document.querySelector('.ptl-tab[data-tab="detallesEvento"]').click();
                }
            } else {
                // Restaurar pestañas y contenidos
                tabItinerario.classList.remove('disabled');
                tabUbicacion.classList.remove('disabled');
                contentItinerario.style.display = 'block';
                contentUbicacion.style.display = 'block';
            }
        });

        // Agregar campos de hora dinámicos
        document.querySelector('.agregar-hora').addEventListener('click', function() {
            const newGroup = document.createElement('div');
            newGroup.className = 'ptl-hora-group';
            newGroup.innerHTML = `
            <label class="ptl-form-label">Hora:</label>
            <input class="ptl-form-input" type="time" name="hora_inicio[]">
            <label class="ptl-form-label">Actividad:</label>
            <input class="ptl-form-input" type="text" name="actividad" required>
            <button type="button" class="ptl-btn ptl-btn-remove eliminar-hora">-</button>
        `;
            document.getElementById('ptl-hora-groups-container').appendChild(newGroup);
        });

        // Eliminar campos de hora
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('eliminar-hora')) {
                e.target.closest('.ptl-hora-group').remove();
            }
        });

        // Preview de imagen
        document.querySelector('input[name="imagen"]').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('previewImagen');
                preview.style.display = 'block';
                preview.src = reader.result;
            }
            if (e.target.files[0]) {
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Autocompletar ubicaciones
        async function autocompletar(inputId) {
            const input = document.getElementById(inputId);
            const query = input.value;

            if (query.length < 3) return;

            const results = await geocode(query);
            const datalist = document.createElement('datalist');
            datalist.id = `${inputId}-list`;

            results.forEach(result => {
                const option = document.createElement('option');
                option.value = result.display_name;
                datalist.appendChild(option);
            });

            // Limpiar lista anterior
            const existingList = document.getElementById(`${inputId}-list`);
            if (existingList) existingList.remove();

            input.setAttribute('list', `${inputId}-list`);
            document.body.appendChild(datalist);
        }

        // Modificar los event listeners para autocompletar
        document.getElementById('origen').addEventListener('input', () => {
            autocompletar('origen');
            debouncer(() => calcularRuta());
        });

        document.getElementById('destino').addEventListener('input', () => {
            autocompletar('destino');
            debouncer(() => calcularRuta());
        });

        // Mostrar/ocultar campos de transporte según selección
        const Viaje = document.getElementById('viajeSelect');
        Viaje.addEventListener('change', function() {
            const requiereViaje = this.value === 'No';
            document.getElementById('destino').disabled = requiereViaje;
            document.getElementById('vehiculoSelect').disabled = requiereViaje;
            document.getElementById('vehiculoOtroInput').disabled = requiereViaje;
            document.getElementById('asientosInput').disabled = requiereViaje;
        });

        // Mostrar/ocultar campo de otro vehículo
        document.getElementById('vehiculoSelect').addEventListener('change', function() {
            const showOtroInput = this.value === 'otro';
            document.getElementById('vehiculoOtroInput').style.display = showOtroInput ? 'block' : 'none';
            document.getElementById('vehiculoOtroInput').required = showOtroInput;
            document.getElementById('asientosInput').style.display = showOtroInput ? 'block' : 'none';
            document.getElementById('asientosInput').required = showOtroInput;
        });

        // Cerrar modal
        document.querySelector('.ptl-close-modal').addEventListener('click', function() {
            document.getElementById('crearEventoModal').style.display = 'none';
        });

        document.querySelector('.cancelar-modal').addEventListener('click', function() {
            document.getElementById('crearEventoModal').style.display = 'none';
        });
    });
</script>