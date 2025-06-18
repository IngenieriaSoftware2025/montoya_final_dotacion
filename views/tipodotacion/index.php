<!-- ========================================= -->
<!-- VISTA: tipodotacion/index.php -->
<!-- ========================================= -->

<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 800px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-tags me-2"></i>Registro de Tipos de Dotación
  </h3>

  <form name="FormularioTiposDotacion" id="FormularioTiposDotacion" method="POST">
    <input type="hidden" id="tipo_dotacion_id" name="tipo_dotacion_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="tipo_dotacion_nombre" class="form-label">
          Nombre del Tipo <span class="text-danger">*</span>
        </label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-tag"></i></span>
          <input type="text" 
                 class="form-control" 
                 id="tipo_dotacion_nombre" 
                 name="tipo_dotacion_nombre" 
                 required 
                 placeholder="Ej. BOTAS, CAMISAS, PANTALONES"
                 maxlength="50">
        </div>
        <div class="form-text">
          <small>Mínimo 2 caracteres, máximo 50</small>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label for="tipo_dotacion_descripcion" class="form-label">
        Descripción <span class="text-danger">*</span>
      </label>
      <textarea class="form-control" 
                id="tipo_dotacion_descripcion" 
                name="tipo_dotacion_descripcion" 
                rows="4" 
                required
                placeholder="Ej. Tipo Vulcano, Industria Militar..."
                maxlength="200"></textarea>
      <div class="form-text">
        <small>Máximo 200 caracteres</small>
      </div>
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

<!-- Tabla de tipos de dotación -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Tipos de Dotación Registrados
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableTiposDotacion">
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
    <div class="col-lg-10">
      <div class="alert alert-info" role="alert">
        <h6 class="alert-heading">
          <i class="bi bi-info-circle me-2"></i>Información sobre Tipos de Dotación
        </h6>
        <p class="mb-0">
          Los tipos de dotación registrados aparecerán disponibles para asociar con el inventario. 
          Asegúrese de que el nombre sea único y la descripción sea clara y descriptiva.
          <strong>Tipos principales:</strong> BOTAS, CAMISAS, PANTALONES.
        </p>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/tipodotacion/index.js') ?>"></script>