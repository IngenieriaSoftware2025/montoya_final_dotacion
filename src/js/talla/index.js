// talla/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioTallas = document.getElementById('FormularioTallas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const talla_nombre = document.getElementById('talla_nombre');
const talla_descripcion = document.getElementById('talla_descripcion');
const talla_tipo = document.getElementById('talla_tipo');

// Validar Nombre de Talla
const ValidarNombreTalla = () => {
    const nombre = talla_nombre.value.trim();
    if (nombre.length >= 1) {
        talla_nombre.classList.add('is-valid');
        talla_nombre.classList.remove('is-invalid');
        return true;
    } else if (nombre.length > 0) {
        talla_nombre.classList.add('is-invalid');
        talla_nombre.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "El nombre de la talla es obligatorio" 
        });
        return false;
    } else {
        talla_nombre.classList.remove('is-valid', 'is-invalid');
        return true;
    }
};

// Validar Descripción
const ValidarDescripcion = () => {
    const descripcion = talla_descripcion.value.trim();
    if (descripcion.length >= 2) {
        talla_descripcion.classList.add('is-valid');
        talla_descripcion.classList.remove('is-invalid');
        return true;
    } else if (descripcion.length > 0) {
        talla_descripcion.classList.add('is-invalid');
        talla_descripcion.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Descripción inválida", 
            text: "La descripción debe tener al menos 2 caracteres" 
        });
        return false;
    } else {
        talla_descripcion.classList.remove('is-valid', 'is-invalid');
        return true;
    }
};

// Validar Tipo
const ValidarTipo = () => {
    const tipo = talla_tipo.value;
    if (['CALZADO', 'ROPA'].includes(tipo)) {
        talla_tipo.classList.add('is-valid');
        talla_tipo.classList.remove('is-invalid');
        return true;
    } else if (tipo !== '') {
        talla_tipo.classList.add('is-invalid');
        talla_tipo.classList.remove('is-valid');
        return false;
    } else {
        talla_tipo.classList.remove('is-valid', 'is-invalid');
        return true;
    }
};

// Guardar Talla
const GuardarTalla = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Validar campos individuales
    const nombreValido = ValidarNombreTalla();
    const descripcionValida = ValidarDescripcion();
    const tipoValido = ValidarTipo();

    if (!nombreValido || !descripcionValida || !tipoValido) {
        BtnGuardar.disabled = false;
        return;
    }

    if (!validarFormulario(FormularioTallas, ['talla_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormularioTallas);
    const url = '/montoya_final_dotacion_ingsoft/talla/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Talla registrada", 
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            limpiarTodo();
            BuscarTallas();
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

// Buscar Tallas
const BuscarTallas = async () => {
    const url = '/montoya_final_dotacion_ingsoft/talla/buscarAPI';
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
            text: "No se pudieron cargar las tallas" 
        });
    }
};

// DataTable Configuración
const datatable = new DataTable('#TableTallas', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "talla_id", 
            render: (data, type, row, meta) => meta.row + 1,
            width: '8%'
        },
        { 
            title: "Nombre", 
            data: "talla_nombre",
            width: '15%'
        },
        { 
            title: "Descripción", 
            data: "talla_descripcion",
            width: '35%'
        },
        { 
            title: "Tipo", 
            data: "talla_tipo",
            render: (data) => {
                const color = data === 'CALZADO' ? 'primary' : 'success';
                const icon = data === 'CALZADO' ? 'bi-boots' : 'bi-person-fill';
                return `<span class="badge bg-${color}"><i class="bi ${icon} me-1"></i>${data}</span>`;
            },
            width: '20%'
        },
        {
            title: "Acciones", 
            data: "talla_id",
            render: (id, type, row) => `
                <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-warning btn-sm modificar" 
                            data-id="${id}" 
                            data-json='${JSON.stringify(row)}'
                            title="Modificar">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm eliminar" 
                            data-id="${id}"
                            title="Eliminar">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            `,
            orderable: false,
            searchable: false,
            width: '22%'
        }
    ]
});

// Llenar formulario para modificar
const llenarFormulario = (e) => {
    const datos = JSON.parse(e.currentTarget.dataset.json);
    for (let key in datos) {
        const input = document.getElementById(key);
        if (input) input.value = datos[key];
    }
    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    FormularioTallas.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    FormularioTallas.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
};

// Modificar Talla
const ModificarTalla = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    // Validar campos individuales
    const nombreValido = ValidarNombreTalla();
    const descripcionValida = ValidarDescripcion();
    const tipoValido = ValidarTipo();

    if (!nombreValido || !descripcionValida || !tipoValido) {
        BtnModificar.disabled = false;
        return;
    }

    if (!validarFormulario(FormularioTallas, ['talla_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos" 
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormularioTallas);
    const url = '/montoya_final_dotacion_ingsoft/talla/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Talla modificada", 
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            limpiarTodo();
            BuscarTallas();
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
        cancelButtonColor: "#6c757d"
    });

    if (confirmar.isConfirmed) {
        const url = `/montoya_final_dotacion_ingsoft/talla/eliminar?id=${id}`;
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
                BuscarTallas();
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
                text: "No se pudo eliminar la talla" 
            });
        }
    }
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    BuscarTallas();
    
    // Validaciones en tiempo real
    talla_nombre.addEventListener('change', ValidarNombreTalla);
    talla_descripcion.addEventListener('change', ValidarDescripcion);
    talla_tipo.addEventListener('change', ValidarTipo);
    
    // Eventos de formulario
    FormularioTallas.addEventListener('submit', GuardarTalla);
    BtnModificar.addEventListener('click', ModificarTalla);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    
    // Eventos de DataTable
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarTalla);
    
    console.log('Módulo de tallas inicializado correctamente');
});