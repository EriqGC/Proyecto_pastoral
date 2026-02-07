<?php include("../includes/seguridad.php") ?>
<?php
include('../includes/conects.php');
session_start();
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Obtener ID del usuario actual
$userId = $_SESSION['id'] ?? 0;
$perfil = $_SESSION['Perfil'] ?? '';
$nombreUsuario = $_SESSION['nombre'] ?? '';
$correoUsuario = $_SESSION['correo'] ?? '';

// Procesar formulario de reporte personalizado
$reportesIniciales = []; // Inicializar array vacío

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_reporte_personalizado'])) {
    // Debug: Ver los datos recibidos
    error_log("Datos recibidos: " . print_r($_POST, true));

    // Procesar tipos de reporte
    $tiposReporte = [];
    if (isset($_POST['tipo_reporte'])) {
        if (is_array($_POST['tipo_reporte'])) {
            $tiposReporte = $_POST['tipo_reporte'];
        } else {
            $tiposReporte = [$_POST['tipo_reporte']];
        }
    }

    // Procesar filtros
    $filtros = [
        'evento_ids' => isset($_POST['evento_ids']) ? (array)$_POST['evento_ids'] : [],
        'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
        'fecha_fin' => $_POST['fecha_fin'] ?? ''
    ];

    // Generar solo los reportes seleccionados
    foreach ($tiposReporte as $tipo) {
        $reportesIniciales[$tipo] = generarReporte($link, $tipo, $perfil, $nombreUsuario, $correoUsuario, $filtros);
    }

    // Crear notificación más detallada
    $notificaciones[] = [
        'titulo' => 'Reporte Generado',
        'mensaje_corto' => "Se generó el reporte personalizado con " . count($tiposReporte) . " secciones",
        'mensaje_largo' => "<div class='notificacion-detalle'>
            <h3>Reporte Personalizado Generado</h3>
            <p><strong>Secciones incluidas:</strong> " . implode(", ", $tiposReporte) . "</p>
            <p><strong>Filtros aplicados:</strong></p>
            <ul>
                " . (!empty($filtros['evento_ids']) ? "<li>Eventos: " . implode(", ", $filtros['evento_ids']) . "</li>" : "") . "
                " . (!empty($filtros['fecha_inicio']) ? "<li>Desde: " . htmlspecialchars($filtros['fecha_inicio']) . "</li>" : "") . "
                " . (!empty($filtros['fecha_fin']) ? "<li>Hasta: " . htmlspecialchars($filtros['fecha_fin']) . "</li>" : "") . "
            </ul>
            <div class='notificacion-acciones'>
                <button class='ver-evento-btn' onclick='$(\".report-tab[data-tab=\\\"" . $tiposReporte[0] . "\\\"]\").click()'>
                    Ver Reporte
                </button>
            </div>
        </div>",
        'fecha' => date('Y-m-d H:i:s'),
        'tipo' => 'reporte',
        'evento_id' => 0
    ];

    // Guardar en sesión para mantener los filtros
    $_SESSION['reporte_personalizado'] = [
        'tipos' => $tiposReporte,
        'filtros' => $filtros,
        'notificacion' => end($notificaciones) // Guardar la última notificación
    ];

    // Redirigir para evitar reenvío del formulario
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Al inicio del script, verificar si hay un reporte en sesión
if (!empty($_SESSION['reporte_personalizado'])) {
    $reporteSession = $_SESSION['reporte_personalizado'];
    if (!empty($reporteSession['notificacion'])) {
        $notificaciones[] = $reporteSession['notificacion'];
    }
    unset($_SESSION['reporte_personalizado']);

    // Mostrar el primer reporte seleccionado
    echo "<script>
        $(document).ready(function() {
            $('[data-tab=\"{$reporteSession['tipos'][0]}\"]').click();
        });
    </script>";
}

// Función para obtener notificaciones
function obtenerNotificaciones($link, $userId, $perfil, $nombreUsuario, $correoUsuario)
{
    $notificaciones = [];

    // 1. Notificaciones de inscripción a eventos
    $queryInscripciones = "SELECT e.id AS evento_id, e.nombre, e.descripcion, e.fecha_inicio, e.costo, 
                          ue.pagado, e.creador, e.supervisor, e.imagen, e.fecha_fin, e.ubicacion
                          FROM PTL_USUARIOS_EVENTOS ue
                          JOIN PTL_EVENTOS e ON ue.evento_id = e.id
                          WHERE ue.persona_id = $userId
                          ORDER BY ue.fecha DESC";
    $resultInscripciones = mysqli_query($link, $queryInscripciones);

    while ($row = mysqli_fetch_assoc($resultInscripciones)) {
        $estadoPago = $row['pagado'] == 1 ? 'completado' : 'pendiente';
        $mensajeCorto = $estadoPago == 'pendiente'
            ? "Inscripción al evento '{$row['nombre']}' (Pago pendiente)"
            : "Inscripción confirmada al evento '{$row['nombre']}'";

        $mensajeLargo = "
            <div class='notificacion-detalle'>
                <div class='notificacion-header'>
                    <img src='../assets/img/{$row['imagen']}' alt='{$row['nombre']}' class='evento-imagen' onerror=\"this.src='../assets/img/ejemplo.jpg'\">
                    <div>
                        <h3>{$row['nombre']}</h3>
                        <p class='fecha-evento'>" . date('d/m/Y', strtotime($row['fecha_inicio'])) . " - " . date('d/m/Y', strtotime($row['fecha_fin'])) . "</p>
                        <p class='ubicacion-evento'><i class='fas fa-map-marker-alt'></i> {$row['ubicacion']}</p>
                    </div>
                </div>
                <div class='notificacion-body'>
                    <p><strong>Descripción:</strong> {$row['descripcion']}</p>
                    <p><strong>Costo:</strong> {$row['costo']}</p>
                    <p><strong>Encargados:</strong> {$row['creador']}, {$row['supervisor']}</p>
                    <p><strong>Estado de pago:</strong> " . ucfirst($estadoPago) . "</p>
                    <div class='notificacion-acciones'>
                        <a href='evento.php?id={$row['evento_id']}' class='ver-evento-btn'>Ver Evento</a>
                    </div>
                </div>
            </div>
        ";

        $notificaciones[] = [
            'titulo' => 'Inscripción a Evento',
            'mensaje_corto' => $mensajeCorto,
            'mensaje_largo' => $mensajeLargo,
            'fecha' => date('Y-m-d H:i:s'),
            'tipo' => 'inscripcion',
            'evento_id' => $row['evento_id'],
            'imagen' => $row['imagen']
        ];
    }

    // 2. Notificaciones de asignación de roles
    if ($perfil != 'Participante') {
        // Para supervisores
        if (in_array($perfil, ['Supervisor', 'Coordinador', 'Administrador'])) {
            $querySupervisor = "SELECT e.id AS evento_id, e.nombre, e.descripcion, e.fecha_inicio, e.creador, e.supervisor, e.imagen 
                              FROM PTL_EVENTOS e
                              WHERE e.supervisor = '" . mysqli_real_escape_string($link, $nombreUsuario) . "'";
            $resultSupervisor = mysqli_query($link, $querySupervisor);

            while ($row = mysqli_fetch_assoc($resultSupervisor)) {
                $mensajeLargo = "
                    <div class='notificacion-detalle'>
                        <div class='notificacion-header'>
                            <img src='../assets/img/{$row['imagen']}' alt='{$row['nombre']}' class='evento-imagen' onerror=\"this.src='../assets/img/ejemplo.jpg'\">
                            <div>
                                <h3>{$row['nombre']}</h3>
                                <p class='fecha-evento'>" . date('d/m/Y', strtotime($row['fecha_inicio'])) . "</p>
                            </div>
                        </div>
                        <div class='notificacion-body'>
                            <p><strong>Descripción:</strong> {$row['descripcion']}</p>
                            <p><strong>Rol asignado:</strong> Supervisor</p>
                            <p><strong>Coordinador:</strong> {$row['creador']}</p>
                            <div class='notificacion-acciones'>
                                <a href='evento.php?id={$row['evento_id']}' class='ver-evento-btn'>Ver Evento</a>
                                <a href='reportes.php?tipo=asistencia&evento_id={$row['evento_id']}' class='reporte-btn'>
                                    <i class='fas fa-chart-bar'></i> Ver reporte
                                </a>
                            </div>
                        </div>
                    </div>
                ";

                $notificaciones[] = [
                    'titulo' => 'Asignación de Rol',
                    'mensaje_corto' => "Has sido asignado como supervisor del evento '{$row['nombre']}'",
                    'mensaje_largo' => $mensajeLargo,
                    'fecha' => date('Y-m-d H:i:s'),
                    'tipo' => 'asignacion',
                    'evento_id' => $row['evento_id']
                ];
            }
        }

        // Para coordinadores
        if (in_array($perfil, ['Coordinador', 'Administrador'])) {
            $queryCoordinador = "SELECT e.id AS evento_id, e.nombre, e.descripcion, e.fecha_inicio, e.creador, e.supervisor, e.imagen 
                                FROM PTL_EVENTOS e
                                WHERE e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
            $resultCoordinador = mysqli_query($link, $queryCoordinador);

            while ($row = mysqli_fetch_assoc($resultCoordinador)) {
                $mensajeLargo = "
                    <div class='notificacion-detalle'>
                        <div class='notificacion-header'>
                            <img src='../assets/img/{$row['imagen']}' alt='{$row['nombre']}' class='evento-imagen' onerror=\"this.src='../assets/img/ejemplo.jpg'\">
                            <div>
                                <h3>{$row['nombre']}</h3>
                                <p class='fecha-evento'>" . date('d/m/Y', strtotime($row['fecha_inicio'])) . "</p>
                            </div>
                        </div>
                        <div class='notificacion-body'>
                            <p><strong>Descripción:</strong> {$row['descripcion']}</p>
                            <p><strong>Rol asignado:</strong> Coordinador</p>
                            <p><strong>Supervisor:</strong> {$row['supervisor']}</p>
                            <div class='notificacion-acciones'>
                                <a href='evento.php?id={$row['evento_id']}' class='ver-evento-btn'>Ver Evento</a>
                                <a href='reportes.php?tipo=participantes&evento_id={$row['evento_id']}' class='reporte-btn'>
                                    <i class='fas fa-users'></i> Ver participantes
                                </a>
                            </div>
                        </div>
                    </div>
                ";

                $notificaciones[] = [
                    'titulo' => 'Asignación de Rol',
                    'mensaje_corto' => "Has sido asignado como coordinador del evento '{$row['nombre']}'",
                    'mensaje_largo' => $mensajeLargo,
                    'fecha' => date('Y-m-d H:i:s'),
                    'tipo' => 'asignacion',
                    'evento_id' => $row['evento_id']
                ];
            }
        }
    }

    // 3. Notificaciones de transporte
    $queryTransporte = "SELECT t.id AS transporte_id, e.id AS evento_id, e.nombre AS evento_nombre, 
                       e.fecha_inicio, t.vehiculo, tu.no_asiento, e.imagen
                       FROM PTL_TRANSPORTE_USUARIOS tu
                       JOIN PTL_TRANSPORTE t ON tu.transporte_id = t.id
                       JOIN PTL_EVENTO_TRANSPORTE et ON t.id = et.transporte_id
                       JOIN PTL_EVENTOS e ON et.evento_id = e.id
                       WHERE tu.personas_id = $userId
                       ORDER BY tu.fecha DESC";
    $resultTransporte = mysqli_query($link, $queryTransporte);

    while ($row = mysqli_fetch_assoc($resultTransporte)) {
        $mensajeLargo = "
            <div class='notificacion-detalle'>
                <div class='notificacion-header'>
                    <img src='../assets/img/{$row['imagen']}' alt='{$row['evento_nombre']}' class='evento-imagen' onerror=\"this.src='../assets/img/ejemplo.jpg'\">
                    <div>
                        <h3>Asignación de Transporte</h3>
                        <p class='fecha-evento'>Evento: {$row['evento_nombre']}</p>
                        <p class='fecha-evento'>" . date('d/m/Y', strtotime($row['fecha_inicio'])) . "</p>
                    </div>
                </div>
                <div class='notificacion-body'>
                    <p><strong>Vehículo:</strong> {$row['vehiculo']}</p>
                    <p><strong>Número de asiento:</strong> {$row['no_asiento']}</p>
                    <div class='notificacion-acciones'>
                        <a href='evento.php?id={$row['evento_id']}' class='ver-evento-btn'>Ver Evento</a>
                    </div>
                </div>
            </div>
        ";

        $notificaciones[] = [
            'titulo' => 'Asignación de Transporte',
            'mensaje_corto' => "Asignación de transporte para el evento '{$row['evento_nombre']}' - Asiento {$row['no_asiento']}",
            'mensaje_largo' => $mensajeLargo,
            'fecha' => date('Y-m-d H:i:s'),
            'tipo' => 'transporte',
            'evento_id' => $row['evento_id']
        ];
    }


    return $notificaciones;
}

// Función para generar reportes con filtros según perfil
function generarReporte($link, $tipoReporte, $perfil, $nombreUsuario, $correoUsuario, $filtros = [])
{
    // Validar tipo de reporte
    $tiposPermitidos = ['eventos', 'participantes', 'salud', 'transporte', 'habilidades'];
    if (!in_array($tipoReporte, $tiposPermitidos)) {
        error_log("Tipo de reporte no válido: $tipoReporte");
        return [];
    }

    $reporte = [];
    $whereClause = "";
    $joinClause = "";

    // Aplicar filtros según perfil
    if ($perfil == 'Supervisor') {
        $joinClause = "JOIN PTL_EVENTOS e ON ue.evento_id = e.id AND e.supervisor = '" . mysqli_real_escape_string($link, $nombreUsuario) . "'";
    } elseif ($perfil == 'Coordinador') {
        $joinClause = "JOIN PTL_EVENTOS e ON ue.evento_id = e.id AND e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
    }

    // Aplicar filtros adicionales
    if (!empty($filtros['evento_ids'])) {
        $eventoIds = array_map('intval', $filtros['evento_ids']);
        $eventoIdsStr = implode(',', $eventoIds);
        $whereClause = "WHERE e.id IN ($eventoIdsStr)";
    }

    if (!empty($filtros['fecha_inicio'])) {
        $fechaInicio = mysqli_real_escape_string($link, $filtros['fecha_inicio']);
        $whereClause .= empty($whereClause) ? "WHERE " : " AND ";
        $whereClause .= "e.fecha_inicio >= '$fechaInicio'";
    }

    if (!empty($filtros['fecha_fin'])) {
        $fechaFin = mysqli_real_escape_string($link, $filtros['fecha_fin']);
        $whereClause .= empty($whereClause) ? "WHERE " : " AND ";
        $whereClause .= "e.fecha_inicio <= '$fechaFin'";
    }

    switch ($tipoReporte) {
        case 'eventos':
            $query = "SELECT e.id, e.nombre, e.descripcion, e.fecha_inicio, e.fecha_fin, e.ubicacion, 
                     e.creador, e.supervisor, e.categoria, COUNT(ue.persona_id) as asistentes,
                     (SELECT COUNT(*) FROM PTL_PGS_FRECUENTES_EVENTOS pfe WHERE pfe.eventos_id = e.id) as preguntas_frecuentes,
                     (SELECT GROUP_CONCAT(DISTINCT t.vehiculo) FROM PTL_EVENTO_TRANSPORTE et 
                      JOIN PTL_TRANSPORTE t ON et.transporte_id = t.id WHERE et.evento_id = e.id) as transportes
                     FROM PTL_EVENTOS e
                     LEFT JOIN PTL_USUARIOS_EVENTOS ue ON e.id = ue.evento_id";

            if ($perfil == 'Supervisor') {
                $query .= " WHERE e.supervisor = '" . mysqli_real_escape_string($link, $nombreUsuario) . "'";
            } elseif ($perfil == 'Coordinador') {
                $query .= " WHERE e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
            } else {
                $query .= " $whereClause";
            }

            $query .= " GROUP BY e.id
                       ORDER BY e.fecha_inicio DESC";
            break;

        case 'participantes':
            $query = "SELECT u.id, u.nombre, u.apellido_p, u.apellido_m, u.sexo, 
                     TIMESTAMPDIFF(YEAR, u.fecha_nacimiento, CURDATE()) as edad,
                     u.enfermedades, u.alergias, u.institucion_educativa, u.licenciatura,
                     u.talla_camisa, u.no_misiones_realizadas, u.puesto,
                     IF(u.id_upaep IS NOT NULL, 'UPAEP', 'Invitado') as tipo_participante,
                     ue.asistencia, ue.pagado
                     FROM PTL_USUARIOS u
                     JOIN PTL_USUARIOS_EVENTOS ue ON u.id = ue.persona_id
                     JOIN PTL_EVENTOS e ON ue.evento_id = e.id";

            if ($perfil == 'Supervisor') {
                $query .= " WHERE e.supervisor = '" . mysqli_real_escape_string($link, $nombreUsuario) . "'";
            } elseif ($perfil == 'Coordinador') {
                $query .= " WHERE e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
            } else {
                $query .= " $whereClause";
            }

            $query .= " ORDER BY u.apellido_p, u.apellido_m, u.nombre";
            break;

        case 'salud':
            $query = "SELECT u.id, u.nombre, u.apellido_p, u.apellido_m, u.sexo, 
                     u.enfermedades, u.alergias, u.tratamiento, u.no_emergencia,
                     e.nombre as evento_nombre, e.fecha_inicio
                     FROM PTL_USUARIOS u
                     JOIN PTL_USUARIOS_EVENTOS ue ON u.id = ue.persona_id
                     JOIN PTL_EVENTOS e ON ue.evento_id = e.id
                     WHERE (u.enfermedades != 'No' OR u.alergias != 'No')";

            if ($perfil == 'Supervisor') {
                $query .= " AND e.supervisor = '" . mysqli_real_escape_string($link, $nombreUsuario) . "'";
            } elseif ($perfil == 'Coordinador') {
                $query .= " AND e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
            } else {
                $query .= " $whereClause";
            }

            $query .= " ORDER BY e.fecha_inicio DESC, u.apellido_p";
            break;

        case 'transporte':
            $query = "SELECT t.id as transporte_id, t.vehiculo, t.asientos, 
                     COUNT(tu.personas_id) as ocupados,
                     e.id as evento_id, e.nombre as evento_nombre, e.fecha_inicio,
                     GROUP_CONCAT(DISTINCT CONCAT(u.nombre, ' ', u.apellido_p) ORDER BY tu.no_asiento SEPARATOR '; ') as pasajeros
                     FROM PTL_TRANSPORTE t
                     JOIN PTL_EVENTO_TRANSPORTE et ON t.id = et.transporte_id
                     JOIN PTL_EVENTOS e ON et.evento_id = e.id
                     LEFT JOIN PTL_TRANSPORTE_USUARIOS tu ON t.id = tu.transporte_id
                     LEFT JOIN PTL_USUARIOS u ON tu.personas_id = u.id";

            if ($perfil == 'Coordinador') {
                $query .= " WHERE e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
            } else {
                $query .= " $whereClause";
            }

            $query .= " GROUP BY t.id, e.id
                      ORDER BY e.fecha_inicio DESC, t.vehiculo";
            break;

        case 'habilidades':
            $query = "SELECT h.nombre as habilidad, 
                     COUNT(DISTINCT hu.personas_id) as cantidad_personas,
                     GROUP_CONCAT(DISTINCT CONCAT(u.nombre, ' ', u.apellido_p) SEPARATOR ', ') as personas
                     FROM PTL_HABILIDADES h
                     JOIN PTL_HABILIDADES_USUARIOS hu ON h.id = hu.habilidades_id
                     JOIN PTL_USUARIOS u ON hu.personas_id = u.id";

            if ($perfil == 'Coordinador') {
                $query .= " JOIN PTL_USUARIOS_EVENTOS ue ON u.id = ue.persona_id
                           JOIN PTL_EVENTOS e ON ue.evento_id = e.id
                           WHERE e.creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
            } else {
                $query .= " $whereClause";
            }

            $query .= " GROUP BY h.id
                      ORDER BY cantidad_personas DESC";
            break;
    }

    $result = mysqli_query($link, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $reporte[] = $row;
    }

    return $reporte;
}

// Obtener notificaciones
$notificaciones = obtenerNotificaciones($link, $userId, $perfil, $nombreUsuario, $correoUsuario);
usort($notificaciones, function ($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});

/* Generar reportes iniciales*/
$reportesIniciales['eventos'] = generarReporte($link, 'eventos', $perfil, $nombreUsuario, $correoUsuario, []);
$reportesIniciales['participantes'] = generarReporte($link, 'participantes', $perfil, $nombreUsuario, $correoUsuario, []);
$reportesIniciales['salud'] = generarReporte($link, 'salud', $perfil, $nombreUsuario, $correoUsuario, []);
$reportesIniciales['transporte'] = generarReporte($link, 'transporte', $perfil, $nombreUsuario, $correoUsuario, []);
$reportesIniciales['habilidades'] = generarReporte($link, 'habilidades', $perfil, $nombreUsuario, $correoUsuario, []);

// Obtener lista de eventos para el modal
$queryEventos = "SELECT id, nombre FROM PTL_EVENTOS";
if ($perfil == 'Supervisor') {
    $queryEventos .= " WHERE supervisor = '" . mysqli_real_escape_string($link, $nombreUsuario) . "'";
} elseif ($perfil == 'Coordinador') {
    $queryEventos .= " WHERE creador = '" . mysqli_real_escape_string($link, $correoUsuario) . "'";
}
$queryEventos .= " ORDER BY fecha_inicio DESC";

$resultEventos = mysqli_query($link, $queryEventos);
$listaEventos = [];
while ($row = mysqli_fetch_assoc($resultEventos)) {
    $listaEventos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones y Reportes</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/mensajes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="top-bar">
            <div class="checkbox-container">
                <input type="checkbox" id="select-all" aria-label="Seleccionar todo">
            </div>
            <div class="search-bar">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" class="search-input" placeholder="Buscar notificaciones..." id="search-notifications">
            </div>
            <button class="filter-button" id="filter-button">
                <i class="fas fa-filter"></i> Filtrar
            </button>

            <?php if ($perfil != "Participante"): ?>
                <button class="report-button" id="generate-report-btn">
                    <i class="fas fa-chart-bar"></i> Generar Reporte
                </button>
            <?php endif; ?>
        </div>

        <!-- Filtros desplegables -->
        <div class="filtros-container" id="filtros-container">
            <h4>Filtrar por tipo:</h4>
            <div class="filtro-option">
                <input type="checkbox" id="filtro-inscripciones" checked>
                <label for="filtro-inscripciones">Inscripciones</label>
            </div>
            <div class="filtro-option">
                <input type="checkbox" id="filtro-asignaciones" checked>
                <label for="filtro-asignaciones">Asignaciones</label>
            </div>
            <div class="filtro-option">
                <input type="checkbox" id="filtro-transporte" checked>
                <label for="filtro-transporte">Transporte</label>
            </div>
            <div class="filtro-option">
                <input type="checkbox" id="filtro-reportes" checked>
                <label for="filtro-reportes">Reportes</label>
            </div>
        </div>

        <div class="report-tabs">
            <div class="report-tab active" data-tab="Mensajes">Mensajes</div>
            <div class="report-tab" data-tab="eventos">Eventos</div>
            <div class="report-tab" data-tab="participantes">Participantes</div>
            <div class="report-tab" data-tab="salud">Salud</div>
            <div class="report-tab" data-tab="transporte">Transporte</div>
            <div class="report-tab" data-tab="habilidades">Habilidades</div>
        </div>

        <div id="Mensajes" class="report-tab-content active">
            <?php if (empty($notificaciones)): ?>
                <div class="notification-item">
                    <div class="notification-details">
                        <div class="notification-message">No tienes notificaciones</div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($notificaciones as $index => $notificacion): ?>
                    <div class="notification-item" data-id="<?= $index ?>" data-tipo="<?= $notificacion['tipo'] ?>">
                        <div class="notification-checkbox-container">
                            <input type="checkbox" class="notification-checkbox" aria-label="Seleccionar notificación">
                        </div>
                        <div class="notification-details">
                            <div class="notification-title"><?= htmlspecialchars($notificacion['titulo'] ?? '') ?></div>
                            <div class="notification-message"><?= htmlspecialchars($notificacion['mensaje_corto'] ?? '') ?></div>
                            <div class="notification-date"><?= date('d/m/Y H:i', strtotime($notificacion['fecha'])) ?></div>

                            <div class="detalle-mensaje" id="detalle-<?= $index ?>">
                                <?= $notificacion['mensaje_largo'] ?>
                            </div>
                        </div>
                        <?php if ($notificacion['tipo'] === 'reporte'): ?>
                            <div class="notification-badge">
                                <i class="fas fa-chart-bar"></i> Reporte
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Reporte de Eventos -->
        <div id="eventos" class="report-tab-content">
            <h3>Reporte de Eventos</h3>
            <p class="report-description">
                Este reporte muestra información detallada de todos los eventos con sus características principales.
            </p>

            <?php if (!empty($reportesIniciales['eventos'])): ?>
                <div class="charts-container" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; align-content: center; align-items: center;">
                    <!-- Gráfico 1: Eventos por categoría -->
                    <div class="chart-wrapper">
                        <canvas id="chartEventosCategoria"></canvas>
                    </div>

                    <!-- Gráfico 2: Eventos con más asistentes -->
                    <div class="chart-wrapper">
                        <canvas id="chartEventosAsistentes"></canvas>
                    </div>

                    <!-- Gráfico 3: Eventos por ubicación -->
                    <div class="chart-wrapper">
                        <canvas id="chartEventosUbicacion"></canvas>
                    </div>

                    <!-- Gráfico 4: Eventos con transporte -->
                    <div class="chart-wrapper">
                        <canvas id="chartEventosTransporte"></canvas>
                    </div>
                </div>

                <div class="report-data">
                    <h4>Listado completo de eventos</h4>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Ubicación</th>
                                <th>Categoría</th>
                                <th>Asistentes</th>
                                <th>Transporte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportesIniciales['eventos'] as $evento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($evento['nombre'] ?? '') ?></td>
                                    <td><?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?></td>
                                    <td><?= htmlspecialchars($evento['ubicacion'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($evento['categoria'] ?? '') ?></td>
                                    <td><?= $evento['asistentes'] ?></td>
                                    <td><?= htmlspecialchars($evento['transportes'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No hay datos disponibles para mostrar.</p>
            <?php endif; ?>
        </div>

        <!-- Reporte de Participantes -->
        <div id="participantes" class="report-tab-content">
            <h3>Reporte de Participantes</h3>
            <p class="report-description">
                Información detallada de los participantes incluyendo datos demográficos y académicos.
            </p>

            <?php if (!empty($reportesIniciales['participantes'])): ?>
                <div class="charts-container">
                    <div class="charts-container" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; align-content: center; align-items: center;">
                        <!-- Gráfico 1: Distribución por género -->
                        <div class="chart-wrapper">
                            <canvas id="chartParticipantesGenero"></canvas>
                        </div>

                        <!-- Gráfico 2: Distribución por institución -->
                        <div class="chart-wrapper">
                            <canvas id="chartParticipantesInstitucion"></canvas>
                        </div>

                        <!-- Gráfico 3: Distribución por licenciatura -->
                        <div class="chart-wrapper">
                            <canvas id="chartParticipantesLicenciatura"></canvas>
                        </div>

                        <!-- Gráfico 4: Participantes UPAEP vs Invitados -->
                        <div class="chart-wrapper">
                            <canvas id="chartParticipantesTipo"></canvas>
                        </div>
                    </div>

                    <div class="report-data">
                        <h4>Listado completo de participantes</h4>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Institución</th>
                                    <th>Licenciatura</th>
                                    <th>Edad</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reportesIniciales['participantes'] as $participante): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($participante['nombre'] . ' ' . $participante['apellido_p'] . ' ' . $participante['apellido_m']) ?></td>
                                        <td><?= htmlspecialchars($participante['institucion_educativa'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($participante['licenciatura'] ?? '') ?></td>
                                        <td><?= $participante['edad'] ?></td>
                                        <td><?= htmlspecialchars($participante['tipo_participante'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <p>No hay datos disponibles para mostrar.</p>
            <?php endif; ?>
        </div>

        <!-- Reporte de Salud -->
        <div id="salud" class="report-tab-content">
            <h3>Reporte de Salud</h3>
            <p class="report-description">
                Información médica relevante de los participantes para garantizar su seguridad durante los eventos.
            </p>

            <?php if (!empty($reportesIniciales['salud'])): ?>
                <div class="charts-container" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; align-content: center; align-items: center;">
                    <!-- Gráfico 1: Participantes con enfermedades -->
                    <div class="chart-wrapper">
                        <canvas id="chartSaludEnfermedades"></canvas>
                    </div>

                    <!-- Gráfico 2: Participantes con alergias -->
                    <div class="chart-wrapper">
                        <canvas id="chartSaludAlergias"></canvas>
                    </div>

                    <!-- Gráfico 3: Tipos de enfermedades -->
                    <div class="chart-wrapper">
                        <canvas id="chartSaludTiposEnfermedades"></canvas>
                    </div>
                </div>

                <div class="report-data">
                    <h4>Listado completo de participantes con condiciones médicas</h4>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Enfermedades</th>
                                <th>Alergias</th>
                                <th>Tratamiento</th>
                                <th>Contacto Emergencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportesIniciales['salud'] as $salud): ?>
                                <tr>
                                    <td><?= htmlspecialchars($salud['nombre'] . ' ' . $salud['apellido_p'] . ' ' . $salud['apellido_m']) ?></td>
                                    <td><?= htmlspecialchars($salud['enfermedades'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($salud['alergias'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($salud['tratamiento'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($salud['no_emergencia'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No hay datos disponibles para mostrar.</p>
            <?php endif; ?>
        </div>

        <!-- Reporte de Transporte -->
        <div id="transporte" class="report-tab-content">
            <h3>Reporte de Transporte</h3>
            <p class="report-description">
                Información sobre el transporte asignado para cada evento, incluyendo capacidad y ocupación.
            </p>

            <?php if (!empty($reportesIniciales['transporte'])): ?>
                <div class="charts-container" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; align-content: center; align-items: center;">
                    <!-- Gráfico 1: Tipos de vehículos -->
                    <div class="chart-wrapper">
                        <canvas id="chartTransporteVehiculos"></canvas>
                    </div>

                    <!-- Gráfico 2: Ocupación vs capacidad -->
                    <div class="chart-wrapper">
                        <canvas id="chartTransporteOcupacion"></canvas>
                    </div>
                </div>

                <div class="report-data">
                    <h4>Listado completo de transporte</h4>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Vehículo</th>
                                <th>Evento</th>
                                <th>Ocupación</th>
                                <th>Capacidad</th>
                                <th>% Ocupación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportesIniciales['transporte'] as $transporte):
                                $porcentaje = round(($transporte['ocupados'] / $transporte['asientos']) * 100, 2);
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($transporte['vehiculo'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($transporte['evento_nombre'] ?? '') ?></td>
                                    <td><?= $transporte['ocupados'] ?></td>
                                    <td><?= $transporte['asientos'] ?></td>
                                    <td><?= $porcentaje ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No hay datos disponibles para mostrar.</p>
            <?php endif; ?>
        </div>

        <!-- Reporte de Habilidades -->
        <div id="habilidades" class="report-tab-content">
            <h3>Reporte de Habilidades</h3>
            <p class="report-description">
                Habilidades de los participantes y su distribución entre los diferentes eventos.
            </p>

            <?php if (!empty($reportesIniciales['habilidades'])): ?>
                <div class="charts-container" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; align-content: center; align-items: center;">
                    <!-- Gráfico 1: Habilidades más comunes -->
                    <div class="chart-wrapper">
                        <canvas id="chartHabilidadesComunes"></canvas>
                    </div>

                    <!-- Gráfico 2: Distribución por tipo de habilidad -->
                    <div class="chart-wrapper">
                        <canvas id="chartHabilidadesTipos"></canvas>
                    </div>
                </div>

                <div class="report-data">
                    <h4>Listado completo de habilidades</h4>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Habilidad</th>
                                <th>Personas</th>
                                <th>Lista de Personas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportesIniciales['habilidades'] as $habilidad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($habilidad['habilidad'] ?? '') ?></td>
                                    <td><?= $habilidad['cantidad_personas'] ?></td>
                                    <td><?= htmlspecialchars($habilidad['personas'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No hay datos disponibles para mostrar.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para generar reportes -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Generar Reporte Personalizado</h2>
            <form id="reportForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="generar_reporte_personalizado" value="1">

                <div class="form-group">
                    <label for="tipo_reporte">Tipos de Reporte:</label>
                    <select id="tipo_reporte" name="tipo_reporte[]" multiple size="5" required>
                        <option value="eventos">Eventos</option>
                        <option value="participantes">Participantes</option>
                        <option value="salud">Salud</option>
                        <option value="transporte">Transporte</option>
                        <option value="habilidades">Habilidades</option>
                    </select>
                    <small>Mantén presionada la tecla Ctrl (Windows) o Comando (Mac) para seleccionar múltiples reportes.</small>
                </div>

                <div class="form-group">
                    <label for="evento_ids">Eventos (opcional):</label>
                    <select id="evento_ids" name="evento_ids[]" multiple size="5">
                        <?php foreach ($listaEventos as $evento): ?>
                            <option value="<?= $evento['id'] ?>"><?= htmlspecialchars($evento['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small>Mantén presionada la tecla Ctrl (Windows) o Comando (Mac) para seleccionar múltiples eventos.</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de inicio (opcional):</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio">
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha de fin (opcional):</label>
                        <input type="date" id="fecha_fin" name="fecha_fin">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" id="cancelReportBtn">Cancelar</button>
                    <button type="submit">Generar Reporte</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Seleccionar/deseleccionar todas las notificaciones
            $('#select-all').change(function() {
                $('.notification-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Buscar notificaciones
            $('#search-notifications').keyup(function() {
                const searchText = $(this).val().toLowerCase();
                $('.notification-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchText));
                });
            });

            // Mostrar/ocultar filtros
            $('#filter-button').click(function() {
                $('#filtros-container').slideToggle();
            });

            // Filtrar notificaciones por tipo
            $('.filtro-option input').change(function() {
                const tiposMostrar = [];

                if ($('#filtro-inscripciones').is(':checked')) tiposMostrar.push('inscripcion');
                if ($('#filtro-asignaciones').is(':checked')) tiposMostrar.push('asignacion');
                if ($('#filtro-transporte').is(':checked')) tiposMostrar.push('transporte');
                if ($('#filtro-reportes').is(':checked')) tiposMostrar.push('reporte');

                $('.notification-item').each(function() {
                    const tipo = $(this).data('tipo');
                    $(this).toggle(tiposMostrar.includes(tipo));
                });
            });

            // Cambiar pestañas de reportes
            $('.report-tab').click(function() {
                $('.report-tab').removeClass('active');
                $(this).addClass('active');

                const tabId = $(this).data('tab');
                $('.report-tab-content').removeClass('active');
                $(`#${tabId}`).addClass('active');

                // Renderizar gráficos cuando se muestran
                if (tabId !== 'Mensajes') {
                    setTimeout(() => {
                        renderCharts(tabId);
                    }, 100);
                }
            });

            // Mostrar/ocultar modal de reportes
            $('#generate-report-btn').click(function() {
                $('#reportModal').show();
            });

            $('.close, #cancelReportBtn').click(function() {
                $('#reportModal').hide();
            });

            // Cerrar modal al hacer clic fuera del contenido
            $(window).click(function(event) {
                if ($(event.target).is('#reportModal')) {
                    $('#reportModal').hide();
                }
            });

            // Mostrar/ocultar detalles del mensaje
            $('.notification-item').on('click', function(e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('a') || $(e.target).is('button')) {
                    return;
                }

                const detalleId = '#detalle-' + $(this).data('id');
                $(detalleId).slideToggle(200);
            });

            // Función para renderizar gráficos
            function renderCharts(tabId) {
                const reporteData = <?= json_encode($reportesIniciales) ?>;

                switch (tabId) {
                    case 'eventos':
                        // Gráfico 1: Eventos por categoría
                        const ctxCategoria = document.getElementById('chartEventosCategoria').getContext('2d');
                        const categorias = {};
                        reporteData.eventos.forEach(evento => {
                            const categoria = evento.categoria || 'Sin categoría';
                            categorias[categoria] = (categorias[categoria] || 0) + 1;
                        });

                        new Chart(ctxCategoria, {
                            type: 'pie',
                            data: {
                                labels: Object.keys(categorias),
                                datasets: [{
                                    data: Object.values(categorias),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Eventos por Categoría'
                                    }
                                }
                            }
                        });

                        // Gráfico 2: Eventos con más asistentes (top 5)
                        const ctxAsistentes = document.getElementById('chartEventosAsistentes').getContext('2d');
                        const eventosOrdenados = [...reporteData.eventos].sort((a, b) => b.asistentes - a.asistentes).slice(0, 5);

                        new Chart(ctxAsistentes, {
                            type: 'bar',
                            data: {
                                labels: eventosOrdenados.map(evento => evento.nombre),
                                datasets: [{
                                    label: 'Asistentes',
                                    data: eventosOrdenados.map(evento => evento.asistentes),
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                indexAxis: 'y',
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Top 5 Eventos con más Asistentes'
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Gráfico 3: Eventos por ubicación
                        const ctxUbicacion = document.getElementById('chartEventosUbicacion').getContext('2d');
                        const ubicaciones = {};
                        reporteData.eventos.forEach(evento => {
                            const ubicacion = evento.ubicacion || 'Sin ubicación';
                            ubicaciones[ubicacion] = (ubicaciones[ubicacion] || 0) + 1;
                        });

                        new Chart(ctxUbicacion, {
                            type: 'doughnut',
                            data: {
                                labels: Object.keys(ubicaciones),
                                datasets: [{
                                    data: Object.values(ubicaciones),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Eventos por Ubicación'
                                    }
                                }
                            }
                        });

                        // Gráfico 4: Eventos con transporte
                        const ctxTransporte = document.getElementById('chartEventosTransporte').getContext('2d');
                        const conTransporte = reporteData.eventos.filter(evento => evento.transportes).length;
                        const sinTransporte = reporteData.eventos.length - conTransporte;

                        new Chart(ctxTransporte, {
                            type: 'pie',
                            data: {
                                labels: ['Con Transporte', 'Sin Transporte'],
                                datasets: [{
                                    data: [conTransporte, sinTransporte],
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.7)',
                                        'rgba(255, 99, 132, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Eventos con Transporte Asignado'
                                    }
                                }
                            }
                        });
                        break;

                    case 'participantes':
                        // Gráfico 1: Distribución por género
                        const ctxGenero = document.getElementById('chartParticipantesGenero').getContext('2d');
                        const generos = {};
                        reporteData.participantes.forEach(participante => {
                            generos[participante.sexo] = (generos[participante.sexo] || 0) + 1;
                        });

                        new Chart(ctxGenero, {
                            type: 'pie',
                            data: {
                                labels: Object.keys(generos),
                                datasets: [{
                                    data: Object.values(generos),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Distribución por Género'
                                    }
                                }
                            }
                        });

                        // Gráfico 2: Distribución por institución
                        const ctxInstitucion = document.getElementById('chartParticipantesInstitucion').getContext('2d');
                        const instituciones = {};
                        reporteData.participantes.forEach(participante => {
                            const institucion = participante.institucion_educativa || 'Sin institución';
                            instituciones[institucion] = (instituciones[institucion] || 0) + 1;
                        });

                        new Chart(ctxInstitucion, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(instituciones),
                                datasets: [{
                                    label: 'Participantes',
                                    data: Object.values(instituciones),
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Distribución por Institución'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Gráfico 3: Distribución por licenciatura
                        const ctxLicenciatura = document.getElementById('chartParticipantesLicenciatura').getContext('2d');
                        const licenciaturas = {};
                        reporteData.participantes.forEach(participante => {
                            const licenciatura = participante.licenciatura || 'Sin licenciatura';
                            licenciaturas[licenciatura] = (licenciaturas[licenciatura] || 0) + 1;
                        });

                        new Chart(ctxLicenciatura, {
                            type: 'doughnut',
                            data: {
                                labels: Object.keys(licenciaturas),
                                datasets: [{
                                    data: Object.values(licenciaturas),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Distribución por Licenciatura'
                                    }
                                }
                            }
                        });

                        // Gráfico 4: Participantes UPAEP vs Invitados
                        const ctxTipo = document.getElementById('chartParticipantesTipo').getContext('2d');
                        const tipos = {};
                        reporteData.participantes.forEach(participante => {
                            tipos[participante.tipo_participante] = (tipos[participante.tipo_participante] || 0) + 1;
                        });

                        new Chart(ctxTipo, {
                            type: 'pie',
                            data: {
                                labels: Object.keys(tipos),
                                datasets: [{
                                    data: Object.values(tipos),
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 99, 132, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Participantes UPAEP vs Invitados'
                                    }
                                }
                            }
                        });
                        break;

                    case 'salud':
                        // Gráfico 1: Participantes con enfermedades
                        const ctxEnfermedades = document.getElementById('chartSaludEnfermedades').getContext('2d');
                        const conEnfermedad = reporteData.salud.filter(p => p.enfermedades !== 'No').length;
                        const sinEnfermedad = reporteData.participantes.length - conEnfermedad;

                        new Chart(ctxEnfermedades, {
                            type: 'pie',
                            data: {
                                labels: ['Con Enfermedades', 'Sin Enfermedades'],
                                datasets: [{
                                    data: [conEnfermedad, sinEnfermedad],
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Participantes con Enfermedades'
                                    }
                                }
                            }
                        });

                        // Gráfico 2: Participantes con alergias
                        const ctxAlergias = document.getElementById('chartSaludAlergias').getContext('2d');
                        const conAlergias = reporteData.salud.filter(p => p.alergias !== 'No').length;
                        const sinAlergias = reporteData.participantes.length - conAlergias;

                        new Chart(ctxAlergias, {
                            type: 'pie',
                            data: {
                                labels: ['Con Alergias', 'Sin Alergias'],
                                datasets: [{
                                    data: [conAlergias, sinAlergias],
                                    backgroundColor: [
                                        'rgba(255, 159, 64, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Participantes con Alergias'
                                    }
                                }
                            }
                        });

                        // Gráfico 3: Tipos de enfermedades
                        const ctxTiposEnfermedades = document.getElementById('chartSaludTiposEnfermedades').getContext('2d');
                        const enfermedades = {};
                        reporteData.salud.forEach(participante => {
                            if (participante.enfermedades !== 'No') {
                                const enfermedad = participante.enfermedades;
                                enfermedades[enfermedad] = (enfermedades[enfermedad] || 0) + 1;
                            }
                        });

                        new Chart(ctxTiposEnfermedades, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(enfermedades),
                                datasets: [{
                                    label: 'Cantidad',
                                    data: Object.values(enfermedades),
                                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Tipos de Enfermedades'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        break;

                    case 'transporte':
                        // Gráfico 1: Tipos de vehículos
                        const ctxVehiculos = document.getElementById('chartTransporteVehiculos').getContext('2d');
                        const vehiculos = {};
                        reporteData.transporte.forEach(t => {
                            vehiculos[t.vehiculo] = (vehiculos[t.vehiculo] || 0) + 1;
                        });

                        new Chart(ctxVehiculos, {
                            type: 'pie',
                            data: {
                                labels: Object.keys(vehiculos),
                                datasets: [{
                                    data: Object.values(vehiculos),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Tipos de Vehículos'
                                    }
                                }
                            }
                        });

                        // Gráfico 2: Ocupación vs capacidad
                        const ctxOcupacion = document.getElementById('chartTransporteOcupacion').getContext('2d');
                        const transporteTop5 = [...reporteData.transporte].slice(0, 5);

                        new Chart(ctxOcupacion, {
                            type: 'bar',
                            data: {
                                labels: transporteTop5.map(t => t.vehiculo + ' - ' + t.evento_nombre),
                                datasets: [{
                                        label: 'Ocupación',
                                        data: transporteTop5.map(t => t.ocupados),
                                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Capacidad',
                                        data: transporteTop5.map(t => t.asientos),
                                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Ocupación vs Capacidad (Top 5)'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        break;

                    case 'habilidades':
                        // Gráfico 1: Habilidades más comunes
                        const ctxHabilidades = document.getElementById('chartHabilidadesComunes').getContext('2d');
                        const habilidadesTop5 = [...reporteData.habilidades].sort((a, b) => b.cantidad_personas - a.cantidad_personas).slice(0, 5);

                        new Chart(ctxHabilidades, {
                            type: 'bar',
                            data: {
                                labels: habilidadesTop5.map(h => h.habilidad),
                                datasets: [{
                                    label: 'Personas con esta habilidad',
                                    data: habilidadesTop5.map(h => h.cantidad_personas),
                                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                indexAxis: 'y',
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Habilidades más Comunes (Top 5)'
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Gráfico 2: Distribución por tipo de habilidad
                        const ctxTiposHabilidades = document.getElementById('chartHabilidadesTipos').getContext('2d');
                        const tiposHabilidades = {};
                        reporteData.habilidades.forEach(h => {
                            const tipo = h.habilidad.split(' ')[0]; // Suponiendo que el tipo es la primera palabra
                            tiposHabilidades[tipo] = (tiposHabilidades[tipo] || 0) + h.cantidad_personas;
                        });

                        new Chart(ctxTiposHabilidades, {
                            type: 'pie',
                            data: {
                                labels: Object.keys(tiposHabilidades),
                                datasets: [{
                                    data: Object.values(tiposHabilidades),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Distribución por Tipo de Habilidad'
                                    }
                                }
                            }
                        });
                        break;
                }
            }

            // Renderizar gráficos iniciales al cargar la página
            renderCharts('eventos');
        });
        // Manejar el envío del formulario de reporte
        $('#reportForm').submit(function(e) {
            e.preventDefault();

            if ($('#tipo_reporte').val() === null || $('#tipo_reporte').val().length === 0) {
                alert('Por favor selecciona al menos un tipo de reporte');
                return false;
            }

            // Mostrar feedback visual
            const submitBtn = $('#reportModal button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generando...');

            // Enviar el formulario
            this.submit();
        });
    </script>
</body>

</html>