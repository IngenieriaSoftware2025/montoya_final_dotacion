// dotacioninventario/index.js - CORREGIDO
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
        const url = '/montoya_final_dotacion_ingsoft/tipodotacion/buscarAPI';
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

        // URL CORREGIDA
        const url = `/montoya_final_dotacion_ingsoft/talla/buscarPorTipoAPI?tipo=${tipoTalla}`;
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
    // URL CORREGIDA
    const url = '/montoya_final_dotacion_ingsoft/dotacioninventario/guardarAPI';

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
    // URL CORREGIDA
    const url = '/montoya_final_dotacion_ingsoft/dotacioninventario/buscarAPI';
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
    // URL CORREGIDA
    const url = '/montoya_final_dotacion_ingsoft/dotacioninventario/modificarAPI';

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
        // URL CORREGIDA
        const url = `/montoya_final_dotacion_ingsoft/dotacioninventario/eliminar?id=${id}`;
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