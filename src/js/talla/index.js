// Talla/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioTalla = document.getElementById('formTalla');
const BtnGuardar = document.getElementById('btnSubmit');
const BtnLimpiar = document.getElementById('btnCancelar');
const talla_codigo = document.getElementById('talla_codigo');
const talla_descripcion = document.getElementById('talla_descripcion');

// Validar Código de Talla
const ValidarCodigoTalla = () => {
    const codigo = talla_codigo.value.trim();
    if (codigo.length >= 1 && codigo.length <= 10) {
        talla_codigo.classList.add('is-valid');
        talla_codigo.classList.remove('is-invalid');
    } else if (codigo.length > 0) {
        talla_codigo.classList.add('is-invalid');
        talla_codigo.classList.remove('is-valid');
    } else {
        talla_codigo.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Descripción
const ValidarDescripcion = () => {
    const descripcion = talla_descripcion.value.trim();
    if (descripcion.length > 50) {
        talla_descripcion.classList.add('is-invalid');
        talla_descripcion.classList.remove('is-valid');
        Swal.fire({ 
            icon: "warning", 
            title: "Descripción muy larga", 
            text: "La descripción no puede exceder 50 caracteres" 
        });
    } else if (descripcion.length > 0) {
        talla_descripcion.classList.add('is-valid');
        talla_descripcion.classList.remove('is-invalid');
    } else {
        talla_descripcion.classList.remove('is-valid', 'is-invalid');
    }
};

// Guardar Talla
const GuardarTalla = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Mostrar estado de carga
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    loading.style.display = 'inline';
    btnText.style.display = 'none';

    if (!validarFormulario(FormularioTalla, ['talla_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    if (talla_codigo.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Código inválido", 
            text: "Debe corregir el código antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const body = new FormData(FormularioTalla);
    const url = '/montoya_final_dotacion_ingsoft/Talla/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Talla registrada", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarTallas();
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

// Buscar Tallas
const BuscarTallas = async () => {
    const url = '/montoya_final_dotacion_ingsoft/Talla/obtenerActivasAPI';
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
                text: mensaje || "No hay tallas registradas" 
            });
        }
    } catch (error) {
        console.error('Error completo al cargar datos:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: `Error al cargar las tallas: ${error.message}` 
        });
    }
};

// DataTable Configuración
const datatable = new DataTable('#TablaTallas', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "talla_id", 
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Código", 
            data: "talla_codigo",
            render: (data) => `<span class="badge bg-success fs-6">${data}</span>`
        },
        { 
            title: "Descripción", 
            data: "talla_descripcion",
            render: (data) => data || '<em class="text-muted">Sin descripción</em>'
        },
        {
            title: "Acciones", 
            data: "talla_id",
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
    order: [[1, 'asc']], // Ordenar por código
    columnDefs: [
        { targets: [3], orderable: false } // Deshabilitar ordenamiento en columna de acciones
    ]
});

// Llenar formulario para modificar
const llenarFormulario = (e) => {
    const datos = JSON.parse(e.currentTarget.dataset.json);
    
    // Llenar campos del formulario
    document.getElementById('talla_id').value = datos.talla_id || '';
    document.getElementById('talla_codigo').value = datos.talla_codigo || '';
    document.getElementById('talla_descripcion').value = datos.talla_descripcion || '';
    
    // Cambiar UI para modo edición
    document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i>Modificar Talla';
    BtnGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar';
    BtnLimpiar.style.display = 'block';
    
    // Quitar validaciones previas
    FormularioTalla.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Scroll al formulario
    document.getElementById('formTalla').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
    
    // Focus en el primer campo
    talla_codigo.focus();
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    FormularioTalla.reset();
    document.getElementById('talla_id').value = '';
    
    // Restaurar UI para modo creación
    document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Registrar Talla';
    BtnGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Guardar';
    BtnLimpiar.style.display = 'none';
    
    // Limpiar validaciones
    FormularioTalla.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Limpiar alertas si existen
    const alertsContainer = document.getElementById('alerts');
    if (alertsContainer) {
        alertsContainer.innerHTML = '';
    }
};

// Modificar Talla
const ModificarTalla = async (event) => {
    event.preventDefault();
    
    const id = document.getElementById('talla_id').value;
    if (!id) {
        return GuardarTalla(event);
    }
    
    BtnGuardar.disabled = true;
    
    const loading = BtnGuardar.querySelector('.loading');
    const btnText = BtnGuardar.querySelector('.btn-text');
    loading.style.display = 'inline';
    btnText.style.display = 'none';

    if (!validarFormulario(FormularioTalla, ['talla_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    if (talla_codigo.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Código inválido", 
            text: "Debe corregir el código antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const body = new FormData(FormularioTalla);
    const url = '/montoya_final_dotacion_ingsoft/Talla/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Talla actualizada", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarTallas();
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

// Eliminar Talla
const EliminarTalla = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    const confirmar = await Swal.fire({
        icon: "warning", 
        title: "¿Eliminar talla?", 
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true, 
        confirmButtonText: "Sí, eliminar", 
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        reverseButtons: true
    });

    if (confirmar.isConfirmed) {
        const url = `/montoya_final_dotacion_ingsoft/Talla/eliminarAPI?id=${id}`;
        try {
            const res = await fetch(url);
            const { codigo, mensaje } = await res.json();
            
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado correctamente", 
                    text: mensaje 
                });
                BuscarTallas();
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
                text: "Error al eliminar la talla" 
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
    BuscarTallas();
};

// Función para detectar el modo del formulario y ejecutar la acción correcta
const manejarSubmitFormulario = (event) => {
    event.preventDefault();
    
    const id = document.getElementById('talla_id').value;
    if (id && id.trim() !== '') {
        // Modo edición
        ModificarTalla(event);
    } else {
        // Modo creación
        GuardarTalla(event);
    }
};

// Función global para el botón de refrescar en el HTML
window.cargarTallas = () => {
    BuscarTallas();
};

// Función global para limpiar formulario desde HTML
window.limpiarFormulario = () => {
    limpiarTodo();
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    // Cargar datos iniciales
    console.log('DOM cargado, iniciando búsqueda de datos...');
    BuscarTallas();
    
    // Eventos de validación
    talla_codigo.addEventListener('blur', ValidarCodigoTalla);
    talla_codigo.addEventListener('input', ValidarCodigoTalla);
    talla_descripcion.addEventListener('blur', ValidarDescripcion);
    
    // Eventos de formulario
    FormularioTalla.addEventListener('submit', manejarSubmitFormulario);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    
    // Eventos de DataTable
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarTalla);
});

// Exportar funciones para uso global si es necesario
window.refrescarTallas = refrescarDatos;
window.limpiarFormularioTalla = limpiarTodo;