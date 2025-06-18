
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

// Validar Nombre de Talla
const ValidarNombreTalla = () => {
    const nombre = talla_nombre.value.trim();
    if (nombre.length >= 1) {
        talla_nombre.classList.add('is-valid');
        talla_nombre.classList.remove('is-invalid');
    } else if (nombre.length > 0) {
        talla_nombre.classList.add('is-invalid');
        talla_nombre.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Nombre inválido", 
            text: "El nombre de la talla es obligatorio" 
        });
    } else {
        talla_nombre.classList.remove('is-valid', 'is-invalid');
    }
};

// Guardar Talla
const GuardarTalla = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

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
        console.error(error);
        Swal.fire({ 
            icon: "error", 
            title: "Error", 
            text: "Ocurrió un error al procesar la solicitud" 
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
            datatable.rows.add(data).draw();
        } else {
            Swal.fire({ 
                icon: "info", 
                title: "Error", 
                text: mensaje 
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ 
            icon: "error", 
            title: "Error", 
            text: "Error al cargar las tallas" 
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
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Nombre", 
            data: "talla_nombre" 
        },
        { 
            title: "Descripción", 
            data: "talla_descripcion" 
        },
        { 
            title: "Tipo", 
            data: "talla_tipo",
            render: (data) => {
                const color = data === 'CALZADO' ? 'primary' : 'success';
                return `<span class="badge bg-${color}">${data}</span>`;
            }
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
                        ✏️
                    </button>
                    <button class="btn btn-danger btn-sm eliminar" 
                            data-id="${id}"
                            title="Eliminar">
                        🗑️
                    </button>
                </div>
            `
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
                text: mensaje 
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
            title: "Error", 
            text: "Ocurrió un error al procesar la solicitud" 
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
                    text: mensaje 
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
                title: "Error", 
                text: "Error al eliminar la talla" 
            });
        }
    }
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    BuscarTallas();
    talla_nombre.addEventListener('change', ValidarNombreTalla);
    FormularioTallas.addEventListener('submit', GuardarTalla);
    BtnModificar.addEventListener('click', ModificarTalla);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarTalla);
});
