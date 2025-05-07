<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio Administrador</title>
    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url('imagenes/inicioA.png') no-repeat center center fixed;
        background-size: cover;
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .top-bar {
        background-color: rgba(51, 51, 51, 0.8); /* Color más transparente */
        color: white;
        padding: 15px;
        border-radius: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
        margin-bottom: 20px;
    }

    .welcome-message {
        font-size: 18px;
        text-align: center;
    }

    .menu-button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 16px;
    }

    .logout-button {
        background-color: #e53935;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
    }

    .menu-button:hover {
        background-color: #45a049;
    }

    .logout-button:hover {
        background-color: #c62828;
    }

    .dropdown-menu {
        background: rgba(255, 255, 255, 0.7); /* Fondo blanco semitransparente */
        backdrop-filter: blur(10px); /* Borroso */
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        min-width: 250px;
        padding: 10px;
        display: none;
        flex-direction: column;
        align-items: center;
    }

    .menu-item {
        padding: 10px;
        width: 100%;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
        border-bottom: 1px solid #ccc;
    }

    .menu-item:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .submenu {
        padding-left: 0;
        display: none;
        width: 100%;
    }

    .submenu a {
        display: block;
        padding: 8px;
        text-decoration: none;
        color: #333;
        text-align: center;
    }

    .submenu a:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .content {
        margin-top: 20px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 15px;
        text-align: center;
    }
</style>

</head>
<body>
    <?php
    // Incluir el archivo de conexión
    include 'conexion.php';
    $usuario = $_GET['usuario'] ?? 'Administrador';
    ?>
    
    <div class="top-bar">
        <button class="menu-button" onclick="toggleMenu()">Menú</button>
        <div class="welcome-message">Bienvenido, <?php echo htmlspecialchars($usuario); ?>. Has ingresado correctamente.</div>
        <a href="login.php" class="logout-button">Salir</a>
    </div>
    
    <div class="dropdown-menu" id="mainMenu">
        <div class="menu-item" onclick="toggleSubmenu('alumnosSubmenu')">Alumnos</div>
        <div class="submenu" id="alumnosSubmenu">
            <a href="alumnos_alta.php">Altas</a>
            <a href="alumnos_consulta.php">Consultas generales</a>
        </div>
        
        <div class="menu-item" onclick="toggleSubmenu('profesoresSubmenu')">Profesores</div>
        <div class="submenu" id="profesoresSubmenu">
            <a href="profesores_alta.php">Altas</a>
            <a href="profesores_consulta.php">Consultas generales</a>
        </div>
        
        <div class="menu-item" onclick="toggleSubmenu('librosSubmenu')">Libros</div>
        <div class="submenu" id="librosSubmenu">
            <a href="libros_alta.php">Altas</a>
            <a href="libros_consulta.php">Consultas generales</a>
        </div>
    </div>
    
    <div class="content">
        <!-- Aquí irá el contenido principal de la página -->
    </div>
    
    <script>
        function toggleMenu() {
            var menu = document.getElementById('mainMenu');
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }
        
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id);
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                submenu.style.display = 'block';
            }
        }
    </script>
</body>
</html>