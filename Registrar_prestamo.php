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
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
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

        select, input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
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
    </style>
    
    
</head>
<body>
    <div class="form-container">
        <h2>Registrar Préstamo de Libro</h2>
        <form action="registrar_prestamo.php" method="POST">
            <label for="tipo_persona">Tipo de persona:</label>
            <select name="tipo_persona" id="tipo_persona" required>
                <option value="">-- Selecciona --</option>
                <option value="alumna">alumno</option>
                <option value="maestra">Maestro</option>
            </select>

            <label for="nombre">Nombre:</label>
            <select name="nombre" id="nombre" required>
                <option value="">-- Selecciona un nombre --</option>
            </select>

            <label for="libro">Libro que desea solicitar:</label>
            <select name="libro" id="libro" required>
                <option value="">-- Selecciona un libro --</option>
            </select>

            <input type="submit" value="Registrar Préstamo">
        </form>
    </div>
    <script>
    document.getElementById('tipo_persona').addEventListener('change', function () {
        const tipoPersona = this.value;

        const nombreSelect = document.getElementById('nombre');
        nombreSelect.innerHTML = '<option value="">Cargando...</option>';

        if (tipoPersona === '') {
            nombreSelect.innerHTML = '<option value="">-- Selecciona un nombre --</option>';
            return;
        }

        fetch('obtener_nombres.php?tipo=' + tipoPersona)
            .then(response => response.json())
            .then(data => {
                nombreSelect.innerHTML = '<option value="">-- Selecciona un nombre --</option>';
                data.forEach(function (persona) {
                    const option = document.createElement('option');
                    option.value = persona.codigo;
                    option.textContent = persona.nombre;
                    nombreSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener nombres:', error);
                nombreSelect.innerHTML = '<option value="">Error al cargar nombres</option>';
            });
    });

    window.addEventListener('DOMContentLoaded', function () {
    const libroSelect = document.getElementById('libro');
    fetch('obtener_libros.php')
        .then(response => response.json())
        .then(data => {
            libroSelect.innerHTML = '<option value="">-- Selecciona un libro --</option>';
            data.forEach(function (libro) {
                const option = document.createElement('option');
                option.value = libro.isbn; // esto se manda en el formulario
                option.textContent = libro.titulo;
                libroSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar libros:', error);
            libroSelect.innerHTML = '<option value="">Error al cargar libros</option>';
        });
});
</script>
</body>
</html>