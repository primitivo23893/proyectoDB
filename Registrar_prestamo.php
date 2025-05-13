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
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #444; }
        select, input[type="text"] { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
        input[type="submit"] { width: 100%; background-color: #0066cc; color: white; padding: 12px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; }
        input[type="submit"]:hover { background-color: #004d99; }
        input[type="submit"]:disabled { background-color: #cccccc; cursor: not-allowed; }
        .feedback-message {
            padding: 10px;
            margin-top: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 0.9em;
        }
        .feedback-message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .feedback-message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        .feedback-message.info { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db;}
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

    function mostrarFeedback(mensaje, tipo = 'info', anadir = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `feedback-message ${tipo}`;
        messageDiv.textContent = mensaje;
        if (anadir) {
            const br = feedbackContainer.querySelector('br:last-child'); // Buscar si ya hay un BR al final
            if (feedbackContainer.childNodes.length > 0 && (!br || br !== feedbackContainer.lastChild) ) {
                 feedbackContainer.appendChild(document.createElement('br'));
            }
            feedbackContainer.appendChild(messageDiv);
        } else {
            feedbackContainer.innerHTML = '';
            feedbackContainer.appendChild(messageDiv);
        }
    }

    // Cargar nombres según tipo de persona
    if (tipoPersonaSelect && nombreSelect) {
        tipoPersonaSelect.addEventListener('change', function () {
            const tipoPersona = this.value;
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
        });
        if (tipoPersonaSelect.value === '') nombreSelect.disabled = true;
    }

    // Cargar libros y manejar selección de ejemplares
    if (libroSelect && ejemplarSelect) {
        ejemplarSelect.disabled = true; // Empezar deshabilitado
        libroSelect.innerHTML = '<option value="">-- Cargando libros... --</option>'; 

        fetch('obtener_libros.php')
            .then(response => {
                console.log('Respuesta fetch obtener_libros:', response);
                if (!response.ok) {
                    return response.text().then(text => { // Obtener el texto del error si no es JSON
                        throw new Error(`Error del servidor (${response.status}) al cargar libros: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos JSON de obtener_libros:', data);

                if (data && data.error) {
                    throw new Error('Error desde obtener_libros.php: ' + data.error);
                }
                if (!Array.isArray(data)) {
                    throw new Error('Respuesta inesperada del servidor al cargar libros. Se esperaba un array.');
                }

                librosConEjemplares = data;
                libroSelect.innerHTML = '<option value="">-- Selecciona un libro --</option>';

                if (librosConEjemplares.length === 0) {
                    libroSelect.innerHTML = '<option value="">No hay libros disponibles</option>';
                } else {
                    librosConEjemplares.forEach(function (libro) {
                        if (typeof libro.isbn === 'undefined' || typeof libro.titulo === 'undefined' || typeof libro.autor === 'undefined') {
                            console.warn('Libro con datos incompletos:', libro);
                            return; 
                        }
                        const option = document.createElement('option');
                        option.value = libro.isbn;
                        option.textContent = `${libro.titulo} - ${libro.autor}`;
                        libroSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error final en fetch/procesamiento de libros:', error);
                libroSelect.innerHTML = `<option value="">Error al cargar libros: ${error.message}</option>`;
                // Puedes añadir un feedback visual también
                mostrarFeedback(`No se pudieron cargar los libros: ${error.message}`, 'error');
            });

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
                ejemplarSelect.innerHTML = '<option value="">No hay ejemplares disponibles para este libro</option>';
            }
        });
    }

    // Manejo del envío del formulario
    if (formRegistrarPrestamo) {
        formRegistrarPrestamo.addEventListener('submit', function(event) {
            event.preventDefault();
            feedbackContainer.innerHTML = '';
            mostrarFeedback('Procesando registro de préstamo...', 'info');

            const formData = new FormData(formRegistrarPrestamo);
            const submitButton = formRegistrarPrestamo.querySelector('input[type="submit"]');
            submitButton.disabled = true;
            submitButton.value = 'Enviando...';

            fetch('Almacenar_prestamo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(dataAlmacenar => {
                if (dataAlmacenar.success) {
                    mostrarFeedback(dataAlmacenar.message, 'success');
                    mostrarFeedback('Enviando correo de confirmación...', 'info', true);
                    
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
                if (dataCorreo) {
                    if (dataCorreo.success) {
                        mostrarFeedback(dataCorreo.message, 'success', true);
                        formRegistrarPrestamo.reset();
                        nombreSelect.innerHTML = '<option value="">-- Selecciona un tipo de persona primero --</option>';
                        nombreSelect.disabled = true;
                        if (tipoPersonaSelect) tipoPersonaSelect.value = "";
                        if (libroSelect) libroSelect.value = "";
                        ejemplarSelect.innerHTML = '<option value="">-- Selecciona un libro primero --</option>';
                        ejemplarSelect.disabled = true;
                    } else {
                        mostrarFeedback(dataCorreo.message || 'Error desconocido al enviar el correo.', 'error', true);
                    }
                }
            })
            .catch(error => {
                console.error('Error en el proceso de registro y/o envío de correo:', error);
                if (feedbackContainer.innerHTML.includes('Procesando') || feedbackContainer.innerHTML === '') {
                     mostrarFeedback(`Error en el proceso: ${error.message}`, 'error');
                } else {
                     mostrarFeedback(`Detalle adicional del error: ${error.message}`, 'error', true);
                }
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.value = 'Registrar Préstamo';
            });
        });
    }
});
</script>
</body>
</html>