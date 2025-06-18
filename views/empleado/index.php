<!-- Contenedor principal del formulario -->
<div class="container mt-5 p-4 rounded-4 shadow-lg bg-light" style="max-width: 900px;">
  <h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-people me-2"></i>Registro de Empleados
  </h3>

  <form name="FormularioEmpleados" id="FormularioEmpleados" method="POST">
    <input type="hidden" id="empleado_id" name="empleado_id">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_nombres" class="form-label">
          Nombres <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_nombres" 
               name="empleado_nombres" 
               required 
               placeholder="Ej. Juan Carlos"
               maxlength="100">
      </div>
      <div class="col-md-6">
        <label for="empleado_apellidos" class="form-label">
          Apellidos <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_apellidos" 
               name="empleado_apellidos" 
               required 
               placeholder="Ej. García López"
               maxlength="100">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_dpi" class="form-label">
          DPI
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_dpi" 
               name="empleado_dpi" 
               placeholder="1234567890123"
               maxlength="13">
      </div>
      <div class="col-md-6">
        <label for="empleado_telefono" class="form-label">
          Teléfono
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_telefono" 
               name="empleado_telefono" 
               placeholder="12345678"
               maxlength="15">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_puesto" class="form-label">
          Puesto
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_puesto" 
               name="empleado_puesto" 
               placeholder="Ej. Operario, Supervisor"
               maxlength="50">
      </div>
      <div class="col-md-6">
        <label for="empleado_departamento" class="form-label">
          Departamento
        </label>
        <input type="text" 
               class="form-control" 
               id="empleado_departamento" 
               name="empleado_departamento" 
               placeholder="Ej. Producción, Calidad"
               maxlength="50">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="empleado_correo" class="form-label">
          Correo Electrónico
        </label>
        <input type="email" 
               class="form-control" 
               id="empleado_correo" 
               name="empleado_correo" 
               placeholder="empleado@empresa.com"
               maxlength="100">
      </div>
      <div class="col-md-6">
        <label for="empleado_fecha_ingreso" class="form-label">
          Fecha de Ingreso
        </label>
        <input type="date" 
               class="form-control" 
               id="empleado_fecha_ingreso" 
               name="empleado_fecha_ingreso">
      </div>
    </div>

    <div class="mb-3">
      <label for="empleado_direccion" class="form-label">
        Dirección
      </label>
      <textarea class="form-control" 
                id="empleado_direccion" 
                name="empleado_direccion" 
                rows="3" 
                placeholder="Dirección completa del empleado..."
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

<!-- Tabla de empleados -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card shadow border-primary border-2 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="text-center mb-0">
            <i class="bi bi-table me-2"></i> Empleados Registrados
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableEmpleados">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('build/js/empleado/index.js') ?>"></script>