<?php
header('Content-Type: application/json');
include 'conexion.php'; // Asegúrate que este archivo establece correctamente la conexión PDO

// Asumimos que conecta() devuelve tu objeto de conexión PDO
$con = conecta();

try {
    // Esta consulta obtiene todos los ejemplares. La agrupación se hace en PHP.
    // Asegúrate que los nombres de las columnas (isbn, titulo, autor, num_ejemplar)
    // y el nombre de la tabla (libro) sean correctos.
    $query = "SELECT isbn, titulo, autor, num_ejemplar FROM libro ORDER BY titulo ASC, isbn ASC, num_ejemplar ASC";
    $stmt = $con->prepare($query); // Es buena práctica preparar la consulta
    $stmt->execute();

    if (!$stmt) {
        throw new PDOException("Error al ejecutar la consulta para obtener los libros.");
    }
    
    $all_exemplars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $libros_agrupados = []; // Aquí se guardarán los libros únicos con sus ejemplares
    
    if (empty($all_exemplars)) {
        // No hay libros en la base de datos o la consulta no devolvió resultados.
        // Devolver un array vacío es válido JSON y el frontend debería manejarlo.
    } else {
        foreach ($all_exemplars as $exemplar_row) {
            $isbn = $exemplar_row['isbn'];
            
            // Si este ISBN no ha sido visto antes, creamos una nueva entrada para el libro
            if (!isset($libros_agrupados[$isbn])) {
                $libros_agrupados[$isbn] = [
                    'isbn' => $isbn,
                    'titulo' => $exemplar_row['titulo'], // Asumimos que título y autor son iguales para el mismo ISBN
                    'autor' => $exemplar_row['autor'],
                    'ejemplares_disponibles' => [] // Inicializamos la lista de sus ejemplares
                ];
            }
            // Añadimos el número de este ejemplar a la lista de ejemplares del libro correspondiente
            $libros_agrupados[$isbn]['ejemplares_disponibles'][] = $exemplar_row['num_ejemplar'];
        }
    }

    // Convertimos el array asociativo (indexado por ISBN) a un array numérico simple para la salida JSON.
    // Esto asegura que el JSON sea un array de objetos libro, como espera el JavaScript.
    echo json_encode(array_values($libros_agrupados));

} catch (PDOException $e) {
    http_response_code(500); 
    error_log("Error en obtener_libros.php: " . $e->getMessage()); // Loguear el error en el servidor
    // Enviar un mensaje de error genérico al cliente por seguridad
    echo json_encode(['error' => 'Error en el servidor al obtener la lista de libros. Por favor, intente más tarde o contacte al administrador.']);
}
?>