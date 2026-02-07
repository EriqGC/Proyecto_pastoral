<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="prueba.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos adicionales */
        .search-filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-box {
            flex: 1;
            min-width: 200px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        .filter-select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
        }
        
        .attendance-checkbox {
            transform: scale(1.5);
            margin: 0 5px;
            cursor: pointer;
        }
        
        .attendance-cell {
            display: flex;
            justify-content: center;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: white;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .pagination button.active {
            background-color: #e63946;
            color: white;
            border-color: #e63946;
        }
        
        .tab-container {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
        }
        
        .tab.active {
            background-color: #e63946;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Pestañas para cambiar entre vistas -->
        <div class="tab-container">
            <div class="tab active" data-tab="supervisor">Supervisor</div>
            <div class="tab" data-tab="coordinador">Coordinador</div>
            <div class="tab" data-tab="administrador">Administrador</div>
        </div>

        <!-- Vista Supervisor -->
        <div id="supervisor" class="content active">
            <h2>Eventos Supervisor</h2>
            
            <!-- Barra de búsqueda y filtros -->
            <div class="search-filter-container">
                <input type="text" id="searchInputSupervisor" class="search-box" placeholder="Buscar...">
                
                <select id="eventFilterSupervisor" class="filter-select">
                    <option value="">Todos los eventos</option>
                    <?php
                    $eventos = ['Peregrinación', 'Misa', 'Retiro', 'Encuentro Misionero'];
                    foreach($eventos as $evento) {
                        echo "<option value='$evento'>$evento</option>";
                    }
                    ?>
                </select>
                
                <button id="resetFiltersSupervisor">Limpiar filtros</button>
            </div>
            
            <table id="participantsTable">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Participante</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Pase de lista</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $participantes = [
                        [
                            'evento' => 'Peregrinación',
                            'nombre' => 'Juan Perez Rosas',
                            'correo' => 'ejem@plo.com',
                            'telefono' => '2245345740',
                            'asistencia' => [false, false, false, false, false]
                        ],
                        [
                            'evento' => 'Misa',
                            'nombre' => 'Ana Martínez Monter',
                            'correo' => 'Rosa@gmail.com',
                            'telefono' => '2246347586',
                            'asistencia' => [false, false, false, false, false]
                        ],
                        [
                            'evento' => 'Retiro',
                            'nombre' => 'Carlos Sánchez López',
                            'correo' => 'carlos@example.com',
                            'telefono' => '2245874123',
                            'asistencia' => [false, false, false, false, false]
                        ],
                        [
                            'evento' => 'Encuentro Misionero',
                            'nombre' => 'María González Ruiz',
                            'correo' => 'maria@example.com',
                            'telefono' => '2245698741',
                            'asistencia' => [false, false, false, false, false]
                        ],
                        [
                            'evento' => 'Encuentro Misionero',
                            'nombre' => 'María González Ruiz',
                            'correo' => 'maria@example.com',
                            'telefono' => '2245698741',
                            'asistencia' => [false, false, false, false, false]
                        ],
                        [
                            'evento' => 'Encuentro Misionero',
                            'nombre' => 'María González Ruiz',
                            'correo' => 'maria@example.com',
                            'telefono' => '2245698741',
                            'asistencia' => [false, false, false, false, false]
                        ]
                    ];
                    
                    foreach($participantes as $index => $participante) {
                        echo "<tr data-id='$index'>";
                        echo "<td>{$participante['evento']}</td>";
                        echo "<td>{$participante['nombre']}</td>";
                        echo "<td>{$participante['correo']}</td>";
                        echo "<td>{$participante['telefono']}</td>";
                        echo "<td class='attendance-cell'>";
                        
                        foreach($participante['asistencia'] as $day => $checked) {
                            $checkedAttr = $checked ? 'checked' : '';
                            echo "<input type='checkbox' class='attendance-checkbox' 
                                  data-participant='$index' data-day='$day' $checkedAttr>";
                        }
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Paginación -->
            <div class="pagination">
    <button class="page-nav" data-action="prev" disabled>‹</button>
    <div class="page-numbers">
        <button class="page-number active" data-page="1">1</button>
    </div>
    <button class="page-nav" data-action="next" disabled>›</button>
</div>
        </div>

        <!-- Vista Coordinador -->
        <div id="coordinador" class="content">
            <h2>Eventos Coordinador</h2>
            
            <!-- Barra de búsqueda y filtros -->
            <div class="search-filter-container">
                <input type="text" id="searchInputCoordinador" class="search-box" placeholder="Buscar...">
                
                <select id="eventFilterCoordinador" class="filter-select">
                    <option value="">Todos los eventos</option>
                    <?php
                    $eventosCoordinador = ['Peregrinación', 'Misa', 'Retiro', 'Encuentro Misionero', 'Taller'];
                    foreach($eventosCoordinador as $evento) {
                        echo "<option value='$evento'>$evento</option>";
                    }
                    ?>
                </select>
                
                <button id="resetFiltersCoordinador">Limpiar filtros</button>
            </div>
            
            <table id="eventsTable">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Costo</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $eventos = [
                        [
                            'nombre' => 'Peregrinación',
                            'fecha_inicio' => '12/03 6:00am',
                            'fecha_fin' => '18/03 10:00am',
                            'costo' => '500$'
                        ],
                        [
                            'nombre' => 'Encuentro Misionero',
                            'fecha_inicio' => '06/01 12:00pm',
                            'fecha_fin' => '06/01 03:00pm',
                            'costo' => '500$'
                        ],
                        [
                            'nombre' => 'Taller',
                            'fecha_inicio' => '15/04 9:00am',
                            'fecha_fin' => '15/04 1:00pm',
                            'costo' => '300$'
                        ],
                        [
                            'nombre' => 'Misa',
                            'fecha_inicio' => '20/05 5:00pm',
                            'fecha_fin' => '20/05 6:00pm',
                            'costo' => '0$'
                        ]
                    ];
                    
                    foreach($eventos as $index => $evento) {
                        echo "<tr data-id='$index'>";
                        echo "<td>{$evento['nombre']}</td>";
                        echo "<td>{$evento['fecha_inicio']}</td>";
                        echo "<td>{$evento['fecha_fin']}</td>";
                        echo "<td>{$evento['costo']}</td>";
                        echo "<td><button class='modify-btn'>Modificar</button></td>";
                        echo "<td><button class='delete-btn'>Eliminar</button></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Paginación -->
            <div class="pagination">
    <button class="page-nav" data-action="prev" disabled>‹</button>
    <div class="page-numbers">
        <button class="page-number active" data-page="1">1</button>
    </div>
    <button class="page-nav" data-action="next" disabled>›</button>
</div>
            
            <button class="add-event-btn">Agregar Evento</button>
        </div>

        <!-- Vista Administrador -->
        <div id="administrador" class="content">
            <h2>Panel de Administración</h2>
            
            <!-- Barra de búsqueda y filtros -->
            <div class="search-filter-container">
                <input type="text" id="searchInputAdmin" class="search-box" placeholder="Buscar...">
                
                <select id="typeFilterAdmin" class="filter-select">
                    <option value="">Todos los tipos</option>
                    <option value="evento">Evento</option>
                    <option value="noticia">Noticia</option>
                    <option value="anuncio">Anuncio</option>
                </select>
                
                <button id="resetFiltersAdmin">Limpiar filtros</button>
            </div>
            
            <table id="adminTable">
                <thead>
                    <tr>
                        <th>Pestaña</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Imágenes</th>
                        <th>Modificar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contenidos = [
                        [
                            'pestana' => 'Inicio',
                            'titulo' => 'Bienvenidos',
                            'descripcion' => 'Página principal del sitio',
                            'imagenes' => 3,
                            'tipo' => 'pagina'
                        ],
                        [
                            'pestana' => 'Eventos',
                            'titulo' => 'Próximos Eventos',
                            'descripcion' => 'Listado de eventos próximos',
                            'imagenes' => 2,
                            'tipo' => 'pagina'
                        ],
                        [
                            'pestana' => 'Noticias',
                            'titulo' => 'Últimas Noticias',
                            'descripcion' => 'Noticias relevantes',
                            'imagenes' => 5,
                            'tipo' => 'noticia'
                        ],
                        [
                            'pestana' => 'Anuncios',
                            'titulo' => 'Recordatorio',
                            'descripcion' => 'Recordatorio importante',
                            'imagenes' => 1,
                            'tipo' => 'anuncio'
                        ]
                    ];
                    
                    foreach($contenidos as $index => $contenido) {
                        echo "<tr data-id='$index' data-type='{$contenido['tipo']}'>";
                        echo "<td>{$contenido['pestana']}</td>";
                        echo "<td>{$contenido['titulo']}</td>";
                        echo "<td>{$contenido['descripcion']}</td>";
                        echo "<td>{$contenido['imagenes']}</td>";
                        echo "<td><button class='modify-btn'>Modificar</button></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Paginación -->
            <div class="pagination">
    <button class="page-nav" data-action="prev" disabled>‹</button>
    <div class="page-numbers">
        <button class="page-number active" data-page="1">1</button>
    </div>
    <button class="page-nav" data-action="next" disabled>›</button>
</div>
        </div>
    </div>

    <script>
$(document).ready(function() {
    // Estado global de la aplicación
    const appState = {
        supervisor: {
            currentPage: 1,
            perPage: 5,
            filteredItems: []
        },
        coordinador: {
            currentPage: 1,
            perPage: 5,
            filteredItems: []
        },
        administrador: {
            currentPage: 1,
            perPage: 5,
            filteredItems: []
        }
    };

    // Inicialización
    initPagination();
    setupEventHandlers();

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

    function setupEventHandlers() {
        // Manejo de pestañas
        $(".tab").click(function() {
            const tabId = $(this).data('tab');
            $(".tab").removeClass("active");
            $(this).addClass("active");
            $(".content").removeClass("active");
            $("#" + tabId).addClass("active");
        });
        
        // Checkboxes de asistencia
        $(".attendance-checkbox").change(function() {
            const participantId = $(this).data('participant');
            const day = $(this).data('day');
            const isChecked = $(this).is(':checked');
            console.log(`Participante: ${participantId}, Día: ${day}, Asistencia: ${isChecked}`);
        });
        
        // Búsquedas y filtros
        $("#searchInputSupervisor, #searchInputCoordinador, #searchInputAdmin").keyup(function() {
            const tab = $(this).attr('id').replace('searchInput', '').toLowerCase();
            filterTable(tab);
        });
        
        $("#eventFilterSupervisor, #eventFilterCoordinador, #typeFilterAdmin").change(function() {
            const tab = $(this).attr('id').replace('Filter', '').replace('event', '').replace('type', '').toLowerCase();
            filterTable(tab);
        });
        
        $("#resetFiltersSupervisor, #resetFiltersCoordinador, #resetFiltersAdmin").click(function() {
            const tab = $(this).attr('id').replace('resetFilters', '').toLowerCase();
            $(`#searchInput${tab.charAt(0).toUpperCase() + tab.slice(1)}`).val('');
            $(`#${tab === 'administrador' ? 'type' : 'event'}Filter${tab.charAt(0).toUpperCase() + tab.slice(1)}`).val('');
            filterTable(tab);
        });
        
        // Paginación
        $(document).on('click', '.page-number', function() {
            const tab = $(this).closest('.content').attr('id');
            const pageNumber = parseInt($(this).data('page'));
            showPage(tab, pageNumber);
        });
        
        $(document).on('click', '[data-action="prev"]', function() {
            const tab = $(this).closest('.content').attr('id');
            const currentPage = appState[tab].currentPage;
            if (currentPage > 1) {
                showPage(tab, currentPage - 1);
            }
        });
        
        $(document).on('click', '[data-action="next"]', function() {
            const tab = $(this).closest('.content').attr('id');
            const currentPage = appState[tab].currentPage;
            const totalPages = Math.ceil(appState[tab].filteredItems.length / appState[tab].perPage);
            
            if (currentPage < totalPages) {
                showPage(tab, currentPage + 1);
            }
        });
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
    const filtered = allItems.filter(function() {
        const row = $(this);
        let matchesSearch = searchInput === "";
        let matchesFilter = filterSelect === "";
        
        // Buscar en todas las celdas si hay texto de búsqueda
        if (searchInput !== "") {
            row.find("td").each(function() {
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
});
</script>
</body>
</html>