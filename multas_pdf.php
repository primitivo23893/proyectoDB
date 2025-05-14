<?php
require_once('tcpdf/tcpdf.php');
include 'conexion.php';

$con = conecta();

$id_prestamo = $_GET['id'] ?? null;

if (!$id_prestamo) {
    die("ID de préstamo no proporcionado.");
}

$query = "
SELECT p.*, 
       l.titulo,
       CASE 
           WHEN p.tipo_solicitante = 'alumna' THEN a.nombre 
           WHEN p.tipo_solicitante = 'maestra' THEN pr.nombre 
           ELSE 'Desconocido'
       END AS nombre_solicitante
FROM prestamo p
LEFT JOIN libro l ON p.id_libro = l.isbn
LEFT JOIN alumno a ON p.tipo_solicitante = 'alumna' AND p.id_solicitante = a.codigo
LEFT JOIN profesor pr ON p.tipo_solicitante = 'maestra' AND p.id_solicitante = pr.codigo
WHERE p.id_prestamo = :id
";

$stmt = $con->prepare($query);
$stmt->bindParam(':id', $id_prestamo, PDO::PARAM_INT);
$stmt->execute();
$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    die("Préstamo no encontrado.");
}

// FECHAS
$fecha_prestamo = new DateTime($prestamo['fecha_prestamo']);
$fecha_limite = new DateTime($prestamo['fecha_limite_entrega']);
$fecha_entrega = $prestamo['fecha_entrega_solicitante'] 
                ? new DateTime($prestamo['fecha_entrega_solicitante']) 
                : new DateTime();

$dias_retraso = 0;
$multa = 0;

if ($fecha_entrega > $fecha_limite) {
    $dias_retraso = $fecha_entrega->diff($fecha_limite)->days;
    $multa = ($prestamo['tipo_solicitante'] === 'alumna') 
             ? $dias_retraso * 5 
             : $dias_retraso * 10;
}

// PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// HTML para el PDF
$html = '
<h2 style="text-align:center;"> Orden de Pago por Multa</h2>
<hr>
<p><strong>Nombre del solicitante:</strong> ' . htmlspecialchars($prestamo['nombre_solicitante']) . '</p>
<p><strong>Tipo:</strong> ' . ucfirst($prestamo['tipo_solicitante']) . '</p>
<p><strong>Libro:</strong> ' . $prestamo['titulo'] . ' (ISBN: ' . $prestamo['id_libro'] . ')</p>

<br>
<table border="1" cellpadding="5" cellspacing="0">
    <tr style="background-color:#f2f2f2;">
        <th> Fecha de préstamo</th>
        <th> Fecha límite</th>
        <th> Fecha de entrega</th>
    </tr>
    <tr>
        <td>' . $fecha_prestamo->format('Y-m-d') . '</td>
        <td>' . $fecha_limite->format('Y-m-d') . '</td>
        <td>' . $fecha_entrega->format('Y-m-d') . '</td>
    </tr>
</table>';

if ($multa > 0) {
    $html .= '
    <br><p> <strong>Atención:</strong> El solicitante ha devuelto el libro con <strong>' . $dias_retraso . ' día(s)</strong> de retraso.</p>
    <p> <strong>La multa correspondiente es de: $' . number_format($multa, 2) . ' pesos.</strong></p>';
} else {
    $html .= '
    <br><p style="color:green;"> <strong>El libro fue entregado a tiempo.</strong> No aplica multa.</p>';
}

$html .= '
<hr>
<p style="text-align:center;"><strong>Total a pagar: $' . number_format($multa, 2) . ' pesos</strong></p>
<p style="text-align:center;">Gracias por utilizar nuestra biblioteca </p>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('orden_pago.pdf', 'I');
