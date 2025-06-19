<!-- dotacionentrega/index.php -->
<div class="container-fluid mt-4">
  <div class="row">
    
    <!-- FORMULARIO -->
    <div class="col-lg-4">
      <div class="card shadow border-info border-2 rounded-4">
        <div class="card-header bg-info text-white rounded-top-4">
          <h5 class="mb-0">
            <i class="bi bi-truck me-2"></i>Nueva Entrega de Dotación
          </h5>
        </div>
        <div class="card-body">
          <form id="FormularioEntregas" method="POST">
            <input type="hidden" id="entrega_id" name="entrega_id">

            <!-- EMPLEADO -->
            <div class="mb-3">
              <label for="empleado_id" class="form-label">
                Empleado <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="empleado_id" name="empleado_id" required>
                <option value="">Seleccione un empleado</option>
              </select>
            </div>

            <!-- INFORMACIÓN LÍMITE EMPLEADO -->
            <div id="info-limite-empleado" class="mb-3"></div>

            <!-- FECHA ENTREGA -->
            <div class="mb-3">
              <label for="entrega_fecha" class="form-label">
                Fecha de Entrega <span class="text-danger">*</span>
              </label>
              <input type="date" class="form-control" id="entrega_fecha" name="entrega_fecha" required>
            </div>

            <!-- AÑO -->
            <div class="mb-3">
              <label for="entrega_año" class="form-label">
                Año <span class="text-danger">*</span>
              </label>
              <input type="number" class="form-control" id="entrega_año" name="entrega_año" 
                     value="<?= date('Y') ?>" min="2020" max="2030" required>
            </div>

            <!-- ENTREGADO POR -->
            <div class="mb-3">
              <label for="entrega_entregado_por" class="form-label">
                Entregado por
              </label>
              <input type="text" class="form-control" id="entrega_entregado_por" 
                     name="entrega_entregado_por" placeholder="Nombre de quien entrega">
            </div>

            <!-- RECIBIDO POR -->
            <div class="mb-3">
              <label for="entrega_recibido_por" class="form-label">
                Recibido por
              </label>
              <input type="text" class="form-control" id="entrega_recibido_por" 
                     name="entrega_recibido_por" placeholder="Nombre de quien recibe">
            </div>

            <!-- OBSERVACIONES -->
            <div class="mb-3">
              <label for="entrega_observaciones" class="form-label">
                Observaciones
              </label>
              <textarea class="form-control" id="entrega_observaciones" name="entrega_observaciones" 
                        rows="3" placeholder="Observaciones generales..."></textarea>
            </div>

            <!-- SEPARADOR -->
            <hr class="my-4">
            <h6 class="text-info">
              <i class="bi bi-plus-circle me-2"></i>Agregar Artículos a Entregar
            </h6>

            <!-- INVENTARIO DISPONIBLE -->
            <div class="mb-3">
              <label for="dotacion_inv_id" class="form-label">
                Artículo del Inventario
              </label>
              <select class="form-select" id="dotacion_inv_id">
                <option value="">Seleccione un artículo</option>
              </select>
              <div class="form-text">
                <small>Se muestra: Tipo - Talla (Marca) - Stock disponible</small>
              </div>
            </div>

            <!-- CANTIDAD A ENTREGAR -->
            <div class="mb-3">
              <label for="cantidad_entrega" class="form-label">
                Cantidad a Entregar
              </label>
              <input type="number" class="form-control" id="cantidad_entrega" min="1" placeholder="Ej. 1">
              <div class="invalid-feedback">
                La cantidad excede el stock disponible
              </div>
            </div>

            <!-- OBSERVACIONES DETALLE -->
            <div class="mb-3">
              <label for="observaciones_detalle" class="form-label">
                Observaciones del artículo
              </label>
              <input type="text" class="form-control" id="observaciones_detalle" 
                     placeholder="Observaciones específicas...">
            </div>

            <!-- BOTÓN AGREGAR DETALLE -->
            <div class="mb-3">
              <button type="button" class="btn btn-warning w-100" id="BtnAgregarDetalle">
                <i class="bi bi-plus-circle me-2"></i>Agregar a la Entrega
              </button>
            </div>

            <!-- TABLA DETALLES -->
            <div class="mb-3">
              <label class="form-label">Artículos a entregar:</label>
              <div class="table-responsive">
                <table class="table table-sm table-bordered" id="TableDetalles">
                </table>
              </div>
            </div>

            <!-- BOTONES PRINCIPALES -->
            <div class="row g-2">
              <div class="col-6">
                <button type="submit" class="btn btn-info w-100" id="BtnGuardar">
                  <i class="bi bi-check-circle me-1"></i>Registrar Entrega
                </button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-secondary w-100" id="BtnLimpiar">
                  <i class="bi bi-eraser me-1"></i>Limpiar
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABLA ENTREGAS -->
    <div class="col-lg-8">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>Entregas de Dotación Registradas
          </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableEntregas">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- INFORMACIÓN ADICIONAL -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="alert alert-warning" role="alert">
        <h6 class="alert-heading">
          <i class="bi bi-exclamation-triangle me-2"></i>Información Importante sobre Entregas
        </h6>
        <p class="mb-0">
          <strong>Límites:</strong> Cada empleado puede recibir un máximo de <strong>3 entregas por año</strong>.<br>
          <strong>Stock:</strong> Al registrar una entrega, el stock del inventario se reduce automáticamente.<br>
          <strong>Eliminación:</strong> Al eliminar una entrega, el stock se restaura automáticamente.<br>
          <strong>Control:</strong> El sistema actualiza automáticamente el control anual de entregas por empleado.
        </p>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/dotacionentrega/index.js') ?>"></script>