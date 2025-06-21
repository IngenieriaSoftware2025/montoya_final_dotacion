<div class="container mt-4">
    <div class="row justify-content-center">
        <!-- Formulario -->
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0 text-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span id="form-title">Registrar Tipo de Dotación</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="formTipoDotacion">
                        <input type="hidden" id="tipo_dotacion_id" name="tipo_dotacion_id">
                        
                        <!-- Campo Nombre -->
                        <div class="mb-3">
                            <label for="tipo_dotacion_nombre" class="form-label">
                                <i class="fas fa-tag me-1 text-primary"></i>
                                <strong>Nombre del Tipo *</strong>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="tipo_dotacion_nombre" 
                                   name="tipo_dotacion_nombre" 
                                   placeholder="Ej: Uniforme, Calzado, EPP..."
                                   required
                                   minlength="2"
                                   maxlength="100">
                            <div class="invalid-feedback">
                                El nombre debe tener al menos 2 caracteres
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="mb-3">
                            <label for="tipo_dotacion_descripcion" class="form-label">
                                <i class="fas fa-align-left me-1 text-primary"></i>
                                <strong>Descripción</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="tipo_dotacion_descripcion" 
                                      name="tipo_dotacion_descripcion" 
                                      placeholder="Descripción del tipo de dotación..."
                                      rows="4"
                                      maxlength="500"></textarea>
                            <div class="form-text">
                                <small class="text-muted">Máximo 500 caracteres</small>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                <span class="loading" style="display: none;">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Procesando...
                                </span>
                                <span class="btn-text">
                                    <i class="fas fa-save me-2"></i>Guardar
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
    </div>
</div>

<!-- Tabla de Tipos de Dotación -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-lg border-primary border-2 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-table me-2"></i> Tipos de Dotación en la Base de Datos
                        </h4>
                        <button class="btn btn-light btn-sm rounded-pill" onclick="cargarTiposDotacion()" title="Actualizar datos">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered w-100" id="TablaTiposDotacion">
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
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="alert alert-info border-0 rounded-4 shadow-sm" role="alert">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle me-2"></i>Información sobre Tipos de Dotación
                </h6>
                <p class="mb-0">
                    Los Tipos de Dotación registrados aparecerán disponibles para asociar con el reporte de Dotaciones. 
                    Asegúrese de que el nombre sea único y la descripción sea clara y descriptiva, sino no se podrá guardar en la base de datos.
                </p>
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
                    <h6>¿Está seguro de que desea eliminar este tipo de dotación?</h6>
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

<script src="<?= asset('build/js/TipoDotacion/index.js') ?>"></script>