<?php
include 'conexion.php';
$con = conecta();
$usuario = $_GET['usuario'] ?? 'Administrador';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $carrera = $_POST['carrera'] ?? '';
    $correo = $_POST['correo'] ?? '';

    if (empty($codigo) || empty($nombre) || empty($carrera) || empty($correo)) {
        $mensaje = "❌ Por favor complete todos los campos.";
        $tipo = "error";
    } else {
        try {
            $sql = "INSERT INTO Alumno (codigo, nombre, carrera, correo) VALUES (:codigo, :nombre, :carrera, :correo)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':carrera', $carrera);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $mensaje = "✅ Alumno registrado correctamente.";
            $tipo = "success";
        } catch (PDOException $e) {
            $mensaje = "❌ Error al registrar alumno: " . $e->getMessage();
            $tipo = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Alta de Alumnos</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: url('imagenes/inicioA.png') no-repeat center center fixed;
        background-size: cover;
        height: 100vh;
        color: #333;
    }

    .top-bar {
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(10px);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 2rem;
        position: fixed;
        width: 100%;
        z-index: 1000;
        top: 0;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
        flex-grow: 1;
    }

    .button-group a, .menu-button {
        background: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        margin-left: 10px;
        text-decoration: none;
        border-radius: 25px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .button-group a.logout-button {
        background: #e53935;
    }

    .button-group a.return-button {
        background: #2196F3;
    }

    .button-group a:hover, .menu-button:hover {
        filter: brightness(0.9);
    }

    .dropdown-menu {
        display: none;
        position: fixed;
        top: 70px;
        left: 20px;
        background: rgba(255,255,255,0.95);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        animation: fadeIn 0.5s;
    }

    .menu-item {
        padding: 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-weight: 500;
    }

    .menu-item:hover {
        background: #f0f0f0;
    }

    .submenu {
        display: none;
        padding-left: 20px;
        background: #fafafa;
    }

    .submenu a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
    }

    .submenu a:hover {
        background: #e0e0e0;
    }

    .content {
        margin-top: 120px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 2rem;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #555;
    }

    input[type="text"], input[type="email"] {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #ccc;
        outline: none;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, input[type="email"]:focus {
        border-color: #4CAF50;
    }

    .submit-button {
        background: linear-gradient(45deg, #4CAF50, #66bb6a);
        border: none;
        color: white;
        padding: 12px;
        width: 100%;
        font-weight: bold;
        font-size: 16px;
        border-radius: 25px;
        margin-top: 10px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .submit-button:hover {
        filter: brightness(0.95);
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>

<div class="top-bar">
    <button class="menu-button" onclick="toggleMenu()">☰ Menú</button>
    <div class="page-title">Alta de Alumnos - Usuario: <?php echo htmlspecialchars($usuario); ?></div>
    <div class="button-group">
        <a href="inicio_admin.php?usuario=<?php echo urlencode($usuario); ?>" class="return-button">Regresar</a>
        <a href="login.php" class="logout-button">Salir</a>
    </div>
</div>

<div class="dropdown-menu" id="mainMenu">
    <div class="menu-item" onclick="toggleSubmenu('alumnosSubmenu')">Alumnos</div>
    <div class="submenu" id="alumnosSubmenu">
        <a href="alumnos_alta.php?usuario=<?php echo urlencode($usuario); ?>">Altas</a>
        <a href="alumnos_consulta.php?usuario=<?php echo urlencode($usuario); ?>">Consultas generales</a>
    </div>

    <div class="menu-item" onclick="toggleSubmenu('profesoresSubmenu')">Profesores</div>
    <div class="submenu" id="profesoresSubmenu">
        <a href="profesores_alta.php?usuario=<?php echo urlencode($usuario); ?>">Altas</a>
        <a href="profesores_consulta.php?usuario=<?php echo urlencode($usuario); ?>">Consultas generales</a>
    </div>

    <div class="menu-item" onclick="toggleSubmenu('librosSubmenu')">Libros</div>
    <div class="submenu" id="librosSubmenu">
        <a href="libros_alta.php?usuario=<?php echo urlencode($usuario); ?>">Altas</a>
        <a href="libros_consulta.php?usuario=<?php echo urlencode($usuario); ?>">Consultas generales</a>
    </div>
</div>

<div class="content">
    <div class="form-container">
        <h2>Registrar Nuevo Alumno</h2>

        <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera:</label>
                <input type="text" id="carrera" name="carrera" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>

            <button type="submit" class="submit-button">Registrar Alumno</button>
        </form>
    </div>
</div>

<script>
function toggleMenu() {
    var menu = document.getElementById('mainMenu');
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

function toggleSubmenu(id) {
    var submenu = document.getElementById(id);
    submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>
