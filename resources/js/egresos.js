/**
 * Módulo de Egresos - JavaScript
 * Gestión de salida de empleados
 */

// Estado global
import * as bootstrap from './bootstrap';
import { Modal, Toast, Tooltip } from 'bootstrap';
import Swal from 'sweetalert2';

const AppState = {
    empleados: [],
    seleccionados: [],
    catalogos: {},
    filtros: {},
    paginacion: {
        currentPage: 1,
        perPage: 50
    },
    esAdmin: document.getElementById('filtroEmpresa') !== null
};

// Configuración
const CONFIG = {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
    routes: {
        listar: '/egresos/listar',
        procesar: '/egresos/procesar',
        catalogos: '/egresos/catalogos'
    }
};

// ===================================
// INICIALIZACIÓN
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Módulo de egresos inicializado');
    
    inicializarEventListeners();
    configurarFechaEgresoDefault();
    
    // Cargar datos iniciales si hay empresa seleccionada
    if (!AppState.esAdmin) {
        cargarCatalogos();
        cargarEmpleados();
    }
});

function inicializarEventListeners() {
    // Formulario de filtros
    const formFiltros = document.getElementById('formFiltros');
    if (formFiltros) {
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            AppState.paginacion.currentPage = 1;
            cargarEmpleados();
        });
    }

    // Cambio de empresa (admin)
    const filtroEmpresa = document.getElementById('filtroEmpresa');
    if (filtroEmpresa) {
        filtroEmpresa.addEventListener('change', function() {
            cargarCatalogos();
            if (this.value) {
                cargarEmpleados();
            } else {
                limpiarTabla();
            }
        });
    }

    // Limpiar filtros
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    }

    // Check todos
    const checkTodos = document.getElementById('checkTodos');
    if (checkTodos) {
        checkTodos.addEventListener('change', toggleTodos);
    }

    // Botón procesar egresos
    const btnProcesarEgresos = document.getElementById('btnProcesarEgresos');
    if (btnProcesarEgresos) {
        btnProcesarEgresos.addEventListener('click', abrirModalEgreso);
    }

    // Formulario de egreso
    const formEgreso = document.getElementById('formEgreso');
    if (formEgreso) {
        formEgreso.addEventListener('submit', confirmarEgreso);
    }

    // Cambio de departamento actualiza puestos
    const filtroDepartamento = document.getElementById('filtroDepartamento');
    if (filtroDepartamento) {
        filtroDepartamento.addEventListener('change', actualizarPuestosPorDepartamento);
    }
}

// ===================================
// CARGA DE DATOS
// ===================================

async function cargarCatalogos() {
    try {
        const empresaId = AppState.esAdmin 
            ? document.getElementById('filtroEmpresa').value 
            : null;

        const url = new URL(CONFIG.routes.catalogos, window.location.origin);
        if (empresaId) {
            url.searchParams.append('empresa_id', empresaId);
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CONFIG.csrfToken
            }
        });

        const data = await response.json();

        if (data.success) {
            AppState.catalogos = data.catalogos;
            llenarCatalogos();
        }
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
}

function llenarCatalogos() {
    // Departamentos
    const selectDepto = document.getElementById('filtroDepartamento');
    if (selectDepto) {
        selectDepto.innerHTML = '<option value="">Todos</option>';
        AppState.catalogos.departamentos.forEach(depto => {
            const option = document.createElement('option');
            option.value = depto.id;
            option.textContent = depto.nombre;
            selectDepto.appendChild(option);
        });
    }

    // Puestos
    const selectPuesto = document.getElementById('filtroPuesto');
    if (selectPuesto) {
        selectPuesto.innerHTML = '<option value="">Todos</option>';
        AppState.catalogos.puestos.forEach(puesto => {
            const option = document.createElement('option');
            option.value = puesto.id;
            option.textContent = puesto.nombre;
            option.dataset.departamentoId = puesto.departamento_id;
            selectPuesto.appendChild(option);
        });
    }

    // Áreas
    const selectArea = document.getElementById('filtroArea');
    if (selectArea) {
        selectArea.innerHTML = '<option value="">Todas</option>';
        AppState.catalogos.areas.forEach(area => {
            const option = document.createElement('option');
            option.value = area;
            option.textContent = area;
            selectArea.appendChild(option);
        });
    }
}

function actualizarPuestosPorDepartamento() {
    const deptoId = document.getElementById('filtroDepartamento').value;
    const selectPuesto = document.getElementById('filtroPuesto');

    if (!deptoId) {
        // Mostrar todos
        Array.from(selectPuesto.options).forEach(option => {
            if (option.value) option.style.display = '';
        });
        return;
    }

    // Filtrar por departamento
    Array.from(selectPuesto.options).forEach(option => {
        if (option.value) {
            option.style.display = option.dataset.departamentoId === deptoId ? '' : 'none';
        }
    });

    selectPuesto.value = '';
}

async function cargarEmpleados(pagina = 1) {
    try {
        mostrarLoading(true);
        limpiarSeleccion();

        const formData = new FormData(document.getElementById('formFiltros'));
        formData.append('page', pagina);
        formData.append('per_page', document.getElementById('filtroPerPage').value);

        const response = await fetch(CONFIG.routes.listar, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            AppState.empleados = data.data;
            AppState.paginacion = data.pagination;
            renderizarTabla();
            renderizarPaginacion();
            actualizarContadores();
        } else {
            mostrarAlerta('error', data.message);
            limpiarTabla();
        }

    } catch (error) {
        console.error('Error al cargar empleados:', error);
        mostrarAlerta('error', 'Error al cargar empleados');
        limpiarTabla();
    } finally {
        mostrarLoading(false);
    }
}

// ===================================
// RENDERIZADO
// ===================================

function renderizarTabla() {
    const tbody = document.getElementById('tbodyEmpleados');
    tbody.innerHTML = '';

    if (AppState.empleados.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted py-5">
                    <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                    <p>No se encontraron empleados con los filtros aplicados</p>
                </td>
            </tr>
        `;
        return;
    }

    AppState.empleados.forEach(empleado => {
        const tr = crearFilaEmpleado(empleado);
        tbody.appendChild(tr);
    });
}

function crearFilaEmpleado(empleado) {
    const tr = document.createElement('tr');
    tr.dataset.id = empleado.id;

    const colSpan = AppState.esAdmin ? 10 : 9;

    tr.innerHTML = `
        <td>
            <input 
                type="checkbox" 
                class="form-check-input check-empleado" 
                value="${empleado.id}"
                data-empleado='${JSON.stringify(empleado)}'
            >
        </td>
        <td><code>${escapeHtml(empleado.identidad)}</code></td>
        <td>${escapeHtml(empleado.nombre_completo)}</td>
        <td>${escapeHtml(empleado.puesto)}</td>
        <td>${escapeHtml(empleado.departamento)}</td>
        ${AppState.esAdmin ? `<td>${escapeHtml(empleado.empresa)}</td>` : ''}
        <td>${escapeHtml(empleado.area)}</td>
        <td>${empleado.fechaIngreso_formatted}</td>
        <td>
            <span class="badge badge-antiguedad bg-info">
                ${escapeHtml(empleado.antiguedad)}
            </span>
        </td>
        <td>
            <button 
                class="btn btn-sm btn-danger" 
                onclick="procesarEgresoIndividual(${empleado.id})"
                title="Procesar egreso"
            >
                <i class="ri-logout-box-line"></i>
            </button>
        </td>
    `;

    // Evento de selección en la fila
    tr.addEventListener('click', function(e) {
        if (e.target.type !== 'checkbox' && !e.target.closest('button')) {
            const checkbox = tr.querySelector('.check-empleado');
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        }
    });

    // Evento del checkbox
    const checkbox = tr.querySelector('.check-empleado');
    checkbox.addEventListener('change', function(e) {
        e.stopPropagation();
        toggleSeleccion(this);
    });

    return tr;
}

function renderizarPaginacion() {
    const nav = document.getElementById('paginacionNav');
    const info = document.getElementById('infoPaginacion');

    if (!AppState.paginacion || AppState.paginacion.total === 0) {
        nav.innerHTML = '';
        info.innerHTML = '';
        return;
    }

    const { current_page, last_page, from, to, total } = AppState.paginacion;

    // Información
    info.innerHTML = `Mostrando ${from} a ${to} de ${total} registros`;

    // Botones de paginación
    let html = '<ul class="pagination pagination-sm mb-0">';

    // Anterior
    html += `
        <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cargarEmpleados(${current_page - 1}); return false;">
                <i class="ri-arrow-left-s-line"></i>
            </a>
        </li>
    `;

    // Páginas
    const maxPages = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxPages / 2));
    let endPage = Math.min(last_page, startPage + maxPages - 1);

    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarEmpleados(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="cargarEmpleados(${i}); return false;">
                    ${i}
                </a>
            </li>
        `;
    }

    if (endPage < last_page) {
        if (endPage < last_page - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarEmpleados(${last_page}); return false;">${last_page}</a></li>`;
    }

    // Siguiente
    html += `
        <li class="page-item ${current_page === last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cargarEmpleados(${current_page + 1}); return false;">
                <i class="ri-arrow-right-s-line"></i>
            </a>
        </li>
    `;

    html += '</ul>';
    nav.innerHTML = html;
}

// ===================================
// SELECCIÓN DE EMPLEADOS
// ===================================

function toggleSeleccion(checkbox) {
    const tr = checkbox.closest('tr');
    const empleado = JSON.parse(checkbox.dataset.empleado);

    if (checkbox.checked) {
        tr.classList.add('selected');
        if (!AppState.seleccionados.find(e => e.id === empleado.id)) {
            AppState.seleccionados.push(empleado);
        }
    } else {
        tr.classList.remove('selected');
        AppState.seleccionados = AppState.seleccionados.filter(e => e.id !== empleado.id);
    }

    actualizarContadores();
}

function toggleTodos() {
    const checkTodos = document.getElementById('checkTodos');
    const checkboxes = document.querySelectorAll('.check-empleado');

    checkboxes.forEach(cb => {
        cb.checked = checkTodos.checked;
        toggleSeleccion(cb);
    });
}

function limpiarSeleccion() {
    AppState.seleccionados = [];
    document.querySelectorAll('.check-empleado').forEach(cb => cb.checked = false);
    document.querySelectorAll('tr.selected').forEach(tr => tr.classList.remove('selected'));
    document.getElementById('checkTodos').checked = false;
    actualizarContadores();
}

// ===================================
// PROCESO DE EGRESO
// ===================================

function abrirModalEgreso() {
    if (AppState.seleccionados.length === 0) {
        mostrarAlerta('warning', 'Selecciona al menos un empleado');
        return;
    }

    // Actualizar cantidad
    document.getElementById('cantidadSeleccionados').textContent = AppState.seleccionados.length;

    // Listar empleados
    const lista = document.getElementById('listaEmpleadosSeleccionados');
    lista.innerHTML = '<ul class="list-group list-group-sm">';
    
    AppState.seleccionados.forEach(emp => {
        lista.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${escapeHtml(emp.nombre_completo)}</span>
                <span class="badge bg-secondary">${escapeHtml(emp.identidad)}</span>
            </li>
        `;
    });
    
    lista.innerHTML += '</ul>';

    // Mostrar modal
    const modal = new Modal(document.getElementById('modalEgreso'));
    modal.show();
}

window.procesarEgresoIndividual = function(empleadoId) {
    const empleado = AppState.empleados.find(e => e.id === empleadoId);
    if (!empleado) return;

    AppState.seleccionados = [empleado];
    abrirModalEgreso();
};

async function confirmarEgreso(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const fechaEgreso = formData.get('fecha_egreso');
    const motivoEgreso = formData.get('motivo_egreso');
    const recomendado = formData.get('recomendado');
    const recontrataria = formData.get('recontrataria');
    const comentarios = formData.get('comentarios');
    const tipoEgreso = formData.get('tipo_egreso');
    
    // Validar campos requeridos
    if (!recomendado) {
        mostrarAlerta('warning', 'Por favor indique si el empleado es recomendado');
        return;
    }

    if (!tipoEgreso) {
        mostrarAlerta('warning', 'Por favor seleccione el tipo de egreso');
        return;
    }

    // Validar fecha
    const hoy = new Date().toISOString().split('T')[0];
    if (fechaEgreso > hoy) {
        mostrarAlerta('warning', 'La fecha de egreso no puede ser futura');
        return;
    }

    const btnConfirmar = document.getElementById('btnConfirmarEgreso');
    const textoOriginal = btnConfirmar.innerHTML;
    btnConfirmar.disabled = true;
    btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

    try {
        const payload = {
            empleados: AppState.seleccionados.map(emp => ({
                id: emp.id,
                fecha_egreso: fechaEgreso
            })),
            motivo_egreso: motivoEgreso,
            recomendado: recomendado,
            recontrataria: recontrataria,
            comentarios: comentarios,
            tipo_egreso: tipoEgreso
        };

        const response = await fetch(CONFIG.routes.procesar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            // Cerrar modal
            Modal.getInstance(document.getElementById('modalEgreso')).hide();

            // Mostrar resultado
            let mensaje = data.message;
            if (data.errores && data.errores.length > 0) {
                mensaje += '\n\nAdvertencias:\n' + data.errores.join('\n');
            }

            await Swal.fire({
                icon: 'success',
                title: '¡Egresos procesados!',
                html: mensaje.replace(/\n/g, '<br>'),
                confirmButtonColor: '#198754'
            });

            // Recargar tabla
            limpiarSeleccion();
            cargarEmpleados(AppState.paginacion.current_page);
            
            // Limpiar formulario
            e.target.reset();
            configurarFechaEgresoDefault();

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar egresos'
        });
    } finally {
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = textoOriginal;
    }
}

// ===================================
// UTILIDADES
// ===================================

function configurarFechaEgresoDefault() {
    const inputFecha = document.getElementById('fechaEgreso');
    if (inputFecha) {
        const hoy = new Date().toISOString().split('T')[0];
        inputFecha.value = hoy;
        inputFecha.max = hoy;
    }
}

function limpiarFiltros() {
    document.getElementById('formFiltros').reset();
    limpiarSeleccion();
    limpiarTabla();
    
    if (AppState.esAdmin) {
        document.getElementById('filtroEmpresa').value = '';
    }
}

function limpiarTabla() {
    document.getElementById('tbodyEmpleados').innerHTML = `
        <tr>
            <td colspan="10" class="text-center text-muted py-5">
                <i class="ri-search-line fs-1 d-block mb-2"></i>
                <p>Usa los filtros para buscar empleados</p>
            </td>
        </tr>
    `;
    document.getElementById('paginacionNav').innerHTML = '';
    document.getElementById('infoPaginacion').innerHTML = '';
    AppState.empleados = [];
    actualizarContadores();
}

function mostrarLoading(mostrar) {
    const loading = document.getElementById('loadingEmpleados');
    const contenedor = document.getElementById('contenedorTabla');
    
    if (mostrar) {
        loading.style.display = 'block';
        contenedor.style.display = 'none';
    } else {
        loading.style.display = 'none';
        contenedor.style.display = 'block';
    }
}

function actualizarContadores() {
    const total = AppState.empleados.length;
    const seleccionados = AppState.seleccionados.length;

    document.getElementById('totalEmpleados').textContent = 
        `${total} empleado${total !== 1 ? 's' : ''} activo${total !== 1 ? 's' : ''}`;
    
    document.getElementById('countSeleccionados').textContent = seleccionados;
    document.getElementById('btnProcesarEgresos').disabled = seleccionados === 0;

    if (seleccionados > 0) {
        document.getElementById('infoSeleccion').textContent = 
            `${seleccionados} empleado${seleccionados !== 1 ? 's' : ''} seleccionado${seleccionados !== 1 ? 's' : ''}`;
    } else {
        document.getElementById('infoSeleccion').textContent = 
            'Selecciona empleados para procesar su egreso';
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function mostrarAlerta(tipo, mensaje) {
    const iconos = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };

    Swal.fire({
        icon: iconos[tipo],
        title: mensaje,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

console.log('✓ Módulo de egresos cargado');