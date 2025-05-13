<?php
header('Content-Type: application/json');
include 'conexion.php'; // Asegúrate que este archivo establece correctamente la conexión PDO
$con = conecta(); // Asumimos que conecta() devuelve tu objeto de conexión PDO

try {
    // 1. Obtener todos los ejemplares de la tabla `libro`
    // Asegúrate que los nombres de las columnas (isbn, titulo, autor, num_ejemplar)
    // y el nombre de la tabla (libro) sean correctos.
    $queryLibros = "SELECT isbn, titulo, autor, num_ejemplar FROM libro ORDER BY titulo ASC, isbn ASC, num_ejemplar ASC";
    $stmtLibros = $con->prepare($queryLibros);
    $stmtLibros->execute();
    $all_exemplars = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);

    if (empty($all_exemplars)) {
        echo json_encode([]); // No hay libros en el catálogo, devuelve array vacío
        exit;
    }

    // 2. Obtener todos los préstamos activos (id_libro es ISBN y num_ejemplar)
    // Asumimos que en tu tabla 'prestamo', 'id_libro' almacena el ISBN y
    // 'num_ejemplar' almacena el número de ejemplar del libro prestado.
    // Y que 'fecha_entrega_solicitante' es NULL si el libro no ha sido devuelto.
    $queryPrestamosActivos = "SELECT id_libro, num_ejemplar FROM prestamo WHERE fecha_entrega_solicitante IS NULL";
    $stmtPrestamosActivos = $con->prepare($queryPrestamosActivos);
    $stmtPrestamosActivos->execute();
    $prestamos_activos_raw = $stmtPrestamosActivos->fetchAll(PDO::FETCH_ASSOC);

    // Crear un set de búsqueda rápida para los préstamos activos (ej: "ISBN_EJEMPLAR")
    $prestamos_activos_set = [];
    foreach ($prestamos_activos_raw as $prestamo) {
        $prestamos_activos_set[$prestamo['id_libro'] . '_' . $prestamo['num_ejemplar']] = true;
    }

    // 3. Procesar y filtrar
    $libros_con_ejemplares_disponibles = [];
    
    foreach ($all_exemplars as $exemplar_row) {
        $isbn = $exemplar_row['isbn'];
        $num_ejemplar_actual = $exemplar_row['num_ejemplar'];

        // Verificar si este ejemplar específico está actualmente prestado
        $clave_prestamo = $isbn . '_' . $num_ejemplar_actual;
        if (isset($prestamos_activos_set[$clave_prestamo])) {
            continue; // Este ejemplar está prestado, saltar al siguiente
        }

        // Si el libro (ISBN) no ha sido añadido a nuestra lista final, lo inicializamos
        if (!isset($libros_con_ejemplares_disponibles[$isbn])) {
            $libros_con_ejemplares_disponibles[$isbn] = [
                'isbn' => $isbn,
                'titulo' => $exemplar_row['titulo'],
                'autor' => $exemplar_row['autor'],
                'ejemplares_disponibles' => [] 
            ];
        }
        // Añadir este ejemplar (que ya sabemos que está disponible) a la lista del libro
        $libros_con_ejemplares_disponibles[$isbn]['ejemplares_disponibles'][] = $num_ejemplar_actual;
    }
    
    // Filtrar libros que quedaron sin ejemplares disponibles después de la verificación
    $libros_finales = [];
    foreach ($libros_con_ejemplares_disponibles as $isbn => $libro_data) {
        if (!empty($libro_data['ejemplares_disponibles'])) {
            // Ordenar los ejemplares disponibles numéricamente (opcional, pero buena práctica)
            sort($libro_data['ejemplares_disponibles'], SORT_NUMERIC);
            $libros_finales[] = $libro_data;
        }
    }

    echo json_encode($libros_finales); // Devolver solo los libros que tienen al menos un ejemplar disponible

} catch (PDOException $e) {
    http_response_code(500); 
    error_log("Error en obtener_libros.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error en el servidor al obtener la lista de libros. Por favor, intente más tarde o contacte al administrador.']);
}
?>