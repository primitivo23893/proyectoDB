<?php
// Establecer el tipo de contenido a JSON para todas las respuestas de este script
header('Content-Type: application/json');
include 'conexion.php'; // Asegúrate que este archivo establece correctamente la conexión PDO
$con = conecta(); // Asumimos que conecta() devuelve tu objeto de conexión PDO

$tipo = $_POST['tipo_persona'] ?? '';
$codigo_solicitante = $_POST['nombre'] ?? ''; // El campo 'nombre' del form contiene el código del solicitante
$isbn_libro = $_POST['libro'] ?? '';
$num_ejemplar_seleccionado = $_POST['ejemplar'] ?? '';

if (empty($tipo) || empty($codigo_solicitante) || empty($isbn_libro) || $num_ejemplar_seleccionado === '') {
    echo json_encode(['success' => false, 'message' => '❌ Faltan datos para registrar el préstamo. Asegúrate de seleccionar tipo, nombre, libro y ejemplar.']);
    exit;
}

if (!in_array($tipo, ['alumna', 'maestra'])) {
    echo json_encode(['success' => false, 'message' => '❌ Tipo de persona inválido.']);
    exit;
}

try {
    $tabla_solicitante = ($tipo === 'alumna') ? 'alumno' : 'profesor';
    
    // Obtener nombre completo y correo del solicitante de la columna 'correo'
    $stmtDatosSol = $con->prepare("SELECT nombre, correo FROM $tabla_solicitante WHERE codigo = :codigo");
    $stmtDatosSol->execute([':codigo' => $codigo_solicitante]);
    $datos_solicitante = $stmtDatosSol->fetch(PDO::FETCH_ASSOC);

    if (!$datos_solicitante) {
        echo json_encode(['success' => false, 'message' => "❌ El código de $tipo '$codigo_solicitante' no existe o no se encontraron datos de contacto."]);
        exit;
    }
    $nombre_completo_solicitante = $datos_solicitante['nombre'];
    $email_solicitante = $datos_solicitante['correo']; // Obtenido de la columna 'correo'

    if (empty($email_solicitante)) {
        // Si necesitas que el correo sea obligatorio, puedes manejar este caso
        echo json_encode(['success' => false, 'message' => "❌ El solicitante '$nombre_completo_solicitante' no tiene un correo electrónico registrado."]);
        exit;
    }

    // Obtener título y autor del libro
    $stmtDatosLibro = $con->prepare("SELECT titulo, autor FROM libro WHERE isbn = :isbn LIMIT 1"); // Asume que título y autor son consistentes para un ISBN
    $stmtDatosLibro->execute([':isbn' => $isbn_libro]);
    $datos_libro = $stmtDatosLibro->fetch(PDO::FETCH_ASSOC);

    if (!$datos_libro) {
        echo json_encode(['success' => false, 'message' => "❌ No se encontraron datos para el libro con ISBN '$isbn_libro'."]);
        exit;
    }
    $titulo_libro = $datos_libro['titulo'];
    $autor_libro = $datos_libro['autor'];


    $fechaPrestamo = date('Y-m-d');
    $fechaLimite = date('Y-m-d', strtotime('+7 days'));

    // Asegúrate que tu tabla 'prestamo' tiene la columna 'num_ejemplar'
    $sql = "INSERT INTO prestamo (
                id_solicitante, tipo_solicitante, id_libro,
                fecha_prestamo, fecha_limite_entrega, fecha_entrega_solicitante, multa, num_ejemplar 
            ) VALUES (
                :id_solicitante, :tipo_solicitante, :id_libro,
                :fecha_prestamo, :fecha_limite_entrega, NULL, NULL, :num_ejemplar 
            )";

    $stmt = $con->prepare($sql);
    $stmt->execute([
        ':id_solicitante' => $codigo_solicitante,
        ':tipo_solicitante' => $tipo,
        ':id_libro' => $isbn_libro, // id_libro en la tabla prestamo es el ISBN
        ':fecha_prestamo' => $fechaPrestamo,
        ':fecha_limite_entrega' => $fechaLimite,
        ':num_ejemplar' => $num_ejemplar_seleccionado
    ]);

    // Preparar datos para el correo
    $fechaPrestamoFormato = date('d/m/y', strtotime($fechaPrestamo));
    $fechaLimiteFormato = date('d/m/y', strtotime($fechaLimite));
    // Formato del libro como: (Título - Autor, Ejemplar X)
    $libro_info_correo = "($titulo_libro - $autor_libro, Ejemplar $num_ejemplar_seleccionado)";


    echo json_encode([
        'success' => true,
        'message' => "✅ Préstamo registrado correctamente para el ISBN: $isbn_libro, Ejemplar: $num_ejemplar_seleccionado.",
        'datos_correo' => [
            'email_destinatario' => $email_solicitante,
            'nombre_destinatario' => $nombre_completo_solicitante,
            'tipo_solicitante' => $tipo, // 'alumna' o 'maestra'
            'libro_info' => $libro_info_correo,
            'fecha_prestamo_formato' => $fechaPrestamoFormato,
            'fecha_limite_formato' => $fechaLimiteFormato
        ]
    ]);

} catch (PDOException $e) {
    // En producción, loguear el error $e->getMessage() y mostrar un mensaje más genérico
    echo json_encode(['success' => false, 'message' => "❌ Error al registrar el préstamo en la BD: " . $e->getMessage()]);
}
?>