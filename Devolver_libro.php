<?php
include 'conexion.php';
$con = conecta();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Devolver Libro - Préstamos Activos</title>
    <style>
        /* ... (tus estilos - sin cambios) ... */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: white;
            padding-top: 70px;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 70px auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #0066cc;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn-devolver {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9em;
        }

        .btn-devolver:hover {
            background-color: #218838;
        }

        .btn-devolver:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .btn-pdf-multa {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.85em;
            margin-left: 10px;
        }

        .btn-pdf-multa:hover {
            background-color: #c82333;
        }

        .feedback-message {
            padding: 10px;
            margin-top: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 1em;
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

        .top-bar {
            background: rgba(0, 0, 0, 0.7);
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

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('imagenes/InicioE.png') no-repeat center center fixed;
            background-size: cover;
            /* height: 100vh; */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .button-group a,
        .menu-button {
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

        .button-group a:hover,
        .menu-button:hover {
            filter: brightness(0.9);
        }

        .dropdown-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
    </style>
</head>

<body>
    <div class="top-bar">
        <button class="menu-button" onclick="toggleMenu()">Menú</button>
        <div class="button-group">
            <a href="inicio_empleado.php?" class="return-button">Regresar</a>
            <a href="login.php" class="logout-button">Salir</a>
        </div>
    </div>
    <div class="dropdown-menu" id="mainMenu">
        <div class="menu-item" onclick="toggleSubmenu('alumnosSubmenu')">Prestamo</div>
        <div class="submenu" id="alumnosSubmenu">
            <a href="Registrar_prestamo.php">Registrar Libro</a>
            <a href="Devolver_libro.php">Devolver Libro</a>
            <a href="Prestamo_consulta.php">Consultar prestamos</a>
        </div>
    </div>

    <div class="container">
        <h2>Préstamos Activos - Devolución de Libros</h2>
        <div id="feedbackContainerGlobal"></div>
        <table>
            <thead>
                <tr>
                    <th>ID Préstamo</th>
                    <th>Solicitante</th>
                    <th>Tipo</th>
                    <th>Libro (ISBN)</th>
                    <th>Ejemplar</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Límite</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaPrestamosActivos">
                <?php
                try {
                    // CAMBIO: Reemplazado DATE_FORMAT con TO_CHAR para PostgreSQL
                    // Y mantenemos el CAST para num_ejemplar como posible corrección anterior
                    $query = "SELECT 
                                p.id_prestamo, 
                                p.id_solicitante, 
                                p.tipo_solicitante, 
                                p.id_libro, 
                                p.num_ejemplar,
                                TO_CHAR(p.fecha_prestamo, 'DD/MM/YYYY') AS fecha_prestamo_f, 
                                TO_CHAR(p.fecha_limite_entrega, 'DD/MM/YYYY') AS fecha_limite_f,
                                l.titulo AS titulo_libro,
                                CASE p.tipo_solicitante
                                    WHEN 'alumna' THEN al.nombre
                                    WHEN 'maestra' THEN pr.nombre
                                    ELSE 'Desconocido'
                                END AS nombre_solicitante
                              FROM prestamo p
                              JOIN libro l ON p.id_libro = l.isbn 
                                    -- Sigue siendo importante verificar los tipos para el CAST de num_ejemplar
                                    AND p.num_ejemplar = CAST(l.num_ejemplar AS INTEGER) 
                              LEFT JOIN alumno al ON p.id_solicitante = al.codigo AND p.tipo_solicitante = 'alumna'
                              LEFT JOIN profesor pr ON p.id_solicitante = pr.codigo AND p.tipo_solicitante = 'maestra'
                              WHERE p.fecha_entrega_solicitante IS NULL
                              ORDER BY p.fecha_limite_entrega ASC";

                    $stmt = $con->prepare($query);
                    $stmt->execute();
                    $prestamos_activos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($prestamos_activos) > 0) {
                        foreach ($prestamos_activos as $prestamo) {
                            echo "<tr id='fila-prestamo-{$prestamo['id_prestamo']}'>";
                            echo "<td>{$prestamo['id_prestamo']}</td>";
                            echo "<td>" . htmlspecialchars($prestamo['nombre_solicitante']) . " ({$prestamo['id_solicitante']})</td>";
                            echo "<td>" . ucfirst($prestamo['tipo_solicitante']) . "</td>";
                            echo "<td>" . htmlspecialchars($prestamo['titulo_libro']) . " ({$prestamo['id_libro']})</td>";
                            echo "<td>{$prestamo['num_ejemplar']}</td>";
                            echo "<td>{$prestamo['fecha_prestamo_f']}</td>"; // Ya está formateada por TO_CHAR
                            echo "<td>{$prestamo['fecha_limite_f']}</td>";   // Ya está formateada por TO_CHAR
                            echo "<td>
                                    <button class='btn-devolver' data-idprestamo='{$prestamo['id_prestamo']}'>Devolver</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align:center;'>No hay préstamos activos en este momento.</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='8' style='text-align:center; color:red;'>Error al cargar los préstamos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // ... (El JavaScript es el mismo que en la respuesta anterior) ...
        document.addEventListener('DOMContentLoaded', function () {
            const tablaPrestamosActivos = document.getElementById('tablaPrestamosActivos');
            const feedbackContainerGlobal = document.getElementById('feedbackContainerGlobal');

            function mostrarFeedbackGlobal(mensaje, tipo = 'info', autoEliminarSegundos = 8) {
                feedbackContainerGlobal.innerHTML = '';
                const messageDiv = document.createElement('div');
                messageDiv.className = `feedback-message ${tipo}`;
                messageDiv.textContent = mensaje;
                feedbackContainerGlobal.appendChild(messageDiv);

                if (autoEliminarSegundos > 0) {
                    setTimeout(() => {
                        if (messageDiv.parentNode) messageDiv.remove();
                    }, autoEliminarSegundos * 1000);
                }
                return messageDiv;
            }

            if (tablaPrestamosActivos) {
                tablaPrestamosActivos.addEventListener('click', function (event) {
                    if (event.target.classList.contains('btn-devolver')) {
                        const boton = event.target;
                        const idPrestamo = boton.dataset.idprestamo;

                        if (!confirm(`¿Está seguro de que desea marcar como devuelto el préstamo ID ${idPrestamo}?`)) {
                            return;
                        }
                        window.scrollTo({top: 0, behavior: 'smooth' });

                        boton.disabled = true;
                        boton.textContent = 'Procesando...';
                        feedbackContainerGlobal.innerHTML = '';


                        const formData = new FormData();
                        formData.append('id_prestamo', idPrestamo);

                        fetch('Procesar_Devolucion.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => {
                                if (!response.ok) {
                                    return response.text().then(text => { throw new Error(`Error del servidor (${response.status}): ${text}`) });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    const msgDiv = mostrarFeedbackGlobal(data.message, 'success', 30);

                                    const fila = document.getElementById(`fila-prestamo-${idPrestamo}`);
                                    if (fila) {
                                        fila.remove();
                                    }
                                    if (tablaPrestamosActivos.getElementsByTagName('tr').length === 0) {
                                        tablaPrestamosActivos.innerHTML = "<tr><td colspan='8' style='text-align:center;'>No hay más préstamos activos.</td></tr>";
                                    }

                                    if (data.multa && data.multa > 0) {
                                        
                                        const pdfButton = document.createElement('a');
                                        pdfButton.href = `multas_pdf.php?id=${data.id_prestamo}`;
                                        pdfButton.className = 'btn-pdf-multa';
                                        pdfButton.textContent = 'Generar PDF de Multa';
                                        pdfButton.target = '_blank';

                                        msgDiv.appendChild(document.createElement('br'));
                                        msgDiv.appendChild(pdfButton);
                                    }

                                } else {
                                    mostrarFeedbackGlobal(data.message || 'Error desconocido al procesar la devolución.', 'error');
                                    boton.disabled = false;
                                    boton.textContent = 'Devolver';
                                }
                            })
                            .catch(error => {
                                console.error('Error en la devolución AJAX:', error);
                                mostrarFeedbackGlobal(`Error de conexión o respuesta inesperada: ${error.message}`, 'error');
                                boton.disabled = false;
                                boton.textContent = 'Devolver';
                            });
                    }
                });
            }
        });
    </script>
</body>
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

</html>