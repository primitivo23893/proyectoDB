<?php
include 'conexion.php';
$con = conecta();

$tipo = $_GET['tipo'] ?? '';

if ($tipo === 'alumna') {
    $query = "SELECT codigo, nombre FROM alumno ORDER BY nombre";
} elseif ($tipo === 'maestra') {
    $query = "SELECT codigo, nombre FROM profesor ORDER BY nombre";
} else {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $con->query($query);
    $nombres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($nombres);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>