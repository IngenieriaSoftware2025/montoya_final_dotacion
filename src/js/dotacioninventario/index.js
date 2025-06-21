// DotacionInventario/index.js - VERSIÓN FINAL SIN CONSOLE.LOG
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const formularioInventario = document.getElementById('formInventario');
const botonGuardar = document.getElementById('btnSubmit');
const botonLimpiar = document.getElementById('btnCancelar');
const selectTipoDotacion = document.getElementById('tipo_dotacion_id');
const selectTalla = document.getElementById('talla_id');
const codigoInventario = document.getElementById('dotacion_inv_codigo');
const cantidadInicial = document.getElementById('dotacion_inv_cantidad_inicial');
const cantidadActual = document.getElementById('dotacion_inv_cantidad_actual');

// URLs de API
const URL_BASE_API = '/montoya_final_dotacion_ingsoft/DotacionInventario';
const URLS_API = {
    guardar: `${URL_BASE_API}/guardarAPI`,
    modificar: `${URL_BASE_API}/modificarAPI`,
    eliminar: `${URL_BASE_API}/eliminarAPI`,
    obtenerInventario: `${URL_BASE_API}/obtenerInventarioAPI`,
    tiposDotacion: `${URL_BASE_API}/obtenerTiposDotacionAPI`,
    tallas: `${URL_BASE_API}/obtenerTallasAPI`,
    stockBajo: `${URL_BASE_API}/obtenerStockBajoAPI`,
    actualizarStock: `${URL_BASE_API}/actualizarStockAPI`
};

// Variable global para almacenar todas las tallas
window.todasLasTallas = [];

// Validar Código del Producto
const validarCodigoProducto = () => {
    const codigo = codigoInventario.value.trim();
    if (codigo.length >= 1 && codigo.length <= 20) {
        codigoInventario.classList.add('is-valid');
        codigoInventario.classList.remove('is-invalid');
    } else if (codigo.length > 0) {
        codigoInventario.classList.add('is-invalid');
        codigoInventario.classList.remove('is-valid');
    } else {
        codigoInventario.classList.remove('is-valid', 'is-invalid');
    }
};

// Validar Cantidades
const validarCantidades = () => {
    const inicial = parseInt(cantidadInicial.value) || 0;
    const actual = parseInt(cantidadActual.value) || 0;
    
    // Validar cantidad inicial
    if (inicial >= 0) {
        cantidadInicial.classList.add('is-valid');
        cantidadInicial.classList.remove('is-invalid');
    } else {
        cantidadInicial.classList.add('is-invalid');
        cantidadInicial.classList.remove('is-valid');
    }
    
    // Validar cantidad actual
    if (actual >= 0 && actual <= inicial) {
        cantidadActual.classList.add('is-valid');
        cantidadActual.classList.remove('is-invalid');
    } else {
        cantidadActual.classList.add('is-invalid');
        cantidadActual.classList.remove('is-valid');
        if (actual > inicial) {
            Swal.fire({ 
                icon: "warning", 
                title: "Cantidad incorrecta", 
                text: "La cantidad actual no puede ser mayor que la inicial" 
            });
        }
    }
};

// Cargar tipos de dotación
async function cargarTiposDotacion() {
    try {
        const respuesta = await fetch(URLS_API.tiposDotacion);
        const textoRespuesta = await respuesta.text();
        
        if (!textoRespuesta) {
            return;
        }
        
        const resultado = JSON.parse(textoRespuesta);
        
        if (resultado.codigo === 1) {
            selectTipoDotacion.innerHTML = '<option value="">Seleccionar tipo...</option>';
            
            const tipos = resultado.datos || resultado.data || [];
            tipos.forEach((tipo) => {
                selectTipoDotacion.innerHTML += `<option value="${tipo.tipo_dotacion_id}">${tipo.tipo_dotacion_nombre}</option>`;
            });
        }
    } catch (error) {
        // Error silencioso
    }
}

// Cargar tallas
async function cargarTallas() {
    try {
        const respuesta = await fetch(URLS_API.tallas);
        const textoRespuesta = await respuesta.text();
        
        if (!textoRespuesta) {
            return;
        }
        
        const resultado = JSON.parse(textoRespuesta);
        
        if (resultado.codigo === 1) {
            const tallas = resultado.datos || resultado.data || [];
            window.todasLasTallas = tallas;
            selectTalla.innerHTML = '<option value="">Primero seleccione un tipo de dotación</option>';
        }
    } catch (error) {
        // Error silencioso
    }
}

// Filtrar tallas según el tipo de dotación
function filtrarTallasPorTipo() {
    const tipoSeleccionado = selectTipoDotacion.options[selectTipoDotacion.selectedIndex];
    
    if (!tipoSeleccionado.value) {
        selectTalla.innerHTML = '<option value="">Primero seleccione un tipo de dotación</option>';
        return;
    }
    
    const nombreTipo = tipoSeleccionado.text.toUpperCase();
    selectTalla.innerHTML = '<option value="">Seleccionar talla...</option>';
    
    // Filtrar tallas según el tipo
    const tallasParaMostrar = window.todasLasTallas.filter(talla => {
        const codigoTalla = talla.talla_codigo.toUpperCase();
        
        if (nombreTipo.includes('BOTA') || nombreTipo.includes('ZAPATO') || nombreTipo.includes('CALZADO')) {
            // Para calzado: mostrar solo números
            const esNumero = /^\d+$/.test(codigoTalla);
            return esNumero;
        } else if (nombreTipo.includes('CAMISA') || nombreTipo.includes('PANTALON') || nombreTipo.includes('UNIFORME')) {
            // Para ropa: mostrar solo letras (XS, S, M, L, XL, etc.)
            const esLetra = /^[A-Z]+$/.test(codigoTalla);
            return esLetra;
        } else {
            // Para otros tipos: mostrar todas las tallas
            return true;
        }
    });
    
    // Ordenar las tallas
    tallasParaMostrar.sort((a, b) => {
        const codigoA = a.talla_codigo;
        const codigoB = b.talla_codigo;
        
        // Si son números, ordenar numéricamente
        if (/^\d+$/.test(codigoA) && /^\d+$/.test(codigoB)) {
            return parseInt(codigoA) - parseInt(codigoB);
        }
        
        // Si son letras, ordenar por tamaño (XS, S, M, L, XL, XXL)
        const ordenTallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        const indiceA = ordenTallas.indexOf(codigoA);
        const indiceB = ordenTallas.indexOf(codigoB);
        
        if (indiceA !== -1 && indiceB !== -1) {
            return indiceA - indiceB;
        }
        
        // Ordenar alfabéticamente si no están en el array predefinido
        return codigoA.localeCompare(codigoB);
    });
    
    // Agregar las tallas filtradas al select
    tallasParaMostrar.forEach((talla) => {
        selectTalla.innerHTML += `<option value="${talla.talla_id}">${talla.talla_codigo}</option>`;
    });
    
    // Mostrar mensaje si no hay tallas para este tipo
    if (tallasParaMostrar.length === 0) {
        selectTalla.innerHTML = '<option value="">No hay tallas disponibles para este tipo</option>';
    }
}

// Guardar Producto en Inventario
const guardarInventario = async (evento) => {
    evento.preventDefault();
    botonGuardar.disabled = true;

    // Mostrar estado de carga
    const cargando = botonGuardar.querySelector('.loading');
    const textoBoton = botonGuardar.querySelector('.btn-text');
    cargando.style.display = 'inline';
    textoBoton.style.display = 'none';

    if (!validarFormulario(formularioInventario, ['dotacion_inv_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Debe completar todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    if (codigoInventario.classList.contains('is-invalid') || 
        cantidadInicial.classList.contains('is-invalid') ||
        cantidadActual.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Datos inválidos", 
            text: "Debe corregir los errores antes de continuar"
        });
        resetearBoton();
        return;
    }

    const cuerpo = new FormData(formularioInventario);
    const url = URLS_API.guardar;

    try {
        const respuesta = await fetch(url, { method: 'POST', body: cuerpo });
        
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}: ${respuesta.statusText}`);
        }
        
        const textoRespuesta = await respuesta.text();
        
        if (!textoRespuesta.trim()) {
            throw new Error('Respuesta vacía del servidor');
        }
        
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
        } catch (errorJson) {
            throw new Error('Respuesta del servidor no es JSON válido');
        }
        
        if (resultado.codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Producto registrado", 
                text: resultado.mensaje 
            });
            limpiarTodo();
            buscarInventario();
            cargarStockBajo();
        } else {
            Swal.fire({ 
                icon: "info", 
                title: "Error", 
                text: resultado.mensaje || 'Error desconocido'
            });
        }
    } catch (error) {
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: `Error: ${error.message}` 
        });
    }
    
    resetearBoton();
};

// Buscar Inventario
const buscarInventario = async () => {
    const url = URLS_API.obtenerInventario;
    
    try {
        const respuesta = await fetch(url);
        const textoRespuesta = await respuesta.text();
        
        // Verificar si la respuesta está vacía
        if (!textoRespuesta.trim()) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El servidor no respondió correctamente"
            });
            return;
        }
        
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
        } catch (errorJson) {
            Swal.fire({
                icon: "error",
                title: "Error de formato",
                text: "La respuesta del servidor no es válida"
            });
            return;
        }
        
        if (resultado.codigo == 1) {
            // Limpiar tabla primero
            tablaInventario.clear().draw();
            
            // Verificar que datos existe y es array (puede ser 'datos' o 'data')
            const datosRespuesta = resultado.datos || resultado.data || [];
            
            if (!Array.isArray(datosRespuesta)) {
                Swal.fire({
                    icon: "info",
                    title: "Sin datos",
                    text: "No hay datos de inventario disponibles"
                });
                return;
            }
            
            // Filtrar objetos vacíos y datos inválidos
            const datosFiltrados = datosRespuesta.filter(elemento => {
                const esValido = elemento && 
                              typeof elemento === 'object' && 
                              Object.keys(elemento).length > 0 && 
                              elemento.dotacion_inv_id;
                
                return esValido;
            });
            
            if (datosFiltrados.length > 0) {
                // Cargar datos en la tabla
                tablaInventario.rows.add(datosFiltrados).draw();
                
                // Actualizar estadísticas
                actualizarEstadisticas(datosFiltrados);
            } else {
                Swal.fire({
                    icon: "info",
                    title: "Inventario Vacío",
                    text: "No se encontraron productos en el inventario"
                });
            }
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: resultado.mensaje || "Error al obtener inventario"
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: `Error al cargar inventario: ${error.message}`
        });
    }
};

// CONFIGURACIÓN DE DATATABLE
const tablaInventario = new DataTable('#TablaInventario', {
    language: lenguaje,
    data: [],
    columns: [
        { 
            title: "No.", 
            data: "dotacion_inv_id", 
            render: (data, type, row, meta) => meta.row + 1 
        },
        { 
            title: "Código", 
            data: "dotacion_inv_codigo",
            render: (data) => data ? `<span class="badge bg-success fs-6">${data}</span>` : '<em class="text-muted">Sin código</em>'
        },
        { 
            title: "Tipo", 
            data: "tipo_dotacion_nombre",
            render: (data) => data ? data : '<em class="text-muted">Sin tipo</em>'
        },
        { 
            title: "Talla", 
            data: "talla_codigo",
            render: (data) => data ? `<span class="badge bg-info">${data}</span>` : '<em class="text-muted">Sin talla</em>'
        },
        { 
            title: "Marca", 
            data: "dotacion_inv_marca",
            render: (data) => data ? data : '<em class="text-muted">Sin marca</em>'
        },
        { 
            title: "Modelo", 
            data: "dotacion_inv_modelo",
            render: (data) => data ? data : '<em class="text-muted">Sin modelo</em>'
        },
        { 
            title: "Stock Inicial", 
            data: "dotacion_inv_cantidad_inicial",
            render: (data) => `<span class="badge bg-secondary">${data || 0}</span>`
        },
        { 
            title: "Stock Actual", 
            data: "dotacion_inv_cantidad_actual",
            render: (data, type, row) => {
                const cantidad = parseInt(data || 0);
                const minima = parseInt(row.dotacion_inv_cantidad_minima || 5);
                let clase = 'bg-success';
                
                if (cantidad === 0) {
                    clase = 'bg-danger';
                } else if (cantidad <= minima) {
                    clase = 'bg-warning text-dark';
                }
                
                return `<span class="badge ${clase} fs-6">${cantidad}</span>`;
            }
        },
        { 
            title: "Precio Unit.", 
            data: "dotacion_inv_precio_unitario",
            render: (data) => {
                const precio = parseFloat(data || 0);
                return precio > 0 ? `$${precio.toFixed(2)}` : '<em class="text-muted">Sin precio</em>';
            }
        },
        {
            title: "Acciones", 
            data: "dotacion_inv_id",
            orderable: false,
            render: (id, type, row) => {
                // Escapar comillas en JSON para evitar errores
                const datosSeguros = JSON.stringify(row).replace(/'/g, '&#39;');
                
                return `
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary modificar" 
                                data-id="${id}" 
                                data-json='${datosSeguros}'
                                title="Modificar registro">
                            <i class="fas fa-edit me-1"></i>✏️
                        </button>
                        <button class="btn btn-outline-warning actualizar-stock" 
                                data-id="${id}"
                                data-codigo="${row.dotacion_inv_codigo || 'Sin código'}"
                                data-actual="${row.dotacion_inv_cantidad_actual || 0}"
                                title="Actualizar stock">
                            <i class="fas fa-boxes me-1"></i>📦
                        </button>
                        <button class="btn btn-outline-danger eliminar" 
                                data-id="${id}"
                                title="Eliminar registro">
                            <i class="fas fa-trash me-1"></i>🗑️
                        </button>
                    </div>
                `;
            }
        }
    ],
    responsive: true,
    pageLength: 10,
    lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
    order: [[1, 'asc']], // Ordenar por código
    columnDefs: [
        { targets: [9], orderable: false } // Deshabilitar ordenamiento en columna de acciones
    ]
});

// Función para actualizar estadísticas
const actualizarEstadisticas = (datos) => {
    const totalProductos = datos.length;
    const stockTotal = datos.reduce((total, producto) => {
        return total + parseInt(producto.dotacion_inv_cantidad_actual || 0);
    }, 0);
    
    // Actualizar elementos HTML
    const elementoTotalProductos = document.getElementById('totalProductos');
    const elementoStockTotal = document.getElementById('stockTotal');
    
    if (elementoTotalProductos) {
        elementoTotalProductos.textContent = totalProductos;
    }
    
    if (elementoStockTotal) {
        elementoStockTotal.textContent = stockTotal;
    }
};

// Función para cargar stock bajo
const cargarStockBajo = async () => {
    const contenedorStockBajo = document.getElementById('stockBajo');
    
    if (!contenedorStockBajo) {
        return;
    }
    
    // Mostrar estado de carga
    contenedorStockBajo.innerHTML = `
        <div class="text-center text-muted">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="small mt-2">Cargando...</p>
        </div>
    `;
    
    try {
        const respuesta = await fetch(URLS_API.stockBajo);
        const resultado = await respuesta.json();
        
        if (resultado.codigo === 1) {
            const stockBajo = resultado.datos || resultado.data || [];
            
            if (stockBajo.length > 0) {
                let html = '';
                stockBajo.forEach((producto) => {
                    const cantidadActual = parseInt(producto.dotacion_inv_cantidad_actual || 0);
                    const cantidadMinima = parseInt(producto.dotacion_inv_cantidad_minima || 5);
                    const porcentaje = cantidadMinima > 0 ? Math.round((cantidadActual / cantidadMinima) * 100) : 0;
                    
                    let colorBadge = 'bg-danger';
                    let iconoEstado = 'fas fa-exclamation-circle';
                    
                    if (cantidadActual === 0) {
                        colorBadge = 'bg-danger';
                        iconoEstado = 'fas fa-times-circle';
                    } else if (porcentaje <= 50) {
                        colorBadge = 'bg-warning text-dark';
                        iconoEstado = 'fas fa-exclamation-triangle';
                    }
                    
                    html += `
                        <div class="alert alert-warning border-0 p-2 mb-2 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <small class="fw-bold d-block">${producto.dotacion_inv_codigo}</small>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge ${colorBadge} fs-7">${cantidadActual}/${cantidadMinima}</span>
                                        <small class="text-muted">(${porcentaje}%)</small>
                                    </div>
                                </div>
                                <i class="${iconoEstado} text-warning fs-5"></i>
                            </div>
                        </div>
                    `;
                });
                
                contenedorStockBajo.innerHTML = html;
            } else {
                contenedorStockBajo.innerHTML = `
                    <div class="text-center text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                        <p class="small mt-2 mb-0">¡Todo el stock está en niveles óptimos!</p>
                    </div>
                `;
            }
        } else {
            contenedorStockBajo.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p class="small mt-2 mb-0">Error al cargar stock bajo</p>
                </div>
            `;
        }
    } catch (error) {
        contenedorStockBajo.innerHTML = `
            <div class="text-center text-danger">
                <i class="fas fa-times"></i>
                <p class="small mt-2 mb-0">Error de conexión</p>
            </div>
        `;
    }
};

// Llenar formulario para modificar
const llenarFormulario = async (evento) => {
    try {
        const datos = JSON.parse(evento.currentTarget.dataset.json);
        
        // Llenar campos del formulario
        document.getElementById('dotacion_inv_id').value = datos.dotacion_inv_id || '';
        document.getElementById('dotacion_inv_codigo').value = datos.dotacion_inv_codigo || '';
        document.getElementById('tipo_dotacion_id').value = datos.tipo_dotacion_id || '';
        document.getElementById('talla_id').value = datos.talla_id || '';
        document.getElementById('dotacion_inv_marca').value = datos.dotacion_inv_marca || '';
        document.getElementById('dotacion_inv_modelo').value = datos.dotacion_inv_modelo || '';
        document.getElementById('dotacion_inv_color').value = datos.dotacion_inv_color || '';
        document.getElementById('dotacion_inv_material').value = datos.dotacion_inv_material || '';
        document.getElementById('dotacion_inv_cantidad_inicial').value = datos.dotacion_inv_cantidad_inicial || '';
        document.getElementById('dotacion_inv_cantidad_actual').value = datos.dotacion_inv_cantidad_actual || '';
        document.getElementById('dotacion_inv_cantidad_minima').value = datos.dotacion_inv_cantidad_minima || '';
        document.getElementById('dotacion_inv_precio_unitario').value = datos.dotacion_inv_precio_unitario || '';
        document.getElementById('dotacion_inv_proveedor').value = datos.dotacion_inv_proveedor || '';
        document.getElementById('dotacion_inv_observaciones').value = datos.dotacion_inv_observaciones || '';
        
        // Cambiar UI para modo edición
        document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i>Modificar Producto';
        botonGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar';
        botonLimpiar.style.display = 'block';
        
        // Quitar validaciones previas
        formularioInventario.querySelectorAll('.is-valid, .is-invalid').forEach(elemento => {
            elemento.classList.remove('is-valid', 'is-invalid');
        });
        
        // Scroll al formulario
        formularioInventario.scrollIntoView({ 
            behavior: 'smooth',
            block: 'center'
        });
        
        // Focus en el primer campo
        codigoInventario.focus();
        
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar datos del producto"
        });
    }
};

// Limpiar todo el formulario
const limpiarTodo = () => {
    formularioInventario.reset();
    document.getElementById('dotacion_inv_id').value = '';
    
    // Restaurar UI para modo creación
    document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Registrar Producto';
    botonGuardar.querySelector('.btn-text').innerHTML = '<i class="fas fa-save me-2"></i>Guardar';
    botonLimpiar.style.display = 'none';
    
    // Limpiar validaciones
    formularioInventario.querySelectorAll('.is-valid, .is-invalid').forEach(elemento => {
        elemento.classList.remove('is-valid', 'is-invalid');
    });
    
    // Resetear selects
    selectTalla.innerHTML = '<option value="">Primero seleccione un tipo de dotación</option>';
    
    // Limpiar alertas si existen
    const contenedorAlertas = document.getElementById('alerts');
    if (contenedorAlertas) {
        contenedorAlertas.innerHTML = '';
    }
};

// Modificar Inventario
const modificarInventario = async (evento) => {
    evento.preventDefault();
    
    const id = document.getElementById('dotacion_inv_id').value;
    if (!id) {
        return guardarInventario(evento);
    }
    
    botonGuardar.disabled = true;
    
    const cargando = botonGuardar.querySelector('.loading');
    const textoBoton = botonGuardar.querySelector('.btn-text');
    cargando.style.display = 'inline';
    textoBoton.style.display = 'none';

    if (!validarFormulario(formularioInventario, ['dotacion_inv_id'])) {
        Swal.fire({ 
            icon: "info", 
            title: "Formulario incompleto", 
            text: "Complete todos los campos requeridos" 
        });
        resetearBoton();
        return;
    }

    if (codigoInventario.classList.contains('is-invalid') || 
        cantidadInicial.classList.contains('is-invalid') ||
        cantidadActual.classList.contains('is-invalid')) {
        Swal.fire({ 
            icon: "error", 
            title: "Datos inválidos", 
            text: "Debe corregir los errores antes de continuar" 
        });
        resetearBoton();
        return;
    }

    const cuerpo = new FormData(formularioInventario);
    const url = URLS_API.modificar;

    try {
        const respuesta = await fetch(url, { method: 'POST', body: cuerpo });
        
        // Verificar que la respuesta sea válida
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}: ${respuesta.statusText}`);
        }
        
        const textoRespuesta = await respuesta.text();
        
        // Verificar que hay contenido
        if (!textoRespuesta.trim()) {
            throw new Error('Respuesta vacía del servidor');
        }
        
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
        } catch (errorJson) {
            throw new Error('Respuesta del servidor no es JSON válido');
        }
        
        if (resultado.codigo == 1) {
            Swal.fire({ 
                icon: "success", 
                title: "Producto actualizado", 
                text: resultado.mensaje 
            });
            limpiarTodo();
            buscarInventario();
            cargarStockBajo();
        } else {
            Swal.fire({ 
                icon: "error", 
                title: "Error al actualizar", 
                text: resultado.mensaje || 'Error desconocido'
            });
        }
    } catch (error) {
        Swal.fire({ 
            icon: "error", 
            title: "Error de conexión", 
            text: `Error: ${error.message}` 
        });
    }
    
    resetearBoton();
};

// Eliminar Producto del Inventario
const eliminarInventario = async (evento) => {
    const id = evento.currentTarget.dataset.id;
    
    const confirmar = await Swal.fire({
        icon: "warning", 
        title: "¿Eliminar producto del inventario?", 
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true, 
        confirmButtonText: "Sí, eliminar", 
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        reverseButtons: true
    });

    if (confirmar.isConfirmed) {
        const url = `${URLS_API.eliminar}?id=${id}`;
        try {
            const respuesta = await fetch(url);
            const { codigo, mensaje } = await respuesta.json();
            
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Eliminado correctamente", 
                    text: mensaje 
                });
                buscarInventario();
                cargarStockBajo();
            } else {
                Swal.fire({ 
                    icon: "error", 
                    title: "Error al eliminar", 
                    text: mensaje 
                });
            }
        } catch (error) {
            Swal.fire({ 
                icon: "error", 
                title: "Error de conexión", 
                text: "Error al eliminar el producto" 
            });
        }
    }
};

// Actualizar Stock
const actualizarStock = async (evento) => {
    const id = evento.currentTarget.dataset.id;
    const codigo = evento.currentTarget.dataset.codigo;
    const stockActual = evento.currentTarget.dataset.actual;
    
    const { value: nuevaCantidad } = await Swal.fire({
        title: `Actualizar Stock - ${codigo}`,
        text: `Stock actual: ${stockActual}`,
        input: 'number',
        inputLabel: 'Nueva cantidad',
        inputValue: stockActual,
        inputAttributes: {
            min: 0,
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value < 0) {
                return 'Ingrese una cantidad válida (mayor o igual a 0)';
            }
        }
    });

    if (nuevaCantidad !== undefined) {
        try {
            const cuerpo = new FormData();
            cuerpo.append('dotacion_inv_id', id);
            cuerpo.append('nueva_cantidad', nuevaCantidad);
            
            const respuesta = await fetch(URLS_API.actualizarStock, { method: 'POST', body: cuerpo });
            const { codigo, mensaje } = await respuesta.json();
            
            if (codigo == 1) {
                Swal.fire({ 
                    icon: "success", 
                    title: "Stock actualizado", 
                    text: mensaje 
                });
                buscarInventario();
                cargarStockBajo();
            } else {
                Swal.fire({ 
                    icon: "error", 
                    title: "Error", 
                    text: mensaje 
                });
            }
        } catch (error) {
            Swal.fire({ 
                icon: "error", 
                title: "Error de conexión", 
                text: "Error al actualizar el stock" 
            });
        }
    }
};

// Función auxiliar para resetear el botón
const resetearBoton = () => {
    const cargando = botonGuardar.querySelector('.loading');
    const textoBoton = botonGuardar.querySelector('.btn-text');
    
    botonGuardar.disabled = false;
    cargando.style.display = 'none';
    textoBoton.style.display = 'inline';
};

// Función para detectar el modo del formulario y ejecutar la acción correcta
const manejarSubmitFormulario = (evento) => {
    evento.preventDefault();
    
    const id = document.getElementById('dotacion_inv_id').value;
    if (id && id.trim() !== '') {
        // Modo edición
        modificarInventario(evento);
    } else {
        // Modo creación
        guardarInventario(evento);
    }
};

// Funciones globales para uso desde HTML
window.cargarInventario = () => buscarInventario();
window.limpiarFormulario = () => limpiarTodo();
window.cargarStockBajo = cargarStockBajo;
window.testConexion = () => {
    cargarTiposDotacion();
    cargarTallas();
    cargarStockBajo();
};

// Eventos del DOM
document.addEventListener('DOMContentLoaded', () => {
    if (!selectTipoDotacion || !selectTalla) {
        return;
    }
    
    // Cargar datos para los selects
    cargarTiposDotacion();
    cargarTallas();
    
    // Cargar inventario inicial
    buscarInventario();
    
    // Cargar stock bajo inicial
    cargarStockBajo();
    
    // Eventos de validación
    if (codigoInventario) {
        codigoInventario.addEventListener('blur', validarCodigoProducto);
        codigoInventario.addEventListener('input', validarCodigoProducto);
    }
    
    if (cantidadInicial && cantidadActual) {
        cantidadInicial.addEventListener('blur', validarCantidades);
        cantidadInicial.addEventListener('input', validarCantidades);
        cantidadActual.addEventListener('blur', validarCantidades);
        cantidadActual.addEventListener('input', validarCantidades);
    }
    
    // Evento para filtrar tallas cuando se selecciona un tipo
    selectTipoDotacion.addEventListener('change', filtrarTallasPorTipo);
    
    // Eventos de formulario
    if (formularioInventario) {
        formularioInventario.addEventListener('submit', manejarSubmitFormulario);
    }
    
    if (botonLimpiar) {
        botonLimpiar.addEventListener('click', limpiarTodo);
    }
    
    // Eventos de DataTable
    tablaInventario.on('click', '.modificar', llenarFormulario);
    tablaInventario.on('click', '.eliminar', eliminarInventario);
    tablaInventario.on('click', '.actualizar-stock', actualizarStock);
});