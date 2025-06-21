// TipoDotacion/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioTipoDotacion = document.getElementById('formTipoDotacion');
const BtnGuardar = document.getElementById('btnSubmit');
const BtnLimpiar = document.getElementById('btnCancelar');
const tipo_dotacion_nombre = document.getElementById('tipo_dotacion_nombre');
const tipo_dotacion_descripcion = document.getElementById('tipo_dotacion_descripcion');

// Validar Nombre de Tipo de Dotación
const ValidarNombreTipoDotacion = () => {
    const nombre = tipo_dotacion_nombre.value.trim();
    if (nombre.length >= 2) {
        tipo_dotacion_nombre.classList.add('is-valid');
        tipo_dotacion_nombre.classList.remove('is-invalid');
    } else if (nombre.length > 0) {
        tipo_dotacion_nombre.classList.add('is-invalid');
        tipo_dotacion_nombre.classList.remove('is-valid');
        // NO mostrar SweetAlert aquí - solo cambiar clase CSS
    } else {
        tipo_dotacion_nombre.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Descripción (opcional pero recomendado)
const ValidarDescripcion = () => {
    const descripcion = tipo_dotacion_descripcion.value.trim();
    if (descripcion.length > 500) {
        tipo_dotacion_descripcion.classList.add('is-invalid');
        tipo_dotacion_descripcion.classList.remove('is-valid');
        Swal.fire({ 
            icon: "warning", 
            title: "Descripción muy larga", 
            text: "La descripción no puede exceder 500 caracteres" 
        });
    } else if (descripcion.length > 0) {
        tipo_dotacion_descripcion.classList.add('is-valid');
        tipo_dotacion_descripcion.classList.remove('is-invalid');
    } else {
        tipo_dotacion_descripcion.classList.remove('is-valid', 'is-invalid');
    }
};

// Guardar Tipo de Dotación
const GuardarTipoDotacion = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Mostrar estado de carga
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    loading.style.display = 'inline';
    btnText.style.display = 'none';

    if (!validarFormulario(FormularioTipoDotacion, ['tipo_dotacion_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    if (tipo_dotacion_nombre.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "Debe corregir el nombre antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const body = new FormData(FormularioTipoDotacion);
    const url = '/montoya_final_dotacion_ingsoft/TipoDotacion/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Tipo de dotación registrado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarTiposDotacion();
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

// Buscar Tipos de Dotación
const BuscarTiposDotacion = async () => {
    const url = '/montoya_final_dotacion_ingsoft/TipoDotacion/obtenerActivosAPI';
    console.log('Intentando cargar desde:', url); // Debug
    
    try {
        const res = await fetch(url);
        console.log('Response status:', res.status); // Debug
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const texto = await res.text();
        console.log('Response text:', texto); // Debug
        
        let resultado;
        try {
            resultado = JSON.parse(texto);
        } catch (parseError) {
            console.error('Error parsing JSON:', parseError);
            throw new Error('Respuesta del servidor no es JSON válido');
        }
        
        const { codigo, mensaje, datos } = resultado;
        console.log('Datos recibidos:', { codigo, mensaje, datos }); // Debug
        
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
                text: mensaje || "No hay tipos de dotación registrados" 
            });
        }
    } catch (error) {
        console.error('Error completo al cargar datos:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: `Error al cargar los tipos de dotación: ${error.message}` 
        });
    }
};

// DataTable Configuración
const datatable = new DataTable('#TablaTiposDotacion', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "tipo_dotacion_id", 
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Nombre", 
            data: "tipo_dotacion_nombre",
            render: (data) => `<strong>${data}</strong>`
        },
        { 
            title: "Descripción", 
            data: "tipo_dotacion_descripcion",
            render: (data) => data || '<em class="text-muted">Sin descripción</em>'
        },
        { 
            title: "Fecha de Registro", 
            data: "tipo_dotacion_fecha_registro",
            render: (data) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
                return '<em class="text-muted">Sin fecha</em>';
            }
        },
        {
            title: "Acciones", 
            data: "tipo_dotacion_id",
            orderable: false,
            render: (id, type, row) => `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-primary modificar" 
                            data-id="${id}" 
                            data-json='${JSON.stringify(row)}'
                            title="Modificar registro">
                        <i class="fas fa-edit me-1"></i>✏️
                    </button>
                    <button class="btn btn-outline-danger eliminar" 
                            data-id="${id}"
                            title="Eliminar registro">
                        <i class="fas fa-trash me-1"></i>🗑️
                    </button>
                </div>
            `
        }
    ],
    responsive: true,
    pageLength: 10,
    lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
    order: [[1, 'asc']], // Ordenar por nombre
    columnDefs: [
        { targets: [4], orderable: false } // Deshabilitar ordenamiento en columna de acciones
    ]
});

// Llenar formulario para modificar
const llenarFormulario = (e) => {
    const datos = JSON.parse(e.currentTarget.dataset.json);
    
    // Llenar campos del formulario
    document.getElementById('tipo_dotacion_id').value = datos.tipo_dotacion_id || '';
    document.getElementById('tipo_dotacion_nombre').value = datos.tipo_dotacion_nombre || '';
    document.getElementById('tipo_dotacion_descripcion').value = datos.tipo_dotacion_descripcion || '';
    
    // Cambiar UI para modo edición
    document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i>Modificar Tipo de Dotación';
    BtnGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar';
    BtnLimpiar.style.display = 'block';
    
    // Quitar validaciones previas
    FormularioTipoDotacion.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Scroll al formulario
    document.getElementById('formTipoDotacion').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
    
    // Focus en el primer campo
    tipo_dotacion_nombre.focus();
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    FormularioTipoDotacion.reset();
    document.getElementById('tipo_dotacion_id').value = '';
    
    // Restaurar UI para modo creación
    document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Registrar Tipo de Dotación';
    BtnGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Guardar';
    BtnLimpiar.style.display = 'none';
    
    // Limpiar validaciones
    FormularioTipoDotacion.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Limpiar alertas si existen
    const alertsContainer = document.getElementById('alerts');
    if (alertsContainer) {
        alertsContainer.innerHTML = '';
    }
};

// Modificar Tipo de Dotación
const ModificarTipoDotacion = async (event) => {
    event.preventDefault();
    
    const id = document.getElementById('tipo_dotacion_id').value;
    if (!id) {
        // Si no hay ID, es una creación normal
        return GuardarTipoDotacion(event);
    }
    
    BtnGuardar.disabled = true;
    
    // Mostrar estado de carga
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    loading.style.display = 'inline';
    btnText.style.display = 'none';

    if (!validarFormulario(FormularioTipoDotacion, ['tipo_dotacion_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    if (tipo_dotacion_nombre.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "Debe corregir el nombre antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const body = new FormData(FormularioTipoDotacion);
    const url = '/montoya_final_dotacion_ingsoft/TipoDotacion/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Tipo de dotación actualizado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarTiposDotacion();
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

// Eliminar Tipo de Dotación
const EliminarTipoDotacion = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    const confirmar = await Swal.fire({
        icon: "warning", 
        title: "¿Eliminar tipo de dotación?", 
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true, 
        confirmButtonText: "Sí, eliminar", 
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        reverseButtons: true
    });

    if (confirmar.isConfirmed) {
        const url = `/montoya_final_dotacion_ingsoft/TipoDotacion/eliminarAPI?id=${id}`;
        try {
            const res = await fetch(url);
            const { codigo, mensaje } = await res.json();
            
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado correctamente", 
                    text: mensaje 
                });
                BuscarTiposDotacion();
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
                text: "Error al eliminar el tipo de dotación" 
            });
        }
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
    BuscarTiposDotacion();
};

// Función para detectar el modo del formulario y ejecutar la acción correcta
const manejarSubmitFormulario = (event) => {
    event.preventDefault();
    
    const id = document.getElementById('tipo_dotacion_id').value;
    if (id && id.trim() !== '') {
        // Modo edición
        ModificarTipoDotacion(event);
    } else {
        // Modo creación
        GuardarTipoDotacion(event);
    }
};

// Función global para el botón de refrescar en el HTML
window.cargarTiposDotacion = () => {
    BuscarTiposDotacion();
};

// Función global para limpiar formulario desde HTML
window.limpiarFormulario = () => {
    limpiarTodo();
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    // Cargar datos iniciales
    console.log('DOM cargado, iniciando búsqueda de datos...');
    BuscarTiposDotacion();
    
    // Eventos de validación
    tipo_dotacion_nombre.addEventListener('blur', ValidarNombreTipoDotacion);
    tipo_dotacion_nombre.addEventListener('input', ValidarNombreTipoDotacion);
    tipo_dotacion_descripcion.addEventListener('blur', ValidarDescripcion);
    
    // Eventos de formulario
    FormularioTipoDotacion.addEventListener('submit', manejarSubmitFormulario);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    
    // Eventos de DataTable
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarTipoDotacion);
});

// Exportar funciones para uso global si es necesario
window.refrescarTiposDotacion = refrescarDatos;
window.limpiarFormularioTipoDotacion = limpiarTodo;