<!-- dotacionsolicitud/index.php -->
<div class="container-fluid mt-4">
  <div class="row">
    
    <!-- FORMULARIO -->
    <div class="col-lg-4">
      <div class="card shadow border-success border-2 rounded-4">
        <div class="card-header bg-success text-white rounded-top-4">
          <h5 class="mb-0">
            <i class="bi bi-clipboard-plus me-2"></i>Nueva Solicitud de Dotación
          </h5>
        </div>
        <div class="card-body">
          <form id="FormularioSolicitudes" method="POST">
            <input type="hidden" id="solicitud_id" name="solicitud_id">

            <!-- EMPLEADO -->
            <div class="mb-3">
              <label for="empleado_id" class="form-label">
                Empleado <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="empleado_id" name="empleado_id" required>
                <option value="">Seleccione un empleado</option>
              </select>
            </div>

            <!-- FECHA SOLICITUD -->
            <div class="mb-3">
              <label for="solicitud_fecha" class="form-label">
                Fecha de Solicitud <span class="text-danger">*</span>
              </label>
              <input type="date" class="form-control" id="solicitud_fecha" name="solicitud_fecha" 
                     value="<?= date('Y-m-d') ?>" required>
            </div>

            <!-- OBSERVACIONES -->
            <div class="mb-3">
              <label for="solicitud_observaciones" class="form-label">
                Observaciones
              </label>
              <textarea class="form-control" id="solicitud_observaciones" name="solicitud_observaciones" 
                        rows="3" placeholder="Observaciones generales..."></textarea>
            </div>

            <!-- SEPARADOR -->
            <hr class="my-4">
            <h6 class="text-primary">
              <i class="bi bi-plus-circle me-2"></i>Agregar Artículos
            </h6>

            <!-- TIPO DOTACIÓN -->
            <div class="mb-3">
              <label for="tipo_dotacion_id" class="form-label">
                Tipo de Dotación
              </label>
              <select class="form-select" id="tipo_dotacion_id">
                <option value="">Seleccione un tipo</option>
              </select>
            </div>

            <!-- TALLA -->
            <div class="mb-3">
              <label for="talla_id" class="form-label">
                Talla
              </label>
              <select class="form-select" id="talla_id">
                <option value="">Primero seleccione el tipo</option>
              </select>
            </div>

            <!-- CANTIDAD -->
            <div class="mb-3">
              <label for="cantidad" class="form-label">
                Cantidad
              </label>
              <input type="number" class="form-control" id="cantidad" min="1" placeholder="Ej. 2">
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
              <button type="button" class="btn btn-info w-100" id="BtnAgregarDetalle">
                <i class="bi bi-plus-circle me-2"></i>Agregar Artículo
              </button>
            </div>

            <!-- TABLA DETALLES -->
            <div class="mb-3">
              <label class="form-label">Artículos solicitados:</label>
              <div class="table-responsive">
                <table class="table table-sm table-bordered" id="TableDetalles">
                </table>
              </div>
            </div>

            <!-- BOTONES PRINCIPALES -->
            <div class="row g-2">
              <div class="col-6">
                <button type="submit" class="btn btn-success w-100" id="BtnGuardar">
                  <i class="bi bi-check-circle me-1"></i>Guardar
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

    <!-- TABLA SOLICITUDES -->
    <div class="col-lg-8">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>Solicitudes de Dotación Registradas
          </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableSolicitudes">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- INFORMACIÓN ADICIONAL -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="alert alert-info" role="alert">
        <h6 class="alert-heading">
          <i class="bi bi-info-circle me-2"></i>Información sobre Solicitudes
        </h6>
        <p class="mb-0">
          <strong>Estados:</strong> 
          <span class="badge bg-warning text-dark me-2">PENDIENTE</span>
          <span class="badge bg-success me-2">APROBADA</span>
          <span class="badge bg-danger me-2">RECHAZADA</span>
          <span class="badge bg-primary">ENTREGADA</span>
          <br><br>
          Solo las solicitudes <strong>PENDIENTES</strong> pueden ser aprobadas o rechazadas.
          Las solicitudes <strong>PENDIENTES</strong> y <strong>RECHAZADAS</strong> pueden eliminarse.
        </p>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/dotacionsolicitud/index.js') ?>"></script>