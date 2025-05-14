<?php
header('Content-Type: application/json');
include 'conexion.php';
$con = conecta();

$id_prestamo_a_devolver = $_POST['id_prestamo'] ?? null;

if (!$id_prestamo_a_devolver) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ID de préstamo.']);
    exit;
}

try {
    // CAMBIO: Añadido CAST a la condición de JOIN para num_ejemplar.
    // ¡¡VERIFICA LOS TIPOS DE DATOS REALES Y AJUSTA EL CAST SI ES NECESARIO!!
    $stmtPrestamo = $con->prepare("SELECT p.id_prestamo, p.id_solicitante, p.tipo_solicitante, p.id_libro, p.num_ejemplar, p.fecha_limite_entrega, l.titulo 
                                   FROM prestamo p
                                   JOIN libro l ON p.id_libro = l.isbn 
                                        -- ASUMIENDO p.num_ejemplar es INTEGER y l.num_ejemplar es VARCHAR
                                        AND p.num_ejemplar = CAST(l.num_ejemplar AS INTEGER)
                                        -- O SI l.num_ejemplar es INTEGER y p.num_ejemplar es VARCHAR:
                                        -- AND CAST(p.num_ejemplar AS INTEGER) = l.num_ejemplar
                                   WHERE p.id_prestamo = :id_prestamo AND p.fecha_entrega_solicitante IS NULL");
    $stmtPrestamo->execute([':id_prestamo' => $id_prestamo_a_devolver]);
    $prestamo = $stmtPrestamo->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        echo json_encode(['success' => false, 'message' => 'Préstamo no encontrado, ya fue devuelto o el ID es incorrecto.']);
        exit;
    }

    $fecha_devolucion_actual_str = date('Y-m-d');
    $multa_generada = 0;
    $dias_retraso = 0;
    $mensaje_retraso = "";

    if ($prestamo['fecha_limite_entrega']) {
        $fecha_limite_obj = new DateTime($prestamo['fecha_limite_entrega']);
        $fecha_devolucion_obj = new DateTime($fecha_devolucion_actual_str);

        if ($fecha_devolucion_obj > $fecha_limite_obj) {
            $intervalo = $fecha_limite_obj->diff($fecha_devolucion_obj);
            $dias_retraso = $intervalo->days;

            if ($dias_retraso > 0) {
                $tarifa_por_dia = ($prestamo['tipo_solicitante'] === 'alumna') ? 5 : 10;
                $multa_generada = $dias_retraso * $tarifa_por_dia;
                $mensaje_retraso = "Libro devuelto con {$dias_retraso} día(s) de retraso. ";
            }
        }
    }
    
    $stmtUpdate = $con->prepare("UPDATE prestamo 
                                 SET fecha_entrega_solicitante = :fecha_entrega, multa = :multa 
                                 WHERE id_prestamo = :id_prestamo");
    $stmtUpdate->execute([
        ':fecha_entrega' => $fecha_devolucion_actual_str,
        ':multa' => $multa_generada,
        ':id_prestamo' => $id_prestamo_a_devolver
    ]);

    $respuesta = [
        'success' => true,
        'message' => $mensaje_retraso . "Libro \"{$prestamo['titulo']}\" (Ej. {$prestamo['num_ejemplar']}) devuelto exitosamente." . ($multa_generada > 0 ? " Multa generada: \${$multa_generada} MXN." : ""),
        'multa' => $multa_generada,
        'id_prestamo' => $id_prestamo_a_devolver
    ];
    echo json_encode($respuesta);

} catch (PDOException $e) {
    error_log("Error PDO al devolver libro: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos al procesar la devolución: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error General al devolver libro: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error general en el servidor al procesar la devolución: ' . $e->getMessage()]);
}
?>