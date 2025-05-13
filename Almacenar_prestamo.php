<?php
include 'conexion.php';
$con = conecta();

$tipo = $_POST['tipo_persona'] ?? '';
$codigo = $_POST['nombre'] ?? '';
$isbn = $_POST['libro'] ?? '';

echo "<pre>";
print_r($_POST);
echo "</pre>";

// Validación básica
if (empty($tipo) || empty($codigo) || empty($isbn)) {
    die("❌ Faltan datos para registrar el préstamo.");
}

if (!in_array($tipo, ['alumna', 'maestra'])) {
    die("❌ Tipo de persona inválido.");
}

// Verificar existencia del solicitante
try {
    $tabla = $tipo === 'alumna' ? 'alumno' : 'profesor';
    $check = $con->prepare("SELECT 1 FROM $tabla WHERE codigo = :codigo");
    $check->execute([':codigo' => $codigo]);

    if (!$check->fetch()) {
        die("❌ El código de $tipo no existe.");
    }

    // Fechas
    $fechaPrestamo = date('Y-m-d');
    $fechaLimite = date('Y-m-d', strtotime('+7 days'));

    // Insertar en la tabla prestamo
    $sql = "INSERT INTO prestamo (
                id_solicitante, tipo_solicitante, id_libro,
                fecha_prestamo, fecha_limite_entrega, fecha_entrega_solicitante, multa
            ) VALUES (
                :id_solicitante, :tipo_solicitante, :id_libro,
                :fecha_prestamo, :fecha_limite_entrega, NULL, NULL
            )";

    $stmt = $con->prepare($sql);
    $stmt->execute([
        ':id_solicitante' => $codigo,
        ':tipo_solicitante' => $tipo,
        ':id_libro' => $isbn,
        ':fecha_prestamo' => $fechaPrestamo,
        ':fecha_limite_entrega' => $fechaLimite
    ]);

    echo "✅ Préstamo registrado correctamente.";

} catch (PDOException $e) {
    echo "❌ Error al registrar el préstamo: " . $e->getMessage();
}
?>