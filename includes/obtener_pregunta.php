<?php
include('../includes/conects.php');

header('Content-Type: application/json');

$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$porPagina = 5;
$offset = ($pagina - 1) * $porPagina;

$link = Conectarse();

// Obtener preguntas para la página actual
$query = "SELECT pf.pregunta, pf.respuesta 
          FROM PTL_preguntas_frecuentes pf
          JOIN PTL_preguntas_frecuentes_evento pfe ON pf.id = pfe.pgs_frecuentes_id
          WHERE pfe.eventos_id = ?
          LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, 'iii', $evento_id, $porPagina, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$preguntas = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Obtener total de preguntas
$queryTotal = "SELECT COUNT(*) as total 
               FROM PTL_preguntas_frecuentes pf
               JOIN PTL_preguntas_frecuentes_evento pfe ON pf.id = pfe.pgs_frecuentes_id
               WHERE pfe.eventos_id = ?";
$stmtTotal = mysqli_prepare($link, $queryTotal);
mysqli_stmt_bind_param($stmtTotal, 'i', $evento_id);
mysqli_stmt_execute($stmtTotal);
$resultTotal = mysqli_stmt_get_result($stmtTotal);
$total = mysqli_fetch_assoc($resultTotal)['total'];

echo json_encode([
    'preguntas' => $preguntas,
    'total' => $total,
    'pagina' => $pagina,
    'porPagina' => $porPagina
]);
?>