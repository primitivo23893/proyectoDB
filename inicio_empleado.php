<?php $usuario = $_GET['usuario'] ?? 'Empleado'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio Empleado</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('imagenes/InicioE.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .message-container {
		text-align: center;
		padding: 30px;
		background-color: rgba(255, 255, 255, 0.1); /* más transparente */
		border-radius: 15px;
		box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25); /* sombra más suave y extendida */
		backdrop-filter: blur(10px); /* desenfoque de fondo */
		-webkit-backdrop-filter: blur(10px); /* soporte para Safari */
		border: 1px solid rgba(255, 255, 255, 0.3); /* borde sutil */
		width: 400px;
	}


        .message {
            padding: 15px;
            border-radius: 8px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            margin: 20px 0;
        }

        .logout-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #e53935;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c62828;
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
    .menu-button:hover {
        background-color: #45a049;
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
    </style>
</head>
<body>
    <div class="message-container">
        <button class="menu-button" onclick="toggleMenu()">Menú</button>
        <div class="message">✅ Bienvenido, <?php echo htmlspecialchars($usuario); ?>. Has ingresado correctamente.</div>
        <a href="login.php" class="logout-button">Salir</a>
    </div>

    <div class="dropdown-menu" id="mainMenu">
        
        <div class="menu-item" onclick="toggleSubmenu('alumnosSubmenu')">PRESTAMOS</div>
        <div class="submenu" id="alumnosSubmenu">
            <a href="Registrar_prestamo.php">Registrar prestamo</a>
            <a href="Prestamo_consulta.php">Consultas Prestamos</a>
        </div>
        
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
