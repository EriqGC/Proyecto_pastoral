<?php
function convertirUTF8($array)
{
    array_walk_recursive($array, function (&$item) {
        if (is_string($item) && !mb_detect_encoding($item, 'UTF-8', true)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1');
        }
    });
    return $array;
}
session_start();
include('../includes/conects.php');
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Obtener noticias de la base de datos
$queryNoticias = "SELECT 
    imagen AS img, 
    nombre, 
    descripcion, 
    categoria, 
    fecha_inicio,
    ubicacion,
    tipo 
    FROM PTL_EVENTOS WHERE tipo = 'Noticia'";
$result = mysqli_query($link, $queryNoticias);

$noticias = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Prepara los datos para JavaScript
        $row['titulo'] = $row['nombre'];
        $row['fecha'] = $row['fecha_inicio'];
        $noticias[] = $row;
    }
} else {
    error_log("Error en consulta de noticias: " . mysqli_error($link));
    $noticias = [];
}

// Obtener eventos de la base de datos
$queryEventos = "SELECT 
    id,
    imagen AS img, 
    nombre, 
    descripcion, 
    categoria, 
    fecha_inicio AS fechainicio,
    ubicacion,
    tipo 
    FROM PTL_EVENTOS WHERE tipo = 'Evento'";
$resultEventos = mysqli_query($link, $queryEventos);

$eventos = array();
if ($resultEventos) {
    while ($row = mysqli_fetch_assoc($resultEventos)) {
        $row['titulo'] = $row['nombre'];
        $eventos[] = $row;
    }
} else {
    error_log("Error en consulta de eventos: " . mysqli_error($link));
    $eventos = [];
}
$noticias = convertirUTF8($noticias);
$jsonNoticias = json_encode($noticias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$eventos = convertirUTF8($eventos);
$jsonEventos = json_encode($eventos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/anuncios2.css">
    <script>
        window.noticiasData = <?php echo json_encode($noticias ?? []); ?>;
        window.eventosData = <?php echo json_encode($eventos ?? []); ?>;
        console.log("Total de noticias:", noticias.length);
        console.log("Noticias:", noticias);
    </script>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php
    include '../includes/hero.php';
    createHero("Tu fé en acción");
    ?>
    <section class="contenedor">
        <div class="header_contenedor">
            <h2>Últimas</br>Noticias</h2>
            <button class="filter-btn" onclick="accion_filtro('noticias')">Filtro <img src="..\assets\img\filtro.png" alt="▼" style="width: 15px;"></button>
        </div>
        <div class="filtros" id="noticias-filtros">
            <div class="filtros_contenedor">
                <!-- Filtro por palabras clave -->
                <div class="buscador">
                    <label for="palabrasClave-noticias">Palabras clave:</label>
                    <input type="text" id="palabrasClave-noticias" placeholder="Escribe palabras clave..." oninput="aplicarFiltros()">
                </div>

                <!-- Filtro por categorías -->
                <div>
                    <label for="categoria-noticias">Categoría:</label>
                    <select id="categoria-noticias" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="Fe y espiritualidad">Fe y espiritualidad</option>
                        <option value="Iglesia y actividades">Iglesia y actividades</option>
                        <option value="Noticias del Papa y la Santa Sede">Noticias del Papa y la Santa Sede</option>
                        <option value="Justicia social y caridad">Justicia social y caridad</option>
                        <option value="Educación y formación religiosa">Educación y formación religiosa</option>
                        <option value="Fiestas y celebraciones litúrgicas">Fiestas y celebraciones litúrgicas</option>
                    </select>
                </div>

                <!-- Filtro por fecha -->
                <div>
                    <label for="fecha-noticias">Fecha de publicación:</label>
                    <select id="fecha-noticias" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="ultima_semana">Última semana</option>
                        <option value="ultimo_mes">Último mes</option>
                        <option value="ultimo_año">Último año</option>
                    </select>
                </div>
                <div class="filtros_contenedor2">
                    <div>
                        <label for="fechaInicio-noticias">Fecha de inicio:</label>
                        <input type="date" id="fechaInicio-noticias" onchange="aplicarFiltros()">
                    </div>
                    <div>
                        <label for="fechaFin-noticias">Fecha de fin:</label>
                        <input type="date" id="fechaFin-noticias" onchange="aplicarFiltros()">
                    </div>
                </div>

                <!-- Filtro por ubicación -->
                <div>
                    <label for="ubicacion-noticias">Ubicación:</label>
                    <select id="ubicacion-noticias" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="local">Local</option>
                        <option value="nacional">Nacional</option>
                        <option value="internacional">Internacional</option>
                    </select>
                </div>

                <!-- Filtro por tipo de fuente -->
                <div>
                    <label for="fuente-noticias">Tipo de fuente:</label>
                    <select id="fuente-noticias" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="oficial">Fuentes oficiales de la iglesia</option>
                        <option value="catolica">Medios católicos</option>
                        <option value="general">Medios generales</option>
                    </select>
                </div>

                <!-- Filtro por relevancia -->
                <div>
                    <label for="relevance-noticias">Relevancia:</label>
                    <select id="relevance-noticias" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="most-viewed">Más vistas</option>
                        <option value="most-commented">Más comentadas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="noticias-grid" id="noticias-grid">
            <!-- Noticias se cargarán dinámicamente aquí -->
        </div>
        <div class="no-results" id="no-results-noticias">Noticia no encontrada</div>

        <!-- Paginación -->
        <div class="pagination" id="pagination-noticias">
            <!-- Los botones se generarán dinámicamente aquí -->
        </div>
        <div class="input_pagination" align="center">
            <input type="number" id="page-input-noticias" min="1" placeholder="Ir a" oninput="irAPaginaNoticias()">
            <button id="btn-pagination" onclick="irAPagina('noticias')">Ir</button>
        </div>


        <!-- Botón "Mostrar más" -->
        <div class="load-more">
            <button onclick="mostrarMasNoticias()" id="load-more-btn-noticias">Mostrar más</button>
        </div>
    </section>

    <section class="contenedor">
        <div class="header_contenedor">
            <h2>Últimos</br>Eventos</h2>
            <button class="filter-btn" onclick="accion_filtro('eventos')">Filtro <img src="..\assets\img\filtro.png" alt="▼" style="width: 15px;"></button>
        </div>
        <div class="filtros" id="eventos-filtros">
            <div class="filtros_contenedor">
                <!-- Filtro por categorías -->
                <div>
                    <label for="categoria-eventos">Categoría:</label>
                    <select id="categoria-eventos" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="Misiones">Misiones</option>
                        <option value="Misas">Misas</option>
                        <option value="Peregrinación">Peregrinación</option>
                        <option value="Reuniones">Reuniones</option>
                        <option value="Clases">Clases</option>
                        <option value="Fiestas">Fiestas</option>
                    </select>
                </div>

                <!-- Filtro por fecha -->
                <div>
                    <label for="fecha-eventos">Fecha de publicación:</label>
                    <select id="fecha-eventos" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="ultima_semana">Última semana</option>
                        <option value="ultimo_mes">Último mes</option>
                        <option value="ultimo_año">Último año</option>
                    </select>
                </div>
                <div class="filtros_contenedor2">
                    <div>
                        <label for="fechaInicio-eventos">Fecha de inicio:</label>
                        <input type="date" id="fechaInicio-eventos" onchange="aplicarFiltros()">
                    </div>
                    <div>
                        <label for="fechaFin-eventos">Fecha de fin:</label>
                        <input type="date" id="fechaFin-eventos" onchange="aplicarFiltros()">
                    </div>
                </div>

                <!-- Filtro por palabras clave -->
                <div>
                    <label for="palabrasClave-eventos">Palabras clave:</label>
                    <input type="text" id="palabrasClave-eventos" placeholder="Escribe palabras clave..." oninput="aplicarFiltros()">
                </div>

                <!-- Filtro por ubicación -->
                <div>
                    <label for="ubicacion-eventos">Ubicación:</label>
                    <select id="ubicacion-eventos" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="local">Local</option>
                        <option value="nacional">Nacional</option>
                        <option value="internacional">Internacional</option>
                    </select>
                </div>

                <!-- Filtro por tipo de fuente -->
                <div>
                    <label for="fuente-eventos">Tipo de fuente:</label>
                    <select id="fuente-eventos" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="oficial">Fuentes oficiales de la iglesia</option>
                        <option value="catolica">Medios católicos</option>
                        <option value="general">Medios generales</option>
                    </select>
                </div>

                <!-- Filtro por relevancia -->
                <div>
                    <label for="relevance-eventos">Relevancia:</label>
                    <select id="relevance-eventos" onchange="aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="most-viewed">Más vistas</option>
                        <option value="most-commented">Más comentadas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="eventos-grid" id="eventos-grid">
            <!-- eventos se cargarán dinámicamente aquí -->
        </div>
        <div class="no-results" id="no-results-eventos">Evento no encontrado</div>

        <!-- Paginación para eventos -->
        <div class="pagination" id="pagination-eventos">
            <!-- Los botones se generarán dinámicamente aquí -->
        </div>
        <div class="input_pagination" align="center">
            <input type="number" id="page-input-eventos" min="1" placeholder="Ir a" oninput="irAPaginaEventos()">
            <button id="btn-pagination" onclick="irAPagina('eventos')">Ir</button>
        </div>

        <!-- Botón "Mostrar más" -->
        <div class="load-more">
            <button onclick="mostrarMasEventos()" id="load-more-btn-eventos">Mostrar más</button>
        </div>
    </section>
    <?php include '../includes/footer.php'; ?>
</body>
<script>
    // Datos de noticias
    const todasLasNoticias = Array.isArray(window.noticiasData) ? window.noticiasData.map(noticia => ({
        img: ('../assets/img/p_anuncios/' + noticia.img) || '../assets/img/ejemplo.jpg',
        titulo: noticia.nombre || 'Sin título',
        descripcion: noticia.descripcion || '',
        resumen: (noticia.descripcion || '').substring(0, 500) + '...',
        categoria: noticia.categoria || '',
        fecha: noticia.fecha || noticia.fecha_inicio || '',
        ubicacion: noticia.ubicacion || '',
        fuente: 'oficial',
        relevance: 'normal'
    })) : [];

    // Datos de eventos
    const todasLosEventos = Array.isArray(window.eventosData) ? window.eventosData.map(evento => ({
        id: evento.id || '',
        img: ('../assets/img/p_anuncios/' + evento.img) || '../assets/img/ejemplo.jpg',
        titulo: evento.nombre || 'Sin título',
        descripcion: evento.descripcion || '',
        resumen: (evento.descripcion || '').substring(0, 500) + '...',
        categoria: evento.categoria || '',
        fecha: evento.fechainicio || evento.fecha_inicio || '',
        ubicacion: evento.ubicacion || '',
        fuente: 'oficial',
        relevance: 'normal'
    })) : [];

    // Función para formatear fecha
    function formatearFecha(fechaBD) {
        if (!fechaBD) return '';

        // Si ya es una fecha formateada
        if (/^\d{4}-\d{2}-\d{2}$/.test(fechaBD)) return fechaBD;

        // Si es un timestamp MySQL
        if (typeof fechaBD === 'string' && fechaBD.includes(' ')) {
            return fechaBD.split(' ')[0];
        }

        // Si es un objeto Date
        if (fechaBD instanceof Date) {
            return fechaBD.toISOString().split('T')[0];
        }

        return '';
    }

    // Configuración inicial
    let paginaActualNoticias = 1;
    let paginaActualEventos = 1;
    const noticiasPorPaginaInicial = 3;
    const noticiasPorPaginaExtra = 9;
    let mostrarTodasLasNoticias = false;
    let mostrarTodosLosEventos = false;

    // Mostrar u ocultar filtros
    function accion_filtro(seccion) {
        const filtros = document.getElementById(`${seccion}-filtros`);
        filtros.style.display = filtros.style.display === 'block' ? 'none' : 'block';
    }

    // Expandir o contraer una tarjeta
    function clickear_btnleermas(boton) {
        const tarjeta = boton.closest('.noticias-card'); // Solo busca noticias-card
        if (!tarjeta) return; // Salir si no es una tarjeta de noticias

        const textoCorto = tarjeta.querySelector('.resumen-text');
        const textoCompleto = tarjeta.querySelector('.descripcion-text');
        const imagen = tarjeta.querySelector('img');
        const todasLasNoticiasCards = document.querySelectorAll('.noticias-card'); // Solo noticias

        if (!textoCorto || !textoCompleto || !imagen) return;

        if (boton.textContent.trim() === 'Leer más') {
            todasLasNoticiasCards.forEach(c => {
                if (c !== tarjeta) c.style.display = 'none';
            });
            tarjeta.style.gridColumn = 'span 2';
            tarjeta.style.minHeight = '500px';
            imagen.style.width = '100%';
            imagen.style.height = '300px';
            textoCorto.style.display = 'none';
            textoCompleto.style.display = 'block';
            boton.textContent = 'Leer menos';
        } else {
            todasLasNoticiasCards.forEach(c => c.style.display = 'block');
            tarjeta.style.gridColumn = 'span 1';
            tarjeta.style.minHeight = '500px';
            imagen.style.width = '100%';
            imagen.style.height = '200px';
            textoCorto.style.display = 'block';
            textoCompleto.style.display = 'none';
            boton.textContent = 'Leer más';
        }
    }

    // Función genérica para renderizar tarjetas
    function renderizarTarjetas(contenedorId, datos, esNoticia = true) {
        const contenedor = document.getElementById(contenedorId);
        const porPagina = esNoticia ?
            (mostrarTodasLasNoticias ? noticiasPorPaginaExtra : noticiasPorPaginaInicial) :
            (mostrarTodosLosEventos ? noticiasPorPaginaExtra : noticiasPorPaginaInicial);

        const paginaActual = esNoticia ? paginaActualNoticias : paginaActualEventos;
        const inicio = (paginaActual - 1) * porPagina;
        const fin = inicio + porPagina;
        const datosPaginados = datos.slice(inicio, fin);

        contenedor.innerHTML = datosPaginados.length === 0 ?
            '<div class="no-results">No se encontraron resultados</div>' :
            datosPaginados.map(item => `
        <div class="${esNoticia ? 'noticias-card' : 'eventos-card'}">
            <div class="card-content">
                <img src="${item.img.startsWith('../') ? item.img : '../' + item.img}" 
                     alt="${item.titulo}" 
                     onerror="this.src='../assets/img/ejemplo.jpg'">
                <h3>${item.titulo}</h3>
                <p class="resumen-text">${item.resumen}</p>
                <p class="descripcion-text" style="display:none">${item.descripcion}</p>
            </div>
            ${esNoticia
                ? `<button class="leer-mas-btn">Leer más</button>`
                : `<div class="evento-btn-container">
                    <form action="../pages/evento.php" method="POST">
                        <input type="hidden" name="id" value="${item.id || ''}">
                        <button type="submit" class="evento-link">Ver evento</button>
                    </form>
                   </div>`}
        </div>
    `).join('');

        // Asignar eventos
        if (esNoticia) {
            const botonesLeerMas = contenedor.querySelectorAll('.leer-mas-btn');
            botonesLeerMas.forEach(boton => {
                boton.addEventListener('click', function() {
                    clickear_btnleermas(this);
                });
            });
        }
        actualizarPaginacion(esNoticia ? 'noticias' : 'eventos', datos.length);
    }

    // Función genérica para actualizar la paginación
    function actualizarPaginacion(seccion, totalItems) {
        const paginacion = document.getElementById(`pagination-${seccion}`);
        const totalPaginas = Math.ceil(totalItems / (
            seccion === 'noticias' ?
            (mostrarTodasLasNoticias ? noticiasPorPaginaExtra : noticiasPorPaginaInicial) :
            (mostrarTodosLosEventos ? noticiasPorPaginaExtra : noticiasPorPaginaInicial)
        ));

        const paginaActual = seccion === 'noticias' ? paginaActualNoticias : paginaActualEventos;

        // Mantener el valor actual del input si existe
        const inputElement = document.getElementById(`page-input-${seccion}`);
        if (inputElement) {
            inputElement.value = '';
            inputElement.setAttribute('max', totalPaginas);
            inputElement.placeholder = `1-${totalPaginas}`;
        }

        let paginacionHTML = `
        <button onclick="cambiarPagina('${seccion}', ${paginaActual - 1})" ${paginaActual === 1 ? 'disabled' : ''}>&lt;</button>
    `;

        // Mostrar siempre el botón de la primera página
        paginacionHTML += `<button onclick="cambiarPagina('${seccion}', 1)" ${1 === paginaActual ? 'class="active"' : ''}>1</button>`;

        // Mostrar puntos suspensivos si hay páginas ocultas entre la 1 y la actual
        if (paginaActual > 3) {
            paginacionHTML += `<span>...</span>`;
        }

        // Mostrar páginas alrededor de la actual (sin incluir la 1 que ya está)
        const inicioRango = Math.max(2, paginaActual - 1);
        const finRango = Math.min(totalPaginas - 1, paginaActual + 1);

        for (let i = inicioRango; i <= finRango; i++) {
            if (i === 1) continue; // Ya mostramos la página 1
            paginacionHTML += `<button onclick="cambiarPagina('${seccion}', ${i})" ${i === paginaActual ? 'class="active"' : ''}>${i}</button>`;
        }

        // Mostrar puntos suspensivos si hay páginas ocultas entre la actual y la última
        if (paginaActual < totalPaginas - 2) {
            paginacionHTML += `<span>...</span>`;
        }

        // Mostrar la última página si es diferente de la primera
        if (totalPaginas > 1) {
            paginacionHTML += `<button onclick="cambiarPagina('${seccion}', ${totalPaginas})" ${totalPaginas === paginaActual ? 'class="active"' : ''}>${totalPaginas}</button>`;
        }

        paginacionHTML += `
        <button onclick="cambiarPagina('${seccion}', ${paginaActual + 1})" ${paginaActual === totalPaginas ? 'disabled' : ''}>&gt;</button>
    `;

        paginacion.innerHTML = paginacionHTML;
    }

    // Cambiar de página
    function cambiarPagina(seccion, pagina) {
        const totalPaginas = Math.ceil(
            (seccion === 'noticias' ? todasLasNoticias : todasLosEventos).length /
            (seccion === 'noticias' ?
                (mostrarTodasLasNoticias ? noticiasPorPaginaExtra : noticiasPorPaginaInicial) :
                (mostrarTodosLosEventos ? noticiasPorPaginaExtra : noticiasPorPaginaInicial)
            )
        );

        if (pagina < 1 || pagina > totalPaginas) return;

        if (seccion === 'noticias') paginaActualNoticias = pagina;
        else paginaActualEventos = pagina;

        aplicarFiltros();
    }

    // Ir a una página específica
    function irAPagina(seccion) {
        const inputElement = document.getElementById(`page-input-${seccion}`);
        const totalPaginas = Math.ceil(
            (seccion === 'noticias' ? todasLasNoticias : todasLosEventos).length /
            (seccion === 'noticias' ?
                (mostrarTodasLasNoticias ? noticiasPorPaginaExtra : noticiasPorPaginaInicial) :
                (mostrarTodosLosEventos ? noticiasPorPaginaExtra : noticiasPorPaginaInicial)
            )
        );


        let pagina = parseInt(inputElement.value);

        // Validar el valor ingresado
        if (isNaN(pagina)) {
            alert('Por favor ingrese un número válido');
            return;
        }

        if (pagina < 1) {
            pagina = 1;
            inputElement.value = 1;
        } else if (pagina > totalPaginas) {
            pagina = totalPaginas;
            inputElement.value = totalPaginas;
        }

        cambiarPagina(seccion, pagina);
    }

    // Mostrar más/menos noticias
    function mostrarMasNoticias() {
        mostrarTodasLasNoticias = !mostrarTodasLasNoticias;
        document.getElementById('load-more-btn-noticias').textContent = mostrarTodasLasNoticias ? 'Mostrar menos' : 'Mostrar más';
        paginaActualNoticias = 1;
        //renderizarTarjetas('noticias-grid', filtrarDatos(todasLasNoticias, obtenerFiltrosNoticias()), true);
        aplicarFiltros();
    }

    // Mostrar más/menos eventos
    function mostrarMasEventos() {
        mostrarTodosLosEventos = !mostrarTodosLosEventos;
        document.getElementById('load-more-btn-eventos').textContent = mostrarTodosLosEventos ? 'Mostrar menos' : 'Mostrar más';
        paginaActualEventos = 1;
        //renderizarTarjetas('eventos-grid', filtrarDatos(todasLosEventos, obtenerFiltrosEventos()), false);
        aplicarFiltros();
    }

    // Aplicar filtros
    function aplicarFiltros() {
        // Filtros para noticias
        const filtrosNoticias = {
            categoria: document.getElementById('categoria-noticias').value,
            fecha: document.getElementById('fecha-noticias').value,
            palabrasClave: document.getElementById('palabrasClave-noticias').value.toLowerCase(),
            ubicacion: document.getElementById('ubicacion-noticias').value,
            fuente: document.getElementById('fuente-noticias').value,
            relevancia: document.getElementById('relevance-noticias').value,
            fechaInicio: document.getElementById('fechaInicio-noticias').value,
            fechaFin: document.getElementById('fechaFin-noticias').value
        };

        // Filtros para eventos
        const filtrosEventos = {
            categoria: document.getElementById('categoria-eventos').value,
            fecha: document.getElementById('fecha-eventos').value,
            palabrasClave: document.getElementById('palabrasClave-eventos').value.toLowerCase(),
            ubicacion: document.getElementById('ubicacion-eventos').value,
            fuente: document.getElementById('fuente-eventos').value,
            relevancia: document.getElementById('relevance-eventos').value,
            fechaInicio: document.getElementById('fechaInicio-eventos').value,
            fechaFin: document.getElementById('fechaFin-eventos').value
        };

        const noticiasFiltradas = filtrarDatos(todasLasNoticias, filtrosNoticias);
        const eventosFiltrados = filtrarDatos(todasLosEventos, filtrosEventos);

        renderizarTarjetas('noticias-grid', noticiasFiltradas, true);
        renderizarTarjetas('eventos-grid', eventosFiltrados, false);
    }


    // Función genérica para filtrar datos
    function filtrarDatos(datos, filtros) {
        return datos.filter(item => {
            const coincideCategoria = !filtros.categoria || item.categoria === filtros.categoria;
            const coincidePalabrasClave = !filtros.palabrasClave ||
                item.titulo.toLowerCase().includes(filtros.palabrasClave) ||
                item.resumen.toLowerCase().includes(filtros.palabrasClave);
            const coincideUbicacion = !filtros.ubicacion || item.ubicacion === filtros.ubicacion;
            const coincideFuente = !filtros.fuente || item.fuente === filtros.fuente;
            const coincideRelevancia = !filtros.relevancia || item.relevance === filtros.relevancia;
            const coincideFecha = filtrarPorFecha(item.fecha, filtros.fecha, filtros.fechaInicio, filtros.fechaFin);

            return coincideCategoria && coincidePalabrasClave && coincideUbicacion && coincideFuente && coincideRelevancia && coincideFecha;
        });
    }

    // Filtrar por fecha
    function filtrarPorFecha(fechaItem, filtroFecha, fechaInicio, fechaFin) {
        if (!fechaItem) return false;

        // Formatea la fecha primero
        const fechaStr = formatearFecha(fechaItem);
        const fecha = new Date(fechaStr);

        // Si no es una fecha válida
        if (isNaN(fecha.getTime())) return false;

        const ahora = new Date();

        if (filtroFecha === "ultima_semana") {
            const ultimaSemana = new Date(ahora.setDate(ahora.getDate() - 7));
            return fecha >= ultimaSemana;
        } else if (filtroFecha === "ultimo_mes") {
            const ultimoMes = new Date(ahora.setMonth(ahora.getMonth() - 1));
            return fecha >= ultimoMes;
        } else if (filtroFecha === "ultimo_año") {
            const ultimoAnio = new Date(ahora.setFullYear(ahora.getFullYear() - 1));
            return fecha >= ultimoAnio;
        } else if (fechaInicio && fechaFin) {
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            return fecha >= inicio && fecha <= fin;
        }

        return true;
    }

    // Inicializar
    aplicarFiltros();
</script>

</html>