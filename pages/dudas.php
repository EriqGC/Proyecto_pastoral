<?php
session_start();
include('../includes/conects.php');
$link = Conectarse();
mysqli_set_charset($link, "utf8");

// Procesar el envío del formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevaPregunta = $_POST['nueva_pregunta'];
    if (!empty($nuevaPregunta)) {
        // Escapar la entrada para prevenir inyección SQL
        $nuevaPregunta = mysqli_real_escape_string($link, $nuevaPregunta);

        // Insertar la nueva pregunta en la base de datos
        $queryInsert = "INSERT INTO PTL_PGS_FRECUENTES (pregunta) VALUES ('$nuevaPregunta')";
        if (mysqli_query($link, $queryInsert)) {
            // Redirigir para evitar la re-inserción al recargar la página
            header("Location: dudas.php"); 
            exit;
        } else {
            echo "Error al guardar la pregunta: " . mysqli_error($link);
        }
    }
}

// Consultar todas las preguntas frecuentes con información del evento
$queryPreguntas = "SELECT pf.id, pf.pregunta, pf.respuesta, e.nombre AS evento_nombre, e.id AS evento_id
                  FROM PTL_PGS_FRECUENTES pf
                  JOIN PTL_PGS_FRECUENTES_EVENTOS pfe ON pf.id = pfe.pgs_frecuentes_id
                  JOIN PTL_EVENTOS e ON pfe.eventos_id = e.id
                  ORDER BY pf.id DESC";
$resultPreguntas = mysqli_query($link, $queryPreguntas);
$preguntas = mysqli_fetch_all($resultPreguntas, MYSQLI_ASSOC);

// Paginación
$totalPreguntas = count($preguntas);
$porPagina = 10;
$totalPaginas = ceil($totalPreguntas / $porPagina);
$paginaActual = isset($_GET['pagina']) ? max(1, min($totalPaginas, intval($_GET['pagina']))) : 1;
$inicio = ($paginaActual - 1) * $porPagina;
$preguntasPagina = array_slice($preguntas, $inicio, $porPagina);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dudas2.css">
    <script defer src="../assets/js/dudas3.js"></script>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/hero.php';
    createHero("Preguntas Frecuentes");
    ?>

    <section class="content">
        <div class="faq-section">
            <!-- Buscador -->
            <div class="faq-search-container">
                <input type="text" id="faq-search" placeholder="Buscar pregunta...">
            </div>

            <!-- Listado de preguntas -->
            <?php if (empty($preguntasPagina)): ?>
                <p>No hay preguntas frecuentes disponibles</p><br>
                
                <div class="nueva-pregunta-form">
                    <h3>¿No encontraste tu pregunta?</h3>
                    <form method="post" action="">
                        <textarea name="nueva_pregunta" rows="4" cols="50" placeholder="Escribe tu pregunta aquí y la responderemos en breve" required></textarea><br>
                        <input type="submit" value="Enviar Pregunta">
                    </form>
                </div>

                
            <?php else: ?>
                <?php foreach ($preguntasPagina as $pregunta): ?>
                    <div class="faq-item" data-event-id="<?php echo $pregunta['evento_id']; ?>">
                        <div class="faq-question">
                            <?php echo htmlspecialchars($pregunta['pregunta']); ?>
                            <span class="faq-event"><b>Evento:</b> <?php echo htmlspecialchars($pregunta['evento_nombre']); ?></span>
                        </div>
                        <div class="faq-answer"><?php echo htmlspecialchars($pregunta['respuesta']); ?></div>
                        <a href="evento.php?id=<?php echo $pregunta['evento_id']; ?>" class="ver-evento-btn">Ver Evento</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Paginación -->
            <div class="pagination">
                <?php if ($paginaActual > 1): ?>
                    <button onclick="window.location.href='?pagina=<?php echo $paginaActual - 1; ?>'">&lt;</button>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <button class="<?php echo $i == $paginaActual ? 'active' : ''; ?>"
                        onclick="window.location.href='?pagina=<?php echo $i; ?>'">
                        <?php echo $i; ?>
                    </button>
                <?php endfor; ?>

                <?php if ($paginaActual < $totalPaginas): ?>
                    <button onclick="window.location.href='?pagina=<?php echo $paginaActual + 1; ?>'">&gt;</button>
                <?php endif; ?>
            </div>
            <div class="input_pagination">
                <input type="number" id="page-input" min="1" max="<?php echo $totalPaginas; ?>"
                    placeholder="Ir a" onchange="irAPagina(this.value)">
                <button onclick="irAPagina(document.getElementById('page-input').value)">Ir</button>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script>
        function irAPagina(pagina) {
            if (pagina >= 1 && pagina <= <?php echo $totalPaginas; ?>) {
                window.location.href = '?pagina=' + pagina;
            }
        }
    </script>
</body>

</html>