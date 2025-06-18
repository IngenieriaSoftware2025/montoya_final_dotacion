// empleado/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioEmpleados = document.getElementById('FormularioEmpleados');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const empleado_nombres = document.getElementById('empleado_nombres');
const empleado_apellidos = document.getElementById('empleado_apellidos');
const empleado_dpi = document.getElementById('empleado_dpi');
const empleado_correo = document.getElementById('empleado_correo');

// Validar Nombres
const ValidarNombres = () => {
    const nombres = empleado_nombres.value.trim();
    if (nombres.length >= 2) {
        empleado_nombres.classList.add('is-valid');
        empleado_nombres.classList.remove('is-invalid');
    } else if (nombres.length > 0) {
        empleado_nombres.classList.add('is-invalid');
        empleado_nombres.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Nombres inválidos", 
            text: "Los nombres deben tener al menos 2 caracteres" 
        });
    } else {
        empleado_nombres.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Apellidos
const ValidarApellidos = () => {
    const apellidos = empleado_apellidos.value.trim();
    if (apellidos.length >= 2) {
        empleado_apellidos.classList.add('is-valid');
        empleado_apellidos.classList.remove('is-invalid');
    } else if (apellidos.length > 0) {
        empleado_apellidos.classList.add('is-invalid');
        empleado_apellidos.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Apellidos inválidos", 
            text: "Los apellidos deben tener al menos 2 caracteres" 
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
        return;
    }
    
    if (!/^\d+$/.test(dpi)) {
        empleado_dpi.classList.add('is-invalid');
        empleado_dpi.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "DPI inválido", 
            text: "El DPI debe contener solo números" 
        });
        return;
    }
    
    if (dpi.length === 13) {
        empleado_dpi.classList.add('is-valid');
        empleado_dpi.classList.remove('is-invalid');
    } else {
        empleado_dpi.classList.add('is-invalid');
        empleado_dpi.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "DPI inválido", 
            text: "El DPI debe tener exactamente 13 dígitos" 
        });
    }
};

// Validar Correo
const ValidarCorreo = () => {
    const correo = empleado_correo.value.trim();
    
    if (correo.length === 0) {
        empleado_correo.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (emailRegex.test(correo)) {
        empleado_correo.classList.add('is-valid');
        empleado_correo.classList.remove('is-invalid');
    } else {
        empleado_correo.classList.add('is-invalid');
        empleado_correo.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Correo inválido", 
            text: "Ingrese un correo electrónico válido" 
        });
    }
};

// Guardar Empleado
const GuardarEmpleado = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormularioEmpleados, ['empleado_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (empleado_nombres.classList.contains('is-invalid') || 
        empleado_apellidos.classList.contains('is-invalid') ||
        empleado_dpi.classList.contains('is-invalid') ||
        empleado_correo.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Datos inválidos", 
            text: "Debe corregir los campos marcados antes de continuar" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormularioEmpleados);
    const url = '/empresa_celulares/empleado/guardarAPI';

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

// Buscar Empleados
const BuscarEmpleados = async () => {
    const url = '/empresa_celulares/empleado/buscarAPI';
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
            text: "Error al cargar los empleados" 
        });
    }
};

// DataTable Configuración
const datatable = new DataTable('#TableEmpleados', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "empleado_id", 
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Nombre Completo", 
            data: null,
            render: (data, type, row) => `${row.empleado_nombres} ${row.empleado_apellidos}`
        },
        { 
            title: "DPI", 
            data: "empleado_dpi" 
        },
        { 
            title: "Puesto", 
            data: "empleado_puesto" 
        },
        { 
            title: "Departamento", 
            data: "empleado_departamento" 
        },
        { 
            title: "Teléfono", 
            data: "empleado_telefono" 
        },
        { 
            title: "Correo", 
            data: "empleado_correo" 
        },
        {
            title: "Acciones", 
            data: "empleado_id",
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
    FormularioEmpleados.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    FormularioEmpleados.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
};

// Modificar Empleado
const ModificarEmpleado = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormularioEmpleados, ['empleado_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos" 
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormularioEmpleados);
    const url = '/empresa_celulares/empleado/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Empleado modificado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarEmpleados();
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
        cancelButtonColor: "#6c757d"
    });

    if (confirmar.isConfirmed) {
        const url = `/empresa_celulares/empleado/eliminar?id=${id}`;
        try {
            const res = await fetch(url);
            const { codigo, mensaje } = await res.json();
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado", 
                    text: mensaje 
                });
                BuscarEmpleados();
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
                text: "Error al eliminar el empleado" 
            });
        }
    }
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    BuscarEmpleados();
    empleado_nombres.addEventListener('change', ValidarNombres);
    empleado_apellidos.addEventListener('change', ValidarApellidos);
    empleado_dpi.addEventListener('change', ValidarDPI);
    empleado_correo.addEventListener('change', ValidarCorreo);
    FormularioEmpleados.addEventListener('submit', GuardarEmpleado);
    BtnModificar.addEventListener('click', ModificarEmpleado);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarEmpleado);
});
