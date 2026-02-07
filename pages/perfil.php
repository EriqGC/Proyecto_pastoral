<?php 
include("../includes/seguridad.php");
include('../includes/conects.php');
include('crear_evento_modal.php');
include('modificar_evento_modal.php');
$link = Conectarse();

$correo = $_SESSION["correo"];
$result_id = mysqli_query($link, "SELECT id FROM PTL_USUARIOS WHERE correo = '$correo'");

if ($row = mysqli_fetch_assoc($result_id)) {
    $id_usuario = $row['id'];
}
mysqli_free_result($result_id);

// Determinar qué pestañas mostrar según el perfil
$perfil = $_SESSION["Perfil"] ?? '';
$showSupervisorTab = in_array($perfil, ['Supervisor', 'Coordinador', 'Administrador']);
$showCoordinadorTab = in_array($perfil, ['Coordinador', 'Administrador']);
$showAdministradorTab = ($perfil == 'Administrador');

// Consultas optimizadas con consultas preparadas para seguridad
$eventosParticipante = [];
$eventosSupervisor = [];
$eventosCoordinador = [];
$participantesSupervisor = [];
$contenidos = [];

// Consulta para eventos donde el usuario es participante
if ($stmt = $link->prepare("SELECT e.id, e.nombre, e.fecha_inicio, e.fecha_fin, e.costo, ue.tipo 
                          FROM PTL_USUARIO_EVENTO ue
                          JOIN PTL_EVENTOS e ON ue.eventos_id = e.id
                          WHERE ue.personas_id = ?")) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventosParticipante = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Consulta para eventos donde el usuario es supervisor
if ($showSupervisorTab && $stmt = $link->prepare("SELECT e.id, e.nombre, e.fecha_inicio, e.fecha_fin, e.costo, et.supervisor
                   FROM PTL_TRANSPORTE t
                   JOIN PTL_EVENTO_TRANSPORTE et ON t.id = et.transporte_id
                   JOIN PTL_EVENTOS e ON et.evento_id = e.id
                   WHERE et.supervisor = ?")) {
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventosSupervisor = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Consulta para eventos donde el usuario es coordinador
if ($showCoordinadorTab && $stmt = $link->prepare("SELECT e.id, e.nombre, e.fecha_inicio, e.fecha_fin, e.costo, e.creador
                    FROM PTL_EVENTOS e
                    WHERE e.creador = ?")) {
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventosCoordinador = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Consulta para participantes de eventos donde el usuario es supervisor
if ($showSupervisorTab && $stmt = $link->prepare("SELECT e.id AS evento_id, e.nombre AS evento, u.nombre, u.correo, u.telefono
                               FROM PTL_USUARIOS u
                               JOIN PTL_USUARIO_EVENTO ue ON u.id = ue.personas_id
                               JOIN PTL_EVENTOS e ON ue.eventos_id = e.id
                               JOIN PTL_EVENTO_TRANSPORTE et ON e.id = et.evento_id
                               JOIN PTL_TRANSPORTE t ON et.transporte_id = t.id
                               WHERE et.supervisor = ?")) {
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $result = $stmt->get_result();
    $participantesSupervisor = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Consulta para contenido administrable (solo administrador)
if ($showAdministradorTab) {
    $queryContenido = "SELECT 
        CASE 
            WHEN tipo = 'Noticia' THEN 'Noticias'
            WHEN tipo = 'Evento' THEN 'Eventos'
            ELSE 'Inicio'
        END AS pestana,
        id,
        nombre AS titulo,
        descripcion,
        (LENGTH(imagen) > 0) AS tiene_imagen,
        tipo
        FROM PTL_EVENTOS
        WHERE tipo IN ('Noticia', 'Evento')
        UNION
        SELECT 'Inicio' AS pestana,
        0 AS id,
        'Bienvenidos' AS titulo,
        'Página principal del sitio' AS descripcion,
        1 AS tiene_imagen,
        'pagina' AS tipo";
    $resultContenido = mysqli_query($link, $queryContenido);
    $contenidos = $resultContenido ? mysqli_fetch_all($resultContenido, MYSQLI_ASSOC) : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Usuario</title>
    <link rel="stylesheet" href="../assets/css/perfil.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/perfil.js"></script>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div id="contenedor-imagen-superior" data-texto="Mi Perfil">
        <img id="imagen-superior" src="../assets/img/imagen-perfil.png" alt="imagen perfil">
        <button class="boton-cambiar-imagen" id="botonCambiarImagen">Cambiar Imagen</button>
    </div>
    
    <div id="contenedor-pagina">
        <!-- Información del perfil -->
        <div class="contenedor-info">
            <div><img id="imagen-perfil" src="../assets/img/<?= htmlspecialchars($_SESSION['img_pfp'] ?? 'predeterminado.jpg') ?>" alt="imagen-usuario"></div>
            <div class="informacion">
                <h1 id="apodo-titulo"><?= htmlspecialchars($_SESSION["nombre"]) ?></h1>
                <p>Correo: <?= htmlspecialchars($_SESSION["correo"]) ?></p>
                <p>Número: +52 <?= htmlspecialchars($_SESSION["numero"] ?? $_SESSION["telefono"] ?? '') ?></p>
                <p>Número Emergencias: +52 <?= htmlspecialchars($_SESSION["numero_emergencia"] ?? $_SESSION["no_emergencia"] ?? '') ?></p>
                <p>Residencia Actual: <?= htmlspecialchars($_SESSION["residencia"] ?? $_SESSION["residencia_actual"] ?? '') ?></p>
                <p>Talla Camisa: <?= htmlspecialchars($_SESSION["talla"] ?? $_SESSION["talla_camisa"] ?? '') ?></p>
                <p>Enfermedades: <?= htmlspecialchars($_SESSION["enfermedades"] ?? 'No') ?></p>
                <p>Alergias: <?= htmlspecialchars($_SESSION["alergias"] ?? 'No') ?></p>
                <p>Puesto: <?= htmlspecialchars($perfil) ?></p>
            </div>
            <div id="btn_editar"><button type="button" id="editar_p">Editar</button></div>
        </div>

        <?php if ($showCoordinadorTab || $showAdministradorTab): ?>
            <button onclick="document.getElementById('crearEventoModal').style.display='block'" class="add-event-btn">Crear Evento</button>
        <?php endif; ?>

        <!-- Eventos del usuario -->
        <div id="contenedor-eventos">
            <h1>Eventos Registrados</h1>
            <div class="contenedor-superior-busqueda">
                <div id="buscador"><input type="text" id="busqueda" placeholder="Buscar por nombre"></div>
                <div id="filtro">
                    <select id="selectFiltro">
                        <option value="all" selected>Todos</option>
                        <option value="Evento">Eventos</option>
                        <option value="Noticia">Noticias</option>
                    </select>
                </div>
            </div>

            <table class="tabla-eventos">
                <thead>
                    <tr class="titulo-evento">
                        <th>Evento</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Costo</th><th>Rol</th>
                        <th>Detalles</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($eventosParticipante)): ?>
                        <tr><td colspan="7">No estás registrado en ningún evento</td></tr>
                    <?php else: ?>
                        <?php foreach ($eventosParticipante as $evento): ?>
                            <tr>
                                <td><?= htmlspecialchars($evento['nombre']) ?></td>
                                <td><?= htmlspecialchars($evento['fecha_inicio']) ?></td>
                                <td><?= htmlspecialchars($evento['fecha_fin']) ?></td>
                                <td>$<?= number_format($evento['costo'], 2) ?></td>
                                <td><?= htmlspecialchars($evento['tipo']) ?></td>
                                <td><button class="detalles" data-event="<?= htmlspecialchars($evento['id']) ?>">Detalles</button></td>
                                <td><button class="salir" data-event="<?= htmlspecialchars($evento['id']) ?>">Salir</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Secciones según perfil -->
        <?php if ($perfil != "Participante"): ?>
            <!-- Pestañas para perfiles no participantes -->
            <div id="contenedor-eventos">
                <div class="tab-container">
                    <?php if ($showSupervisorTab): ?>
                        <div class="tab <?= ($perfil == "Supervisor") ? 'active' : '' ?>" data-tab="supervisor">Supervisor</div>
                    <?php endif; ?>
                    <?php if ($showCoordinadorTab): ?>
                        <div class="tab <?= ($perfil == "Coordinador") ? 'active' : '' ?>" data-tab="coordinador">Coordinador</div>
                    <?php endif; ?>
                    <?php if ($showAdministradorTab): ?>
                        <div class="tab <?= ($perfil == "Administrador") ? 'active' : '' ?>" data-tab="administrador">Administrador</div>
                    <?php endif; ?>
                </div>

                <!-- Vista Supervisor -->
                <?php if ($showSupervisorTab): ?>
                <div id="supervisor" class="content <?= ($perfil == "Supervisor") ? 'active' : '' ?>">
                    <h2>Eventos como Supervisor</h2>
                    <div class="search-filter-container">
                        <input type="text" id="searchInputSupervisor" class="search-box" placeholder="Buscar...">
                        <select id="eventFilterSupervisor" class="filter-select">
                            <option value="">Todos los eventos</option>
                            <?php foreach (array_unique(array_column($eventosSupervisor, 'nombre')) as $evento): ?>
                                <option value="<?= htmlspecialchars($evento) ?>"><?= htmlspecialchars($evento) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button id="resetFiltersSupervisor" class="btn btn-secondary">Limpiar filtros</button>
                    </div>

                    <table id="participantsTable">
                        <thead>
                            <tr><th>Evento</th><th>Participante</th><th>Correo</th><th>Teléfono</th><th>Asistencia</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($participantesSupervisor)): ?>
                                <tr><td colspan="5">No hay participantes asignados a tus eventos</td></tr>
                            <?php else: ?>
                                <?php foreach ($participantesSupervisor as $participante): ?>
                                    <tr data-id='<?= htmlspecialchars($participante['evento_id']) ?>'>
                                        <td><?= htmlspecialchars($participante['evento']) ?></td>
                                        <td><?= htmlspecialchars($participante['nombre']) ?></td>
                                        <td><?= htmlspecialchars($participante['correo']) ?></td>
                                        <td><?= htmlspecialchars($participante['telefono']) ?></td>
                                        <td class='attendance-cell'>
                                            <input type='checkbox' class='attendance-checkbox' data-participant='<?= htmlspecialchars($participante['correo']) ?>' data-day='1'> Día 1
                                            <input type='checkbox' class='attendance-checkbox' data-participant='<?= htmlspecialchars($participante['correo']) ?>' data-day='2'> Día 2
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <button class="page-nav" data-action="prev" disabled>‹</button>
                        <div class="page-numbers"><button class="page-number active" data-page="1">1</button></div>
                        <button class="page-nav" data-action="next" disabled>›</button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Vista Coordinador -->
                <?php if ($showCoordinadorTab): ?>
                <div id="coordinador" class="content <?= ($perfil == "Coordinador") ? 'active' : '' ?>">
                    <h2>Eventos como Coordinador</h2>
                    <div class="search-filter-container">
                        <input type="text" id="searchInputCoordinador" class="search-box" placeholder="Buscar...">
                        <select id="eventFilterCoordinador" class="filter-select">
                            <option value="">Todos los eventos</option>
                            <?php foreach (array_unique(array_column($eventosCoordinador, 'nombre')) as $evento): ?>
                                <option value="<?= htmlspecialchars($evento) ?>"><?= htmlspecialchars($evento) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button id="resetFiltersCoordinador" class="btn btn-secondary">Limpiar filtros</button>
                    </div>

                    <table id="eventsTable">
                        <thead>
                            <tr><th>Evento</th><th>Fecha inicio</th><th>Fecha fin</th><th>Costo</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($eventosCoordinador)): ?>
                                <tr><td colspan="5">No has creado ningún evento</td></tr>
                            <?php else: ?>
                                <?php foreach ($eventosCoordinador as $evento): ?>
                                    <tr data-id='<?= htmlspecialchars($evento['id']) ?>'>
                                        <td><?= htmlspecialchars($evento['nombre']) ?></td>
                                        <td><?= htmlspecialchars($evento['fecha_inicio']) ?></td>
                                        <td><?= htmlspecialchars($evento['fecha_fin']) ?></td>
                                        <td>$<?= number_format($evento['costo'], 2) ?></td>
                                        <td>
                                            <button class='modify-btn' onclick="abrirModalModificacion(<?= htmlspecialchars($evento['id']) ?>)">Modificar</button>
                                            <button class='delete-btn' data-id='<?= htmlspecialchars($evento['id']) ?>'>Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <button class="page-nav" data-action="prev" disabled>‹</button>
                        <div class="page-numbers"><button class="page-number active" data-page="1">1</button></div>
                        <button class="page-nav" data-action="next" disabled>›</button>
                    </div>
                    <button class="add-event-btn" id="agregarEvento" onclick="document.getElementById('crearEventoModal').style.display='block'">Agregar Evento</button>
                </div>
                <?php endif; ?>

                <!-- Vista Administrador -->
                <?php if ($showAdministradorTab): ?>
                <div id="administrador" class="content <?= ($perfil == "Administrador") ? 'active' : '' ?>">
                    <h2>Panel de Administración</h2>
                    <div class="search-filter-container">
                        <input type="text" id="searchInputAdmin" class="search-box" placeholder="Buscar...">
                        <select id="typeFilterAdmin" class="filter-select">
                            <option value="">Todos los tipos</option>
                            <option value="evento">Evento</option>
                            <option value="noticia">Noticia</option>
                            <option value="pagina">Página</option>
                        </select>
                        <button id="resetFiltersAdmin" class="btn btn-secondary">Limpiar filtros</button>
                    </div>

                    <table id="adminTable">
                        <thead>
                            <tr><th>Pestaña</th><th>Título</th><th>Descripción</th><th>Imágenes</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contenidos)): ?>
                                <tr><td colspan="5">No hay contenido para administrar</td></tr>
                            <?php else: ?>
                                <?php foreach ($contenidos as $contenido): ?>
                                    <tr data-id='<?= htmlspecialchars($contenido['id']) ?>' data-type='<?= htmlspecialchars($contenido['tipo']) ?>'>
                                        <td><?= htmlspecialchars($contenido['pestana']) ?></td>
                                        <td><?= htmlspecialchars($contenido['titulo']) ?></td>
                                        <td><?= htmlspecialchars(substr($contenido['descripcion'], 0, 50)) ?>...</td>
                                        <td><?= $contenido['tiene_imagen'] ? 'Sí' : 'No' ?></td>
                                        <td>
                                            <button class='modify-btn' data-id='<?= htmlspecialchars($contenido['id']) ?>'>Modificar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <button class="page-nav" data-action="prev" disabled>‹</button>
                        <div class="page-numbers"><button class="page-number active" data-page="1">1</button></div>
                        <button class="page-nav" data-action="next" disabled>›</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Botón Eliminar Cuenta -->
        <div id="btn-eliminar-cuenta">
            <button type="button" id="eliminar-cuenta" class="btn btn-danger">Eliminar Cuenta</button>
        </div>
    </div>

    <!-- Modales -->
    <div id="confirmDeleteAccountModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirmar Eliminación de Cuenta</h2>
            <p>¿Estás seguro de que deseas eliminar tu cuenta permanentemente?</p>
            <div class="modal-actions">
                <button type="button" id="cancelDeleteAccountBtn" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="confirmDeleteAccountBtn" class="btn btn-danger">Eliminar Cuenta</button>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar imagen -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Cambiar Imagen de Perfil</h2>
            <form id="imageForm" action="../includes/actualizar_imagen.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="imageUpload">Seleccionar imagen:</label>
                    <input type="file" id="imageUpload" name="imagen_perfil" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label>Vista previa:</label>
                    <img id="imagePreview" src="#" alt="Vista previa" style="max-width: 200px; display: none;">
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelImageBtn" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar perfil -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Perfil</h2>
            <form action="modificar_datos.php" method="POST" id="form-edit-info">
                <div class="form-group">
                    <label for="numero">Número de teléfono:</label>
                    <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($_SESSION["numero"] ?? $_SESSION["telefono"] ?? '') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="emergencias">Número de emergencia:</label>
                    <input type="text" id="emergencias" name="no_emergencia" value="<?= htmlspecialchars($_SESSION["numero_emergencia"] ?? $_SESSION["no_emergencia"] ?? '') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="talla">Talla de camisa:</label>
                    <input type="text" id="talla" name="talla" value="<?= htmlspecialchars($_SESSION["talla"] ?? $_SESSION["talla_camisa"] ?? '') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="enfermedades">Enfermedades:</label>
                    <input type="text" id="enfermedades" name="enfermedades" value="<?= htmlspecialchars($_SESSION["enfermedades"] ?? 'No') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="alergias">Alergias:</label>
                    <input type="text" id="alergias" name="alergias" value="<?= htmlspecialchars($_SESSION["alergias"] ?? 'No') ?>" class="form-control">
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelar" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" id="confirmar" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    // Funcionalidad de pestañas
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.getAttribute('data-tab');
            
            // Desactivar todas las pestañas y contenidos
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));
            
            // Activar la pestaña y contenido seleccionados
            tab.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Modal para cambiar imagen
    document.getElementById('botonCambiarImagen').addEventListener('click', () => {
        document.getElementById('imageModal').style.display = 'block';
    });

    // Vista previa de imagen
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('imagePreview').src = event.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Modal para editar perfil
    document.getElementById('editar_p').addEventListener('click', () => {
        document.getElementById('modalEditar').style.display = 'block';
    });

    // Cerrar modales
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', () => {
            closeBtn.closest('.modal').style.display = 'none';
        });
    });

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // Función para abrir modal de modificación de evento
    function abrirModalModificacion(eventoId) {
        window.location.href = `?id=${eventoId}#modificarEventoModal`;
    }

    // Si hay un hash en la URL para el modal de modificación, mostrarlo
    if (window.location.hash === '#modificarEventoModal') {
        document.getElementById('modificarEventoModal').style.display = 'block';
    }

    // Filtrado de eventos
    document.getElementById('busqueda').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.tabla-eventos tbody tr');
        
        rows.forEach(row => {
            const eventName = row.cells[0].textContent.toLowerCase();
            row.style.display = eventName.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filtrado por tipo de evento
    document.getElementById('selectFiltro').addEventListener('change', function() {
        const filterValue = this.value;
        const rows = document.querySelectorAll('.tabla-eventos tbody tr');
        
        rows.forEach(row => {
            if (filterValue === 'all') {
                row.style.display = '';
            } else {
                const eventType = row.cells[4].textContent;
                row.style.display = eventType === filterValue ? '' : 'none';
            }
        });
    });

    // Funcionalidad para salir de un evento
    document.querySelectorAll('.salir').forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event');
            if (confirm('¿Estás seguro de que quieres salir de este evento?')) {
                fetch(`salir_evento.php?id=${eventId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').remove();
                        alert('Has salido del evento correctamente');
                    } else {
                        alert('Error al salir del evento: ' + data.message);
                    }
                });
            }
        });
    });

    // Funcionalidad para eliminar eventos (coordinador/administrador)
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-id');
            if (confirm('¿Estás seguro de que quieres eliminar este evento? Esta acción no se puede deshacer.')) {
                fetch(`eliminar_evento.php?id=${eventId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').remove();
                        alert('Evento eliminado correctamente');
                    } else {
                        alert('Error al eliminar el evento: ' + data.message);
                    }
                });
            }
        });
    });

    // Funcionalidad para marcar asistencia (supervisor)
    document.querySelectorAll('.attendance-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const participantEmail = this.getAttribute('data-participant');
            const day = this.getAttribute('data-day');
            const isPresent = this.checked;
            
            fetch('registrar_asistencia.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    correo: participantEmail,
                    dia: day,
                    presente: isPresent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Error al registrar asistencia: ' + data.message);
                    this.checked = !isPresent; // Revertir el cambio
                }
            });
        });
    });
    </script>
</body>
</html>