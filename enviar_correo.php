<?php
header('Content-Type: application/json');

// Datos esperados del POST, enviados desde el JavaScript después de un préstamo exitoso
$email_destinatario = $_POST['email_destinatario'] ?? null;
$nombre_destinatario = $_POST['nombre_destinatario'] ?? 'Estimad@ Usuario';
$tipo_solicitante = $_POST['tipo_solicitante'] ?? null;
$libro_info = $_POST['libro_info'] ?? '(Información del libro no disponible)';
$fecha_prestamo_formato = $_POST['fecha_prestamo_formato'] ?? 'N/A';
$fecha_limite_formato = $_POST['fecha_limite_formato'] ?? 'N/A';

if (!$email_destinatario || !$tipo_solicitante) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos esenciales (email o tipo de solicitante) para enviar el correo.']);
    exit;
}

// Cargar librerías de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar archivos de PHPMailer (¡ASEGÚRATE QUE LA RUTA ES CORRECTA!)
// Considera usar Composer para manejar dependencias y autoloading en el futuro.
require '/opt/lampp/htdocs/PHPMailer-master/src/Exception.php';
require '/opt/lampp/htdocs/PHPMailer-master/src/PHPMailer.php';
require '/opt/lampp/htdocs/PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Configurar servidor SMTP para Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'primitivo.oyoque6952@alumnos.udg.mx'; // Tu dirección de correo Gmail
    // ¡¡IMPORTANTE!! Coloca tu contraseña de aplicación de Gmail aquí si usas 2FA,
    // o tu contraseña normal si tienes "Acceso de aplicaciones menos seguras" habilitado (no recomendado).
    $mail->Password = 'istoilfmsmhyowwx'; //https://myaccount.google.com/apppasswords
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8'; // Para correcta visualización de acentos y ñ

    // Remitente (tu correo configurado arriba) y Destinatario (el solicitante del préstamo)
    $mail->setFrom('primitivo.oyoque6952@alumnos.udg.mx', 'Biblioteca CUCEI CID');
    $mail->addAddress($email_destinatario, $nombre_destinatario);

    // Contenido del correo
    $mail->isHTML(true); // El correo será en formato HTML

    $asunto_correo = "";
    $cuerpo_mensaje = "";
    $termino_solicitante = ""; // "profesor@" o "alumn@"
    $saludo_inicial = "";

    if ($tipo_solicitante === 'maestra') {
        $asunto_correo = "Préstamo de libro Biblioteca CUCEI - Profesores";
        $multa_str = "10\$ pesos"; // Pesos mexicanos
        $termino_solicitante = "profesor@";
        $saludo_inicial = "Buen día, estimad@ {$termino_solicitante} {$nombre_destinatario},";
        $cuerpo_mensaje = "{$saludo_inicial}<br><br>El motivo de este correo es para comentarle lo necesario sobre el préstamo del libro que pidió {$libro_info} con la fecha correspondiente a: {$fecha_prestamo_formato}.<br><br>";
        $cuerpo_mensaje .= "También le comento que tendrá una semana de tolerancia para poder devolver el libro, la fecha límite para devolver el libro será el día {$fecha_limite_formato}.<br><br>";
        $cuerpo_mensaje .= "Si el libro no fue devuelto en la fecha indicada, se cobrarán {$multa_str} por cada día que el libro sea devuelto tarde.<br><br>De antemano muchas gracias, qué tenga un lindo día.";
    } elseif ($tipo_solicitante === 'alumna') {
        $asunto_correo = "Préstamo de libro Biblioteca CUCEI - Alumnos";
        $multa_str = "5\$ pesos"; // Pesos mexicanos
        $termino_solicitante = "alumn@";
        $saludo_inicial = "Buen día, estimad@ {$termino_solicitante} {$nombre_destinatario},";
        $cuerpo_mensaje = "{$saludo_inicial}<br><br>El motivo de este correo es para comentarle lo necesario sobre el préstamo del libro que pidió {$libro_info} con la fecha correspondiente a: {$fecha_prestamo_formato}.<br><br>";
        $cuerpo_mensaje .= "También le comento que tendrá una semana de tolerancia para poder devolver el libro, la fecha límite para devolver el libro será el día {$fecha_limite_formato}.<br><br>";
        $cuerpo_mensaje .= "Si el libro no fue devuelto en la fecha indicada, se cobrarán {$multa_str} por cada día que el libro sea devuelto tarde.<br><br>De antemano muchas gracias, qué tenga un lindo día.";
    } else {
        // Tipo de solicitante no reconocido, no se puede generar la plantilla.
        echo json_encode(['success' => false, 'message' => 'Tipo de solicitante no reconocido para la plantilla de correo.']);
        exit;
    }

    $mail->Subject = $asunto_correo;
    $mail->Body    = $cuerpo_mensaje;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Correo de confirmación de préstamo enviado exitosamente.']);

} catch (Exception $e) {
    // En un entorno de producción, es mejor loguear $mail->ErrorInfo en un archivo de log
    // y mostrar un mensaje más genérico al JavaScript que hizo la llamada.
    error_log("Error PHPMailer: " . $mail->ErrorInfo); // Log del error detallado en el servidor
    echo json_encode(['success' => false, 'message' => "Error al enviar el correo de confirmación. Por favor, contacte al administrador. Detalles: {$mail->ErrorInfo}"]);
}
?>