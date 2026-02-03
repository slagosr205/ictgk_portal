/**
 * Validador de Importación - JavaScript
 * Laravel 10 + Vanilla JS
 */
import * as bootstrap from 'bootstrap';
import { Collapse, Modal, Toast, Tooltip } from 'bootstrap';
import Swal from 'sweetalert2';
// Estado global
const AppState = {
    registros: [],
    catalogos: {},
    filtroActual: 'todos',
    modalValidacion: null,
    modalEdicion: null
};

// Configuración
const CONFIG = {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
    routes: {
        validar: '/validador-importacion/validar',
        revalidar: '/validador-importacion/revalidar',
        confirmar: '/validador-importacion/confirmar',
        catalogos: '/validador-importacion/catalogos'
    }
};

// ===================================
// INICIALIZACIÓN
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    // IMPORTANTE: Solo inicializar si estamos en la página del validador
    const formValidar = document.getElementById('formValidarArchivo');
    
    if (!formValidar) {
        console.log('No se encuentra el formulario de validación, no inicializar');
        return; // Salir si no estamos en la página correcta
    }

    console.log('Inicializando validador...');
    inicializarApp();
});



function inicializarApp() {
    // Verificar que Bootstrap esté cargado
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado');
        return;
    }

    // Inicializar modales
    const modalValidacionEl = document.getElementById('modalValidacion');
    const modalEdicionEl = document.getElementById('modalEdicion');

    if (modalValidacionEl) {
        AppState.modalValidacion = new Modal(modalValidacionEl);
    }

    if (modalEdicionEl) {
        AppState.modalEdicion = new Modal(modalEdicionEl);
    }

    // Event Listeners
    inicializarEventListeners();

    // Cargar catálogos DESPUÉS de inicializar event listeners
    cargarCatalogos();

    console.log('Validador inicializado correctamente');
}

function inicializarEventListeners() {
    // Formulario de validación - CRÍTICO: prevenir submit por defecto
    const formValidar = document.getElementById('formValidarArchivo');
    if (formValidar) {
        formValidar.addEventListener('submit', function (e) {
            e.preventDefault(); // IMPORTANTE: prevenir el envío normal
            e.stopPropagation();
            handleValidarArchivo(e);
        });
        console.log('Event listener agregado al formulario');
    } else {
        console.error('Formulario #formValidarArchivo no encontrado');
    }

    // Filtros de tabla
    const filtros = document.querySelectorAll('input[name="filtroEstado"]');
    filtros.forEach(filtro => {
        filtro.addEventListener('change', aplicarFiltros);
    });

    // Botones de expandir/contraer
    const btnExpandir = document.getElementById('btnExpandirTodos');
    const btnContraer = document.getElementById('btnContraerTodos');

    if (btnExpandir) {
        btnExpandir.addEventListener('click', () => toggleDetalles(true));
    }

    if (btnContraer) {
        btnContraer.addEventListener('click', () => toggleDetalles(false));
    }

    // Confirmar importación
    const btnConfirmar = document.getElementById('btnConfirmarImportacion');
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', confirmarImportacion);
    }

    // Formulario de edición
    const formEditar = document.getElementById('formEditarRegistro');
    if (formEditar) {
        formEditar.addEventListener('submit', function (e) {
            e.preventDefault();
            handleGuardarEdicion(e);
        });
    }

    // Select de empresa cambia puestos
    const selectEmpresa = document.getElementById('editEmpresa');
    if (selectEmpresa) {
        selectEmpresa.addEventListener('change', function () {
            actualizarPuestosPorEmpresa(this.value);
        });
    }

    // Formateo de identidad
    const editIdentidad = document.getElementById('editIdentidad');
    if (editIdentidad) {
        editIdentidad.addEventListener('input', formatearDni);
    }
}

// ===================================
// CARGA DE CATÁLOGOS
// ===================================

async function cargarCatalogos() {
    try {
        const response = await fetch(CONFIG.routes.catalogos);
        const data = await response.json();

        if (data.success) {
            AppState.catalogos = data.catalogos;
            llenarSelectEmpresas();
            console.log('Catálogos cargados:', AppState.catalogos);
        }
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
        mostrarAlerta('error', 'Error al cargar los catálogos del sistema');
    }
}

function llenarSelectEmpresas() {
    const select = document.getElementById('editEmpresa');
    if (!select) return;

    select.innerHTML = '<option value="">Seleccione una empresa...</option>';

    if (!AppState.catalogos.empresas) return;

    AppState.catalogos.empresas.forEach(empresa => {
        const option = document.createElement('option');
        option.value = empresa.id;
        option.textContent = empresa.nombre;
        select.appendChild(option);
    });
}

function actualizarPuestosPorEmpresa(empresaId) {
    const selectPuesto = document.getElementById('editPuesto');
    const helpText = document.getElementById('helpPuesto');

    if (!selectPuesto) return;

    selectPuesto.innerHTML = '<option value="">Seleccione un puesto...</option>';

    if (!empresaId) {
        if (helpText) {
            helpText.textContent = 'Primero seleccione una empresa';
            helpText.classList.add('text-warning');
        }
        return;
    }

    // Filtrar departamentos de la empresa
    const departamentos = AppState.catalogos.departamentos.filter(d => d.empresa_id == empresaId);
    const deptosIds = departamentos.map(d => d.id);

    // Filtrar puestos de esos departamentos
    const puestosFiltrados = AppState.catalogos.puestos.filter(p =>
        deptosIds.includes(p.departamento_id)
    );

    if (puestosFiltrados.length === 0) {
        if (helpText) {
            helpText.textContent = 'Esta empresa no tiene puestos registrados';
            helpText.classList.add('text-warning');
        }
        return;
    }

    puestosFiltrados.forEach(puesto => {
        const option = document.createElement('option');
        option.value = puesto.id;
        option.textContent = puesto.nombre;
        selectPuesto.appendChild(option);
    });

    if (helpText) {
        helpText.textContent = `${puestosFiltrados.length} puesto(s) disponible(s)`;
        helpText.classList.remove('text-warning');
    }
}

// ===================================
// VALIDACIÓN DE ARCHIVO
// ===================================

async function handleValidarArchivo(e) {
    console.log('handleValidarArchivo ejecutándose...');

    const formData = new FormData(e.target);
    const archivo = formData.get('archivo');

    // Validar que hay un archivo
    if (!archivo || archivo.size === 0) {
        mostrarAlerta('warning', 'Por favor seleccione un archivo CSV');
        return;
    }

    console.log('Archivo seleccionado:', archivo.name, 'Tamaño:', archivo.size);

    // Mostrar loading
    const btnValidar = document.getElementById('btnValidar');
    const textoOriginal = btnValidar.innerHTML;
    btnValidar.disabled = true;
    btnValidar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Validando...';

    try {
        console.log('Enviando archivo al servidor...');

        const response = await fetch(CONFIG.routes.validar, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'Accept': 'application/json'
            }
        });

        console.log('Respuesta recibida:', response.status);

        const data = await response.json();
        console.log('Datos:', data);

        if (data.success) {
            AppState.registros = data.data.registros;
            mostrarResultadosValidacion(data.data);
        } else {
            mostrarAlerta('error', data.message || 'Error al validar el archivo');
        }

    } catch (error) {
        console.error('Error completo:', error);
        mostrarAlerta('error', 'Error al procesar el archivo: ' + error.message);
    } finally {
        btnValidar.disabled = false;
        btnValidar.innerHTML = textoOriginal;
    }
}

// ===================================
// MOSTRAR RESULTADOS
// ===================================

function mostrarResultadosValidacion(data) {
    console.log('Mostrando resultados...', data);

    // Actualizar estadísticas
    document.getElementById('estadTotal').textContent = data.estadisticas.total;
    document.getElementById('estadValidos').textContent = data.estadisticas.validos;
    document.getElementById('estadErrores').textContent = data.estadisticas.con_errores;
    document.getElementById('estadAdvertencias').textContent = data.estadisticas.con_advertencias;
    document.getElementById('contadorValidos').textContent = data.estadisticas.validos;

    // Renderizar tabla
    renderizarTablaValidacion();

    // Mostrar modal
    if (AppState.modalValidacion) {
        AppState.modalValidacion.show();
    } else {
        console.error('Modal de validación no está inicializado');
    }

    // Limpiar formulario
    document.getElementById('formValidarArchivo').reset();
}

function renderizarTablaValidacion() {
    const tbody = document.getElementById('tablaValidacion');
    if (!tbody) {
        console.error('Tabla de validación no encontrada');
        return;
    }

    tbody.innerHTML = '';

    if (AppState.registros.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                    <p class="mb-0">No hay registros para mostrar</p>
                </td>
            </tr>
        `;
        return;
    }

    AppState.registros.forEach((registro, index) => {
        const tr = crearFilaRegistro(registro, index);
        tbody.appendChild(tr);
    });

    console.log(`Tabla renderizada con ${AppState.registros.length} registros`);
}

function crearFilaRegistro(registro, index) {
    const tr = document.createElement('tr');
    tr.dataset.index = index;
    tr.dataset.valido = registro.valido;

    // Clases de estado según el tipo de acción
    if (!registro.valido) {
        tr.classList.add('table-danger');
    } else if (registro.datos._accion === 'reactivar') {
        tr.classList.add('table-info'); // Azul para reactivaciones
    } else if (registro.advertencias && registro.advertencias.length > 0) {
        tr.classList.add('table-warning');
    }

    // ============================================
    // ICONO DE ESTADO CON TIPO DE ACCIÓN
    // ============================================
    let iconoEstado = '';
    if (!registro.valido) {
        iconoEstado = '<i class="ri-error-warning-fill text-danger fs-5"></i>';
    } else if (registro.datos._accion === 'reactivar') {
        iconoEstado = '<i class="ri-restart-line text-info fs-5" title="Reactivación"></i>';
    } else {
        iconoEstado = '<i class="ri-checkbox-circle-fill text-success fs-5"></i>';
    }

    // ============================================
    // OBTENER NOMBRES DE EMPRESA Y PUESTO
    // ============================================
    const nombreEmpresa = obtenerNombreEmpresa(registro.datos.id_empresa);
    const nombrePuesto = obtenerNombrePuesto(registro.datos.id_puesto);

    // ============================================
    // NOMBRE COMPLETO DEL COLABORADOR
    // ============================================
    const nombreCompleto = `${registro.datos.nombre || ''} ${registro.datos.apellido || ''}`.trim();

    // ============================================
    // ERRORES Y ADVERTENCIAS
    // ============================================
    let mensajes = '';
    
    if (registro.errores && registro.errores.length > 0) {
        mensajes += '<div class="text-danger small mb-1">';
        registro.errores.forEach(error => {
            mensajes += `<div><i class="ri-close-circle-line me-1"></i>${escapeHtml(error)}</div>`;
        });
        mensajes += '</div>';
    }

    if (registro.advertencias && registro.advertencias.length > 0) {
        mensajes += '<div class="text-warning small">';
        registro.advertencias.forEach(adv => {
            mensajes += `<div><i class="ri-alert-line me-1"></i>${escapeHtml(adv)}</div>`;
        });
        mensajes += '</div>';
    }

    if (registro.valido && (!registro.advertencias || registro.advertencias.length === 0)) {
        mensajes = '<span class="text-success small"><i class="ri-checkbox-line me-1"></i>Sin problemas</span>';
    }

    // ============================================
    // SUGERENCIAS (COLAPSABLE)
    // ============================================
    let sugerencias = '';
    if (registro.sugerencias && registro.sugerencias.length > 0) {
        sugerencias = `
            <button class="btn btn-link btn-sm p-0 mt-1" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#sugerencias-${index}">
                <i class="ri-lightbulb-line"></i> Ver sugerencias
            </button>
            <div class="collapse" id="sugerencias-${index}">
                <div class="text-info small mt-1">
                    ${registro.sugerencias.map(s => `<div><i class="ri-lightbulb-flash-line me-1"></i>${escapeHtml(s)}</div>`).join('')}
                </div>
            </div>
        `;
    }

    // ============================================
    // CONSTRUIR LA FILA HTML
    // ============================================
    tr.innerHTML = `
        <td class="text-center">${registro.fila}</td>
        <td class="text-center">${iconoEstado}</td>
        <td>${escapeHtml(nombreCompleto || '-')}</td>
        <td><code>${escapeHtml(registro.datos.identidad || '-')}</code></td>
        <td>${nombreEmpresa}</td>
        <td>${nombrePuesto}</td>
        <td>${escapeHtml(registro.datos.fechaIngreso || '-')}</td>
        <td>${mensajes}${sugerencias}</td>
        <td class="text-center">
            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-primary" onclick="editarRegistro(${index})" title="Editar">
                    <i class="ri-edit-line"></i>
                </button>
                <button class="btn btn-danger" onclick="eliminarRegistro(${index})" title="Eliminar">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </td>
    `;

    return tr;
}

// ===================================
// FILTROS
// ===================================

function aplicarFiltros() {
    const filtro = document.querySelector('input[name="filtroEstado"]:checked')?.value || 'todos';
    AppState.filtroActual = filtro;

    const filas = document.querySelectorAll('#tablaValidacion tr');

    filas.forEach(fila => {
        const valido = fila.dataset.valido === 'true';

        switch (filtro) {
            case 'validos':
                fila.style.display = valido ? '' : 'none';
                break;
            case 'errores':
                fila.style.display = !valido ? '' : 'none';
                break;
            default:
                fila.style.display = '';
        }
    });
}

function toggleDetalles(expandir) {
    const botones = document.querySelectorAll('[data-bs-toggle="collapse"]');
    botones.forEach(btn => {
        const target = document.querySelector(btn.dataset.bsTarget);
        if (target) {
            const bsCollapse = Collapse.getInstance(target) || new bootstrap.Collapse(target, { toggle: false });
            if (expandir) {
                bsCollapse.show();
            } else {
                bsCollapse.hide();
            }
        }
    });
}

// ===================================
// EDICIÓN DE REGISTROS
// ===================================

// ===================================
// EDICIÓN DE REGISTROS
// ===================================

window.editarRegistro = function (index) {
    const registro = AppState.registros[index];
    if (!registro) {
        console.error('Registro no encontrado en índice:', index);
        return;
    }

    console.log('Editando registro:', registro);

    // Verificar que todos los elementos existen
    const elementos = {
        editIndice: document.getElementById('editIndice'),
        editNumeroFila: document.getElementById('editNumeroFila'),
        editIdentidad: document.getElementById('editIdentidad'),
        editNombre: document.getElementById('editNombre'),
        editApellido: document.getElementById('editApellido'),
        editGenero: document.getElementById('editGenero'),
        editFechaNacimiento: document.getElementById('editFechaNacimiento'),
        editTelefono: document.getElementById('editTelefono'),
        editCorreo: document.getElementById('editCorreo'),
        editDireccion: document.getElementById('editDireccion'),
        editEmpresa: document.getElementById('editEmpresa'),
        editPuesto: document.getElementById('editPuesto'),
        editFechaIngreso: document.getElementById('editFechaIngreso'),
        editFechaEgreso: document.getElementById('editFechaEgreso'),
        editArea: document.getElementById('editArea')
    };

    // Verificar elementos faltantes
    const faltantes = [];
    for (const [nombre, elemento] of Object.entries(elementos)) {
        if (!elemento) {
            faltantes.push(nombre);
        }
    }

    if (faltantes.length > 0) {
        console.error('Elementos faltantes en el modal de edición:', faltantes);
        mostrarAlerta('error', 'Error: Formulario de edición incompleto. Revise la consola.');
        return;
    }

    // Llenar formulario - ÍNDICE Y FILA
    elementos.editIndice.value = index;
    elementos.editNumeroFila.textContent = registro.fila;

    // Llenar formulario - CANDIDATO
    elementos.editIdentidad.value = registro.datos.identidad || '';
    elementos.editNombre.value = registro.datos.nombre || '';
    elementos.editApellido.value = registro.datos.apellido || '';
    elementos.editGenero.value = registro.datos.generoM_F || '';
    elementos.editFechaNacimiento.value = registro.datos.fecha_nacimiento || '';
    elementos.editTelefono.value = registro.datos.telefono || '';
    elementos.editCorreo.value = registro.datos.correo || '';
    elementos.editDireccion.value = registro.datos.direccion || '';

    // Llenar formulario - INGRESO
    elementos.editEmpresa.value = registro.datos.id_empresa || '';
    elementos.editFechaIngreso.value = registro.datos.fechaIngreso || '';
    elementos.editFechaEgreso.value = registro.datos.fechaEgreso || '';
    elementos.editArea.value = registro.datos.area || '';

    // Actualizar puestos y seleccionar
    actualizarPuestosPorEmpresa(registro.datos.id_empresa);

    setTimeout(() => {
        if (elementos.editPuesto) {
            elementos.editPuesto.value = registro.datos.id_puesto || '';
        }
    }, 100);

    // Mostrar modal
    if (AppState.modalEdicion) {
        AppState.modalEdicion.show();
    } else {
        console.error('Modal de edición no está inicializado');
        // Intentar inicializarlo
        const modalEl = document.getElementById('modalEdicion');
        if (modalEl) {
            AppState.modalEdicion = new bootstrap.Modal(modalEl);
            AppState.modalEdicion.show();
        } else {
            mostrarAlerta('error', 'No se pudo abrir el modal de edición');
        }
    }
};

async function handleGuardarEdicion(e) {
    const index = parseInt(document.getElementById('editIndice').value);
    const registro = AppState.registros[index];

    // Recopilar datos editados
    const datosEditados = {
        identidad: document.getElementById('editIdentidad').value.trim(),
        nombre: document.getElementById('editNombre').value.trim(),
        apellido: document.getElementById('editApellido').value.trim(),
        generoM_F: document.getElementById('editGenero').value,
        fecha_nacimiento: document.getElementById('editFechaNacimiento').value,
        telefono: document.getElementById('editTelefono').value.trim(),
        correo: document.getElementById('editCorreo').value.trim(),
        direccion: document.getElementById('editDireccion').value.trim(),
        id_empresa: parseInt(document.getElementById('editEmpresa').value),
        id_puesto: parseInt(document.getElementById('editPuesto').value),
        fechaIngreso: document.getElementById('editFechaIngreso').value,
        fechaEgreso: document.getElementById('editFechaEgreso').value,
        area: document.getElementById('editArea').value.trim()
    };

    console.log('Guardando edición:', datosEditados);

    // Mostrar loading
    const btnGuardar = e.target.querySelector('button[type="submit"]');
    const textoOriginal = btnGuardar.innerHTML;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    try {
        const response = await fetch(CONFIG.routes.revalidar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                datos: datosEditados,
                fila: registro.fila
            })
        });

        const data = await response.json();

        if (data.success) {
            AppState.registros[index] = data.registro;
            recalcularEstadisticas();
            renderizarTablaValidacion();
            aplicarFiltros();

            if (AppState.modalEdicion) {
                AppState.modalEdicion.hide();
            }

            mostrarAlerta('success', 'Registro actualizado correctamente', 2000);
        } else {
            mostrarAlerta('error', data.message || 'Error al guardar los cambios');
        }

    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('error', 'Error al guardar: ' + error.message);
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = textoOriginal;
    }
}

window.eliminarRegistro = function (index) {
    const registro = AppState.registros[index];

    Swal.fire({
        title: '¿Eliminar registro?',
        html: `<p>Fila ${registro.fila}: <strong>${registro.datos.nombre} ${registro.datos.apellido}</strong></p>
               <p class="text-muted">Este registro no será importado</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            AppState.registros.splice(index, 1);
            recalcularEstadisticas();
            renderizarTablaValidacion();
            aplicarFiltros();

            mostrarAlerta('info', 'Registro eliminado', 2000);
        }
    });
};

function recalcularEstadisticas() {
    const total = AppState.registros.length;
    const validos = AppState.registros.filter(r => r.valido).length;
    const conErrores = AppState.registros.filter(r => !r.valido).length;
    const conAdvertencias = AppState.registros.filter(r =>
        r.advertencias && r.advertencias.length > 0
    ).length;

    document.getElementById('estadTotal').textContent = total;
    document.getElementById('estadValidos').textContent = validos;
    document.getElementById('estadErrores').textContent = conErrores;
    document.getElementById('estadAdvertencias').textContent = conAdvertencias;
    document.getElementById('contadorValidos').textContent = validos;
}

// ===================================
// CONFIRMACIÓN DE IMPORTACIÓN
// ===================================

async function confirmarImportacion() {
    const registrosValidos = AppState.registros.filter(r => r.valido);

    if (registrosValidos.length === 0) {
        mostrarAlerta('warning', 'No hay registros válidos para importar');
        return;
    }

    // Contar reactivaciones
    const reactivaciones = registrosValidos.filter(r => r.datos._accion === 'reactivar').length;
    const nuevos = registrosValidos.length - reactivaciones;

    let detalleHTML = '<ul class="text-start mb-0">';
    if (nuevos > 0) {
        detalleHTML += `<li><strong>${nuevos}</strong> nuevo(s) ingreso(s)</li>`;
    }
    if (reactivaciones > 0) {
        detalleHTML += `<li><strong>${reactivaciones}</strong> reactivación(es)</li>`;
    }
    detalleHTML += '</ul>';

    const result = await Swal.fire({
        title: '¿Confirmar importación?',
        html: `
            <p>Se procesarán <strong>${registrosValidos.length}</strong> registros válidos:</p>
            ${detalleHTML}
            ${AppState.registros.length - registrosValidos.length > 0 
                ? `<p class="text-warning mt-3">Se omitirán ${AppState.registros.length - registrosValidos.length} registros con errores.</p>`
                : ''
            }
            <p class="mt-3">¿Desea continuar?</p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, importar',
        cancelButtonText: 'Cancelar',
        width: '600px'
    });

    if (!result.isConfirmed) return;

    // Mostrar loading
    Swal.fire({
        title: 'Importando registros...',
        html: 'Por favor espere mientras se procesan los datos',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch(CONFIG.routes.confirmar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                registros: registrosValidos
            })
        });

        const data = await response.json();

        Swal.close();

        if (data.success) {
            // Construir mensaje de éxito
            let html = `<div class="alert alert-success text-start mb-3">
                <h6 class="alert-heading mb-2">
                    <i class="ri-checkbox-circle-line me-2"></i>Importación completada exitosamente
                </h6>
                <ul class="mb-0">`;

            if (data.insertados > 0) {
                html += `<li><strong>${data.insertados}</strong> nuevo(s) ingreso(s) creado(s)</li>`;
            }

            if (data.reactivados > 0) {
                html += `<li><strong>${data.reactivados}</strong> colaborador(es) reactivado(s)</li>`;
            }

            html += `</ul></div>`;

            // Mostrar errores si los hay
            if (data.errores && data.errores.length > 0) {
                html += `
                    <div class="alert alert-warning text-start">
                        <h6 class="alert-heading mb-2">
                            <i class="ri-error-warning-line me-2"></i>Algunos registros no pudieron procesarse
                        </h6>
                        <ul class="mb-0" style="max-height: 200px; overflow-y: auto;">
                            ${data.errores.map(e => 
                                `<li><strong>Fila ${e.fila}</strong> (${e.identidad}): ${e.errores.join(', ')}</li>`
                            ).join('')}
                        </ul>
                    </div>
                `;
            }

            await Swal.fire({
                icon: 'success',
                title: '¡Importación completada!',
                html: html,
                confirmButtonColor: '#198754',
                width: '700px'
            });

            // Cerrar modal
            if (AppState.modalValidacion) {
                AppState.modalValidacion.hide();
            }
            
            // Resetear estado
            AppState.registros = [];
            
            // Recargar página o redirigir
            setTimeout(() => {
                window.location.href = '/home';
            }, 1500);

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en la importación',
                html: `<p>${data.message}</p>`,
                confirmButtonColor: '#dc3545'
            });
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al importar los registros: ' + error.message,
            confirmButtonColor: '#dc3545'
        });
    }
}



// ===================================
// UTILIDADES
// ===================================

function obtenerNombreEmpresa(id) {
    if (!id) return '<span class="text-muted">-</span>';
    const empresa = AppState.catalogos.empresas?.find(e => e.id == id);
    return empresa ? escapeHtml(empresa.nombre) : `<span class="text-danger">ID: ${id}</span>`;
}

function obtenerNombrePuesto(id) {
    if (!id) return '<span class="text-muted">-</span>';
    const puesto = AppState.catalogos.puestos?.find(p => p.id == id);
    return puesto ? escapeHtml(puesto.nombre) : `<span class="text-danger">ID: ${id}</span>`;
}

function formatearDni(e) {
    let value = e.target.value.replace(/[^\d-]/g, '');

    // Formato: 0000-0000-00000
    if (value.length > 4 && value.indexOf('-') === -1) {
        value = value.slice(0, 4) + '-' + value.slice(4);
    }
    if (value.length > 9 && value.lastIndexOf('-') === 4) {
        value = value.slice(0, 9) + '-' + value.slice(9);
    }

    e.target.value = value.slice(0, 15);
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

function mostrarAlerta(tipo, mensaje, duracion = 3000) {
    const iconos = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };

    Swal.fire({
        icon: iconos[tipo] || 'info',
        title: mensaje,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: duracion,
        timerProgressBar: true
    });
}

  


    const formDescargar = document.getElementById('formDescargarPlantilla');
    const loaderModal = new Modal(document.getElementById('loaderModal'));
    const cantidadFilas = document.getElementById('cantidadFilas');
    const filasCount = document.getElementById('filasCount');
    
    // Actualizar contador
    cantidadFilas.addEventListener('change', function() {
        filasCount.textContent = this.value;
    });

    if (formDescargar) {
        formDescargar.addEventListener('submit', async function(e) {
            e.preventDefault();

            const filas = cantidadFilas.value;
            filasCount.textContent = filas;

            console.log('Solicitando plantilla con', filas, 'filas');

            // Mostrar loader
            loaderModal.show();

            try {
                // OPCIÓN 1: Usando fetch con blob
                const url = `/validador-importacion/plantilla?filas=${filas}`;
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error('Error del servidor: ' + response.status);
                }

                // Convertir a blob
                const blob = await response.blob();
                console.log('Blob size:', blob.size);

                if (blob.size === 0) {
                    throw new Error('El archivo descargado está vacío');
                }

                // Crear URL del blob
                const blobUrl = window.URL.createObjectURL(blob);
                
                // Crear link de descarga
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = `plantilla_importacion_${filas}_filas_${new Date().toISOString().slice(0,10)}.xlsx`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Liberar memoria
                window.URL.revokeObjectURL(blobUrl);

                // Ocultar loader
                setTimeout(() => {
                    loaderModal.hide();
                    
                    // Notificación de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Plantilla Generada!',
                        html: `Se ha descargado la plantilla con <strong>${filas} filas</strong>.`,
                        timer: 3000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                }, 1000);

            } catch (error) {
                console.error('Error completo:', error);
                
                loaderModal.hide();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error al Descargar',
                    html: `No se pudo generar la plantilla.<br><small>${error.message}</small>`,
                    confirmButtonText: 'Entendido'
                });
            }
        });
    }


// Log para debugging
console.log('Archivo validador-importacion.js cargado');