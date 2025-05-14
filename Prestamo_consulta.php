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
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f5f5f5;
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
</body>
</html>
