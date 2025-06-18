// tipodotacion/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioTiposDotacion = document.getElementById('FormularioTiposDotacion');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const tipo_dotacion_nombre = document.getElementById('tipo_dotacion_nombre');

// Validar Nombre de Tipo de Dotación
const ValidarNombreTipo = () => {
    const nombre = tipo_dotacion_nombre.value.trim();
    if (nombre.length >= 2) {
        tipo_dotacion_nombre.classList.add('is-valid');
        tipo_dotacion_nombre.classList.remove('is-invalid');
        return true;
    } else if (nombre.length > 0) {
        tipo_dotacion_nombre.classList.add('is-invalid');
        tipo_dotacion_nombre.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "El nombre debe tener al menos 2 caracteres" 
        });
        return false;
    } else {
        tipo_dotacion_nombre.classList.remove('is-valid', 'is-invalid');
        return true;
    }
};

// Guardar Tipo de Dotación
const GuardarTipoDotacion = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormularioTiposDotacion, ['tipo_dotacion_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (tipo_dotacion_nombre.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "Debe corregir el nombre antes de continuar" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormularioTiposDotacion);
    const url = '/montoya_final_dotacion_ingsoft/tipodotacion/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Tipo de dotación registrado", 
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            limpiarTodo();
            BuscarTiposDotacion();
        } else {
            Swal.fire({ 
                icon: "error", 
                title: "Error", 
                text: mensaje 
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: "No se pudo completar la operación" 
        });
    }
    BtnGuardar.disabled = false;
};

// Buscar Tipos de Dotación
const BuscarTiposDotacion = async () => {
    const url = '/montoya_final_dotacion_ingsoft/tipodotacion/buscarAPI';
    try {
        const res = await fetch(url);
        const { codigo, mensaje, data } = await res.json();
        if (codigo == 1) {
            datatable.clear().draw();
            if (data.length > 0) {
                datatable.rows.add(data).draw();
            }
        } else {
            Swal.fire({ 
                icon: "info", 
                title: "Sin datos", 
                text: mensaje 
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: "No se pudieron cargar los tipos de dotación" 
        });
    }
};

// DataTable Configuración
const datatable = new DataTable('#TableTiposDotacion', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "tipo_dotacion_id", 
            render: (data, type, row, meta) => meta.row + 1,
            width: '8%'
        },
        { 
            title: "Nombre", 
            data: "tipo_dotacion_nombre",
            width: '25%'
        },
        { 
            title: "Descripción", 
            data: "tipo_dotacion_descripcion",
            width: '35%'
        },
        { 
            title: "Fecha de Registro", 
            data: "tipo_dotacion_fecha_registro",
            render: (data) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return '';
            },
            width: '15%'
        },
        {
            title: "Acciones", 
            data: "tipo_dotacion_id",
            render: (id, type, row) => {
                const rowJson = JSON.stringify(row).replace(/'/g, "&#39;");
                return `
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-warning btn-sm modificar" 
                                data-id="${id}" 
                                data-json='${rowJson}'
                                title="Modificar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm eliminar" 
                                data-id="${id}"
                                title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                `;
            },
            orderable: false,
            searchable: false,
            width: '17%'
        }
    ]
});

// Llenar formulario para modificar
const llenarFormulario = (e) => {
    try {
        const datos = JSON.parse(e.currentTarget.dataset.json);
        for (let key in datos) {
            const input = document.getElementById(key);
            if (input) input.value = datos[key];
        }
        BtnGuardar.classList.add('d-none');
        BtnModificar.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        console.error('Error parsing JSON:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error", 
            text: "Error al cargar los datos para modificar" 
        });
    }
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    FormularioTiposDotacion.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    FormularioTiposDotacion.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
};

// Modificar Tipo de Dotación
const ModificarTipoDotacion = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormularioTiposDotacion, ['tipo_dotacion_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos" 
        });
        BtnModificar.disabled = false;
        return;
    }

    if (tipo_dotacion_nombre.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "Debe corregir el nombre antes de continuar" 
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormularioTiposDotacion);
    const url = '/montoya_final_dotacion_ingsoft/tipodotacion/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Tipo de dotación modificado", 
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            limpiarTodo();
            BuscarTiposDotacion();
        } else {
            Swal.fire({ 
                icon: "error", 
                title: "Error", 
                text: mensaje 
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: "No se pudo completar la modificación" 
        });
    }
    BtnModificar.disabled = false;
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
        cancelButtonColor: "#6c757d"
    });

    if (confirmar.isConfirmed) {
        const url = `/montoya_final_dotacion_ingsoft/tipodotacion/eliminar?id=${id}`;
        try {
            const res = await fetch(url);
            const { codigo, mensaje } = await res.json();
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado", 
                    text: mensaje,
                    timer: 2000,
                    showConfirmButton: false
                });
                BuscarTiposDotacion();
            } else {
                Swal.fire({ 
                    icon: "error", 
                    title: "Error", 
                    text: mensaje 
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({ 
                icon: "error", 
                title: "Error de conexión", 
                text: "No se pudo eliminar el tipo de dotación" 
            });
        }
    }
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    BuscarTiposDotacion();
    
    // Validaciones en tiempo real
    tipo_dotacion_nombre.addEventListener('change', ValidarNombreTipo);
    
    // Eventos de formulario
    FormularioTiposDotacion.addEventListener('submit', GuardarTipoDotacion);
    BtnModificar.addEventListener('click', ModificarTipoDotacion);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    
    // Eventos de DataTable
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarTipoDotacion);
    
    console.log('Módulo de tipos de dotación inicializado correctamente');
});