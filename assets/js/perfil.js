<<<<<<< HEAD
const appState = {
    supervisor: {
        currentPage: 1,
        perPage: 5,
        filteredItems: []
    },
    coordinador: {
        currentPage: 1,
        perPage: 5,
        filteredItems: [],
        events: [
            {
                'nombre': 'Peregrinación',
                'fecha_inicio': '12/03 6:00am',
                'fecha_fin': '18/03 10:00am',
                'costo': '500$'
            },
            {
                'nombre': 'Encuentro Misionero',
                'fecha_inicio': '06/01 12:00pm',
                'fecha_fin': '06/01 03:00pm',
                'costo': '500$'
            },
            {
                'nombre': 'Taller',
                'fecha_inicio': '15/04 9:00am',
                'fecha_fin': '15/04 1:00pm',
                'costo': '300$'
            },
            {
                'nombre': 'Misa',
                'fecha_inicio': '20/05 5:00pm',
                'fecha_fin': '20/05 6:00pm',
                'costo': '0$'
            }
        ]
    },
    administrador: {
        currentPage: 1,
        perPage: 5,
        filteredItems: []
    },
    imageModal: {
        currentImage: null
    },

    profileModal: {
        currentImage: null,
        formData: {
            nombre: '',
            email: '',
            numero: '',
            emergencias: '',
            talla: '',
            enfermedades: '',
            tratamiento: ''
        }
    }
};

// Variables para el modal
let currentEventId = null;
let isEditMode = false;

$(document).ready(function () {
    // Primero inicializar pestañas
    setupTabs();
    
    // Luego el resto de la inicialización
    initPagination();
    setupEventHandlers();
    
    // Depuración adicional
    console.log("Contenido activo actual:", $(".content.active").attr('id'));
});

// Funciones principales
function initPagination() {
    // Primero hacer visibles todos los elementos
    $("#participantsTable tbody tr").show();
    $("#eventsTable tbody tr").show();
    $("#adminTable tbody tr").show();

    // Luego inicializar el filtrado y paginación
    updateFilteredItems('supervisor');
    updateFilteredItems('coordinador');
    updateFilteredItems('administrador');

    // Mostrar primera página
    showPage('supervisor', 1);
    showPage('coordinador', 1);
    showPage('administrador', 1);
}

 // Manejo de pestañas
 function setupTabs() {
    let initialTab = "supervisor"; // Valor por defecto
    const userProfile = '<?php echo $_SESSION["Perfil"] ?? ""; ?>';
    
=======
// Estado de la aplicación
const appState = {
    currentTab: 'supervisor',
    currentEventId: null,
    modals: {
        editProfile: null,
        deleteAccount: null,
        changeImage: null,
        event: null,
        confirmation: null
    },
    imageModal: {
        currentImage: null
    }
};

// Inicialización cuando el DOM está listo
$(document).ready(function() {
    initializeModals();
    setupEventHandlers();
    setupTabs();
});

// Inicializar modales
function initializeModals() {
    appState.modals.editProfile = new bootstrap.Modal(document.getElementById('modalEditar'));
    appState.modals.deleteAccount = new bootstrap.Modal(document.getElementById('confirmDeleteAccountModal'));
    appState.modals.changeImage = new bootstrap.Modal(document.getElementById('imageModal'));
    appState.modals.event = new bootstrap.Modal(document.getElementById('eventModal'));
    appState.modals.confirmation = new bootstrap.Modal(document.getElementById('confirmModal'));
}

// Configurar manejadores de eventos
function setupEventHandlers() {
    // Botón editar perfil
    $('#editar_p').click(loadProfileData);
    
    // Botón eliminar cuenta
    $('#eliminar-cuenta').click(() => appState.modals.deleteAccount.show());
    
    // Botón cambiar imagen
    $('#botonCambiarImagen').click(() => appState.modals.changeImage.show());
    
    // Vista previa de imagen
    $('#imageUpload').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result).show();
                appState.imageModal.currentImage = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Guardar imagen
    $('#imageForm').submit(function(e) {
        e.preventDefault();
        if (appState.imageModal.currentImage) {
            $('#imagen-superior, #imagen-perfil').attr('src', appState.imageModal.currentImage);
            appState.modals.changeImage.hide();
        }
    });
    
    // Cancelar modales
    $('.modal .close, .btn-secondary').click(function() {
        const modalId = $(this).closest('.modal').attr('id');
        if (modalId === 'imageModal') {
            $('#imagePreview').hide().attr('src', '#');
            $('#imageUpload').val('');
            appState.imageModal.currentImage = null;
        }
        appState.modals[getModalKey(modalId)]?.hide();
    });
}

// Obtener clave del modal
function getModalKey(modalId) {
    const map = {
        'modalEditar': 'editProfile',
        'confirmDeleteAccountModal': 'deleteAccount',
        'imageModal': 'changeImage',
        'eventModal': 'event',
        'confirmModal': 'confirmation'
    };
    return map[modalId] || '';
}

// Configurar pestañas
function setupTabs() {
    const userProfile = '<?php echo $_SESSION["Perfil"] ?? ""; ?>';
    let initialTab = "supervisor";

>>>>>>> 33aecbf629f13d3fb4938b4c0ec5b6869e26cf4f
    if (userProfile === "Coordinador" && $('.tab[data-tab="coordinador"]').length) {
        initialTab = "coordinador";
    } else if (userProfile === "Supervisor" && $('.tab[data-tab="supervisor"]').length) {
        initialTab = "supervisor";
    } else if (userProfile === "Administrador" && $('.tab[data-tab="administrador"]').length) {
        initialTab = "administrador";
    }

<<<<<<< HEAD
    $(".tab").removeClass("active");
    $(`.tab[data-tab="${initialTab}"]`).addClass("active");
    
    $(".content").removeClass("active").hide();
    $(`#${initialTab}`).show().addClass("active");

    // Manejador de clics para pestañas
    $(".tab").off('click').on('click', function() {
        const tabId = $(this).data('tab');
        
        $(".tab").removeClass("active");
        $(this).addClass("active");
        
        $(".content").removeClass("active").hide();
        $(`#${tabId}`).show().addClass("active");
        
        console.log("Cambiando a pestaña:", tabId);
        
        // Actualizar paginación al cambiar de pestaña
        updateFilteredItems(tabId);
        showPage(tabId, 1);
    });
}

function setupEventHandlers() {
    $("#editar_p").click(function() {
        // Cargar datos actuales del perfil (puedes obtenerlos de donde los tengas almacenados)
        loadProfileData();
        $("#modalEditar").show();
    });
    
    // Manejador para cancelar/cerrar el modal
    $("#cancelar, #modalEditar .close").click(function() {
        $("#modalEditar").hide();
    });
    
    // Manejador para confirmar los cambios
    $("#confirmar").click(function() {
        saveProfileData();
        $("#modalEditar").hide();
    });


    // Modal para cambiar imagen
    $("#botonCambiarImagen").click(function () {
        $("#imageModal").show();
    });

    // Vista previa de imagen
    $("#imageUpload").change(function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#imagePreview").attr("src", e.target.result).show();
                appState.imageModal.currentImage = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Guardar imagen
    $("#imageForm").submit(function (e) {
        e.preventDefault();
        if (appState.imageModal.currentImage) {
            $("#imagen-superior").attr("src", appState.imageModal.currentImage);
            $("#imagen-perfil").attr("src", appState.imageModal.currentImage);
            $("#imageModal").hide();
        }
    });

    // Cerrar modal de imagen
    $("#cancelImageBtn, #imageModal .close").click(function () {
        $("#imageModal").hide();
        $("#imagePreview").hide().attr("src", "#");
        $("#imageUpload").val("");
        appState.imageModal.currentImage = null;
    });

    // Checkboxes de asistencia
    $(".attendance-checkbox").change(function () {
        const participantId = $(this).data('participant');
        const day = $(this).data('day');
        const isChecked = $(this).is(':checked');
        console.log(`Participante: ${participantId}, Día: ${day}, Asistencia: ${isChecked}`);
    });

    // Búsquedas y filtros
    $("#searchInputSupervisor, #searchInputCoordinador, #searchInputAdmin").keyup(function () {
        const tab = $(this).attr('id').replace('searchInput', '').toLowerCase();
        filterTable(tab);
    });

    $("#eventFilterSupervisor, #eventFilterCoordinador, #typeFilterAdmin").change(function () {
        const tab = $(this).attr('id').replace('Filter', '').replace('event', '').replace('type', '').toLowerCase();
        filterTable(tab);
    });

    $("#resetFiltersSupervisor, #resetFiltersCoordinador, #resetFiltersAdmin").click(function () {
        const tab = $(this).attr('id').replace('resetFilters', '').toLowerCase();
        $(`#searchInput${tab.charAt(0).toUpperCase() + tab.slice(1)}`).val('');
        $(`#${tab === 'administrador' ? 'type' : 'event'}Filter${tab.charAt(0).toUpperCase() + tab.slice(1)}`).val('');
        filterTable(tab);
    });

    // Paginación
    $(document).on('click', '.page-number', function () {
        const tab = $(this).closest('.content').attr('id');
        const pageNumber = parseInt($(this).data('page'));
        showPage(tab, pageNumber);
    });

    $(document).on('click', '[data-action="prev"]', function () {
        const tab = $(this).closest('.content').attr('id');
        const currentPage = appState[tab].currentPage;
        if (currentPage > 1) {
            showPage(tab, currentPage - 1);
        }
    });

    $(document).on('click', '[data-action="next"]', function () {
        const tab = $(this).closest('.content').attr('id');
        const currentPage = appState[tab].currentPage;
        const totalPages = Math.ceil(appState[tab].filteredItems.length / appState[tab].perPage);

        if (currentPage < totalPages) {
            showPage(tab, currentPage + 1);
        }
    });

    $("#agregarEvento").click(function () {
        $("#agregareventoModal").show();
    })

    // Modal para eventos
    $("#addEventBtn").click(function () {
        isEditMode = false;
        $("#modalTitle").text("Agregar Nuevo Evento");
        $("#eventForm")[0].reset();
        $("#eventId").val("");
        $("#eventModal").show();
    });

    $(document).on('click', '.modify-btn', function () {
        isEditMode = true;
        const eventId = $(this).data('id');
        currentEventId = eventId;

        // Obtener datos del evento
        const event = appState.coordinador.events[eventId];

        // Llenar el formulario
        $("#modalTitle").text("Modificar Evento");
        $("#eventId").val(eventId);
        $("#eventName").val(event.nombre);
        $("#startDate").val(event.fecha_inicio);
        $("#endDate").val(event.fecha_fin);
        $("#eventCost").val(event.costo);

        $("#eventModal").show();
    });

    $(document).on('click', '.delete-btn', function () {
        currentEventId = $(this).data('id');
        $("#confirmModal").show();
    });

    // Cerrar modales
    $(".close, #cancelBtn").click(function () {
        $("#eventModal").hide();
        $("#agregareventoModal").hide();
    });

    $("#cancelDeleteBtn").click(function () {
        $("#confirmModal").hide();
    });

    // Cerrar modal al hacer clic fuera del contenido
    $(window).click(function (event) {
        if ($(event.target).hasClass('modal')) {
            $(".modal").hide();
        }
    });

    $(window).click(function (event) {
        if ($(event.target).hasClass('modal') && event.target.tagName === 'DIV') {
            $(".modal").hide();
        }
    });

    // Guardar evento
    $("#eventForm").submit(function (e) {
        e.preventDefault();

        const eventData = {
            nombre: $("#eventName").val(),
            fecha_inicio: $("#startDate").val(),
            fecha_fin: $("#endDate").val(),
            costo: $("#eventCost").val()
        };

        if (isEditMode) {
            // Modificar evento existente
            appState.coordinador.events[currentEventId] = eventData;

            // Actualizar la fila en la tabla
            const row = $(`#eventsTable tbody tr[data-id="${currentEventId}"]`);
            row.find("td:eq(0)").text(eventData.nombre);
            row.find("td:eq(1)").text(eventData.fecha_inicio);
            row.find("td:eq(2)").text(eventData.fecha_fin);
            row.find("td:eq(3)").text(eventData.costo);
        } else {
            // Agregar nuevo evento
            const newId = appState.coordinador.events.length;
            appState.coordinador.events.push(eventData);

            // Agregar nueva fila a la tabla
            const newRow = `
                <tr data-id="${newId}">
                    <td>${eventData.nombre}</td>
                    <td>${eventData.fecha_inicio}</td>
                    <td>${eventData.fecha_fin}</td>
                    <td>${eventData.costo}</td>
                    <td><button class="modify-btn" data-id="${newId}">Modificar</button></td>
                    <td><button class="delete-btn" data-id="${newId}">Eliminar</button></td>
                </tr>
            `;
            $("#eventsTable tbody").append(newRow);

            // Actualizar filtros y paginación
            updateFilteredItems('coordinador');
            showPage('coordinador', Math.ceil(appState.coordinador.filteredItems.length / appState.coordinador.perPage));
        }

        $("#eventModal").hide();
    });

    // Confirmar eliminación
    $("#confirmDeleteBtn").click(function () {
        // Eliminar del array
        appState.coordinador.events.splice(currentEventId, 1);

        // Eliminar la fila de la tabla
        $(`#eventsTable tbody tr[data-id="${currentEventId}"]`).remove();

        // Reindexar los IDs de las filas restantes
        $("#eventsTable tbody tr").each(function (index) {
            $(this).attr('data-id', index);
            $(this).find('.modify-btn, .delete-btn').attr('data-id', index);
        });

        // Actualizar el array en el estado
        appState.coordinador.events = [];
        $("#eventsTable tbody tr").each(function () {
            const id = $(this).data('id');
            appState.coordinador.events[id] = {
                nombre: $(this).find("td:eq(0)").text(),
                fecha_inicio: $(this).find("td:eq(1)").text(),
                fecha_fin: $(this).find("td:eq(2)").text(),
                costo: $(this).find("td:eq(3)").text()
            };
        });

        // Actualizar filtros y paginación
        updateFilteredItems('coordinador');
        showPage('coordinador', 1);

        $("#confirmModal").hide();
    });
}

// Función para cargar datos del perfil en el modal
function loadProfileData() {
    $('input[name="correo_user_edit"]').val(usuarioData.correo);
    $('input[name="numero_user_edit"]').val(usuarioData.numero);
    $('input[name="noemergencia_user_edit"]').val(usuarioData.emergencias);
    $('input[name="talla_user_edit"]').val(usuarioData.talla);
    $('input[name="enfermedades_user_edit"]').val(usuarioData.enfermedades);
    $('input[name="tratamiento_user_edit"]').val(usuarioData.tratamiento);
}

// Función para guardar los cambios del perfil
function saveProfileData() {
    // Obtener datos del formulario
    const newData = {
        nombre: $("#nombre").val(),
        email: $("#email").val(),
        numero: $("#numero").val(),
        emergencias: $("#emergencias").val(),
        talla: $("#talla").val(),
        enfermedades: $("#enfermedades").val(),
        tratamiento: $("#tratamiento").val()
    };
    
    // Actualizar la vista (esto es un ejemplo, ajusta según tu estructura real)
    $("#apodo-titulo").text(newData.nombre);
    $(".informacion p:contains('Correo')").text(`Correo: ${newData.email}`);
    $(".informacion p:contains('Número')").text(`Número: ${newData.numero}`);
    $(".informacion p:contains('Número Emergencias')").text(`Número Emergencias: ${newData.emergencias}`);
    $(".informacion p:contains('Talla Camisa')").text(`Talla Camisa: ${newData.talla}`);
    $(".informacion p:contains('Enfermedades')").text(`Enfermedades: ${newData.enfermedades}`);
    $(".informacion p:contains('Tratamiento')").text(`Tratamiento: ${newData.tratamiento}`);
    
    // Aquí deberías también hacer una llamada AJAX para guardar los datos en el servidor
    //saveToServer(newData);
}

function filterTable(tab) {
    updateFilteredItems(tab);
    showPage(tab, 1); // Siempre volver a la primera página al filtrar
}

function updateFilteredItems(tab) {
    let searchInput, filterSelect, allItems;

    if (tab === 'supervisor') {
        searchInput = $("#searchInputSupervisor").val().toLowerCase();
        filterSelect = $("#eventFilterSupervisor").val().toLowerCase();
        allItems = $("#participantsTable tbody tr");
    } else if (tab === 'coordinador') {
        searchInput = $("#searchInputCoordinador").val().toLowerCase();
        filterSelect = $("#eventFilterCoordinador").val().toLowerCase();
        allItems = $("#eventsTable tbody tr");
    } else if (tab === 'administrador') {
        searchInput = $("#searchInputAdmin").val().toLowerCase();
        filterSelect = $("#typeFilterAdmin").val().toLowerCase();
        allItems = $("#adminTable tbody tr");
    }

    // Primero mostrar todos los elementos
    allItems.show();

    // Filtrar elementos
    const filtered = allItems.filter(function () {
        const row = $(this);
        let matchesSearch = searchInput === "";
        let matchesFilter = filterSelect === "";

        // Buscar en todas las celdas si hay texto de búsqueda
        if (searchInput !== "") {
            row.find("td").each(function () {
                if ($(this).text().toLowerCase().includes(searchInput)) {
                    matchesSearch = true;
                    return false; // Salir del bucle si encuentra coincidencia
                }
            });
        }

        // Filtrar por selección si hay filtro aplicado
        if (filterSelect !== "") {
            if (tab === 'administrador') {
                const rowType = row.data('type');
                matchesFilter = rowType === filterSelect;
            } else {
                const evento = row.find("td:eq(0)").text().toLowerCase();
                matchesFilter = evento === filterSelect;
            }
        }

        return matchesSearch && matchesFilter;
    });

    // Guardar elementos filtrados en el estado
    appState[tab].filteredItems = filtered;

    return filtered;
}

function showPage(tab, pageNumber) {
    const state = appState[tab];
    const filteredItems = state.filteredItems;
    const totalPages = Math.ceil(filteredItems.length / state.perPage);

    // Actualizar estado
    state.currentPage = pageNumber;

    // Ocultar todos los elementos
    if (tab === 'supervisor') {
        $("#participantsTable tbody tr").hide();
    } else if (tab === 'coordinador') {
        $("#eventsTable tbody tr").hide();
    } else if (tab === 'administrador') {
        $("#adminTable tbody tr").hide();
    }

    // Mostrar solo los elementos de la página actual
    const start = (pageNumber - 1) * state.perPage;
    const end = start + state.perPage;

    filteredItems.slice(start, end).show();

    // Actualizar controles de paginación
    updatePaginationControls(tab, totalPages);
}

function updatePaginationControls(tab, totalPages) {
    const pagination = $(`#${tab} .pagination`);
    const pageNumbers = pagination.find('.page-numbers');

    // Limpiar números existentes
    pageNumbers.empty();

    // Siempre mostrar al menos la página 1
    pageNumbers.append(
        `<button class="page-number ${appState[tab].currentPage === 1 ? 'active' : ''}" 
        data-page="1">1</button>`
    );

    // Mostrar página 2 si existe
    if (totalPages > 1) {
        pageNumbers.append(
            `<button class="page-number ${appState[tab].currentPage === 2 ? 'active' : ''}" 
            data-page="2">2</button>`
        );
    }

    // Mostrar página 3 si existe
    if (totalPages > 2) {
        pageNumbers.append(
            `<button class="page-number ${appState[tab].currentPage === 3 ? 'active' : ''}" 
            data-page="3">3</button>`
        );
    }

    // Mostrar puntos suspensivos y última página si hay más de 3 páginas
    if (totalPages > 3) {
        // Mostrar página actual si está fuera del rango 1-3
        if (appState[tab].currentPage > 3 && appState[tab].currentPage < totalPages) {
            pageNumbers.append('<span class="page-dots">...</span>');
            pageNumbers.append(
                `<button class="page-number" data-page="${appState[tab].currentPage}">${appState[tab].currentPage}</button>`
            );
            pageNumbers.append('<span class="page-dots">...</span>');
        } else {
            pageNumbers.append('<span class="page-dots">...</span>');
        }

        pageNumbers.append(
            `<button class="page-number ${appState[tab].currentPage === totalPages ? 'active' : ''}" 
            data-page="${totalPages}">${totalPages}</button>`
        );
    }

    // Habilitar/deshabilitar botones de navegación
    pagination.find('[data-action="prev"]').prop('disabled', appState[tab].currentPage === 1);
    pagination.find('[data-action="next"]').prop('disabled', appState[tab].currentPage === totalPages || totalPages <= 1);
}
=======
    switchTab(initialTab);

    $(".tab").click(function() {
        const tabId = $(this).data('tab');
        switchTab(tabId);
    });
}

// Cambiar pestaña
function switchTab(tabId) {
    $(".tab").removeClass("active");
    $(`.tab[data-tab="${tabId}"]`).addClass("active");
    $(".content").removeClass("active").hide();
    $(`#${tabId}`).show().addClass("active");
    appState.currentTab = tabId;
}

// Cargar datos del perfil
function loadProfileData() {
    const currentData = {
        nombre: $("#apodo-titulo").text(),
        email: $(".informacion p:contains('Correo')").text().replace('Correo: ', ''),
        numero: $(".informacion p:contains('Número')").text().replace('Número: +52 ', ''),
        emergencias: $(".informacion p:contains('Número Emergencias')").text().replace('Número Emergencias: +52 ', ''),
        talla: $(".informacion p:contains('Talla Camisa')").text().replace('Talla Camisa: ', ''),
        enfermedades: $(".informacion p:contains('Enfermedades')").text().replace('Enfermedades: ', ''),
        tratamiento: $(".informacion p:contains('Tratamiento')").text().replace('Tratamiento: ', '')
    };
    
    $('#modalEditar #nombre').val(currentData.nombre);
    $('#modalEditar #email').val(currentData.email);
    $('#modalEditar #numero').val(currentData.numero);
    $('#modalEditar #emergencias').val(currentData.emergencias);
    $('#modalEditar #talla').val(currentData.talla);
    $('#modalEditar #enfermedades').val(currentData.enfermedades);
    $('#modalEditar #tratamiento').val(currentData.tratamiento);
    
    appState.modals.editProfile.show();
}

// Guardar datos del perfil
function saveProfileData() {
    const formData = $('#form-edit-info').serialize();
    
    $.post('../includes/modificar_datos.php', formData, function(response) {
        if (response.success) {
            location.reload();
        } else {
            alert('Error al guardar: ' + response.message);
        }
    }).fail(function() {
        alert('Error de conexión con el servidor');
    });
}

// Manejar eventos
$(document).on('click', '.salir', function() {
    const eventName = $(this).data('event');
    if (confirm(`¿Estás seguro que deseas salir del evento ${eventName}?`)) {
        $.post('salir_evento.php', { eventName }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        });
    }
});

$(document).on('click', '.detalles', function() {
    const eventName = $(this).data('event');
    window.location.href = `detalles_evento.php?nombre=${encodeURIComponent(eventName)}`;
});

// Confirmar eliminación de cuenta
$('#confirmDeleteAccountBtn').click(function() {
    $.post('eliminar_cuenta.php', function(response) {
        if (response.success) {
            window.location.href = '../logout.php';
        } else {
            alert('Error: ' + response.message);
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Mostrar/ocultar respuestas en formato de tabla
    document.querySelectorAll('.tabla-eventos .faq-item').forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });

    // Buscador de preguntas
    const buscador = document.getElementById('faq-search');
    if (buscador) {
        buscador.addEventListener('input', function() {
            const termino = this.value.toLowerCase();
            document.querySelectorAll('.tabla-eventos .faq-item').forEach(item => {
                const pregunta = item.querySelector('.faq-question').textContent.toLowerCase();
                const respuesta = item.querySelector('.faq-answer').textContent.toLowerCase();
                if (pregunta.includes(termino) || respuesta.includes(termino)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Manejo de nueva pregunta (mantener igual)
    const nuevaPreguntaBtn = document.getElementById('nueva-pregunta-btn');
    if (nuevaPreguntaBtn) {
        nuevaPreguntaBtn.addEventListener('click', function() {
            const form = document.getElementById('nueva-pregunta-form');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        });

        document.getElementById('enviar-pregunta-btn').addEventListener('click', function() {
            const preguntaText = document.getElementById('pregunta-text').value.trim();
            if (preguntaText) {
                enviarPregunta(preguntaText);
            }
        });
    }

    // Manejo de pestañas
    document.querySelectorAll('.tab-container .tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Ocultar todas las pestañas de contenido
            document.querySelectorAll('.content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Desactivar todas las pestañas
            document.querySelectorAll('.tab-container .tab').forEach(t => {
                t.classList.remove('active');
            });
            
            // Activar la pestaña seleccionada
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});

// Mantener las funciones enviarPregunta y cargarPreguntas igual
>>>>>>> 33aecbf629f13d3fb4938b4c0ec5b6869e26cf4f
