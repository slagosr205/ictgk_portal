import jQuery from 'jquery';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

window.$ = jQuery;
window.jQuery = jQuery;

const tableBody = document.querySelector('#tbdepartamentos tbody');

function buildRow(data) {
    return `
        <tr data-id="${data.id}">
            <td><span class="badge-id">${data.id}</span></td>
            <td class="dep-nombre">${data.nombredepartamento || ''}</td>
            <td class="dep-empresa">${data.empresa_nombre || ''}</td>
            <td>${data.created_at || ''}</td>
            <td>${data.updated_at || ''}</td>
            <td>
                <button class="btn-modern btn-warning btnInfoDepto" data-id="${data.id}">
                    <i class="ri-pencil-line"></i>
                    <span>Actualizar</span>
                </button>
            </td>
        </tr>
    `;
}

$(document).on('submit', '#insertDepartamento', function (e) {
    e.preventDefault();
    const formInsert = new FormData($(this)[0]);

    $.ajax({
        url: '/insert-departament',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formInsert,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.code === 202 && tableBody) {
                tableBody.insertAdjacentHTML('afterbegin', buildRow(res.data));
                const createModalEl = document.getElementById('nuevodepartamento');
                if (createModalEl) {
                    const createModal = Modal.getOrCreateInstance(createModalEl);
                    createModal.hide();
                }
                $('#insertDepartamento')[0].reset();
                Swal.fire({
                    title: 'Departamento creado',
                    text: res.mensaje,
                    icon: 'success'
                });
            }
        },
        error: function (xhr) {
            Swal.fire({
                title: 'Error',
                text: xhr.responseJSON?.message || 'No se pudo crear el departamento',
                icon: 'error'
            });
        }
    });
});

$(document).on('click', '.btnInfoDepto', function () {
    const departamentoId = $(this).data('id');
    const modal = new Modal('#actualizardepartamento');
    const nombreInput = $('#nombredepartamentoactual');
    const idInput = $('#updatedepartamento_id');

    $.ajax({
        url: '/consulting-departament/' + departamentoId,
        type: 'GET',
        success: function (res) {
            idInput.val(departamentoId);
            nombreInput.val(res.departamento[0].nombredepartamento);
            modal.show();
        }
    });
});

$(document).on('submit', '#updateDepartamento', function (e) {
    e.preventDefault();
    const nombre = $('#nombredepartamentoactual').val();
    const departamentoId = $('#updatedepartamento_id').val();

    $.ajax({
        url: '/update-departament',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { departamento_id: departamentoId, nombredepartamento: nombre },
        success: function (res) {
            if (res.status === 200) {
                Swal.fire({
                    title: 'Actualización exitosa',
                    text: res.success,
                    icon: 'success',
                    allowOutsideClick: false
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: res.errorUpdate || 'No se pudo actualizar',
                    icon: 'error'
                });
            }
        }
    });
});

$(document).on('submit', '#insertDepartamentoBulk', function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const formBulk = new FormData(form);
    const rows = form.querySelectorAll('#bulkDepartamentosTable tbody tr');
    const csvText = form.querySelector('#csv_text')?.value?.trim();
    const csvFile = form.querySelector('#csv_file')?.files?.length || 0;

    let hasValidRow = false;
    rows.forEach(function (row) {
        const nombre = row.querySelector('input[name*="[nombredepartamento]"]')?.value?.trim();
        const empresa = row.querySelector('select[name*="[empresa_id]"]')?.value?.trim();

        if (nombre || empresa) {
            if (!nombre) {
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
        url: '/insert-departament-bulk',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formBulk,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.code === 202 && tableBody) {
                res.data.forEach(function (item) {
                    tableBody.insertAdjacentHTML('afterbegin', buildRow(item));
                });
                const bulkModalEl = document.getElementById('departamentoMasivo');
                if (bulkModalEl) {
                    const bulkModal = Modal.getOrCreateInstance(bulkModalEl);
                    bulkModal.hide();
                }
                $('#insertDepartamentoBulk')[0].reset();
                Swal.fire({
                    title: 'Carga masiva completada',
                    text: res.mensaje,
                    icon: 'success'
                });
            }
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.message || 'No se pudo procesar la carga masiva';
            Swal.fire({
                title: 'Error',
                text: msg,
                icon: 'error'
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('departamentosTableWrapper');
    const btnLeft = document.getElementById('departamentosScrollLeft');
    const btnRight = document.getElementById('departamentosScrollRight');

    if (wrapper && btnLeft && btnRight) {
        const scrollByAmount = 240;
        btnLeft.addEventListener('click', function () {
            wrapper.scrollBy({ left: -scrollByAmount, behavior: 'smooth' });
        });
        btnRight.addEventListener('click', function () {
            wrapper.scrollBy({ left: scrollByAmount, behavior: 'smooth' });
        });
    }

    const filterForm = document.getElementById('departamentosFilterForm');
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

    const bulkTable = document.getElementById('bulkDepartamentosTable');
    const addRowBtn = document.getElementById('addBulkRowDepto');
    const clearRowsBtn = document.getElementById('clearBulkRowsDepto');
    const empresaDefaultSelect = document.getElementById('empresa_id_bulk');

    if (bulkTable && addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            const tbody = bulkTable.querySelector('tbody');
            const index = tbody.querySelectorAll('tr').length;
            const isAdmin = !!document.getElementById('empresa_id_bulk');
            const row = document.createElement('tr');
            row.className = 'bulk-row';
            row.innerHTML = `
                <td><input type="text" name="rows[${index}][nombredepartamento]" class="form-control" required></td>
                ${isAdmin ? `<td>${document.querySelector('select[name="rows[0][empresa_id]"]').outerHTML.replace('rows[0][empresa_id]', `rows[${index}][empresa_id]`)}</td>` : ''}
                <td>
                    <button type="button" class="btn-remove-row" aria-label="Eliminar fila">
                        <i class="ri-close-line"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);

            if (empresaDefaultSelect && empresaDefaultSelect.value) {
                const rowSelect = row.querySelector('select[name="rows[' + index + '][empresa_id]"]');
                if (rowSelect) {
                    rowSelect.value = empresaDefaultSelect.value;
                    rowSelect.disabled = true;
                }
            }
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
                    row.querySelectorAll('select').forEach(select => {
                        select.value = '';
                        select.disabled = false;
                    });
                } else {
                    row.remove();
                }
            });
        });
    }

    if (empresaDefaultSelect && bulkTable) {
        empresaDefaultSelect.addEventListener('change', function () {
            const value = empresaDefaultSelect.value;
            const selects = bulkTable.querySelectorAll('select[name*="[empresa_id]"]');
            selects.forEach(function (select) {
                if (value) {
                    select.value = value;
                    select.disabled = true;
                } else {
                    select.disabled = false;
                }
            });
        });
    }
});
