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

// Obtener eventos
$queryEventos = "SELECT id, imagen AS img, nombre, descripcion, categoria, 
                DATE_FORMAT(fecha_inicio, '%Y-%m-%d') AS fecha_inicio,
                DATE_FORMAT(fecha_fin, '%Y-%m-%d') AS fecha_fin,
                ubicacion, tipo 
                FROM PTL_EVENTOS WHERE tipo = 'Evento'";
$resultEventos = mysqli_query($link, $queryEventos);

$eventos = array();
if ($resultEventos) {
    while ($row = mysqli_fetch_assoc($resultEventos)) {
        $eventos[] = $row;
    }
}

// Obtener cumpleaños
$queryCumpleanos = "SELECT nombre, apellido_p, apellido_m, fecha_nacimiento,
                   lugar_nacimiento, imagen AS img 
                   FROM PTL_USUARIOS";
$result = mysqli_query($link, $queryCumpleanos);

$cumpleanos = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['titulo'] = $row['nombre'];
        $row['fecha'] = $row['fecha_nacimiento'];
        $cumpleanos[] = $row;
    }
}

$eventos = convertirUTF8($eventos);
$cumpleanos = convertirUTF8($cumpleanos);
?>
<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos del calendario */
        .wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
        }

        .calendar-section {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .calendar-month-year {
            width: 100%;
            text-align: center;
            color: #FF0000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-nav button {
            background: none;
            border: none;
            color: #FF0000;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .calendar-days-header {
            width: 100%;
            background: #50555B;
            border: 2px black solid;
            display: flex;
            margin-bottom: 5px;
        }

        .day-header {
            flex: 1;
            min-width: 0;
            text-align: center;
            color: #F0F0F0;
            font-weight: 700;
            padding: 10px 0;
            border-right: 1px solid #F0F0F0;
        }

        .calendar-days {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .week {
            display: flex;
            width: 100%;
        }

        .day-box {
            flex: 1;
            min-width: 0;
            aspect-ratio: 1;
            background: white;
            border: 2px black solid;
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 5px;
            box-sizing: border-box;
        }

        .day-number {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 25px;
            height: 25px;
            text-align: center;
            font-weight: 700;
        }

        .day-icons {
            position: absolute;
            top: 5px;
            left: 5px;
            display: flex;
            gap: 3px;
        }

        .day-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .event-icon {
            color: #FF0000;
        }

        .birthday-icon {
            color: #FFC107;
        }

        .day-events {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .event-item,
        .birthday-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .event-item img,
        .birthday-item img {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            object-fit: cover;
        }

        .event-link {
            display: flex;
            align-items: center;
            width: 100%;
            height: 100%;
            background-color: var(--color-blanco);
            box-shadow: 0 .1px 10px rgba(0, 0, 0, 0.01);
            padding: 2px 5px;
            border-radius: 3px;
        }

        .view-more-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #f0f0f0;
            border: none;
            padding: 2px 5px;
            border-radius: 3px;
            cursor: pointer;
        }

        .today {
            background-color: #ffecec;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-width: 700px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .modal-tab {
            padding: 10px 20px;
            cursor: pointer;
        }

        .modal-tab.active {
            border-bottom: 2px solid #FF0000;
            font-weight: bold;
        }

        .modal-tab-content {
            display: none;
        }

        .modal-tab-content.active {
            display: block;
        }

        .modal-event-item,
        .modal-birthday-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .modal-event-item img,
        .modal-birthday-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .modal-event-info,
        .modal-birthday-info {
            flex: 1;
        }

        .modal-event-name,
        .modal-birthday-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .view-event-btn {
            background-color: #FF0000;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        @media screen and (max-width: 768px) {

            .event-link,
            .birthday-item {
                display: none;
            }

            .day-icons {
                display: none;
            }

            .view-more-btn{
                background-color: var(--color-rojo);
                color:var(--color-blanco);
                bottom: 2px;
            }
        }
    </style>
</head>

<body>
    <?php 
    
    include '../includes/navbar.php'; 
    ?>
    <?php include '../includes/hero.php';
    createHero("Calendario de Eventos");
    ?>

    <div class="wrapper">
        <div class="main-container">
            <div class="calendar-section">
                <div class="calendar-month-year">
                    <button id="prev-month"><i class="fas fa-chevron-left"></i></button>
                    <h2 id="current-month-year"></h2>
                    <button id="next-month"><i class="fas fa-chevron-right"></i></button>
                </div>

                <div class="calendar-days-header">
                    <div class="day-header">Dom</div>
                    <div class="day-header">Lun</div>
                    <div class="day-header">Mar</div>
                    <div class="day-header">Mié</div>
                    <div class="day-header">Jue</div>
                    <div class="day-header">Vie</div>
                    <div class="day-header">Sáb</div>
                </div>

                <div class="calendar-days" id="calendar-days"></div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar todos los eventos -->
    <div id="eventsModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modal-title">Eventos y Cumpleaños</h2>
            <div class="modal-tabs">
                <div class="modal-tab active" data-tab="events">Eventos</div>
                <div class="modal-tab" data-tab="birthdays">Cumpleaños</div>
            </div>

            <div id="eventsTab" class="modal-tab-content active"></div>
            <div id="birthdaysTab" class="modal-tab-content"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Datos de PHP convertidos a JavaScript
        const todosLosEventos = <?php echo json_encode($eventos); ?>;
        const todosLosCumpleanos = <?php echo json_encode($cumpleanos); ?>;

        // Variables del calendario
        const monthYearElement = document.getElementById('current-month-year');
        const calendarDaysElement = document.getElementById('calendar-days');
        const prevMonthButton = document.getElementById('prev-month');
        const nextMonthButton = document.getElementById('next-month');
        let currentDate = new Date();

        // Procesar datos para búsqueda rápida
        const eventosPorFecha = {};
        const cumpleanosPorFecha = {};

        todosLosEventos.forEach(evento => {
            if (evento.fecha_inicio) {
                const fecha = evento.fecha_inicio.substring(5); // MM-DD
                if (!eventosPorFecha[fecha]) {
                    eventosPorFecha[fecha] = [];
                }
                eventosPorFecha[fecha].push({
                    ...evento,
                    img: evento.img.startsWith('../') ? evento.img : '../' + evento.img
                });
            }
        });

        todosLosCumpleanos.forEach(cumpleano => {
            if (cumpleano.fecha_nacimiento) {
                const fecha = formatDate(cumpleano.fecha_nacimiento).substring(5); // MM-DD
                if (!cumpleanosPorFecha[fecha]) {
                    cumpleanosPorFecha[fecha] = [];
                }
                cumpleanosPorFecha[fecha].push({
                    ...cumpleano,
                    img: cumpleano.img.startsWith('../') ? cumpleano.img : '../' + cumpleano.img
                });
            }
        });

        // Funciones auxiliares
        function formatDate(dateString) {
            if (!dateString) return '';
            if (dateString.includes('/')) {
                const parts = dateString.split('/');
                return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
            }
            return dateString;
        }

        function getMonthName(month) {
            const monthNames = [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];
            return monthNames[month];
        }

        // Renderizar calendario
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();

            monthYearElement.textContent = `${getMonthName(month)} ${year}`;

            const firstDayOfMonth = new Date(year, month, 1);
            const lastDayOfMonth = new Date(year, month + 1, 0);
            const daysInMonth = lastDayOfMonth.getDate();
            const startDay = firstDayOfMonth.getDay();

            let calendarHTML = '';
            let dayCounter = 1;

            const totalWeeks = Math.ceil((startDay + daysInMonth) / 7);

            for (let week = 0; week < totalWeeks; week++) {
                calendarHTML += '<div class="week">';

                for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
                    if ((week === 0 && dayOfWeek < startDay) || dayCounter > daysInMonth) {
                        calendarHTML += '<div class="day-box"></div>';
                    } else {
                        const isToday = today.getDate() === dayCounter &&
                            today.getMonth() === month &&
                            today.getFullYear() === year;

                        const currentDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}`;
                        const currentMonthDayStr = `${String(month + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}`;

                        const eventosDelDia = eventosPorFecha[currentMonthDayStr] || [];
                        const cumpleanosDelDia = cumpleanosPorFecha[currentMonthDayStr] || [];

                        // Iconos
                        let iconsHTML = '';
                        if (eventosDelDia.length > 0 || cumpleanosDelDia.length > 0) {
                            iconsHTML = '<div class="day-icons">';
                            if (eventosDelDia.length > 0) {
                                iconsHTML += '<div class="day-icon event-icon" title="Evento"><i class="fas fa-calendar-day"></i></div>';
                            }
                            if (cumpleanosDelDia.length > 0) {
                                iconsHTML += '<div class="day-icon birthday-icon" title="Cumpleaños"><i class="fas fa-birthday-cake"></i></div>';
                            }
                            iconsHTML += '</div>';
                        }

                        // Eventos y cumpleaños
                        let eventsHTML = '<div class="day-events">';

                        // Mostrar solo el primer evento
                        if (eventosDelDia.length > 0) {
                            const evento = eventosDelDia[0];
                            eventsHTML += `
                                <div class="event-item">
                                    <form action="../pages/evento.php" method="POST" onsubmit="console.log('ID enviado:', this.id.value)">
                                        
                                        <input type="hidden" name="id" value="${evento.id || ''}">
                                        <button type="submit" class="event-link">
                                            <img src="${evento.img}" alt="${evento.nombre}" onerror="this.src='../assets/img/ejemplo.jpg'">    
                                            <span class="event-name">${evento.nombre}</span>
                                        </button>
                                    </form>
                                </div>
                            `;
                        }

                        // Mostrar solo el primer cumpleaños
                        if (cumpleanosDelDia.length > 0) {
                            const cumpleano = cumpleanosDelDia[0];
                            eventsHTML += `
                                <div class="birthday-item">
                                    <img src="${cumpleano.img}" alt="${cumpleano.nombre}" onerror="this.src='../assets/img/ejemplo.jpg'">
                                    <span class="birthday-name">${cumpleano.nombre}</span>
                                </div>
                            `;
                        }
                        eventsHTML += '</div>';

                        // Botón "Ver más" si hay más de 1 evento o cumpleaños
                        let showMoreBtn = '';
                        if (eventosDelDia.length > 1 || cumpleanosDelDia.length > 1) {
                            showMoreBtn = `<button class="view-more-btn" onclick="openEventsModal('${currentMonthDayStr}')">más</button>`;
                        }

                        calendarHTML += `
                            <div class="day-box ${isToday ? 'today' : ''}">
                                ${iconsHTML}
                                <div class="day-number">${dayCounter}</div>
                                ${eventsHTML}
                                ${showMoreBtn}
                            </div>
                        `;
                        dayCounter++;
                    }
                }

                calendarHTML += '</div>';
            }

            calendarDaysElement.innerHTML = calendarHTML;
        }

        // Función para abrir el modal con todos los eventos
        function openEventsModal(dateStr) {
            const modal = document.getElementById('eventsModal');
            const eventsTab = document.getElementById('eventsTab');
            const birthdaysTab = document.getElementById('birthdaysTab');

            // Obtener eventos y cumpleaños para esta fecha
            const eventosDelDia = eventosPorFecha[dateStr] || [];
            const cumpleanosDelDia = cumpleanosPorFecha[dateStr] || [];

            // Actualizar título
            const [month, day] = dateStr.split('-');
            document.getElementById('modal-title').textContent = `Eventos y Cumpleaños - ${day}/${month}`;

            // Limpiar contenido previo
            eventsTab.innerHTML = '';
            birthdaysTab.innerHTML = '';

            // Llenar eventos
            if (eventosDelDia.length > 0) {
                eventosDelDia.forEach(evento => {
                    const eventItem = document.createElement('div');
                    eventItem.className = 'modal-event-item';
                    eventItem.innerHTML = `
                        <img src="${evento.img}" alt="${evento.nombre}" onerror="this.src='../assets/img/ejemplo.jpg'">
                        <div class="modal-event-info">
                            <div class="modal-event-name">${evento.nombre}</div>
                            <div class="modal-event-desc">${evento.descripcion || 'Sin descripción'}</div>
                        </div>
                        <a href="../pages/evento.php?id=${evento.id}" class="view-event-btn">Ver</a>
                    `;
                    eventsTab.appendChild(eventItem);
                });
            } else {
                eventsTab.innerHTML = '<p>No hay eventos para este día.</p>';
            }

            // Llenar cumpleaños
            if (cumpleanosDelDia.length > 0) {
                cumpleanosDelDia.forEach(cumpleano => {
                    const birthdayItem = document.createElement('div');
                    birthdayItem.className = 'modal-birthday-item';
                    birthdayItem.innerHTML = `
                        <img src="${cumpleano.img}" alt="${cumpleano.nombre}" onerror="this.src='../assets/img/ejemplo.jpg'">
                        <div class="modal-birthday-info">
                            <div class="modal-birthday-name">${cumpleano.nombre} ${cumpleano.apellido_p} ${cumpleano.apellido_m}</div>
                            <div class="modal-birthday-date">${cumpleano.fecha_nacimiento}</div>
                        </div>
                    `;
                    birthdaysTab.appendChild(birthdayItem);
                });
            } else {
                birthdaysTab.innerHTML = '<p>No hay cumpleaños para este día.</p>';
            }

            // Mostrar modal
            modal.style.display = 'block';

            // Cerrar modal
            document.querySelector('.close-modal').onclick = function() {
                modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            // Funcionalidad de pestañas
            const tabs = document.querySelectorAll('.modal-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    document.querySelectorAll('.modal-tab-content').forEach(content => {
                        content.classList.remove('active');
                    });

                    document.getElementById(this.dataset.tab + 'Tab').classList.add('active');
                });
            });
        }

        // Event listeners
        prevMonthButton.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextMonthButton.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        // Inicializar calendario
        renderCalendar();
    </script>
</body>

</html>