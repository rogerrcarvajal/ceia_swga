<?php
// pages/consultar_planilla_salida.php
// Incluir cabecera y validación de sesión como en planilla_salida.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Consultar Autorizaciones de Salida</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/app.css">
</head>
<body>
    <?php include __DIR__ . '/../src/templates/header.php'; ?>

    <main class="container">
        <h2>Consultar Autorizaciones de Salida</h2>

        <div class="filtros">
            <div class="campo">
                <label for="filtro-semana">Seleccionar Semana:</label>
                <input type="week" id="filtro-semana">
            </div>
            </div>

        <table id="tabla-resultados">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estudiante</th>
                    <th>Retirado por</th>
                    <th>Parentesco</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </main>

    <script>
        const filtroSemana = document.getElementById('filtro-semana');
        const tablaBody = document.querySelector('#tabla-resultados tbody');

        async function cargarResultados() {
            const semana = filtroSemana.value;
            if (!semana) {
                tablaBody.innerHTML = '<tr><td colspan="6">Seleccione una semana para ver los resultados.</td></tr>';
                return;
            }

            const response = await fetch(`/ceia_swga/api/consultar_salidas.php?semana=${semana}`);
            const data = await response.json();

            tablaBody.innerHTML = ''; // Limpiar tabla
            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    const fila = `
                        <tr>
                            <td>${reg.fecha_salida}</td>
                            <td>${reg.hora_salida}</td>
                            <td>${reg.nombre_estudiante}</td>
                            <td>${reg.retirado_por_nombre}</td>
                            <td>${reg.retirado_por_parentesco}</td>
                            <td>${reg.motivo}</td>
                        </tr>`;
                    tablaBody.innerHTML += fila;
                });
            } else {
                tablaBody.innerHTML = '<tr><td colspan="6">No se encontraron registros para la semana seleccionada.</td></tr>';
            }
        }

        filtroSemana.addEventListener('change', cargarResultados);
    </script>
</body>
</html>