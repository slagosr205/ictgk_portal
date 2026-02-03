import jQuery from 'jquery';
import Swal from 'sweetalert2';

window.$ = jQuery;
window.jQuery = jQuery;

const tableBody = document.querySelector('#tbempresas tbody');

function buildEstadoSwitch(id, estado) {
    const checked = estado === 'a' ? 'checked' : '';
    const label = estado === 'a' ? 'Activo' : 'Inactivo';
    return `
        <div class="form-check form-switch">
            <input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="flexSwitchCheckChecked-${id}" ${checked}>
            <label class="form-check-label" for="flexSwitchCheckChecked-${id}">${label}</label>
        </div>
    `;
}

function buildRow(data) {
    return `
        <tr data-id="${data.id}">
            <td data-campo="id"><span class="badge-id">${data.id}</span></td>
            <td data-campo="nombre">${data.nombre || ''}</td>
            <td data-campo="telefonos">${data.telefonos || ''}</td>
            <td data-campo="contacto">${data.contacto || ''}</td>
            <td data-campo="correo">${data.correo || ''}</td>
            <td data-campo="estado">
                ${buildEstadoSwitch(data.id, data.estado || 'a')}
            </td>
            <td>
                <button type="button" class="btn-modern btn-warning btn-consulta" data-id="${data.id}" data-bs-toggle="modal" data-bs-target="#modificarempresa">
                    <i class="ri-pencil-line"></i>
                    <span>Modificar</span>
                </button>
            </td>
        </tr>
    `;
}

function formatearNumero(numero) {
    if (!numero) return '';
    return numero.replace(/(\d{4})(\d{4})/, '$1-$2');
}

async function updateStateCompany(data) {
    try {
        const response = await fetch('/update-company-state', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return await response.json();
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
        return null;
    }
}

$(document).on('submit', '#insertCompany', function (e) {
    e.preventDefault();
    const formInsertCompany = new FormData($(this)[0]);

    $.ajax({
        url: '/insert-company',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formInsertCompany,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.code === 202 && tableBody) {
                tableBody.insertAdjacentHTML('afterbegin', buildRow(res.data));
                $('#nuevaempresa').modal('hide');
                $('#insertCompany')[0].reset();
                Swal.fire({
                    title: 'Registro creado',
                    text: res.mensaje,
                    icon: 'success'
                });
            }
        },
        error: function (xhr) {
            Swal.fire({
                title: 'Error',
                text: xhr.responseJSON?.message || 'No se pudo crear la empresa',
                icon: 'error'
            });
        }
    });
});

$(document).on('change', '.chkactivoEmpresa', function () {
    const checkbox = this;
    const idRow = checkbox.closest('tr');
    const label = checkbox.closest('.form-check').querySelector('.form-check-label');
    const state = checkbox.checked ? 'a' : 'n';
    const idCompany = idRow?.getAttribute('data-id');
    if (!idCompany) {
        return;
    }

    if (label) {
        label.textContent = checkbox.checked ? 'Activo' : 'Inactivo';
    }

    updateStateCompany({ state, idCompany });
});

$(document).on('click', '.btn-consulta', function () {
    const id = $(this).data('id');
    const nombre = $('#modnombre');
    const telefono = $('#modtelefonos');
    const correo = $('#modcorreo');
    const contacto = $('#modcontacto');
    const direccion = $('#moddireccion');
    const id_empresa = $('#id_empresa');
    const form = $('#updateCompany');
    const inputs = form.find('input, select, textarea');

    $.ajax({
        url: '/consulting-company/' + id,
        type: 'GET',
        beforeSend: function () {
            $('#estado').html('');
            inputs.prop('disabled', true);
            nombre.val('Cargando...');
            telefono.val('');
            correo.val('');
            contacto.val('');
            direccion.val('');
        },
        success: function (response) {
            nombre.val(response.data[0].nombre);
            telefono.val(formatearNumero(response.data[0].telefonos));
            correo.val(response.data[0].correo);
            contacto.val(response.data[0].contacto);
            direccion.val(response.data[0].direccion);
            id_empresa.val(id);
            const checked = response.data[0].estado === 'a' ? 'checked' : '';
            $('#estado').html(
                '<div class="form-check form-switch">' +
                '<input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="estado" name="estado" ' + checked + '>' +
                '<label class="form-check-label" for="estado">' + (checked ? 'Activo' : 'Inactivo') + '</label>' +
                '</div>'
            );
            inputs.prop('disabled', false);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            inputs.prop('disabled', false);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo cargar la información de la empresa.',
                icon: 'error'
            });
        }
    });
});

$(document).on('submit', '#updateCompany', function (e) {
    e.preventDefault();
    const formUpdateCompany = new FormData($(this)[0]);

    $.ajax({
        url: '/update-company',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formUpdateCompany,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.code === 202) {
                Swal.fire({
                    title: 'Registro actualizado',
                    text: res.mensaje,
                    icon: 'success'
                });

                const idFila = res.data.id;
                const fila = $('#tbempresas').find('tr[data-id="' + idFila + '"]');
                if (fila.length > 0) {
                    fila.find('td').each(function () {
                        const campo = $(this).data('campo');
                        if (!campo || res.data[campo] === undefined) {
                            return;
                        }
                        if (campo === 'id') {
                            $(this).html('<span class="badge-id">' + res.data[campo] + '</span>');
                            return;
                        }
                        if (campo === 'estado') {
                            $(this).html(buildEstadoSwitch(res.data.id, res.data[campo]));
                            return;
                        }
                        $(this).text(res.data[campo]);
                    });
                }
                $('#modificarempresa').modal('hide');
            } else {
                Swal.fire({
                    title: 'Error en la actualización',
                    text: res.mensaje,
                    icon: 'error'
                });
            }
        },
        error: function (xhr) {
            Swal.fire({
                title: 'Error',
                text: xhr.responseJSON?.message || 'No se pudo actualizar la empresa',
                icon: 'error'
            });
        }
    });
});

$(document).on('submit', '#insertCompanyBulk', function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const formBulk = new FormData(form);
    const rows = form.querySelectorAll('#bulkCompaniesTable tbody tr');
    const csvText = form.querySelector('#csv_text')?.value?.trim();
    const csvFile = form.querySelector('#csv_file')?.files?.length || 0;

    let hasValidRow = false;
    rows.forEach(function (row) {
        const nombre = row.querySelector('input[name*="[nombre]"]')?.value?.trim();
        const direccion = row.querySelector('input[name*="[direccion]"]')?.value?.trim();
        const telefonos = row.querySelector('input[name*="[telefonos]"]')?.value?.trim();
        const contacto = row.querySelector('input[name*="[contacto]"]')?.value?.trim();
        const correo = row.querySelector('input[name*="[correo]"]')?.value?.trim();
        if (nombre || direccion || telefonos || contacto || correo) {
            if (!nombre || !direccion || !telefonos || !contacto || !correo) {
                row.classList.add('row-error');
            } else {
                row.classList.remove('row-error');
                hasValidRow = true;
            }
        } else {
            row.classList.remove('row-error');
        }
    });

    if (!hasValidRow && !csvText && !csvFile) {
        Swal.fire({
            title: 'Datos incompletos',
            text: 'Agrega al menos una fila válida o carga un CSV.',
            icon: 'warning'
        });
        return;
    }

    if (document.querySelectorAll('.row-error').length > 0) {
        Swal.fire({
            title: 'Revisa las filas',
            text: 'Completa todos los campos obligatorios en las filas marcadas.',
            icon: 'warning'
        });
        return;
    }

    $.ajax({
        url: '/insert-company-bulk',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formBulk,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.code === 202 && tableBody) {
                res.data.forEach(function (empresa) {
                    tableBody.insertAdjacentHTML('afterbegin', buildRow(empresa));
                });
                $('#empresaMasiva').modal('hide');
                $('#insertCompanyBulk')[0].reset();
                Swal.fire({
                    title: 'Carga masiva completada',
                    text: res.mensaje,
                    icon: 'success'
                });
            }
        },
        error: function (xhr) {
            Swal.fire({
                title: 'Error',
                text: xhr.responseJSON?.message || 'No se pudo procesar la carga masiva',
                icon: 'error'
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('empresasTableWrapper');
    const btnLeft = document.getElementById('empresasScrollLeft');
    const btnRight = document.getElementById('empresasScrollRight');

    if (!wrapper || !btnLeft || !btnRight) {
        return;
    }

    const scrollByAmount = 240;
    btnLeft.addEventListener('click', function () {
        wrapper.scrollBy({ left: -scrollByAmount, behavior: 'smooth' });
    });
    btnRight.addEventListener('click', function () {
        wrapper.scrollBy({ left: scrollByAmount, behavior: 'smooth' });
    });

    const filterForm = document.getElementById('empresasFilterForm');
    if (filterForm) {
        const searchInput = filterForm.querySelector('#search');
        const selects = filterForm.querySelectorAll('select');
        if (searchInput) {
            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    filterForm.submit();
                }
            });
        }
        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }

    const bulkTable = document.getElementById('bulkCompaniesTable');
    const addRowBtn = document.getElementById('addBulkRow');
    const clearRowsBtn = document.getElementById('clearBulkRows');

    if (bulkTable && addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            const tbody = bulkTable.querySelector('tbody');
            const index = tbody.querySelectorAll('tr').length;
            const row = document.createElement('tr');
            row.className = 'bulk-row';
            row.innerHTML = `
                <td><input type="text" name="rows[${index}][nombre]" class="form-control" required></td>
                <td><input type="text" name="rows[${index}][direccion]" class="form-control" required></td>
                <td><input type="text" name="rows[${index}][telefonos]" class="form-control" required></td>
                <td><input type="text" name="rows[${index}][contacto]" class="form-control" required></td>
                <td><input type="email" name="rows[${index}][correo]" class="form-control" required></td>
                <td>
                    <select name="rows[${index}][estado]" class="form-select">
                        <option value="a">Activo</option>
                        <option value="n">Inactivo</option>
                    </select>
                </td>
                <td><input type="text" name="rows[${index}][logo]" class="form-control" placeholder="https://..."></td>
                <td>
                    <button type="button" class="btn-remove-row" aria-label="Eliminar fila">
                        <i class="ri-close-line"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    if (bulkTable) {
        bulkTable.addEventListener('click', function (event) {
            const btn = event.target.closest('.btn-remove-row');
            if (!btn) return;
            const row = btn.closest('tr');
            if (row && bulkTable.querySelectorAll('tbody tr').length > 1) {
                row.remove();
            }
        });
    }

    if (clearRowsBtn && bulkTable) {
        clearRowsBtn.addEventListener('click', function () {
            const rows = bulkTable.querySelectorAll('tbody tr');
            rows.forEach(function (row, index) {
                if (index === 0) {
                    row.querySelectorAll('input').forEach(input => input.value = '');
                    row.querySelectorAll('select').forEach(select => select.value = 'a');
                } else {
                    row.remove();
                }
            });
        });
    }
});
