<div class="container mt-4">
    <div class="row">
        <!-- Formulario -->
        <div class="col-lg-5 col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h5 class="mb-0 text-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span id="form-title">Registrar en Inventario</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="formInventario">
                        <input type="hidden" id="dotacion_inv_id" name="dotacion_inv_id">
                        
                        <!-- Campos Principales -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dotacion_inv_codigo" class="form-label">
                                    <i class="fas fa-barcode me-1 text-info"></i>
                                    <strong>Código *</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="dotacion_inv_codigo" 
                                       name="dotacion_inv_codigo" 
                                       placeholder="Ej: DOT-001"
                                       required
                                       maxlength="20">
                                <div class="invalid-feedback">
                                    El código es requerido (máximo 20 caracteres)
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tipo_dotacion_id" class="form-label">
                                    <i class="fas fa-tag me-1 text-info"></i>
                                    <strong>Tipo de Dotación *</strong>
                                </label>
                                <select class="form-select" id="tipo_dotacion_id" name="tipo_dotacion_id" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                                <div class="invalid-feedback">
                                    Debe seleccionar un tipo de dotación
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="talla_id" class="form-label">
                                    <i class="fas fa-ruler me-1 text-info"></i>
                                    <strong>Talla *</strong>
                                </label>
                                <select class="form-select" id="talla_id" name="talla_id" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                                <div class="invalid-feedback">
                                    Debe seleccionar una talla
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dotacion_inv_marca" class="form-label">
                                    <i class="fas fa-copyright me-1 text-info"></i>
                                    <strong>Marca</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="dotacion_inv_marca" 
                                       name="dotacion_inv_marca" 
                                       placeholder="Ej: Nike, Adidas..."
                                       maxlength="50">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dotacion_inv_modelo" class="form-label">
                                    <i class="fas fa-shapes me-1 text-info"></i>
                                    <strong>Modelo</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="dotacion_inv_modelo" 
                                       name="dotacion_inv_modelo" 
                                       placeholder="Modelo del producto"
                                       maxlength="50">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dotacion_inv_color" class="form-label">
                                    <i class="fas fa-palette me-1 text-info"></i>
                                    <strong>Color</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="dotacion_inv_color" 
                                       name="dotacion_inv_color" 
                                       placeholder="Color principal"
                                       maxlength="30">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dotacion_inv_material" class="form-label">
                                <i class="fas fa-industry me-1 text-info"></i>
                                <strong>Material</strong>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="dotacion_inv_material" 
                                   name="dotacion_inv_material" 
                                   placeholder="Material de fabricación"
                                   maxlength="100">
                        </div>

                        <!-- Cantidades -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dotacion_inv_cantidad_inicial" class="form-label">
                                    <i class="fas fa-plus-square me-1 text-success"></i>
                                    <strong>Cant. Inicial *</strong>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="dotacion_inv_cantidad_inicial" 
                                       name="dotacion_inv_cantidad_inicial" 
                                       min="0"
                                       required>
                                <div class="invalid-feedback">
                                    Cantidad inicial requerida
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="dotacion_inv_cantidad_actual" class="form-label">
                                    <i class="fas fa-warehouse me-1 text-primary"></i>
                                    <strong>Cant. Actual *</strong>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="dotacion_inv_cantidad_actual" 
                                       name="dotacion_inv_cantidad_actual" 
                                       min="0"
                                       required>
                                <div class="invalid-feedback">
                                    Cantidad actual requerida
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="dotacion_inv_cantidad_minima" class="form-label">
                                    <i class="fas fa-exclamation-triangle me-1 text-warning"></i>
                                    <strong>Cant. Mínima</strong>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="dotacion_inv_cantidad_minima" 
                                       name="dotacion_inv_cantidad_minima" 
                                       min="0"
                                       value="5">
                            </div>
                        </div>

                        <!-- Precio y Proveedor -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dotacion_inv_precio_unitario" class="form-label">
                                    <i class="fas fa-dollar-sign me-1 text-success"></i>
                                    <strong>Precio Unitario</strong>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="dotacion_inv_precio_unitario" 
                                       name="dotacion_inv_precio_unitario" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dotacion_inv_proveedor" class="form-label">
                                    <i class="fas fa-truck me-1 text-info"></i>
                                    <strong>Proveedor</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="dotacion_inv_proveedor" 
                                       name="dotacion_inv_proveedor" 
                                       placeholder="Nombre del proveedor"
                                       maxlength="100">
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3">
                            <label for="dotacion_inv_observaciones" class="form-label">
                                <i class="fas fa-sticky-note me-1 text-secondary"></i>
                                <strong>Observaciones</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="dotacion_inv_observaciones" 
                                      name="dotacion_inv_observaciones" 
                                      placeholder="Observaciones adicionales..."
                                      rows="3"></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-info btn-lg" id="btnSubmit">
                                <span class="loading" style="display: none;">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Procesando...
                                </span>
                                <span class="btn-text">
                                    <i class="fas fa-save me-2"></i>Guardar en Inventario
                                </span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnCancelar" onclick="limpiarFormulario()" style="display: none;">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>

                    <!-- Alerts -->
                    <div id="alerts" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Panel de Stock Bajo -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow border-warning border-2 rounded-4">
                <div class="card-header bg-warning text-dark rounded-top-4">
                    <h6 class="mb-0 text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Stock Bajo
                    </h6>
                </div>
                <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                    <div id="stockBajo">
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p class="small mt-2">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="col-lg-4 col-md-12">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-boxes fa-2x mb-2"></i>
                            <h4 id="totalProductos">0</h4>
                            <small>Total Productos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-warehouse fa-2x mb-2"></i>
                            <h4 id="stockTotal">0</h4>
                            <small>Stock Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Inventario -->
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-info border-2 rounded-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-clipboard-data me-2"></i> Inventario de Dotaciones
                        </h4>
                        <div class="btn-group">
                            <button class="btn btn-light btn-sm rounded-pill me-2" onclick="cargarInventario()" title="Actualizar datos">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn btn-light btn-sm rounded-pill" onclick="exportarExcel()" title="Exportar a Excel">
                                <i class="fas fa-file-excel"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TablaInventario">
                            <!-- Contenido cargado dinámicamente -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información adicional -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0 rounded-4 shadow-sm" role="alert">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle me-2"></i>Información sobre el Inventario
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            • <strong>Código:</strong> Identificador único del producto (máximo 20 caracteres)<br>
                            • <strong>Cantidades:</strong> Inicial = stock de entrada, Actual = disponible ahora<br>
                            • <strong>Stock Mínimo:</strong> Nivel de alerta para reposición
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0">
                            • <strong>Tipo y Talla:</strong> Clasificación del producto<br>
                            • <strong>Fecha Vencimiento:</strong> Solo para productos perecederos<br>
                            • <strong>Stock Bajo:</strong> Se muestra cuando actual ≤ mínimo
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h6>¿Está seguro de que desea eliminar este producto del inventario?</h6>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Actualizar Stock -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Actualizar Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formStock">
                    <input type="hidden" id="stock_producto_id">
                    <div class="mb-3">
                        <label for="nueva_cantidad" class="form-label">
                            <strong>Nueva Cantidad</strong>
                        </label>
                        <input type="number" 
                               class="form-control form-control-lg" 
                               id="nueva_cantidad" 
                               min="0" 
                               required>
                        <div class="form-text">
                            Cantidad actual que estará disponible en inventario
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/DotacionInventario/index.js') ?>"></script>