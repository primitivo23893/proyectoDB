<?php
header('Content-Type: application/json');
include 'conexion.php'; // Asegúrate que este archivo establece correctamente la conexión PDO

// Asumimos que conecta() devuelve tu objeto de conexión PDO
$con = conecta();

try {
    // CAMBIO: La consulta sigue siendo la misma, pero el procesamiento posterior en PHP cambiará
    // para agrupar los ejemplares por libro.
    // 'num_ejemplar' se asume que es el identificador de la copia específica para un ISBN dado.
    $query = "SELECT isbn, titulo, autor, num_ejemplar FROM libro ORDER BY titulo ASC, isbn ASC, num_ejemplar ASC";
    $stmt = $con->query($query);

    // Es buena práctica verificar si la consulta se ejecutó correctamente,
    // especialmente si PDO no está configurado para lanzar excepciones en todos los errores.
    if (!$stmt) {
        throw new PDOException("Error al ejecutar la consulta para obtener los libros.");
    }
    
    $all_exemplars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // CAMBIO: Agrupar ejemplares por libro
    $libros_agrupados = [];
    foreach ($all_exemplars as $exemplar_row) {
        $isbn = $exemplar_row['isbn'];
        
        // Si el libro (identificado por ISBN) aún no está en nuestro array de agrupados, lo inicializamos.
        if (!isset($libros_agrupados[$isbn])) {
            $libros_agrupados[$isbn] = [
                'isbn' => $isbn,
                'titulo' => $exemplar_row['titulo'],
                'autor' => $exemplar_row['autor'],
                'ejemplares_disponibles' => [] // Aquí guardaremos los números de ejemplar
            ];
        }
        // Añadimos el número de ejemplar actual a la lista de ejemplares disponibles para este libro.
        $libros_agrupados[$isbn]['ejemplares_disponibles'][] = $exemplar_row['num_ejemplar'];
    }

    // CAMBIO: Convertir el array asociativo (indexado por ISBN) a un array numérico simple para la salida JSON.
    // Esto asegura que el JSON sea un array de objetos libro, como espera el JavaScript.
    echo json_encode(array_values($libros_agrupados));

} catch (PDOException $e) {
    // CAMBIO: Mejor manejo de errores
    http_response_code(500); // Error Interno del Servidor
    // En un entorno de producción, es mejor loguear $e->getMessage() y mostrar un error genérico al usuario.
    echo json_encode(['error' => 'Error al obtener la lista de libros desde el servidor: ' . $e->getMessage()]);
}
?>