<div class="container mt-4">
    <div class="row">
        <!-- Formulario -->
        <div class="col-lg-5 col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0 text-center">
                        <i class="fas fa-user-plus me-2"></i>
                        <span id="form-title">Registrar Empleado</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="formularioEmpleado">
                        <input type="hidden" id="empleado_id" name="empleado_id">
                        
                        <!-- Campos Principales -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empleado_codigo" class="form-label">
                                    <i class="fas fa-id-card me-1 text-primary"></i>
                                    <strong>Código *</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="empleado_codigo" 
                                       name="empleado_codigo" 
                                       placeholder="Ej: EMP-001"
                                       required
                                       maxlength="20">
                                <div class="invalid-feedback">
                                    El código es requerido (máximo 20 caracteres)
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="empleado_dpi" class="form-label">
                                    <i class="fas fa-id-badge me-1 text-primary"></i>
                                    <strong>DPI</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="empleado_dpi" 
                                       name="empleado_dpi" 
                                       placeholder="Ej: 1234567890101"
                                       maxlength="15">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empleado_nombres" class="form-label">
                                    <i class="fas fa-user me-1 text-primary"></i>
                                    <strong>Nombres *</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="empleado_nombres" 
                                       name="empleado_nombres" 
                                       placeholder="Nombres completos"
                                       required
                                       maxlength="100">
                                <div class="invalid-feedback">
                                    Los nombres son requeridos
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="empleado_apellidos" class="form-label">
                                    <i class="fas fa-user me-1 text-primary"></i>
                                    <strong>Apellidos *</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="empleado_apellidos" 
                                       name="empleado_apellidos" 
                                       placeholder="Apellidos completos"
                                       required
                                       maxlength="100">
                                <div class="invalid-feedback">
                                    Los apellidos son requeridos
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empleado_puesto" class="form-label">
                                    <i class="fas fa-briefcase me-1 text-primary"></i>
                                    <strong>Puesto</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="empleado_puesto" 
                                       name="empleado_puesto" 
                                       placeholder="Cargo o puesto"
                                       maxlength="50">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="empleado_departamento" class="form-label">
                                    <i class="fas fa-building me-1 text-primary"></i>
                                    <strong>Departamento</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="empleado_departamento" 
                                       name="empleado_departamento" 
                                       placeholder="Departamento o área"
                                       maxlength="50">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empleado_fecha_ingreso" class="form-label">
                                    <i class="fas fa-calendar me-1 text-primary"></i>
                                    <strong>Fecha de Ingreso</strong>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="empleado_fecha_ingreso" 
                                       name="empleado_fecha_ingreso">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="empleado_telefono" class="form-label">
                                    <i class="fas fa-phone me-1 text-primary"></i>
                                    <strong>Teléfono</strong>
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="empleado_telefono" 
                                       name="empleado_telefono" 
                                       placeholder="Ej: 5555-5555"
                                       maxlength="15">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="empleado_correo" class="form-label">
                                <i class="fas fa-envelope me-1 text-primary"></i>
                                <strong>Correo Electrónico</strong>
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="empleado_correo" 
                                   name="empleado_correo" 
                                   placeholder="empleado@empresa.com"
                                   maxlength="100">
                            <div class="invalid-feedback">
                                Ingrese un correo válido
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="empleado_direccion" class="form-label">
                                <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                <strong>Dirección</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="empleado_direccion" 
                                      name="empleado_direccion" 
                                      placeholder="Dirección completa..."
                                      rows="3"></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                <span class="loading" style="display: none;">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Procesando...
                                </span>
                                <span class="btn-text">
                                    <i class="fas fa-save me-2"></i>Guardar Empleado
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

        <!-- Estadísticas -->
        <div class="col-lg-7 col-md-6">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h4 id="totalEmpleados">0</h4>
                            <small>Total Empleados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <h4 id="totalDepartamentos">0</h4>
                            <small>Departamentos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                            <h4 id="totalPuestos">0</h4>
                            <small>Puestos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <h4 id="nuevosEmpleados">0</h4>
                            <small>Nuevos (30 días)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Empleados -->
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-primary border-2 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i> Gestión de Empleados
                        </h4>
                        <div class="btn-group">
                            <button class="btn btn-light btn-sm rounded-pill me-2" onclick="cargarEmpleados()" title="Actualizar datos">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn btn-light btn-sm rounded-pill" onclick="exportarExcel()" title="Exportar a Excel">
                                <i class="fas fa-file-excel"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Filtros de Búsqueda -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="criterio_busqueda" class="form-label">Buscar por:</label>
                            <select class="form-select" id="criterio_busqueda">
                                <option value="">Todos los empleados</option>
                                <option value="nombres">Nombres/Apellidos</option>
                                <option value="codigo">Código</option>
                                <option value="dpi">DPI</option>
                                <option value="puesto">Puesto</option>
                                <option value="departamento">Departamento</option>
                                <option value="correo">Correo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="valor_busqueda" class="form-label">Valor:</label>
                            <input type="text" class="form-control" id="valor_busqueda" placeholder="Ingrese el valor a buscar...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-primary d-block w-100" onclick="buscarEmpleados()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TablaEmpleados">
                            <!-- Contenido cargado dinámicamente -->
                        </table>
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
                    <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                    <h6>¿Está seguro de que desea eliminar este empleado?</h6>
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

<script src="<?= asset('build/js/empleado/index.js') ?>"></script>