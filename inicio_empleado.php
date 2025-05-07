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
    </style>
</head>
<body>
    <div class="message-container">
        <div class="message">✅ Bienvenido, <?php echo htmlspecialchars($usuario); ?>. Has ingresado correctamente.</div>
        <a href="login.php" class="logout-button">Salir</a>
    </div>
</body>
</html>
