<?php
header('Content-Type: application/json');
include 'conexion.php';

$con = conecta();

try {
    $query = "SELECT isbn, titulo FROM libro GROUP BY isbn, titulo ORDER BY titulo ASC";
    $stmt = $con->query($query);
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($libros);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener libros: ' . $e->getMessage()]);
}
?>