// Empleado/index.js - Parte 1
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioEmpleado = document.getElementById('formularioEmpleado');
const BtnGuardar = document.getElementById('btnSubmit');
const BtnLimpiar = document.getElementById('btnCancelar');
const empleado_codigo = document.getElementById('empleado_codigo');
const empleado_nombres = document.getElementById('empleado_nombres');
const empleado_apellidos = document.getElementById('empleado_apellidos');
const empleado_dpi = document.getElementById('empleado_dpi');
const empleado_correo = document.getElementById('empleado_correo');
const empleado_telefono = document.getElementById('empleado_telefono');

// Validar Código de Empleado
const ValidarCodigoEmpleado = () => {
    const codigo = empleado_codigo.value.trim();
    if (codigo.length >= 1 && codigo.length <= 20) {
        empleado_codigo.classList.add('is-valid');
        empleado_codigo.classList.remove('is-invalid');
    } else if (codigo.length > 0) {
        empleado_codigo.classList.add('is-invalid');
        empleado_codigo.classList.remove('is-valid');
    } else {
        empleado_codigo.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Nombres
const ValidarNombres = () => {
    const nombres = empleado_nombres.value.trim();
    if (nombres.length > 0 && nombres.length <= 100) {
        empleado_nombres.classList.add('is-valid');
        empleado_nombres.classList.remove('is-invalid');
    } else if (nombres.length > 100) {
        empleado_nombres.classList.add('is-invalid');
        empleado_nombres.classList.remove('is-valid');
        Swal.fire({ 
            icon: "warning", 
            title: "Nombres muy largos", 
            text: "Los nombres no pueden exceder 100 caracteres" 
        });
    } else {
        empleado_nombres.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Apellidos
const ValidarApellidos = () => {
    const apellidos = empleado_apellidos.value.trim();
    if (apellidos.length > 0 && apellidos.length <= 100) {
        empleado_apellidos.classList.add('is-valid');
        empleado_apellidos.classList.remove('is-invalid');
    } else if (apellidos.length > 100) {
        empleado_apellidos.classList.add('is-invalid');
        empleado_apellidos.classList.remove('is-valid');
        Swal.fire({ 
            icon: "warning", 
            title: "Apellidos muy largos", 
            text: "Los apellidos no pueden exceder 100 caracteres" 
        });
    } else {
        empleado_apellidos.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar DPI
const ValidarDPI = () => {
    const dpi = empleado_dpi.value.trim();
    if (dpi.length === 0) {
        empleado_dpi.classList.remove('is-valid', 'is-invalid');
    } else if (dpi.length <= 15) {
        empleado_dpi.classList.add('is-valid');
        empleado_dpi.classList.remove('is-invalid');
    } else {
        empleado_dpi.classList.add('is-invalid');
        empleado_dpi.classList.remove('is-valid');
        Swal.fire({ 
            icon: "warning", 
            title: "DPI muy largo", 
            text: "El DPI no puede exceder 15 caracteres" 
        });
    }
};

// Validar Correo Electrónico
const ValidarCorreo = () => {
    const correo = empleado_correo.value.trim();
    const patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (correo.length === 0) {
        empleado_correo.classList.remove('is-valid', 'is-invalid');
    } else if (patronCorreo.test(correo) && correo.length <= 100) {
        empleado_correo.classList.add('is-valid');
        empleado_correo.classList.remove('is-invalid');
    } else {
        empleado_correo.classList.add('is-invalid');
        empleado_correo.classList.remove('is-valid');
    }
};

// Validar Teléfono
const ValidarTelefono = () => {
    const telefono = empleado_telefono.value.trim();
    if (telefono.length === 0) {
        empleado_telefono.classList.remove('is-valid', 'is-invalid');
    } else if (telefono.length <= 15) {
        empleado_telefono.classList.add('is-valid');
        empleado_telefono.classList.remove('is-invalid');
    } else {
        empleado_telefono.classList.add('is-invalid');
        empleado_telefono.classList.remove('is-valid');
        Swal.fire({ 
            icon: "warning", 
            title: "Teléfono muy largo", 
            text: "El teléfono no puede exceder 15 caracteres" 
        });
    }
};

// Guardar Empleado
const GuardarEmpleado = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Mostrar estado de carga
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    loading.style.display = 'inline';
    btnText.style.display = 'none';

    if (!validarFormulario(FormularioEmpleado, ['empleado_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    // Validar campos específicos
    if (empleado_codigo.classList.contains('is-invalid') || 
        empleado_nombres.classList.contains('is-invalid') ||
        empleado_apellidos.classList.contains('is-invalid') ||
        empleado_correo.classList.contains('is-invalid') ||
        empleado_dpi.classList.contains('is-invalid') ||
        empleado_telefono.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Datos inválidos", 
            text: "Debe corregir los errores antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const body = new FormData(FormularioEmpleado);
    const url = '/montoya_final_dotacion_ingsoft/Empleado/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Empleado registrado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarEmpleados();
            CargarEstadisticas();
        } else {
            Swal.fire({ 
                icon: "info", 
                title: "Error", 
                text: mensaje 
            });
        }
    } catch (error) {
        console.error('Error al guardar:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: "Ocurrió un error al procesar la solicitud" 
        });
    }
    
    resetearBoton();
};

// Buscar Empleados
const BuscarEmpleados = async () => {
    const url = '/montoya_final_dotacion_ingsoft/Empleado/obtenerEmpleadosAPI';
    console.log('Intentando cargar desde:', url);
    
    try {
        const res = await fetch(url);
        console.log('Response status:', res.status);
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const texto = await res.text();
        console.log('Response text:', texto);
        
        let resultado;
        try {
            resultado = JSON.parse(texto);
        } catch (parseError) {
            console.error('Error parsing JSON:', parseError);
            throw new Error('Respuesta del servidor no es JSON válido');
        }
        
        const { codigo, mensaje, datos } = resultado;
        console.log('Datos recibidos:', { codigo, mensaje, datos });
        
        if (codigo == 1) {
            datatable.clear().draw();
            if (datos && datos.length > 0) {
                datatable.rows.add(datos).draw();
            } else {
                console.log('No hay datos para mostrar');
            }
        } else {
            console.error('Error del servidor:', mensaje);
            Swal.fire({ 
                icon: "info", 
                title: "Sin datos", 
                text: mensaje || "No hay empleados registrados" 
            });
        }
    } catch (error) {
        console.error('Error completo al cargar datos:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: `Error al cargar los empleados: ${error.message}` 
        });
    }
};

// Cargar Estadísticas
const CargarEstadisticas = async () => {
    const url = '/montoya_final_dotacion_ingsoft/Empleado/obtenerEstadisticasAPI';
    
    try {
        const res = await fetch(url);
        const { codigo, datos } = await res.json();
        
        if (codigo == 1 && datos) {
            document.getElementById('totalEmpleados').textContent = datos.total_empleados || 0;
            document.getElementById('totalDepartamentos').textContent = datos.total_departamentos || 0;
            document.getElementById('totalPuestos').textContent = datos.total_puestos || 0;
            document.getElementById('nuevosEmpleados').textContent = datos.nuevos_ultimo_mes || 0;
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
};
// Empleado/index.js - Parte 2

// DataTable Configuración
const datatable = new DataTable('#TablaEmpleados', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "empleado_id", 
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Código", 
            data: "empleado_codigo",
            render: (data) => `<span class="badge bg-primary fs-6">${data}</span>`
        },
        { 
            title: "Nombres Completos", 
            data: null,
            render: (data, type, row) => {
                const nombres = row.empleado_nombres || '';
                const apellidos = row.empleado_apellidos || '';
                return `<strong>${apellidos}, ${nombres}</strong>`;
            }
        },
        { 
            title: "DPI", 
            data: "empleado_dpi",
            render: (data) => data || '<em class="text-muted">Sin DPI</em>'
        },
        { 
            title: "Puesto", 
            data: "empleado_puesto",
            render: (data) => data || '<em class="text-muted">Sin asignar</em>'
        },
        { 
            title: "Departamento", 
            data: "empleado_departamento",
            render: (data) => data || '<em class="text-muted">Sin asignar</em>'
        },
        { 
            title: "Teléfono", 
            data: "empleado_telefono",
            render: (data) => data ? `<i class="fas fa-phone text-success me-1"></i>${data}` : '<em class="text-muted">Sin teléfono</em>'
        },
        { 
            title: "Correo", 
            data: "empleado_correo",
            render: (data) => data ? `<i class="fas fa-envelope text-info me-1"></i>${data}` : '<em class="text-muted">Sin correo</em>'
        },
        {
            title: "Acciones", 
            data: "empleado_id",
            orderable: false,
            render: (id, type, row) => `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-primary modificar" 
                            data-id="${id}" 
                            data-json='${JSON.stringify(row)}'
                            title="Modificar empleado">
                        <i class="fas fa-edit me-1"></i>✏️
                    </button>
                    <button class="btn btn-outline-danger eliminar" 
                            data-id="${id}"
                            title="Eliminar empleado">
                        <i class="fas fa-trash me-1"></i>🗑️
                    </button>
                </div>
            `
        }
    ],
    responsive: true,
    pageLength: 10,
    lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
    order: [[2, 'asc']], // Ordenar por nombres
    columnDefs: [
        { targets: [8], orderable: false } // Deshabilitar ordenamiento en columna de acciones
    ]
});

// Llenar formulario para modificar
const llenarFormulario = (e) => {
    const datos = JSON.parse(e.currentTarget.dataset.json);
    
    // Llenar campos del formulario
    document.getElementById('empleado_id').value = datos.empleado_id || '';
    document.getElementById('empleado_codigo').value = datos.empleado_codigo || '';
    document.getElementById('empleado_nombres').value = datos.empleado_nombres || '';
    document.getElementById('empleado_apellidos').value = datos.empleado_apellidos || '';
    document.getElementById('empleado_dpi').value = datos.empleado_dpi || '';
    document.getElementById('empleado_puesto').value = datos.empleado_puesto || '';
    document.getElementById('empleado_departamento').value = datos.empleado_departamento || '';
    document.getElementById('empleado_fecha_ingreso').value = datos.empleado_fecha_ingreso || '';
    document.getElementById('empleado_telefono').value = datos.empleado_telefono || '';
    document.getElementById('empleado_correo').value = datos.empleado_correo || '';
    document.getElementById('empleado_direccion').value = datos.empleado_direccion || '';
    
    // Cambiar UI para modo edición
    document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i>Modificar Empleado';
    BtnGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar';
    BtnLimpiar.style.display = 'block';
    
    // Quitar validaciones previas
    FormularioEmpleado.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Scroll al formulario
    document.getElementById('formularioEmpleado').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
    
    // Focus en el primer campo
    empleado_codigo.focus();
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    FormularioEmpleado.reset();
    document.getElementById('empleado_id').value = '';
    
    // Restaurar UI para modo creación
    document.getElementById('form-title').innerHTML = '<i class="fas fa-user-plus me-2"></i>Registrar Empleado';
    BtnGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Guardar Empleado';
    BtnLimpiar.style.display = 'none';
    
    // Limpiar validaciones
    FormularioEmpleado.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Limpiar alertas si existen
    const alertsContainer = document.getElementById('alerts');
    if (alertsContainer) {
        alertsContainer.innerHTML = '';
    }
};

// Modificar Empleado
const ModificarEmpleado = async (event) => {
    event.preventDefault();
    
    const id = document.getElementById('empleado_id').value;
    if (!id) {
        return GuardarEmpleado(event);
    }
    
    BtnGuardar.disabled = true;
    
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    loading.style.display = 'inline';
    btnText.style.display = 'none';

    if (!validarFormulario(FormularioEmpleado, ['empleado_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    // Validar campos específicos
    if (empleado_codigo.classList.contains('is-invalid') || 
        empleado_nombres.classList.contains('is-invalid') ||
        empleado_apellidos.classList.contains('is-invalid') ||
        empleado_correo.classList.contains('is-invalid') ||
        empleado_dpi.classList.contains('is-invalid') ||
        empleado_telefono.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Datos inválidos", 
            text: "Debe corregir los errores antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const body = new FormData(FormularioEmpleado);
    const url = '/montoya_final_dotacion_ingsoft/Empleado/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Empleado actualizado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarEmpleados();
            CargarEstadisticas();
        } else {
            Swal.fire({ 
                icon: "error", 
                title: "Error al actualizar", 
                text: mensaje 
            });
        }
    } catch (error) {
        console.error('Error al modificar:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: "Ocurrió un error al procesar la solicitud" 
        });
    }
    
    resetearBoton();
};

// Eliminar Empleado
const EliminarEmpleado = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    const confirmar = await Swal.fire({
        icon: "warning", 
        title: "¿Eliminar empleado?", 
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true, 
        confirmButtonText: "Sí, eliminar", 
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        reverseButtons: true
    });

    if (confirmar.isConfirmed) {
        const url = `/montoya_final_dotacion_ingsoft/Empleado/eliminarAPI?id=${id}`;
        try {
            const res = await fetch(url);
            const { codigo, mensaje } = await res.json();
            
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado correctamente", 
                    text: mensaje 
                });
                BuscarEmpleados();
                CargarEstadisticas();
            } else {
                Swal.fire({ 
                    icon: "error", 
                    title: "Error al eliminar", 
                    text: mensaje 
                });
            }
        } catch (error) {
            console.error('Error al eliminar:', error);
            Swal.fire({ 
                icon: "error", 
                title: "Error de conexión", 
                text: "Error al eliminar el empleado" 
            });
        }
    }
};

// Buscar Empleados por criterios
const buscarEmpleados = async () => {
    const criterio = document.getElementById('criterio_busqueda').value;
    const valor = document.getElementById('valor_busqueda').value.trim();
    
    let url = '/montoya_final_dotacion_ingsoft/Empleado/obtenerEmpleadosAPI';
    
    if (criterio && valor) {
        url = `/montoya_final_dotacion_ingsoft/Empleado/buscarAPI?criterio=${encodeURIComponent(criterio)}&valor=${encodeURIComponent(valor)}`;
    }
    
    try {
        const res = await fetch(url);
        const { codigo, datos } = await res.json();
        
        if (codigo == 1) {
            datatable.clear().draw();
            if (datos && datos.length > 0) {
                datatable.rows.add(datos).draw();
            } else {
                Swal.fire({ 
                    icon: "info", 
                    title: "Sin resultados", 
                    text: "No se encontraron empleados con esos criterios" 
                });
            }
        }
    } catch (error) {
        console.error('Error al buscar:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de búsqueda", 
            text: "Error al realizar la búsqueda" 
        });
    }
};

// Función auxiliar para resetear el botón
const resetearBoton = () => {
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    
    BtnGuardar.disabled = false;
    loading.style.display = 'none';
    btnText.style.display = 'inline';
};

// Función para actualizar datos (uso público)
const refrescarDatos = () => {
    BuscarEmpleados();
    CargarEstadisticas();
};

// Función para detectar el modo del formulario y ejecutar la acción correcta
const manejarSubmitFormulario = (event) => {
    event.preventDefault();
    
    const id = document.getElementById('empleado_id').value;
    if (id && id.trim() !== '') {
        // Modo edición
        ModificarEmpleado(event);
    } else {
        // Modo creación
        GuardarEmpleado(event);
    }
};

// Funciones globales para el HTML
window.cargarEmpleados = () => {
    BuscarEmpleados();
};

window.limpiarFormulario = () => {
    limpiarTodo();
};

window.buscarEmpleados = buscarEmpleados;

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    // Cargar datos iniciales
    console.log('DOM cargado, iniciando búsqueda de empleados...');
    BuscarEmpleados();
    CargarEstadisticas();
    
    // Eventos de validación
    empleado_codigo.addEventListener('blur', ValidarCodigoEmpleado);
    empleado_codigo.addEventListener('input', ValidarCodigoEmpleado);
    empleado_nombres.addEventListener('blur', ValidarNombres);
    empleado_nombres.addEventListener('input', ValidarNombres);
    empleado_apellidos.addEventListener('blur', ValidarApellidos);
    empleado_apellidos.addEventListener('input', ValidarApellidos);
    empleado_dpi.addEventListener('blur', ValidarDPI);
    empleado_dpi.addEventListener('input', ValidarDPI);
    empleado_correo.addEventListener('blur', ValidarCorreo);
    empleado_correo.addEventListener('input', ValidarCorreo);
    empleado_telefono.addEventListener('blur', ValidarTelefono);
    empleado_telefono.addEventListener('input', ValidarTelefono);
    
    // Eventos de formulario
    FormularioEmpleado.addEventListener('submit', manejarSubmitFormulario);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    
    // Eventos de DataTable
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarEmpleado);
    
    // Evento para búsqueda en tiempo real
    document.getElementById('valor_busqueda').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            buscarEmpleados();
        }
    });
});

// Exportar funciones para uso global si es necesario
window.refrescarEmpleados = refrescarDatos;
window.limpiarFormularioEmpleado = limpiarTodo;