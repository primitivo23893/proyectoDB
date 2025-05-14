<?php
include 'conexion.php';
$con = conecta();

// Consulta todos los préstamos
$query = "SELECT * FROM prestamo ORDER BY fecha_prestamo DESC";

$stmt = $con->prepare($query);
$stmt->execute();
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Préstamos</title>
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
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
        h2 {
            text-align: center;
        }

        table {
            
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }

        th, td {
            
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #0066cc;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
     <div class="top-bar">
        <button class="menu-button" onclick="toggleMenu()">Menú</button>
        <div class="button-group">
            <a href="inicio_empleado.php?usuario=<?php echo urlencode($usuario); ?>" class="return-button">Regresar</a>
            <a href="login.php" class="logout-button">Salir</a>
        </div>
    </div>
    <div class="dropdown-menu" id="mainMenu">
        <div class="menu-item" onclick="toggleSubmenu('alumnosSubmenu')">Prestamo</div>
        <div class="submenu" id="alumnosSubmenu">
            <a href="Registrar_prestamo.php?usuario=<?php echo urlencode($usuario); ?>">Registrar</a>
            <a href="Prestamo_consulta.php?usuario=<?php echo urlencode($usuario); ?>">Consultas</a>
        </div>
        
       
        
       
    </div>
    <h2>Préstamos Registrados</h2>
    <table>
        <tr>
            <th>ID Préstamo</th>
            <th>Solicitante</th>
            <th>Tipo</th>
            <th>Libro (ISBN)</th>
            <th>Nº Ejemplar</th>
            <th>Fecha Préstamo</th>
            <th>Fecha Entrega</th>
            <th>Multa</th>
        </tr>
        <?php foreach ($prestamos as $p): ?>
        <tr>
            <td><?= $p['id_prestamo'] ?></td>
            <td><?= htmlspecialchars($p['id_solicitante']) ?></td>
            <td><?= ucfirst($p['tipo_solicitante']) ?></td>
            <td><?= $p['id_libro'] ?></td>
            <td><?= $p['num_ejemplar'] ?></td>
            <td><?= $p['fecha_prestamo'] ?></td>
            <td><?= $p['fecha_entrega_solicitante'] ?? '-' ?></td>
            <td><?= $p['multa'] ?? '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
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
