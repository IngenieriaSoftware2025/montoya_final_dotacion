<!-- ========================================= -->
<!-- VISTA: talla/index.php -->
<!-- ========================================= -->

<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 800px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-rulers me-2"></i>Registro de Tallas
  </h3>

  <form name="FormularioTallas" id="FormularioTallas" method="POST">
    <input type="hidden" id="talla_id" name="talla_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="talla_nombre" class="form-label">
          Nombre de la Talla <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="talla_nombre" 
               name="talla_nombre" 
               required 
               placeholder="Ej. 35, XS, M, L"
               maxlength="10">
      </div>
      <div class="col-md-6">
        <label for="talla_tipo" class="form-label">
          Tipo de Talla <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="talla_tipo" name="talla_tipo" required>
          <option value="">Seleccione el tipo</option>
          <option value="CALZADO">CALZADO</option>
          <option value="ROPA">ROPA</option>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label for="talla_descripcion" class="form-label">
        Descripción <span class="text-danger">*</span>
      </label>
      <input type="text" 
             class="form-control" 
             id="talla_descripcion" 
             name="talla_descripcion" 
             required
             placeholder="Ej. Talla 35, Extra Small, Medium..."
             maxlength="50">
    </div>

    <div class="row justify-content-center mt-4 g-2">
      <div class="col-auto">
        <button type="submit" class="btn btn-success px-4" id="BtnGuardar">
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-warning d-none px-4" id="BtnModificar">
          <i class="bi bi-pencil-square me-1"></i> Modificar
        </button>
      </div>
      <div class="col-auto">
        <button type="reset" class="btn btn-secondary px-4" id="BtnLimpiar">
          <i class="bi bi-eraser me-1"></i> Limpiar
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Tabla de tallas -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Tallas Registradas
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableTallas">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/talla/index.js') ?>"></script>

<!-- ========================================= -->
<!-- JAVASCRIPT: talla/index.js -->
<!-- ========================================= -->

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
    const url = '/empresa_celulares/talla/guardarAPI';

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
    const url = '/empresa_celulares/talla/buscarAPI';
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
    const url = '/empresa_celulares/talla/modificarAPI';

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
        const url = `/empresa_celulares/talla/eliminar?id=${id}`;
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

<!-- ========================================= -->
<!-- VISTA: empleado/index.php -->
<!-- ========================================= -->

<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 900px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-people me-2"></i>Registro de Empleados
  </h3>

  <form name="FormularioEmpleados" id="FormularioEmpleados" method="POST">
    <input type="hidden" id="empleado_id" name="empleado_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_nombres" class="form-label">
          Nombres <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_nombres" 
               name="empleado_nombres" 
               required 
               placeholder="Ej. Juan Carlos"
               maxlength="100">
      </div>
      <div class="col-md-6">
        <label for="empleado_apellidos" class="form-label">
          Apellidos <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_apellidos" 
               name="empleado_apellidos" 
               required 
               placeholder="Ej. García López"
               maxlength="100">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_dpi" class="form-label">
          DPI
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_dpi" 
               name="empleado_dpi" 
               placeholder="1234567890123"
               maxlength="13">
      </div>
      <div class="col-md-6">
        <label for="empleado_telefono" class="form-label">
          Teléfono
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_telefono" 
               name="empleado_telefono" 
               placeholder="12345678"
               maxlength="15">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_puesto" class="form-label">
          Puesto
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_puesto" 
               name="empleado_puesto" 
               placeholder="Ej. Operario, Supervisor"
               maxlength="50">
      </div>
      <div class="col-md-6">
        <label for="empleado_departamento" class="form-label">
          Departamento
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_departamento" 
               name="empleado_departamento" 
               placeholder="Ej. Producción, Calidad"
               maxlength="50">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_correo" class="form-label">
          Correo Electrónico
        </label>
        <input type="email" 
               class="form-control" 
               id="empleado_correo" 
               name="empleado_correo" 
               placeholder="empleado@empresa.com"
               maxlength="100">
      </div>
      <div class="col-md-6">
        <label for="empleado_fecha_ingreso" class="form-label">
          Fecha de Ingreso
        </label>
        <input type="date" 
               class="form-control" 
               id="empleado_fecha_ingreso" 
               name="empleado_fecha_ingreso">
      </div>
    </div>

    <div class="mb-3">
      <label for="empleado_direccion" class="form-label">
        Dirección
      </label>
      <textarea class="form-control" 
                id="empleado_direccion" 
                name="empleado_direccion" 
                rows="3" 
                placeholder="Dirección completa del empleado..."
                maxlength="500"></textarea>
    </div>

    <div class="row justify-content-center mt-4 g-2">
      <div class="col-auto">
        <button type="submit" class="btn btn-success px-4" id="BtnGuardar">
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-warning d-none px-4" id="BtnModificar">
          <i class="bi bi-pencil-square me-1"></i> Modificar
        </button>
      </div>
      <div class="col-auto">
        <button type="reset" class="btn btn-secondary px-4" id="BtnLimpiar">
          <i class="bi bi-eraser me-1"></i> Limpiar
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Tabla de empleados -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Empleados Registrados
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableEmpleados">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/empleado/index.js') ?>"></script>

<!-- ========================================= -->
<!-- JAVASCRIPT: empleado/index.js -->
<!-- ========================================= -->

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

<!-- ========================================= -->
<!-- VISTA: dotacioninventario/index.php -->
<!-- ========================================= -->

<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 900px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-boxes me-2"></i>Registro de Inventario de Dotaciones
  </h3>

  <form name="FormularioDotaciones" id="FormularioDotaciones" method="POST">
    <input type="hidden" id="dotacion_inv_id" name="dotacion_inv_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="tipo_dotacion_id" class="form-label">
          Tipo de Dotación <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="tipo_dotacion_id" name="tipo_dotacion_id" required>
          <option value="">Seleccione un tipo</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="talla_id" class="form-label">
          Talla <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="talla_id" name="talla_id" required>
          <option value="">Primero seleccione el tipo</option>
        </select>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label for="dotacion_inv_marca" class="form-label">
          Marca <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_marca" 
               name="dotacion_inv_marca" 
               required 
               placeholder="Ej. VULCANO, MILITAR"
               maxlength="50">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_modelo" class="form-label">
          Modelo <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_modelo" 
               name="dotacion_inv_modelo" 
               required 
               placeholder="Ej. Militar Industrial"
               maxlength="50">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_color" class="form-label">
          Color
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_color" 
               name="dotacion_inv_color" 
               placeholder="Ej. Negro, Verde Olivo"
               maxlength="30">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label for="dotacion_inv_cantidad_inicial" class="form-label">
          Cantidad Inicial <span class="text-danger">*</span>
        </label>
        <input type="number" 
               class="form-control" 
               id="dotacion_inv_cantidad_inicial" 
               name="dotacion_inv_cantidad_inicial" 
               required 
               min="1"
               placeholder="Ej. 50">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_cantidad_minima" class="form-label">
          Stock Mínimo
        </label>
        <input type="number" 
               class="form-control" 
               id="dotacion_inv_cantidad_minima" 
               name="dotacion_inv_cantidad_minima" 
               value="5"
               min="0"
               placeholder="Ej. 5">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_precio_unitario" class="form-label">
          Precio Unitario (Q)
        </label>
        <input type="number" 
               class="form-control" 
               id="dotacion_inv_precio_unitario" 
               name="dotacion_inv_precio_unitario" 
               step="0.01"
               min="0"
               placeholder="Ej. 650.00">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="dotacion_inv_proveedor" class="form-label">
          Proveedor
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_proveedor" 
               name="dotacion_inv_proveedor" 
               placeholder="Ej. Industria Militar"
               maxlength="100">
      </div>
      <div class="col-md-6">
        <label for="dotacion_inv_fecha_ingreso" class="form-label">
          Fecha de Ingreso
        </label>
        <input type="date" 
               class="form-control" 
               id="dotacion_inv_fecha_ingreso" 
               name="dotacion_inv_fecha_ingreso">
      </div>
    </div>

    <div class="mb-3">
      <label for="dotacion_inv_observaciones" class="form-label">
        Observaciones
      </label>
      <textarea class="form-control" 
                id="dotacion_inv_observaciones" 
                name="dotacion_inv_observaciones" 
                rows="3" 
                placeholder="Observaciones adicionales..."
                maxlength="500"></textarea>
    </div>

    <div class="row justify-content-center mt-4 g-2">
      <div class="col-auto">
        <button type="submit" class="btn btn-success px-4" id="BtnGuardar">
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-warning d-none px-4" id="BtnModificar">
          <i class="bi bi-pencil-square me-1"></i> Modificar
        </button>
      </div>
      <div class="col-auto">
        <button type="reset" class="btn btn-secondary px-4" id="BtnLimpiar">
          <i class="bi bi-eraser me-1"></i> Limpiar
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Tabla de inventario -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Inventario de Dotaciones Registrado
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableDotaciones">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/dotacioninventario/index.js') ?>"></script>

<!-- ========================================= -->
<!-- JAVASCRIPT: dotacioninventario/index.js -->
<!-- ========================================= -->

// dotacioninventario/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioDotaciones = document.getElementById('FormularioDotaciones');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const tipo_dotacion_id = document.getElementById('tipo_dotacion_id');
const talla_id = document.getElementById('talla_id');
const dotacion_inv_marca = document.getElementById('dotacion_inv_marca');
const dotacion_inv_cantidad_inicial = document.getElementById('dotacion_inv_cantidad_inicial');

// Validar Marca
const ValidarMarca = () => {
    const marca = dotacion_inv_marca.value.trim();
    if (marca.length >= 2) {
        dotacion_inv_marca.classList.add('is-valid');
        dotacion_inv_marca.classList.remove('is-invalid');
    } else if (marca.length > 0) {
        dotacion_inv_marca.classList.add('is-invalid');
        dotacion_inv_marca.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Marca inválida", 
            text: "La marca debe tener al menos 2 caracteres" 
        });
    } else {
        dotacion_inv_marca.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Cantidad
const ValidarCantidad = () => {
    const cantidad = parseInt(dotacion_inv_cantidad_inicial.value);
    if (cantidad > 0) {
        dotacion_inv_cantidad_inicial.classList.add('is-valid');
        dotacion_inv_cantidad_inicial.classList.remove('is-invalid');
    } else if (dotacion_inv_cantidad_inicial.value !== '') {
        dotacion_inv_cantidad_inicial.classList.add('is-invalid');
        dotacion_inv_cantidad_inicial.classList.remove('is-valid');
        Swal.fire({ 
            icon: "error", 
            title: "Cantidad inválida", 
            text: "La cantidad debe ser mayor a 0" 
        });
    } else {
        dotacion_inv_cantidad_inicial.classList.remove('is-valid', 'is-invalid');
    }
};

// Cargar Tipos de Dotación
const CargarTiposDotacion = async () => {
    try {
        const url = '/empresa_celulares/tipodotacion/buscarAPI';
        const respuesta = await fetch(url);
        const { codigo, data } = await respuesta.json();
        
        if (codigo == 1) {
            tipo_dotacion_id.innerHTML = '<option value="">Seleccione un tipo</option>';
            data.forEach(tipo => {
                tipo_dotacion_id.innerHTML += `<option value="${tipo.tipo_dotacion_id}">${tipo.tipo_dotacion_nombre}</option>`;
            });
        }
    } catch (error) {
        console.error('Error al cargar tipos de dotación:', error);
    }
};

// Cargar Tallas según el tipo seleccionado
const CargarTallas = async () => {
    const tipoSeleccionado = tipo_dotacion_id.value;
    talla_id.innerHTML = '<option value="">Seleccione una talla</option>';
    
    if (!tipoSeleccionado) {
        talla_id.innerHTML = '<option value="">Primero seleccione el tipo</option>';
        return;
    }

    try {
        // Determinar el tipo de talla según el tipo de dotación
        let tipoTalla = 'ROPA'; // Por defecto para camisas y pantalones
        if (tipo_dotacion_id.options[tipo_dotacion_id.selectedIndex].text === 'BOTAS') {
            tipoTalla = 'CALZADO';
        }

        const url = `/empresa_celulares/talla/buscarPorTipoAPI?tipo=${tipoTalla}`;
        const respuesta = await fetch(url);
        const { codigo, data } = await respuesta.json();
        
        if (codigo == 1) {
            data.forEach(talla => {
                talla_id.innerHTML += `<option value="${talla.talla_id}">${talla.talla_nombre} - ${talla.talla_descripcion}</option>`;
            });
        }
    } catch (error) {
        console.error('Error al cargar tallas:', error);
        Swal.fire({ 
            icon: "error", 
            title: "Error", 
            text: "Error al cargar las tallas disponibles" 
        });
    }
};

// Guardar Inventario
const GuardarInventario = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormularioDotaciones, ['dotacion_inv_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (dotacion_inv_marca.classList.contains('is-invalid') || 
        dotacion_inv_cantidad_inicial.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Datos inválidos", 
            text: "Debe corregir los campos marcados antes de continuar" 
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormularioDotaciones);
    const url = '/empresa_celulares/dotacioninventario/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Inventario registrado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarInventario();
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

// Buscar Inventario
const BuscarInventario = async () => {
    const url = '/empresa_celulares/dotacioninventario/buscarAPI';
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
            text: "Error al cargar el inventario" 
        });
    }
};

// DataTable Configuración
const datatable = new DataTable('#TableDotaciones', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "dotacion_inv_id", 
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Tipo", 
            data: "tipo_dotacion_nombre" 
        },
        { 
            title: "Talla", 
            data: "talla_nombre" 
        },
        { 
            title: "Marca", 
            data: "dotacion_inv_marca" 
        },
        { 
            title: "Modelo", 
            data: "dotacion_inv_modelo" 
        },
        { 
            title: "Color", 
            data: "dotacion_inv_color" 
        },
        { 
            title: "Stock Actual", 
            data: "dotacion_inv_cantidad_actual",
            render: (data, type, row) => {
                let clase = 'badge bg-success';
                if (data <= row.dotacion_inv_cantidad_minima) {
                    clase = 'badge bg-danger';
                } else if (data <= (row.dotacion_inv_cantidad_minima * 2)) {
                    clase = 'badge bg-warning';
                }
                return `<span class="${clase}">${data}</span>`;
            }
        },
        { 
            title: "Stock Mínimo", 
            data: "dotacion_inv_cantidad_minima" 
        },
        { 
            title: "Precio", 
            data: "dotacion_inv_precio_unitario",
            render: (data) => data ? `Q${parseFloat(data).toFixed(2)}` : 'N/A'
        },
        { 
            title: "Proveedor", 
            data: "dotacion_inv_proveedor" 
        },
        {
            title: "Acciones", 
            data: "dotacion_inv_id",
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
const llenarFormulario = async (e) => {
    const datos = JSON.parse(e.currentTarget.dataset.json);
    
    // Llenar campos básicos
    for (let key in datos) {
        const input = document.getElementById(key);
        if (input) input.value = datos[key];
    }

    // Esperar a que se carguen las tallas del tipo seleccionado
    await CargarTallas();
    
    // Seleccionar la talla correcta
    if (datos.talla_id) {
        talla_id.value = datos.talla_id;
    }

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    FormularioDotaciones.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    FormularioDotaciones.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
    
    // Resetear tallas
    talla_id.innerHTML = '<option value="">Primero seleccione el tipo</option>';
};

// Modificar Inventario
const ModificarInventario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormularioDotaciones)) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos" 
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormularioDotaciones);
    const url = '/empresa_celulares/dotacioninventario/modificarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body });
        const { codigo, mensaje } = await respuesta.json();
        if (codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Inventario modificado", 
                text: mensaje 
            });
            limpiarTodo();
            BuscarInventario();
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

// Eliminar Inventario
const EliminarInventario = async (e) => {
    const id = e.currentTarget.dataset.id;
    const confirmar = await Swal.fire({
        icon: "warning", 
        title: "¿Eliminar inventario?", 
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true, 
        confirmButtonText: "Sí, eliminar", 
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d"
    });

    if (confirmar.isConfirmed) {
        const url = `/empresa_celulares/dotacioninventario/eliminar?id=${id}`;
        try {
            const res = await fetch(url);
            const { codigo, mensaje } = await res.json();
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado", 
                    text: mensaje 
                });
                BuscarInventario();
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
                text: "Error al eliminar el inventario" 
            });
        }
    }
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    CargarTiposDotacion();
    BuscarInventario();
    
    dotacion_inv_marca.addEventListener('change', ValidarMarca);
    dotacion_inv_cantidad_inicial.addEventListener('change', ValidarCantidad);
    tipo_dotacion_id.addEventListener('change', CargarTallas);
    FormularioDotaciones.addEventListener('submit', GuardarInventario);
    BtnModificar.addEventListener('click', ModificarInventario);
    BtnLimpiar.addEventListener('click', limpiarTodo);
    datatable.on('click', '.modificar', llenarFormulario);
    datatable.on('click', '.eliminar', EliminarInventario);
});