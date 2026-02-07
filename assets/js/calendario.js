const monthYearElement = document.getElementById('current-month-year');
const calendarDaysElement = document.getElementById('calendar-days');
const prevMonthButton = document.getElementById('prev-month');
const nextMonthButton = document.getElementById('next-month');

let currentDate = new Date();

// Procesar datos de eventos y cumpleaños
const todosLosEventos = Array.isArray(window.eventosData) ? window.eventosData.map(evento => ({
    id: evento.id || '',
    img: ('../assets/img/p_anuncios/' + (evento.img || 'ejemplo.jpg')),
    titulo: evento.nombre || 'Sin título',
    descripcion: evento.descripcion || '',
    categoria: evento.categoria || '',
    fecha_inicio: evento.fecha_inicio || '',
    fecha_fin: evento.fecha_fin || '',
    ubicacion: evento.ubicacion || ''
})) : [];

const todosLosCumpleanos = Array.isArray(window.cumpleanosData) ? window.cumpleanosData.map(cumpleano => ({
    img: ('../assets/img/p_anuncios/' + (cumpleano.img || 'ejemplo.jpg')),
    nombre: cumpleano.nombre || '',
    apellido_p: cumpleano.apellido_p || '',
    apellido_m: cumpleano.apellido_m || '',
    fecha_nacimiento: formatearFecha(cumpleano.fecha_nacimiento) || '',
    lugar_nacimiento: cumpleano.lugar_nacimiento || ''
})) : [];

// Crear estructuras para búsqueda por fecha
const eventosPorFecha = {};
const cumpleanosPorFecha = {};

todosLosEventos.forEach(evento => {
    if (evento.fecha_inicio) {
        const fecha = evento.fecha_inicio.substring(5); // Solo mes y día (MM-DD)
        if (!eventosPorFecha[fecha]) {
            eventosPorFecha[fecha] = [];
        }
        eventosPorFecha[fecha].push(evento);
    }
});

todosLosCumpleanos.forEach(cumpleano => {
    if (cumpleano.fecha_nacimiento) {
        const fecha = cumpleano.fecha_nacimiento.substring(5); // Solo mes y día (MM-DD)
        if (!cumpleanosPorFecha[fecha]) {
            cumpleanosPorFecha[fecha] = [];
        }
        cumpleanosPorFecha[fecha].push(cumpleano);
    }
});

function formatearFecha(fechaBD) {
    if (!fechaBD) return '';

    // Reemplazar / por - si es necesario
    if (typeof fechaBD === 'string' && fechaBD.includes('/')) {
        fechaBD = fechaBD.replace(/\//g, '-');
    }

    // Si ya está en formato YYYY-MM-DD
    if (/^\d{4}-\d{2}-\d{2}$/.test(fechaBD)) return fechaBD;

    // Si es un timestamp MySQL (YYYY-MM-DD HH:MM:SS)
    if (typeof fechaBD === 'string' && fechaBD.includes(' ')) {
        return fechaBD.split(' ')[0];
    }

    // Si es un objeto Date
    if (fechaBD instanceof Date) {
        return fechaBD.toISOString().split('T')[0];
    }

    return '';
}

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

                const currentMonthDayStr = `${String(month + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}`;
                const eventosDelDia = eventosPorFecha[currentMonthDayStr] || [];
                const cumpleanosDelDia = cumpleanosPorFecha[currentMonthDayStr] || [];

                // Iconos para la esquina superior izquierda
                let iconsHTML = '';
                if (cumpleanosDelDia.length > 0) {
                    iconsHTML += '<i class="bi bi-balloon-heart-fill text-pink" style="font-size: 1rem;"></i>';
                }
                if (eventosDelDia.length > 0) {
                    iconsHTML += '<i class="bi bi-flag-fill text-primary ms-1" style="font-size: 1rem;"></i>';
                }

                // Contenido del día (eventos/cumpleaños)
                let contentHTML = '';
                if (eventosDelDia.length > 0 || cumpleanosDelDia.length > 0) {
                    contentHTML = `
                        <div class="day-content">
                            ${eventosDelDia.length > 0 ? 
                                `<div class="event-item">${eventosDelDia[0].titulo}</div>` : ''}
                            ${cumpleanosDelDia.length > 0 ? 
                                `<div class="birthday-item">${cumpleanosDelDia[0].nombre}</div>` : ''}
                        </div>
                    `;
                }

                calendarHTML += `
                    <div class="day-box ${isToday ? 'today' : ''}" 
                         data-date="${year}-${String(month + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}"
                         data-events='${JSON.stringify(eventosDelDia)}'
                         data-birthdays='${JSON.stringify(cumpleanosDelDia)}'
                         onclick="showDayDetails(this)">
                        <div class="day-icons">${iconsHTML}</div>
                        <div class="day-number">${dayCounter}</div>
                        ${contentHTML}
                    </div>
                `;
                dayCounter++;
            }
        }

        calendarHTML += '</div>';
    }

    calendarDaysElement.innerHTML = calendarHTML;
}

// Función para mostrar el modal con los detalles del día
function showDayDetails(dayElement) {
    const date = dayElement.getAttribute('data-date');
    const eventos = JSON.parse(dayElement.getAttribute('data-events'));
    const cumpleanos = JSON.parse(dayElement.getAttribute('data-birthdays'));

    if (eventos.length === 0 && cumpleanos.length === 0) return;

    let modalContent = `
        <div class="modal fade" id="dayModal" tabindex="-1" aria-labelledby="dayModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dayModalLabel">Eventos del ${date}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
    `;

    if (eventos.length > 0) {
        modalContent += `<h6><i class="bi bi-flag-fill text-primary me-2"></i>Eventos</h6><ul class="list-group mb-3">`;
        eventos.forEach(evento => {
            modalContent += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${evento.titulo}
                    <form action="../pages/evento.php" method="POST" class="m-0">
                        <input type="hidden" name="id" value="${evento.id || ''}">
                        <button type="submit" class="btn btn-sm btn-primary">Ver Evento</button>
                    </form>
                </li>
            `;
        });
        modalContent += `</ul>`;
    }

    if (cumpleanos.length > 0) {
        modalContent += `<h6><i class="bi bi-balloon-heart-fill text-pink me-2"></i>Cumpleaños</h6><ul class="list-group">`;
        cumpleanos.forEach(cumpleano => {
            modalContent += `
                <li class="list-group-item">
                    ${cumpleano.nombre} ${cumpleano.apellido_p} ${cumpleano.apellido_m}
                </li>
            `;
        });
        modalContent += `</ul>`;
    }

    modalContent += `
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Crear e insertar el modal
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalContent;
    document.body.appendChild(modalContainer);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('dayModal'));
    modal.show();

    // Eliminar el modal cuando se cierre
    document.getElementById('dayModal').addEventListener('hidden.bs.modal', function() {
        modalContainer.remove();
    });
}

function getMonthName(month) {
    const monthNames = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    return monthNames[month];
}

prevMonthButton.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

nextMonthButton.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Renderizar el calendario al cargar la página
renderCalendar();

// Ajustar el tamaño del calendario cuando cambia el tamaño de la ventana
window.addEventListener('resize', function () {
    // Forzar un nuevo renderizado para ajustar los tamaños
    renderCalendar();
});