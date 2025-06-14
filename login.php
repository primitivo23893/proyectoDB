<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Asegúrate que la ruta a tu imagen de fondo sea correcta */
            background: url('imagenes/Fondo.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
            width: 320px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        h2 {
            text-align: center;
            color: #000;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #000;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.8);
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        input[type="submit"]:hover {
            background-color: #fb8c00;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
        }

        /* Estilos para mensajes */
        .message {
            padding: 15px;
            border-radius: 8px;
            font-size: 14px; /* Ajustado tamaño */
            margin-top: 20px;
            text-align: center;
            display: none; /* Oculto por defecto */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .success { /* Aunque no lo uses ahora, lo dejamos por si acaso */
            background-color: #4CAF50;
            color: white;
        }

        .error {
            background-color: #f44336;
            color: white;
        }

    </style>
</head>
<body>
    <form id="loginForm">
        <h2>Iniciar Sesión</h2>

        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <input type="submit" value="Ingresar">
        <div id="message" class="message"></div>
    </form>

    <script>
    $(document).ready(function () {
        $('#loginForm').on('submit', function (e) {
            e.preventDefault(); // Prevenir el envío normal del formulario
            $('#message').fadeOut().removeClass('error success'); // Ocultar mensaje previo

            let usuario = $('#usuario').val();
            let contrasena = $('#contrasena').val();

            $.ajax({
                url: 'verificar.php', 
                type: 'POST',
                data: {
                    usuario: usuario,
                    contrasena: contrasena
                },
                dataType: 'json', 
                success: function (response) {
                    console.log('Respuesta recibida:', response); 

                    if (response && response.success) {
                        
                        window.location.href = response.redirect;
                    } else {
                        
                        let errorMessage = (response && response.message) ? response.message : 'Error desconocido.';
                         $('#message')
                            .removeClass('success') 
                            .addClass('error')
                            .text(errorMessage)
                            .fadeIn();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Error en la comunicación AJAX (ej. servidor no responde, error 500, JSON mal formado)
                    console.error("Error AJAX:", textStatus, errorThrown, jqXHR.responseText); // Log detallado en consola
                    $('#message')
                        .removeClass('success')
                        .addClass('error')
                        .text('No se pudo conectar con el servidor. Inténtalo de nuevo.')
                        .fadeIn();
                }
            });
        });
    });
    </script>

</body>
</html>