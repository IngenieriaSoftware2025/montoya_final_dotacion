<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 900px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-boxes me-2"></i>Registro de Inventario de Dotaciones
  </h3>

  <form name="FormularioDotaciones" id="FormularioDotaciones" method="POST">
    <input type="hidden" id="dotacion_inv_id" name="dotacion_inv_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="tipo_dotacion_id" class="form-label">
          Tipo de Dotación <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="tipo_dotacion_id" name="tipo_dotacion_id" required>
          <option value="">Seleccione un tipo</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="talla_id" class="form-label">
          Talla <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="talla_id" name="talla_id" required>
          <option value="">Primero seleccione el tipo</option>
        </select>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label for="dotacion_inv_marca" class="form-label">
          Marca <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_marca" 
               name="dotacion_inv_marca" 
               required 
               placeholder="Ej. VULCANO, MILITAR"
               maxlength="50">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_modelo" class="form-label">
          Modelo <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_modelo" 
               name="dotacion_inv_modelo" 
               required 
               placeholder="Ej. Militar Industrial"
               maxlength="50">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_color" class="form-label">
          Color
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_color" 
               name="dotacion_inv_color" 
               placeholder="Ej. Negro, Verde Olivo"
               maxlength="30">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label for="dotacion_inv_cantidad_inicial" class="form-label">
          Cantidad Inicial <span class="text-danger">*</span>
        </label>
        <input type="number" 
               class="form-control" 
               id="dotacion_inv_cantidad_inicial" 
               name="dotacion_inv_cantidad_inicial" 
               required 
               min="1"
               placeholder="Ej. 50">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_cantidad_minima" class="form-label">
          Stock Mínimo
        </label>
        <input type="number" 
               class="form-control" 
               id="dotacion_inv_cantidad_minima" 
               name="dotacion_inv_cantidad_minima" 
               value="5"
               min="0"
               placeholder="Ej. 5">
      </div>
      <div class="col-md-4">
        <label for="dotacion_inv_precio_unitario" class="form-label">
          Precio Unitario (Q)
        </label>
        <input type="number" 
               class="form-control" 
               id="dotacion_inv_precio_unitario" 
               name="dotacion_inv_precio_unitario" 
               step="0.01"
               min="0"
               placeholder="Ej. 650.00">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="dotacion_inv_proveedor" class="form-label">
          Proveedor
        </label>
        <input type="text" 
               class="form-control" 
               id="dotacion_inv_proveedor" 
               name="dotacion_inv_proveedor" 
               placeholder="Ej. Industria Militar"
               maxlength="100">
      </div>
      <div class="col-md-6">
        <label for="dotacion_inv_fecha_ingreso" class="form-label">
          Fecha de Ingreso
        </label>
        <input type="date" 
               class="form-control" 
               id="dotacion_inv_fecha_ingreso" 
               name="dotacion_inv_fecha_ingreso">
      </div>
    </div>

    <div class="mb-3">
      <label for="dotacion_inv_observaciones" class="form-label">
        Observaciones
      </label>
      <textarea class="form-control" 
                id="dotacion_inv_observaciones" 
                name="dotacion_inv_observaciones" 
                rows="3" 
                placeholder="Observaciones adicionales..."
                maxlength="500"></textarea>
    </div>

    <div class="row justify-content-center mt-4 g-2">
      <div class="col-auto">
        <button type="submit" class="btn btn-success px-4" id="BtnGuardar">
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-warning d-none px-4" id="BtnModificar">
          <i class="bi bi-pencil-square me-1"></i> Modificar
        </button>
      </div>
      <div class="col-auto">
        <button type="reset" class="btn btn-secondary px-4" id="BtnLimpiar">
          <i class="bi bi-eraser me-1"></i> Limpiar
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Tabla de inventario -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Inventario de Dotaciones Registrado
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableDotaciones">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/dotacioninventario/index.js') ?>"></script>
