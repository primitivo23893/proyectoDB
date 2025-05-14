<?php
// Incluir el archivo de conexión
include 'conexion.php';
$con = conecta();
$usuario = $_GET['usuario'] ?? 'Administrador';

// Consultar todos los libros
try {
    $sql = "SELECT * FROM libro ORDER BY titulo";
    $stmt = $con->query($sql);
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al consultar la base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Libros</title>
    <style>
        /* Tu mismo estilo general */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: url('imagenes/inicioA.png') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
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
        .button-group a.logout-button { background: #e53935; }
        .button-group a.return-button { background: #2196F3; }
        .button-group a:hover, .menu-button:hover { filter: brightness(0.9); }
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
        .menu-item { padding: 15px; cursor: pointer; border-bottom: 1px solid #eee; font-weight: 500; }
        .menu-item:hover { background: #f0f0f0; }
        .submenu { display: none; padding-left: 20px; background: #fafafa; }
        .submenu a { display: block; padding: 10px 15px; color: #333; text-decoration: none; }
        .submenu a:hover { background: #e0e0e0; }
        .content {
            margin-top: 120px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 1000px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table thead {
            background-color: #4CAF50;
            color: white;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ebccd1;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <button class="menu-button" onclick="toggleMenu()">Menú</button>
        <div class="page-title">Consulta de Libros - Usuario: <?php echo htmlspecialchars($usuario); ?></div>
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
        <div class="table-container">
            <h2>Listado de Libros</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php elseif (empty($libros)): ?>
                <p>No hay libros registrados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Editorial</th>
                            <th>Año</th>
                            <th>Ejemplares</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($libro['isbn']); ?></td>
                                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                <td><?php echo htmlspecialchars($libro['editorial']); ?></td>
                                <td><?php echo htmlspecialchars($libro['anio_publi']); ?></td>
                                <td><?php echo htmlspecialchars($libro['num_ejemplar']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleMenu() {
            var menu = document.getElementById('mainMenu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id);
            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
