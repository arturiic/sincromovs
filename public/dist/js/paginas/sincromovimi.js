let table = null; // Solo inicializamos una vez

$(document).ready(function () {
    // Inicializar DataTable
    $('#tbl_sincromovi').DataTable({
        "language": {
            "url": '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
    });
});

function verSincronizacionMovimientos() {
    const fechaInicio = $('#datefechaini').val();
    const fechaFin = $('#datefechafin').val();

    if (fechaInicio > fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR EN LA BUSQUEDA',
            text: 'La fecha de inicio no puede ser mayor a la fecha de fin',
        });
        return;
    }

    const url = URL_PY + 'movimientos/sincro?desde=' + fechaInicio + '&hasta=' + fechaFin;
    $.ajaxblock();
    $('#tbl_sincromovi tbody').empty();

    table = $('#tbl_sincromovi').DataTable({
        "destroy": true,
        "responsive": true,
        "autoWidth": true,
        "ajax": {
            'url': url,
            'method': 'GET',
            'dataSrc': function (json) {
                //console.log("Datos recibidos del servidor:", json);
                $.ajaxunblock();
                return json.data;
            },
        },
        "columns": [
            { "data": 'titulo' },
            { "data": 'enviado_a' },
            { "data": 'fecha_y_hora' },
            { "data": 'monto' },
            { "data": 'moneda' },
            { "data": 'noperacion' }
        ],
        order: [[2, 'desc']],
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
}
function insertarMovimientos() {
    const table = $('#tbl_sincromovi').DataTable();
    const datos = table.rows().data().toArray();

    const movimientos = datos.map(item => ({
        "nombre_depositante": item.titulo || '',
        "observacion": item.enviado_a || '',
        "fecha_hora": item.fecha_y_hora || '',
        "monto": item.monto || '',
        "moneda": item.moneda || '',
        "noperacion": item.noperacion || '-'
    }));

    if (movimientos.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'ERROR AL REGISTRAR',
            text: 'No hay datos para registrar.',
        });
        return;
    }

    $.ajax({
        url: URL_PY + "movimientos/insertar_sincromov",
        type: 'POST',
        data: JSON.stringify({ movimientos: movimientos }),
        contentType: 'application/json',
        success: function (response) {
            let htmlContent = `<div style="text-align: left;">`;

            // Mensaje general
            const todosExitosos = response.details.omitidos.length === 0 && response.details.errores.length === 0;
            const algunosExitosos = response.details.registrados.length > 0;

            htmlContent += `<p><strong>${todosExitosos ? 'Todos los movimientos fueron registrados' :
                algunosExitosos ? 'Algunos movimientos fueron registrados' :
                    'Ningún movimiento fue registrado'}</strong></p>`;

            // Mostrar resumen estadístico
            const totalRegistrados = response.details.registrados.length;
            const totalOmitidos = response.details.omitidos.length;
            const totalErrores = response.details.errores.length;

            htmlContent += `<p>Total movimientos: ${movimientos.length}</p>`;
            htmlContent += `<p>Registrados correctamente: ${totalRegistrados}</p>`;
            if (totalOmitidos > 0) {
                htmlContent += `<p>Omitidos (duplicados): ${totalOmitidos}</p>`;
            }
            if (totalErrores > 0) {
                htmlContent += `<p>Errores: ${totalErrores}</p>`;
            }

            // Mostrar detalles si hay omitidos o errores
            if (totalOmitidos > 0 || totalErrores > 0) {
                htmlContent += `<hr><button id="btnDetalles" class="btn btn-link p-0" style="text-decoration: none;">
                <h5 style="cursor: pointer; color: #007bff;">▼ Detalles</h5>
            </button>`;

                htmlContent += `<div id="detallesContenido" style="display: none;">`;

                if (totalOmitidos > 0) {
                    htmlContent += `<p><strong>Movimientos omitidos (duplicados):</strong></p><ul>`;
                    response.details.omitidos.forEach(item => {
                        htmlContent += `<li>No. Operación: ${item.noperacion} - ${item.mensaje}</li>`;
                    });
                    htmlContent += `</ul>`;
                }

                if (totalErrores > 0) {
                    htmlContent += `<p><strong>Movimientos con errores:</strong></p><ul>`;
                    response.details.errores.forEach(item => {
                        htmlContent += `<li>No. Operación: ${item.noperacion} - ${item.mensaje}</li>`;
                    });
                    htmlContent += `</ul>`;
                }

                htmlContent += `</div>`;
            }

            htmlContent += `</div>`;

            // Determinar el icono según el resultado
            let icon, title;
            if (todosExitosos) {
                icon = 'success';
                title = '¡REGISTRO EXITOSO!';
            } else if (algunosExitosos) {
                icon = 'warning';
                title = 'REGISTRO PARCIAL';
            } else {
                icon = 'error';
                title = 'ERROR AL REGISTRAR';
            }

            Swal.fire({
                icon: icon,
                title: title,
                html: htmlContent,
                confirmButtonText: 'Aceptar',
                width: '800px',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false,
                didOpen: () => {
                    // Agregar evento click al botón de detalles
                    const btnDetalles = document.getElementById('btnDetalles');
                    if (btnDetalles) {
                        btnDetalles.addEventListener('click', () => {
                            const detalles = document.getElementById('detallesContenido');
                            const h5 = btnDetalles.querySelector('h5');
                            if (detalles.style.display === 'none') {
                                detalles.style.display = 'block';
                                h5.innerHTML = '▲ Detalles';
                            } else {
                                detalles.style.display = 'none';
                                h5.innerHTML = '▼ Detalles';
                            }
                        });
                    }
                }
            }).then(() => {
                // Limpiar tabla solo si hubo algún registro exitoso
                if (todosExitosos) {
                    table.clear().draw();
                }
            });
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'ERROR',
                text: 'Ocurrió un error al intentar registrar los movimientos.'
            });
        }
    });
}

