<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Préstamo</title>
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

        .form-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 450px;
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #444;
        }

        select,
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #0066cc;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #004d99;
        }

        input[type="submit"]:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .feedback-message {
            padding: 10px;
            margin-top: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 0.9em;
        }

        .feedback-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .feedback-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .feedback-message.info {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Registrar Préstamo de Libro</h2>
        <form id="formRegistrarPrestamo" method="POST">
            <label for="tipo_persona">Tipo de persona:</label>
            <select name="tipo_persona" id="tipo_persona" required>
                <option value="">-- Selecciona --</option>
                <option value="alumna">Alumno</option>
                <option value="maestra">Maestro</option>
            </select>

            <label for="nombre">Nombre (Código del solicitante):</label>
            <select name="nombre" id="nombre" required>
                <option value="">-- Selecciona un tipo de persona primero --</option>
            </select>

            <label for="libro">Libro que desea solicitar:</label>
            <select name="libro" id="libro" required>
                <option value="">-- Cargando libros... --</option>
            </select>

            <label for="ejemplar">Ejemplar disponible:</label>
            <select name="ejemplar" id="ejemplar" required>
                <option value="">-- Selecciona un libro primero --</option>
            </select>

            <input type="submit" value="Registrar Préstamo">
        </form>
        <div id="feedbackContainer"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipoPersonaSelect = document.getElementById('tipo_persona');
            const nombreSelect = document.getElementById('nombre');
            const libroSelect = document.getElementById('libro');
            const ejemplarSelect = document.getElementById('ejemplar');
            const formRegistrarPrestamo = document.getElementById('formRegistrarPrestamo');
            const feedbackContainer = document.getElementById('feedbackContainer');
            let librosConEjemplares = [];

            // --- CAMBIO: Función mostrarFeedback mejorada ---
            function mostrarFeedback(mensaje, tipo = 'info', anadir = false, idMensaje = null, autoEliminarSegundos = 0) {
                // Si se proporciona un idMensaje, intenta eliminar uno existente con ese id
                if (idMensaje) {
                    const mensajeExistente = document.getElementById(idMensaje);
                    if (mensajeExistente) {
                        mensajeExistente.remove();
                    }
                }

                const messageDiv = document.createElement('div');
                messageDiv.className = `feedback-message ${tipo}`;
                messageDiv.textContent = mensaje;
                if (idMensaje) {
                    messageDiv.id = idMensaje; // Asignar ID si se proporciona
                }

                if (anadir) {
                    const ultimoBr = feedbackContainer.querySelector('br:last-child');
                    if (feedbackContainer.childNodes.length > 0 && (!ultimoBr || ultimoBr !== feedbackContainer.lastChild)) {
                        feedbackContainer.appendChild(document.createElement('br'));
                    }
                    feedbackContainer.appendChild(messageDiv);
                } else {
                    // Si no se añade, y no es para reemplazar un mensaje específico por ID, limpiar todo.
                    // Si es para reemplazar por ID y el mensaje no existía, no limpiar todo.
                    if (!idMensaje || (idMensaje && !document.getElementById(idMensaje))) {
                        //feedbackContainer.innerHTML = ''; // Comentado para evitar borrar mensajes que deben persistir
                    }
                    feedbackContainer.appendChild(messageDiv);
                }

                if (autoEliminarSegundos > 0) {
                    setTimeout(() => {
                        // Verificar si el div todavía existe antes de intentar removerlo
                        if (messageDiv.parentNode) {
                            // Si hay un <br> justo antes y es el último elemento, también quitarlo.
                            const prevSibling = messageDiv.previousElementSibling;
                            if (prevSibling && prevSibling.tagName === 'BR' && messageDiv.nextElementSibling === null) {
                                prevSibling.remove();
                            }
                            messageDiv.remove();
                        }
                    }, autoEliminarSegundos * 1000);
                }
                return messageDiv; // Devolver el elemento por si se necesita manipular más
            }

            // Función para eliminar un mensaje por ID
            function eliminarFeedback(idMensaje) {
                const mensajeExistente = document.getElementById(idMensaje);
                if (mensajeExistente) {
                    const prevSibling = mensajeExistente.previousElementSibling;
                    mensajeExistente.remove();
                    // Si había un <br> antes y ya no quedan más mensajes, o el siguiente no es un mensaje, quitar el br
                    if (prevSibling && prevSibling.tagName === 'BR') {
                        if (feedbackContainer.querySelectorAll('.feedback-message').length === 0 ||
                            (prevSibling.previousElementSibling && prevSibling.previousElementSibling.classList.contains('feedback-message'))) {
                            // No quitar el BR si hay un mensaje antes de él.
                        } else if (!prevSibling.nextElementSibling || !prevSibling.nextElementSibling.classList.contains('feedback-message')) {
                            prevSibling.remove();
                        }
                    }
                }
            }


            // --- Función para cargar Nombres (sin cambios funcionales mayores) ---
            function cargarNombres(tipoPersona) {
                // ... (código existente de cargarNombres)
                nombreSelect.innerHTML = '<option value="">Cargando...</option>';
                nombreSelect.disabled = true;
                if (tipoPersona === '') {
                    nombreSelect.innerHTML = '<option value="">-- Selecciona un tipo de persona primero --</option>';
                    return;
                }
                fetch('obtener_nombres.php?tipo=' + tipoPersona)
                    .then(response => {
                        if (!response.ok) throw new Error(`Error HTTP ${response.status} al obtener nombres.`);
                        return response.json();
                    })
                    .then(data => {
                        nombreSelect.innerHTML = '<option value="">-- Selecciona un nombre --</option>';
                        if (data && data.error) throw new Error(data.error);
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function (persona) {
                                const option = document.createElement('option');
                                option.value = persona.codigo;
                                option.textContent = persona.nombre;
                                nombreSelect.appendChild(option);
                            });
                            nombreSelect.disabled = false;
                        } else {
                            nombreSelect.innerHTML = '<option value="">No hay nombres para este tipo</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener nombres:', error);
                        nombreSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
                    });
            }

            if (tipoPersonaSelect && nombreSelect) {
                tipoPersonaSelect.addEventListener('change', function () { cargarNombres(this.value); });
                if (tipoPersonaSelect.value === '') nombreSelect.disabled = true;
                else cargarNombres(tipoPersonaSelect.value);
            }

            // --- Función para cargar Libros y Ejemplares (sin cambios funcionales mayores) ---
            function cargarLibrosYEjemplares() {
                // ... (código existente de cargarLibrosYEjemplares)
                if (!libroSelect || !ejemplarSelect) return;
                ejemplarSelect.innerHTML = '<option value="">-- Selecciona un libro primero --</option>';
                ejemplarSelect.disabled = true;
                libroSelect.innerHTML = '<option value="">-- Cargando libros... --</option>';
                fetch('obtener_libros.php')
                    .then(response => {
                        if (!response.ok) { return response.text().then(text => { throw new Error(`Error Servidor (${response.status}): ${text}`); }); }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.error) throw new Error('PHP Error: ' + data.error);
                        if (!Array.isArray(data)) throw new Error('Respuesta de servidor inesperada.');
                        librosConEjemplares = data;
                        libroSelect.innerHTML = '<option value="">-- Selecciona un libro --</option>';
                        if (librosConEjemplares.length === 0) {
                            libroSelect.innerHTML = '<option value="">No hay libros disponibles</option>';
                        } else {
                            librosConEjemplares.forEach(function (libro) {
                                if (!libro.isbn || !libro.titulo || !libro.autor) return;
                                const option = document.createElement('option');
                                option.value = libro.isbn;
                                option.textContent = `${libro.titulo} - ${libro.autor}`;
                                libroSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error cargando libros:', error);
                        libroSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
                        mostrarFeedback(`No se pudieron cargar libros: ${error.message}`, 'error');
                    });
            }
            cargarLibrosYEjemplares(); // Carga inicial

            if (libroSelect) {
                libroSelect.addEventListener('change', function () {
                    const selectedIsbn = this.value;
                    ejemplarSelect.innerHTML = '';
                    ejemplarSelect.disabled = true;
                    if (!selectedIsbn) {
                        ejemplarSelect.innerHTML = '<option value="">-- Selecciona un libro primero --</option>';
                        return;
                    }
                    const libroSeleccionado = librosConEjemplares.find(b => b.isbn === selectedIsbn);
                    if (libroSeleccionado && libroSeleccionado.ejemplares_disponibles && libroSeleccionado.ejemplares_disponibles.length > 0) {
                        ejemplarSelect.innerHTML = '<option value="">-- Selecciona un ejemplar --</option>';
                        libroSeleccionado.ejemplares_disponibles.forEach(function (num_ejemplar) {
                            const option = document.createElement('option');
                            option.value = num_ejemplar;
                            option.textContent = `Ejemplar ${num_ejemplar}`;
                            ejemplarSelect.appendChild(option);
                        });
                        ejemplarSelect.disabled = false;
                    } else {
                        ejemplarSelect.innerHTML = '<option value="">No hay ejemplares disponibles</option>';
                    }
                });
            }

            // Manejo del envío del formulario
            if (formRegistrarPrestamo) {
                formRegistrarPrestamo.addEventListener('submit', function (event) {
                    event.preventDefault();
                    feedbackContainer.innerHTML = ''; // Limpiar todos los mensajes anteriores al iniciar un nuevo envío
                    mostrarFeedback('Procesando registro de préstamo...', 'info', false, 'msg-procesando-prestamo');

                    const formData = new FormData(formRegistrarPrestamo);
                    const submitButton = formRegistrarPrestamo.querySelector('input[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.value = 'Enviando...';

                    let idMensajePrestamoExitoso = 'msg-prestamo-exito-' + Date.now(); // ID único para el mensaje de préstamo

                    fetch('Almacenar_prestamo.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(dataAlmacenar => {
                            eliminarFeedback('msg-procesando-prestamo'); // Eliminar "Procesando registro..."
                            if (dataAlmacenar.success) {
                                // Mostrar mensaje de préstamo exitoso y programar su eliminación
                                mostrarFeedback(dataAlmacenar.message, 'success', true, idMensajePrestamoExitoso, 8);

                                // Mostrar "Enviando correo..."
                                mostrarFeedback('Enviando correo de confirmación...', 'info', true, 'msg-enviando-correo');

                                const datosParaCorreo = new FormData();
                                for (const key in dataAlmacenar.datos_correo) {
                                    datosParaCorreo.append(key, dataAlmacenar.datos_correo[key]);
                                }
                                return fetch('enviar_correo.php', {
                                    method: 'POST',
                                    body: datosParaCorreo
                                });
                            } else {
                                throw new Error(dataAlmacenar.message || 'Error desconocido al registrar el préstamo.');
                            }
                        })
                        .then(responseCorreo => {
                            if (!responseCorreo) return;
                            return responseCorreo.json();
                        })
                        .then(dataCorreo => {
                            eliminarFeedback('msg-enviando-correo'); // Eliminar "Enviando correo..." tan pronto como tengamos respuesta

                            if (dataCorreo) {
                                if (dataCorreo.success) {
                                    mostrarFeedback(dataCorreo.message, 'success', true, "xd", 8); // Mensaje de correo enviado
                                    formRegistrarPrestamo.reset();
                                    nombreSelect.innerHTML = '<option value="">-- Selecciona un tipo de persona primero --</option>';
                                    nombreSelect.disabled = true;
                                    if (tipoPersonaSelect) tipoPersonaSelect.value = "";

                                    mostrarFeedback('Actualizando lista de libros disponibles...', 'info', true, 'msg-actualizando-libros');
                                    cargarLibrosYEjemplares();
                                    // El mensaje "Actualizando..." se quedará hasta la próxima acción o se podría quitar también
                                    // setTimeout(() => eliminarFeedback('msg-actualizando-libros'), 3000); // Opcional: quitar después de un tiempo
                                } else {
                                    mostrarFeedback(dataCorreo.message || 'Error desconocido al enviar el correo.', 'error', true);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error en el proceso de registro y/o envío de correo:', error);
                            // Asegurarse de eliminar mensajes de progreso si estaban visibles
                            eliminarFeedback('msg-procesando-prestamo');
                            eliminarFeedback('msg-enviando-correo');
                            eliminarFeedback('msg-actualizando-libros');

                            mostrarFeedback(`Error en el proceso: ${error.message}`, 'error');
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            submitButton.value = 'Registrar Préstamo';
                            // Quitar mensaje de "actualizando libros" si aún existe después de un delay,
                            // para asegurar que no se quede si la carga fue muy rápida o falló.
                            setTimeout(() => eliminarFeedback('msg-actualizando-libros'), 2000);
                        });
                });
            }
        });

    </script>
</body>

</html>