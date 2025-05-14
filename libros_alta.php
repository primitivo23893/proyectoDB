<?php
// Incluir el archivo de conexión
include 'conexion.php';
$usuario = $_GET['usuario'] ?? 'Administrador';
$con = conecta();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn = $_POST['isbn'] ?? '';
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $editorial = $_POST['editorial'] ?? '';
    $anio_publicacion = $_POST['anio_publicacion'] ?? '';
    $num_ejemplares = $_POST['num_ejemplares'] ?? '';
    
    // Validación básica
    if (empty($isbn) || empty($titulo) || empty($autor)) {
        $mensaje = "❌ Por favor complete los campos obligatorios.";
        $tipo = "error";
    } else {
        try {
            // Preparar la consulta SQL
            $sql = "INSERT INTO Libro (isbn, titulo, autor, editorial, anio_publicacion, num_ejemplares) 
                    VALUES (:isbn, :titulo, :autor, :editorial, :anio_publicacion, :num_ejemplares)";
            $stmt = $con->prepare($sql);
            
            // Vincular parámetros
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':editorial', $editorial);
            $stmt->bindParam(':anio_publicacion', $anio_publicacion);
            $stmt->bindParam(':num_ejemplares', $num_ejemplares);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            $mensaje = "✅ Libro registrado correctamente.";
            $tipo = "success";
        } catch (PDOException $e) {
            $mensaje = "❌ Error al registrar libro: " . $e->getMessage();
            $tipo = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Libros</title>
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

        
        .content {
            margin-top: 80px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 500px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .submit-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        
        .submit-button:hover {
            background-color: #45a049;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <button class="menu-button" onclick="toggleMenu()">Menú</button>
        <div class="page-title">Alta de Libros - Usuario: <?php echo htmlspecialchars($usuario); ?></div>
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
            <h2>Registrar Nuevo Libro</h2>
            
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" required>
                </div>
                
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label for="autor">Autor:</label>
                    <input type="text" id="autor" name="autor" required>
                </div>
                
                <div class="form-group">
                    <label for="editorial">Editorial:</label>
                    <input type="text" id="editorial" name="editorial">
                </div>
                
                <div class="form-group">
                    <label for="anio_publicacion">Año de publicación:</label>
                    <input type="number" id="anio_publicacion" name="anio_publicacion" min="1000" max="<?php echo date('Y'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="num_ejemplares">Número de ejemplares:</label>
                    <input type="number" id="num_ejemplares" name="num_ejemplares" min="0">
                </div>
                
                <button type="submit" class="submit-button">Registrar Libro</button>
            </form>
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