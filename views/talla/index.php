<div class="container mt-4">
    <div class="row justify-content-center">
        <!-- Formulario -->
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0 text-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span id="form-title">Registrar Talla</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="formTalla">
                        <input type="hidden" id="talla_id" name="talla_id">
                        
                        <!-- Campo Código -->
                        <div class="mb-3">
                            <label for="talla_codigo" class="form-label">
                                <i class="fas fa-tag me-1 text-success"></i>
                                <strong>Código de Talla *</strong>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="talla_codigo" 
                                   name="talla_codigo" 
                                   placeholder="Ej: XS, S, M, L, XL, 38, 40..."
                                   required
                                   minlength="1"
                                   maxlength="10">
                            <div class="invalid-feedback">
                                El código es requerido (máximo 10 caracteres)
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="mb-3">
                            <label for="talla_descripcion" class="form-label">
                                <i class="fas fa-align-left me-1 text-success"></i>
                                <strong>Descripción</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="talla_descripcion" 
                                      name="talla_descripcion" 
                                      placeholder="Descripción de la talla..."
                                      rows="3"
                                      maxlength="50"></textarea>
                            <div class="form-text">
                                <small class="text-muted">Máximo 50 caracteres</small>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
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

<!-- Tabla de Tallas -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-lg border-success border-2 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-table me-2"></i> Tallas en la Base de Datos
                        </h4>
                        <button class="btn btn-light btn-sm rounded-pill" onclick="cargarTallas()" title="Actualizar datos">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered w-100" id="TablaTallas">
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
            <div class="alert alert-success border-0 rounded-4 shadow-sm" role="alert">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle me-2"></i>Información sobre Tallas
                </h6>
                <p class="mb-0">
                    Las tallas registradas aparecerán disponibles para asociar con las dotaciones. 
                    Asegúrese de que el código sea único y descriptivo. Puede usar códigos como: XS, S, M, L, XL para ropa o números para calzado.
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
                    <h6>¿Está seguro de que desea eliminar esta talla?</h6>
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

<script src="<?= asset('build/js/Talla/index.js') ?>"></script>