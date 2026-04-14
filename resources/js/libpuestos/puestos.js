import jQuery from 'jquery';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

$(document).on('click', '.btn-consulta', function () {
    let id = $(this).data('id');
    console.log('el valor es ' + id);
    
    let puestonombre = document.getElementById('puestonombre');
    let departamento_id_mod = document.getElementById('departamento_id_mod');
    let puesto_id = document.getElementById('puesto_id');
    
    puestonombre.value = '';
    departamento_id_mod.value = '';
    
    fetch('/consulta-puesto/' + id)
    .then(response => response.json())
    .then(data => {
        console.log(data[0][0].nombrepuesto);
        puestonombre.value = data[0][0].nombrepuesto;
        puesto_id.value = data[0][0].id;
        departamento_id_mod.value = data[0][0].departamento_id;
    })
    .catch(error => console.error('Error:', error));
});

var notificationSuccess = $('#updatedPositions');
var notificationupdatedPositionserror = $('#updatedPositionserror');

if (notificationSuccess.length > 0) {
    setTimeout(function() {
        notificationSuccess.fadeOut();
    }, 3000);
}

if (notificationupdatedPositionserror.length > 0) {
    setTimeout(function() {
        notificationupdatedPositionserror.fadeOut();
    }, 3000);
}

$(document).on('change','#id_empresa', function(){
    const id_empresa=$(this).val()

    fetch(`/consultaPuestosxEmpresas/${id_empresa}`)
    .then(response=>response.text())
    .then(
        data=>{
            $('#positions-selected').html(data);
        }
            
    )
    .catch(err=>console.log(err))
})

// Bulk rows for puestos
(function() {
    const bulkTable = document.getElementById('bulkPuestosTable');
    const addRowBtn = document.getElementById('addBulkRowPuesto');
    const clearRowsBtn = document.getElementById('clearBulkRowsPuesto');
    const empresaSelect = document.getElementById('empresa_bulk');
    const puestoMasivoModal = document.getElementById('puestoMasivo');
    
    const deptOptionsContainer = document.querySelector('.departamentos-select');
    const deptOptions = deptOptionsContainer ? deptOptionsContainer.innerHTML : '';
    
    const pageContainer = document.getElementById('dtpuestos');
    const isAdmin = pageContainer ? pageContainer.dataset.isAdmin === 'true' : false;
    const userEmpresaId = pageContainer ? pageContainer.dataset.userEmpresaId : null;

    function filterDepartamentos(empresaId) {
        const selects = bulkTable ? bulkTable.querySelectorAll('select[name$="[departamento_id]"]') : [];
        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Seleccionar</option>';
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = deptOptions;
            const options = tempDiv.querySelectorAll('option');
            options.forEach(opt => {
                const deptEmpresaId = opt.getAttribute('data-empresa') || opt.dataset.empresa;
                if (!empresaId || deptEmpresaId == empresaId) {
                    select.appendChild(opt.cloneNode(true));
                }
            });
            if (currentValue) select.value = currentValue;
        });
    }

    function initFilasConFiltro() {
        if (!bulkTable) return;
        const empresaId = empresaSelect ? empresaSelect.value : (isAdmin ? '' : userEmpresaId);
        const selects = bulkTable.querySelectorAll('select[name$="[departamento_id]"]');
        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Seleccionar</option>';
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = deptOptions;
            const options = tempDiv.querySelectorAll('option');
            options.forEach(opt => {
                const deptEmpresaId = opt.getAttribute('data-empresa') || opt.dataset.empresa;
                if (!empresaId || deptEmpresaId == empresaId) {
                    select.appendChild(opt.cloneNode(true));
                }
            });
            if (currentValue) select.value = currentValue;
        });
    }

    // Inicializar cuando se abre el modal
    if (puestoMasivoModal) {
        puestoMasivoModal.addEventListener('shown.bs.modal', function() {
            // Resetear el select de empresa
            if (empresaSelect) empresaSelect.value = '';
            // Inicializar las filas con filtro vacío (todos los deptos para admin)
            initFilasConFiltro();
        });
    }

    if (empresaSelect) {
        empresaSelect.addEventListener('change', function() {
            filterDepartamentos(this.value);
        });
    }

    if (bulkTable && addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            const tbody = bulkTable.querySelector('tbody');
            const index = tbody.querySelectorAll('tr').length;
            const empresaId = empresaSelect ? empresaSelect.value : (isAdmin ? '' : userEmpresaId);
            
            const row = document.createElement('tr');
            row.className = 'bulk-row';
            row.innerHTML = `
                <td><input type="text" name="rows[${index}][nombrepuesto]" class="form-control" required></td>
                <td>
                    <select name="rows[${index}][departamento_id]" class="form-select" required>
                        <option value="">Seleccionar</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn-remove-row" aria-label="Eliminar fila">
                        <i class="ri-close-line"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            
            const newSelect = row.querySelector('select[name$="[departamento_id]"]');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = deptOptions;
            const options = tempDiv.querySelectorAll('option');
            options.forEach(opt => {
                const deptEmpresaId = opt.getAttribute('data-empresa') || opt.dataset.empresa;
                if (!empresaId || deptEmpresaId == empresaId) {
                    newSelect.appendChild(opt.cloneNode(true));
                }
            });
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
            const tbody = bulkTable.querySelector('tbody');
            while (tbody.children.length > 1) {
                tbody.removeChild(tbody.lastChild);
            }
            const firstRow = tbody.querySelector('tr');
            if (firstRow) {
                firstRow.querySelectorAll('input').forEach(input => input.value = '');
                const select = firstRow.querySelector('select');
                if (select) select.value = '';
            }
        });
    }
})();