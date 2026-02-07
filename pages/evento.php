<?php
session_start();
include('../includes/conects.php');
include('modificar_evento_modal.php');
$link = Conectarse();
mysqli_set_charset($link, "utf8");

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
$showCasualTab = ($perfil == '');

// Obtener ID del evento desde POST o GET
$evento_id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 1);

// Definir el número de preguntas por página
$preguntasPorPagina = 5;

// Obtener el número de página actual desde GET
$paginaActual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$inicio = ($paginaActual - 1) * $preguntasPorPagina;

// Consultar información del evento
$queryEvento = "SELECT e.*, 
                    u.nombre AS coordinador_nombre, 
                    u.correo AS coordinador_email,
                    u.telefono AS coordinador_telefono,
                    u.imagen AS coordinador_imagen
                    FROM PTL_EVENTOS e
                    LEFT JOIN PTL_USUARIOS u ON e.creador = u.correo
                    WHERE e.id = $evento_id";
$resultEvento = mysqli_query($link, $queryEvento);

if (!$resultEvento || mysqli_num_rows($resultEvento) === 0) {
    header("Location: ../index.php?error=evento_no_encontrado");
    exit();
}

$evento = mysqli_fetch_assoc($resultEvento);

// Consultar preguntas frecuentes del evento con LIMIT para la paginación
$queryPreguntas = "SELECT pf.pregunta, pf.respuesta
                    FROM PTL_PGS_FRECUENTES pf
                    JOIN PTL_PGS_FRECUENTES_EVENTOS pfe ON pf.id = pfe.pgs_frecuentes_id
                    WHERE pfe.eventos_id = $evento_id
                    LIMIT $inicio, $preguntasPorPagina";
$resultPreguntas = mysqli_query($link, $queryPreguntas);
$preguntas = mysqli_fetch_all($resultPreguntas, MYSQLI_ASSOC);

// Consultar total de preguntas para calcular el número total de páginas
$queryTotalPreguntas = "SELECT COUNT(*) as total
                        FROM PTL_PGS_FRECUENTES pf
                        JOIN PTL_PGS_FRECUENTES_EVENTOS pfe ON pf.id = pfe.pgs_frecuentes_id
                        WHERE pfe.eventos_id = $evento_id";
$resultTotal = mysqli_query($link, $queryTotalPreguntas);
$totalPreguntas = mysqli_fetch_assoc($resultTotal)['total'];

// Calcular el número total de páginas
$totalPaginas = ceil($totalPreguntas / $preguntasPorPagina);

// Consulta para obtener supervisores
$supervisores = [];
if (!empty($evento['supervisor'])) {
    $supervisorEmails = explode(',', $evento['supervisor']);
    foreach ($supervisorEmails as $email) {
        $email = trim($email);
        $querySupervisor = "SELECT nombre, telefono, correo, imagen 
                            FROM PTL_USUARIOS 
                            WHERE correo = '$email'";
        $resultSupervisor = mysqli_query($link, $querySupervisor);
        if ($supervisor = mysqli_fetch_assoc($resultSupervisor)) {
            $supervisores[] = $supervisor;
        }
    }
}

// Consulta para obtener el itinerario (necesitarías crear esta tabla)
$itinerario = [];
$queryItinerario = "SELECT i.hora, i.actividad 
                    FROM PTL_ITINERARIO i
                    JOIN PTL_ITINERARIO_EVENTOS ie ON i.id = ie.itinerario_id
                    WHERE ie.evento_id = $evento_id 
                    ORDER BY i.hora";
$resultItinerario = mysqli_query($link, $queryItinerario);
while ($row = mysqli_fetch_assoc($resultItinerario)) {
    $itinerario[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evento['nombre'] ?? 'Evento'); ?> Preguntas Frecuentes</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/evento.css">
    <script defer src="../assets/js/evento.js"></script>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="hero">
        <img src="../assets/img/img_eventos/<?php echo htmlspecialchars($evento['imagen'] ?? 'img-roullet-3.jpg'); ?>" alt="Imagen de fondo">
        <div class="hero-overlay"></div>
        <form method="post" action="inscribirse_evento.php">
            <input type="hidden" name="evento_id" value="<?php echo $evento_id; ?>">
            <button type="submit" name="inscribirse" class="registrarse-button">¡Inscribite Ahora!</button>
        </form>
        <div class="event-details">
            <div>
                <div class="date"><?php echo date('d/m/Y', strtotime($evento['fecha_inicio'])); ?> - <?php echo htmlspecialchars($evento['ubicacion'] ?? ''); ?></div>
                <h2><?php echo htmlspecialchars($evento['nombre'] ?? 'Evento'); ?></h2>
                <p>
                    <?php echo htmlspecialchars($evento['descripcion'] ?? 'Descripción no disponible'); ?>
                </p>
            </div>
            <div class="view-options">
                <button class="view-option active" data-view="details">Detalles del Evento</button>
                <button class="view-option" data-view="itinerary">Ver Itinerario</button>
                <button class="view-option" data-view="staff">Ver Encargados</button>
            </div>
        </div>

        <!-- Contenedor para itinerario -->
        <div class="itinerary-container">
            <h3>Itinerario del Evento</h3>
            <div>
                <?php if (empty($itinerario)): ?>
                    <p>No hay itinerario disponible para este evento.</p>
                <?php else: ?>
                    <table class="itinerary-table">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Actividad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itinerario as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['hora']); ?></td>
                                    <td><?php echo htmlspecialchars($item['actividad']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="view-options">
                <button class="view-option active" data-view="details">Detalles del Evento</button>
                <button class="view-option" data-view="itinerary">Ver Itinerario</button>
                <button class="view-option" data-view="staff">Ver Encargados</button>
            </div>
        </div>

        <!-- Contenedor para encargados -->
        <div class="staff-container">
            <h3>Encargados del Evento</h3>
            <div class="staff-grid">
                <!-- Coordinador -->
                <div class="staff-card">
                    <!--<img src="../assets/img/<?php //echo htmlspecialchars($evento['coordinador_imagen'] ?? 'ejemplo.png'); 
                                                ?>" alt="Coordinador">-->
                    <img src="../assets/img/ejemplo.png" alt="Coordinador">
                    <h4>Coordinador</h4>
                    <p><?php echo htmlspecialchars($evento['coordinador_nombre'] ?? 'No asignado'); ?></p>
                    <p>Tel: <?php echo htmlspecialchars($evento['coordinador_telefono'] ?? 'N/A'); ?></p>
                    <p><?php echo htmlspecialchars($evento['coordinador_email'] ?? ''); ?></p>
                </div>

                <!-- Supervisores -->
                <?php foreach ($supervisores as $supervisor): ?>
                    <div class="staff-card">
                        <!--<img src="../assets/img/<?php //echo htmlspecialchars($supervisor['imagen'] ?? 'default.png'); 
                                                    ?>" alt="Supervisor">-->
                        <img src="../assets/img/ejemplo.png" alt="Supervisor">
                        <h4>Supervisor</h4>
                        <p><?php echo htmlspecialchars($supervisor['nombre']); ?></p>
                        <p>Tel: <?php echo htmlspecialchars($supervisor['telefono']); ?></p>
                        <p><?php echo htmlspecialchars($supervisor['correo']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-options">
                <button class="view-option active" data-view="details">Detalles del Evento</button>
                <button class="view-option" data-view="itinerary">Ver Itinerario</button>
                <button class="view-option" data-view="staff">Ver Encargados</button>
            </div>
        </div>
    </section>

    <button onclick="abrirModalModificacion(<?php echo $evento_id; ?>)">Modificar Evento</button>

    <section class="content">
    <?php if ($perfil != "Participante"): ?>
    <!-- Pestañas para perfiles no participantes -->
    <div class="tab-container">
        <div class="tab <?= ($perfil == "" || $perfil == "Participante") ? 'active' : '' ?>" data-tab="faq">Preguntas Frecuentes</div>
        <?php if ($showSupervisorTab): ?>
            <div class="tab" data-tab="supervisor">Supervisor</div>
        <?php endif; ?>
        <?php if ($showCoordinadorTab): ?>
            <div class="tab" data-tab="coordinador">Coordinador</div>
        <?php endif; ?>
        <?php if ($showAdministradorTab): ?>
            <div class="tab" data-tab="administrador">Administrador</div>
        <?php endif; ?>
    </div>

    <!-- Contenido de las pestañas -->
    <div id="faq" class="tab-content <?= ($perfil == "" || $perfil == "Participante") ? 'active' : '' ?>">
                    <div class="faq-title">Preguntas Frecuentes</div>

                    <div class="faq-search-container">
                        <input type="text" id="faq-search" placeholder="Buscar pregunta...">
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <button id="nueva-pregunta-btn">Hacer una pregunta</button>
                        <?php endif; ?>
                    </div>

                    <div id="nueva-pregunta-form" style="display: none;">
                        <textarea id="pregunta-text" placeholder="Escribe tu pregunta aquí..."></textarea>
                        <button id="enviar-pregunta-btn">Enviar pregunta</button>
                    </div>

                    <div class="faq-filter">
                        <div>Filtrar</div>
                        <div></div>
                    </div>

                    <?php foreach ($preguntas as $pregunta): ?>
                        <div class="faq-item">
                            <div class="faq-question"><?php echo htmlspecialchars($pregunta['pregunta']); ?></div>
                            <div class="faq-answer"><?php echo htmlspecialchars($pregunta['respuesta']); ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="pagination">
                        <?php if ($paginaActual > 1): ?>
                            <button onclick="window.location.href='?id=<?php echo $evento_id; ?>&pagina=<?php echo $paginaActual - 1; ?>'">&lt;</button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <button onclick="window.location.href='?id=<?php echo $evento_id; ?>&pagina=<?php echo $i; ?>'" class="<?php echo $i == $paginaActual ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>

                        <?php if ($paginaActual < $totalPaginas): ?>
                            <button onclick="window.location.href='?id=<?php echo $evento_id; ?>&pagina=<?php echo $paginaActual + 1; ?>'">&gt;</button>
                        <?php endif; ?>
                    </div>
                    <div class="input_pagination">
                        <input type="number" id="page-input" min="1" max="<?php echo $totalPaginas; ?>" placeholder="Ir a"
                            onchange="redirigirAPagina(this.value, <?php echo $evento_id; ?>)">
                        <button onclick="redirigirAPagina(document.getElementById('page-input').value, <?php echo $evento_id; ?>)">Ir</button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Vista Supervisor -->
            <?php if ($showSupervisorTab): ?>
                <div id="supervisor" class="tab-content">
                    <h2>Panel de Supervisor</h2>
                <div class="search-filter-container">

                </div>

                <table id="participantsTable">

                </table>

            </div>
        <?php endif; ?>

        <!-- Vista Coordinador -->
        <?php if ($showCoordinadorTab): ?>
        <div id="coordinador" class="tab-content">
            <h2>Panel de Coordinador</h2>
                <div class="search-filter-container">

                </div>

                <table id="eventsTable">

                </table>

            </div>
        <?php endif; ?>

        <!-- Vista Administrador -->
        <?php if ($showAdministradorTab): ?>
        <div id="administrador" class="tab-content">
            <h2>Panel de Administrador</h2>
                <div class="search-filter-container">

                </div>

                <table id="adminTable">

                </table>

            </div>
        <?php endif; ?>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Almacenar el ID del evento para uso en JavaScript
        const eventoId = <?php echo $evento_id; ?>;

        function redirigirAPagina(pagina, eventoId) {
            if (pagina >= 1 && pagina <= <?php echo $totalPaginas; ?>) {
                window.location.href = `?id=${eventoId}&pagina=${pagina}`;
            } else {
                alert('Por favor, ingresa un número de página válido.');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
    const viewOptions = document.querySelectorAll('.view-option');
    const viewContainers = {
        details: document.querySelector('.event-details'),
        itinerary: document.querySelector('.itinerary-container'),
        staff: document.querySelector('.staff-container')
    };

    // Ocultar Itinerario y Staff al inicio
    viewContainers.itinerary.classList.remove('active');
    viewContainers.staff.classList.remove('active');

    viewOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remover clase "active" de todos los botones y contenedores
            document.querySelectorAll('.view-option').forEach(btn => btn.classList.remove('active'));
            Object.values(viewContainers).forEach(container => container.classList.remove('active'));

            // Añadir clase "active" al botón y contenedor seleccionado
            this.classList.add('active');
            const viewType = this.dataset.view;
            if (viewContainers[viewType]) {
                viewContainers[viewType].classList.add('active');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remover clase active de todas las pestañas y contenidos
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Activar pestaña y contenido clickeados
            this.classList.add('active');
            const tabId = this.dataset.tab;
            document.getElementById(tabId).classList.add('active');
        });
    });
});
    </script>
</body>

</html>