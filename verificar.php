<?php
session_start();

$host = 'localhost';
$port = '5432';
$dbname = 'biblioteca';
$user = 'postgres';
$password = '1234';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recoge los datos del formulario
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Consulta sin comillas dobles
    $stmt = $conn->prepare('SELECT * FROM usuario WHERE usuario = :usuario AND contraseña = :contrasena');
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':contrasena', $contrasena);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $_SESSION['usuario'] = $usuario;

        if ($usuario === 'admin') {
            header("Location: inicio_admin.php");
        } else {
            header("Location: inicio_empleado.php");
        }
        exit();
    } else {
        echo "<div class='message error'>❌ Usuario o contraseña incorrectos.</div>";
    }

} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>
