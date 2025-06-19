// dotacionsolicitud/index.js
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormularioSolicitudes = document.getElementById('FormularioSolicitudes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnAgregarDetalle = document.getElementById('BtnAgregarDetalle');
const empleado_id = document.getElementById('empleado_id');
const tipo_dotacion_id = document.getElementById('tipo_dotacion_id');
const talla_id = document.getElementById('talla_id');
const cantidad = document.getElementById('cantidad');
const observaciones_detalle = document.getElementById('observaciones_detalle');

// Variables globales
let detallesSolicitud = [];
let datatable;
let datatableDetalles;

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
        let tipoTalla = 'ROPA';
        if (tipo_dotacion_id.options[tipo_dotacion_id.selectedIndex].text === 'BOTAS') {
            tipoTalla = 'CALZADO';
        }

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
    }
};

// Agregar Detalle a la Solicitud
const AgregarDetalle = () => {
    if (!tipo_dotacion_id.value || !talla_id.value || !cantidad.value || cantidad.value <= 0) {
        Swal.fire({
            icon: "warning",
            title: "Datos incompletos",
            text: "Complete todos los campos del detalle"
        });
        return;
    }

    // Verificar si ya existe el mismo tipo y talla
    const existe = detallesSolicitud.find(detalle => 
        detalle.tipo_dotacion_id == tipo_dotacion_id.value && 
        detalle.talla_id == talla_id.value
    );

    if (existe) {
        Swal.fire({
            icon: "warning",
            title: "Detalle duplicado",
            text: "Ya existe un detalle con este tipo y talla"
        });
        return;
    }

    const detalle = {
        tipo_dotacion_id: tipo_dotacion_id.value,
        talla_id: talla_id.value,
        tipo_dotacion_nombre: tipo_dotacion_id.options[tipo_dotacion_id.selectedIndex].text,
        talla_nombre: talla_id.options[talla_id.selectedIndex].text,
        cantidad: parseInt(cantidad.value),
        observaciones: observaciones_detalle.value.trim()
    };

    detallesSolicitud.push(detalle);
    ActualizarTablaDetalles();
    LimpiarDetalle();
};

// Actualizar tabla de detalles
const ActualizarTablaDetalles = () => {
    datatableDetalles.clear().draw();
    datatableDetalles.rows.add(detallesSolicitud).draw();
};

// Limpiar formulario de detalle
const LimpiarDetalle = () => {
    tipo_dotacion_id.value = '';
    talla_id.innerHTML = '<option value="">Primero seleccione el tipo</option>';
    cantidad.value = '';
    observaciones_detalle.value = '';
};

// Eliminar detalle
const EliminarDetalle = (index) => {
    detallesSolicitud.splice(index, 1);
    ActualizarTablaDetalles();
};

// Guardar Solicitud
const GuardarSolicitud = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormularioSolicitudes, ['solicitud_id'])) {
        Swal.fire({
            icon: "info",
            title: "Formulario incompleto",
            text: "Complete todos los campos requeridos"
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (detallesSolicitud.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Sin detalles",
            text: "Debe agregar al menos un artículo a la solicitud"
        });
        BtnGuardar.disabled = false;
        return;
    }

    const formData = new FormData(FormularioSolicitudes);
    formData.append('detalles', JSON.stringify(detallesSolicitud));

    const url = '/montoya_final_dotacion_ingsoft/dotacionsolicitud/guardarAPI';

    try {
        const respuesta = await fetch(url, { method: 'POST', body: formData });
        const { codigo, mensaje } = await respuesta.json();
        
        if (codigo == 1) {
            Swal.fire({
                icon: "success",
                title: "Solicitud guardada",
                text: mensaje
            });
            LimpiarTodo();
            BuscarSolicitudes();
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
            text: "Error al procesar la solicitud"
        });
    }
    BtnGuardar.disabled = false;
};

// Buscar Solicitudes
const BuscarSolicitudes = async () => {
    const url = '/montoya_final_dotacion_ingsoft/dotacionsolicitud/buscarAPI';
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
            text: "Error al cargar las solicitudes"
        });
    }
};

// Limpiar formulario completo
const LimpiarTodo = () => {
    FormularioSolicitudes.reset();
    detallesSolicitud = [];
    ActualizarTablaDetalles();
    LimpiarDetalle();
};

// Aprobar solicitud
const AprobarSolicitud = async (id) => {
    const { value: aprobadoPor } = await Swal.fire({
        title: '¿Aprobar solicitud?',
        input: 'text',
        inputLabel: 'Aprobado por:',
        inputPlaceholder: 'Nombre de quien aprueba',
        showCancelButton: true,
        confirmButtonText: 'Aprobar',
        cancelButtonText: 'Cancelar'
    });

    if (aprobadoPor) {
        try {
            const formData = new FormData();
            formData.append('solicitud_id', id);
            formData.append('aprobado_por', aprobadoPor);

            const url = '/montoya_final_dotacion_ingsoft/dotacionsolicitud/aprobarAPI';
            const respuesta = await fetch(url, { method: 'POST', body: formData });
            const { codigo, mensaje } = await respuesta.json();

            if (codigo == 1) {
                Swal.fire({
                    icon: "success",
                    title: "Solicitud aprobada",
                    text: mensaje
                });
                BuscarSolicitudes();
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
                text: "Error al aprobar la solicitud"
            });
        }
    }
};

// Rechazar solicitud
const RechazarSolicitud = async (id) => {
    const { value: observaciones } = await Swal.fire({
        title: '¿Rechazar solicitud?',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo:',
        inputPlaceholder: 'Escriba el motivo...',
        showCancelButton: true,
        confirmButtonText: 'Rechazar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    });

    if (observaciones) {
        try {
            const formData = new FormData();
            formData.append('solicitud_id', id);
            formData.append('observaciones', observaciones);

            const url = '/montoya_final_dotacion_ingsoft/dotacionsolicitud/rechazarAPI';
            const respuesta = await fetch(url, { method: 'POST', body: formData });
            const { codigo, mensaje } = await respuesta.json();

            if (codigo == 1) {
                Swal.fire({
                    icon: "success",
                    title: "Solicitud rechazada",
                    text: mensaje
                });
                BuscarSolicitudes();
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
                text: "Error al rechazar la solicitud"
            });
        }
    }
};

// Eliminar solicitud
const EliminarSolicitud = async (id) => {
    const confirmar = await Swal.fire({
        icon: "warning",
        title: "¿Eliminar solicitud?",
        text: "Esta acción no se puede deshacer",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33"
    });

    if (confirmar.isConfirmed) {
        try {
            const url = `/montoya_final_dotacion_ingsoft/dotacionsolicitud/eliminar?id=${id}`;
            const respuesta = await fetch(url);
            const { codigo, mensaje } = await respuesta.json();

            if (codigo == 1) {
                Swal.fire({
                    icon: "success",
                    title: "Eliminada",
                    text: mensaje
                });
                BuscarSolicitudes();
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
                text: "Error al eliminar la solicitud"
            });
        }
    }
};

// DataTable principal - Solicitudes
datatable = new DataTable('#TableSolicitudes', {
    language: lenguaje,
    data: [],
    columns: [
        {
            title: "No.",
            data: "solicitud_id",
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
            data: "solicitud_fecha",
            render: (data) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return '';
            }
        },
        {
            title: "Estado",
            data: "solicitud_estado",
            render: (data) => {
                const colores = {
                    'PENDIENTE': 'warning',
                    'APROBADA': 'success',
                    'RECHAZADA': 'danger',
                    'ENTREGADA': 'primary'
                };
                return `<span class="badge bg-${colores[data] || 'secondary'}">${data}</span>`;
            }
        },
        {
            title: "Artículos",
            data: "detalle",
            render: (data) => {
                if (data && data.length > 0) {
                    return data.map(item => 
                        `${item.tipo_dotacion_nombre} (${item.talla_nombre}) x${item.solicitud_det_cantidad}`
                    ).join('<br>');
                }
                return 'Sin detalles';
            }
        },
        {
            title: "Acciones",
            data: "solicitud_id",
            render: (id, type, row) => {
                let botones = '';
                
                if (row.solicitud_estado === 'PENDIENTE') {
                    botones += `
                        <button class="btn btn-success btn-sm me-1 aprobar" data-id="${id}" title="Aprobar">
                            <i class="bi bi-check-circle"></i>
                        </button>
                        <button class="btn btn-warning btn-sm me-1 rechazar" data-id="${id}" title="Rechazar">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    `;
                }
                
                if (['PENDIENTE', 'RECHAZADA'].includes(row.solicitud_estado)) {
                    botones += `
                        <button class="btn btn-danger btn-sm eliminar" data-id="${id}" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    `;
                }
                
                return `<div class="d-flex justify-content-center">${botones}</div>`;
            }
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
            title: "Tipo",
            data: "tipo_dotacion_nombre"
        },
        {
            title: "Talla",
            data: "talla_nombre"
        },
        {
            title: "Cantidad",
            data: "cantidad"
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
    CargarTiposDotacion();
    BuscarSolicitudes();
    
    // Eventos del formulario
    if (FormularioSolicitudes) FormularioSolicitudes.addEventListener('submit', GuardarSolicitud);
    if (BtnLimpiar) BtnLimpiar.addEventListener('click', LimpiarTodo);
    if (BtnAgregarDetalle) BtnAgregarDetalle.addEventListener('click', AgregarDetalle);
    if (tipo_dotacion_id) tipo_dotacion_id.addEventListener('change', CargarTallas);
    
    // Eventos de las tablas
    datatable.on('click', '.aprobar', (e) => AprobarSolicitud(e.currentTarget.dataset.id));
    datatable.on('click', '.rechazar', (e) => RechazarSolicitud(e.currentTarget.dataset.id));
    datatable.on('click', '.eliminar', (e) => EliminarSolicitud(e.currentTarget.dataset.id));
    datatableDetalles.on('click', '.eliminar-detalle', (e) => EliminarDetalle(parseInt(e.currentTarget.dataset.index)));
});