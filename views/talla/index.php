<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 800px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-rulers me-2"></i>Registro de Tallas
  </h3>

  <form name="FormularioTallas" id="FormularioTallas" method="POST">
    <input type="hidden" id="talla_id" name="talla_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="talla_nombre" class="form-label">
          Nombre de la Talla <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="talla_nombre" 
               name="talla_nombre" 
               required 
               placeholder="Ej. 35, XS, M, L"
               maxlength="10">
      </div>
      <div class="col-md-6">
        <label for="talla_tipo" class="form-label">
          Tipo de Talla <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="talla_tipo" name="talla_tipo" required>
          <option value="">Seleccione el tipo</option>
          <option value="CALZADO">CALZADO</option>
          <option value="ROPA">ROPA</option>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label for="talla_descripcion" class="form-label">
        Descripción <span class="text-danger">*</span>
      </label>
      <input type="text" 
             class="form-control" 
             id="talla_descripcion" 
             name="talla_descripcion" 
             required
             placeholder="Ej. Talla 35, Extra Small, Medium..."
             maxlength="50">
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

<!-- Tabla de tallas -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Tallas Registradas
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableTallas">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/talla/index.js') ?>"></script>