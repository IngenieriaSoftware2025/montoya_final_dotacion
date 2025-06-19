// dotacionentrega/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioEntregas = document.getElementById('FormularioEntregas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnAgregarDetalle = document.getElementById('BtnAgregarDetalle');
const empleado_id = document.getElementById('empleado_id');
const dotacion_inv_id = document.getElementById('dotacion_inv_id');
const cantidad_entrega = document.getElementById('cantidad_entrega');
const observaciones_detalle = document.getElementById('observaciones_detalle');

// Variables globales
let detallesEntrega = [];
let datatable;
let datatableDetalles;
let inventarioDisponible = [];

// Cargar Empleados
const CargarEmpleados = async () => {
    try {
        const url = '/montoya_final_dotacion_ingsoft/empleado/buscarAPI';
        const respuesta = await fetch(url);
        const { codigo, data } = await respuesta.json();
        
        if (codigo == 1) {
            empleado_id.innerHTML = '<option value="">Seleccione un empleado</option>';
            data.forEach(empleado => {
                empleado_id.innerHTML += `<option value="${empleado.empleado_id}">${empleado.empleado_nombres} ${empleado.empleado_apellidos}</option>`;
            });
        }
    } catch (error) {
        console.error('Error al cargar empleados:', error);
    }
};

// Cargar Inventario Disponible
const CargarInventarioDisponible = async () => {
    try {
        const url = '/montoya_final_dotacion_ingsoft/dotacioninventario/buscarDisponibleAPI';
        const respuesta = await fetch(url);
        const { codigo, data } = await respuesta.json();
        
        if (codigo == 1) {
            inventarioDisponible = data;
            dotacion_inv_id.innerHTML = '<option value="">Seleccione un artículo</option>';
            data.forEach(item => {
                dotacion_inv_id.innerHTML += `
                    <option value="${item.dotacion_inv_id}" data-stock="${item.dotacion_inv_cantidad_actual}">
                        ${item.tipo_dotacion_nombre} - ${item.talla_nombre} (${item.dotacion_inv_marca}) - Stock: ${item.dotacion_inv_cantidad_actual}
                    </option>
                `;
            });
        }
    } catch (error) {
        console.error('Error al cargar inventario:', error);
    }
};

// Verificar Límite Anual del Empleado
const VerificarLimiteEmpleado = async () => {
    if (!empleado_id.value) return;

    try {
        const url = `/montoya_final_dotacion_ingsoft/dotacionentrega/verificarLimiteAPI?empleado_id=${empleado_id.value}&año=${new Date().getFullYear()}`;
        const respuesta = await fetch(url);
        const { codigo, data } = await respuesta.json();

        if (codigo == 1) {
            const infoElement = document.getElementById('info-limite-empleado');
            if (infoElement) {
                if (data.puede_recibir) {
                    infoElement.innerHTML = `
                        <div class="alert alert-success">
                            <strong>✅ Puede recibir dotación</strong><br>
                            Entregas realizadas: ${data.entregas_realizadas}/3<br>
                            Entregas disponibles: ${data.entregas_disponibles}
                        </div>
                    `;
                } else {
                    infoElement.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>❌ Límite alcanzado</strong><br>
                            El empleado ya recibió sus 3 entregas anuales
                        </div>
                    `;
                }
            }
        }
    } catch (error) {
        console.error('Error al verificar límite:', error);
    }
};

// Validar Stock Disponible
const ValidarStock = () => {
    if (!dotacion_inv_id.value || !cantidad_entrega.value) return;

    const stockDisponible = parseInt(dotacion_inv_id.options[dotacion_inv_id.selectedIndex].dataset.stock || 0);
    const cantidadSolicitada = parseInt(cantidad_entrega.value);

    if (cantidadSolicitada > stockDisponible) {
        cantidad_entrega.classList.add('is-invalid');
        Swal.fire({
            icon: "warning",
            title: "Stock insuficiente",
            text: `Solo hay ${stockDisponible} unidades disponibles`
        });
        return false;
    } else {
        cantidad_entrega.classList.remove('is-invalid');
        cantidad_entrega.classList.add('is-valid');
        return true;
    }
};

// Agregar Detalle a la Entrega
const AgregarDetalle = () => {
    if (!dotacion_inv_id.value || !cantidad_entrega.value || cantidad_entrega.value <= 0) {
        Swal.fire({
            icon: "warning",
            title: "Datos incompletos",
            text: "Complete todos los campos del detalle"
        });
        return;
    }

    if (!ValidarStock()) return;

    // Verificar si ya existe el mismo inventario
    const existe = detallesEntrega.find(detalle => 
        detalle.dotacion_inv_id == dotacion_inv_id.value
    );

    if (existe) {
        Swal.fire({
            icon: "warning",
            title: "Artículo duplicado",
            text: "Ya existe este artículo en la entrega"
        });
        return;
    }

    const inventarioItem = inventarioDisponible.find(item => 
        item.dotacion_inv_id == dotacion_inv_id.value
    );

    const detalle = {
        dotacion_inv_id: dotacion_inv_id.value,
        tipo_dotacion_nombre: inventarioItem.tipo_dotacion_nombre,
        talla_nombre: inventarioItem.talla_nombre,
        dotacion_inv_marca: inventarioItem.dotacion_inv_marca,
        dotacion_inv_modelo: inventarioItem.dotacion_inv_modelo,
        cantidad: parseInt(cantidad_entrega.value),
        stock_disponible: inventarioItem.dotacion_inv_cantidad_actual,
        observaciones: observaciones_detalle.value.trim()
    };

    detallesEntrega.push(detalle);
    ActualizarTablaDetalles();
    LimpiarDetalle();
};

// Actualizar tabla de detalles
const ActualizarTablaDetalles = () => {
    datatableDetalles.clear().draw();
    datatableDetalles.rows.add(detallesEntrega).draw();
};

// Limpiar formulario de detalle
const LimpiarDetalle = () => {
    dotacion_inv_id.value = '';
    cantidad_entrega.value = '';
    observaciones_detalle.value = '';
    cantidad_entrega.classList.remove('is-valid', 'is-invalid');
};

// Eliminar detalle
const EliminarDetalle = (index) => {
    detallesEntrega.splice(index, 1);
    ActualizarTablaDetalles();
};

// Guardar Entrega
const GuardarEntrega = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormularioEntregas, ['entrega_id'])) {
        Swal.fire({
            icon: "info",
            title: "Formulario incompleto",
            text: "Complete todos los campos requeridos"
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (detallesEntrega.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Sin detalles",
            text: "Debe agregar al menos un artículo a la entrega"
        });
        BtnGuardar.disabled = false;
        return;
    }

    const formData = new FormData(FormularioEntregas);
    formData.append('detalles', JSON.stringify(detallesEntrega));

    const url = '/montoya_final_dotacion_ingsoft/dotacionentrega/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body: formData });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({
                icon: "success",
                title: "Entrega registrada",
                text: mensaje
            });
            LimpiarTodo();
            BuscarEntregas();
            CargarInventarioDisponible(); // Actualizar stock disponible
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
            text: "Error al procesar la entrega"
        });
    }
    BtnGuardar.disabled = false;
};

// Buscar Entregas
const BuscarEntregas = async () => {
    const url = '/montoya_final_dotacion_ingsoft/dotacionentrega/buscarAPI';
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
            text: "Error al cargar las entregas"
        });
    }
};

// Limpiar formulario completo
const LimpiarTodo = () => {
    FormularioEntregas.reset();
    detallesEntrega = [];
    ActualizarTablaDetalles();
    LimpiarDetalle();
    
    // Limpiar información de límite
    const infoElement = document.getElementById('info-limite-empleado');
    if (infoElement) infoElement.innerHTML = '';
    
    // Establecer fecha actual
    document.getElementById('entrega_fecha').value = new Date().toISOString().split('T')[0];
};

// Eliminar entrega
const EliminarEntrega = async (id) => {
    const confirmar = await Swal.fire({
        icon: "warning",
        title: "¿Eliminar entrega?",
        text: "Esta acción restaurará el stock y no se puede deshacer",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33"
    });

    if (confirmar.isConfirmed) {
        try {
            const url = `/montoya_final_dotacion_ingsoft/dotacionentrega/eliminar?id=${id}`;
            const respuesta = await fetch(url);
            const { codigo, mensaje } = await respuesta.json();

            if (codigo == 1) {
                Swal.fire({
                    icon: "success",
                    title: "Eliminada",
                    text: mensaje
                });
                BuscarEntregas();
                CargarInventarioDisponible(); // Actualizar stock
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
                text: "Error al eliminar la entrega"
            });
        }
    }
};

// DataTable principal - Entregas
datatable = new DataTable('#TableEntregas', {
    language: lenguaje,
    data: [],
    columns: [
        {
            title: "No.",
            data: "entrega_id",
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            title: "Empleado",
            data: null,
            render: (data, type, row) => `${row.empleado_nombres} ${row.empleado_apellidos}`
        },
        {
            title: "Puesto",
            data: "empleado_puesto"
        },
        {
            title: "Fecha",
            data: "entrega_fecha",
            render: (data) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return '';
            }
        },
        {
            title: "Año",
            data: "entrega_año"
        },
        {
            title: "Artículos Entregados",
            data: "detalle",
            render: (data) => {
                if (data && data.length > 0) {
                    return data.map(item => 
                        `${item.tipo_dotacion_nombre} (${item.talla_nombre}) x${item.entrega_det_cantidad}`
                    ).join('<br>');
                }
                return 'Sin detalles';
            }
        },
        {
            title: "Entregado por",
            data: "entrega_entregado_por"
        },
        {
            title: "Recibido por",
            data: "entrega_recibido_por"
        },
        {
            title: "Acciones",
            data: "entrega_id",
            render: (id, type, row) => `
                <div class="d-flex justify-content-center">
                    <button class="btn btn-danger btn-sm eliminar" data-id="${id}" title="Eliminar">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            `
        }
    ]
});

// DataTable detalles
datatableDetalles = new DataTable('#TableDetalles', {
    language: lenguaje,
    data: [],
    paging: false,
    searching: false,
    info: false,
    columns: [
        {
            title: "Artículo",
            data: null,
            render: (data, type, row) => `${row.tipo_dotacion_nombre} - ${row.talla_nombre}`
        },
        {
            title: "Marca/Modelo",
            data: null,
            render: (data, type, row) => `${row.dotacion_inv_marca} ${row.dotacion_inv_modelo}`
        },
        {
            title: "Cantidad",
            data: "cantidad"
        },
        {
            title: "Stock Disponible",
            data: "stock_disponible",
            render: (data) => `<span class="badge bg-info">${data}</span>`
        },
        {
            title: "Observaciones",
            data: "observaciones"
        },
        {
            title: "Acciones",
            data: null,
            render: (data, type, row, meta) => `
                <button class="btn btn-danger btn-sm eliminar-detalle" data-index="${meta.row}" title="Eliminar">
                    <i class="bi bi-trash3"></i>
                </button>
            `
        }
    ]
});

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    CargarEmpleados();
    CargarInventarioDisponible();
    BuscarEntregas();
    
    // Establecer fecha actual
    document.getElementById('entrega_fecha').value = new Date().toISOString().split('T')[0];
    
    // Eventos del formulario
    if (FormularioEntregas) FormularioEntregas.addEventListener('submit', GuardarEntrega);
    if (BtnLimpiar) BtnLimpiar.addEventListener('click', LimpiarTodo);
    if (BtnAgregarDetalle) BtnAgregarDetalle.addEventListener('click', AgregarDetalle);
    if (empleado_id) empleado_id.addEventListener('change', VerificarLimiteEmpleado);
    if (cantidad_entrega) cantidad_entrega.addEventListener('input', ValidarStock);
    
    // Eventos de las tablas
    datatable.on('click', '.eliminar', (e) => EliminarEntrega(e.currentTarget.dataset.id));
    datatableDetalles.on('click', '.eliminar-detalle', (e) => EliminarDetalle(parseInt(e.currentTarget.dataset.index)));
});