<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Empleado;
use MVC\Router;

class EmpleadoController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('empleado/index', []);
    }

    // API: Obtener Empleados
    public static function obtenerEmpleadosAPI()
    {
        getHeadersApi();

        try {
            $empleados = self::fetchArray("
                SELECT 
                    empleado_id,
                    empleado_codigo,
                    empleado_nombres,
                    empleado_apellidos,
                    empleado_dpi,
                    empleado_puesto,
                    empleado_departamento,
                    empleado_fecha_ingreso,
                    empleado_telefono,
                    empleado_correo,
                    empleado_direccion,
                    empleado_situacion,
                    empleado_fecha_registro
                FROM empleado
                WHERE empleado_situacion = 1
                ORDER BY empleado_apellidos ASC, empleado_nombres ASC
            ");
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $empleados
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener empleados: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'codigo' => 0, 
                'mensaje' => 'Error al obtener los datos', 
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // API: Guardar Empleado
    public static function guardarAPI()
    {
        getHeadersApi();

        $campos = [
            'empleado_codigo', 'empleado_nombres', 'empleado_apellidos'
        ];

        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "Falta el campo $campo"]);
                return;
            }
        }

        // Validaciones específicas
        if (strlen($_POST['empleado_codigo']) > 20) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Código demasiado largo (máximo 20 caracteres)']);
            return;
        }

        if (strlen($_POST['empleado_nombres']) > 100) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Nombres demasiado largos (máximo 100 caracteres)']);
            return;
        }

        if (strlen($_POST['empleado_apellidos']) > 100) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Apellidos demasiado largos (máximo 100 caracteres)']);
            return;
        }

        // Validar correo si se proporciona
        if (!empty($_POST['empleado_correo']) && !filter_var($_POST['empleado_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Formato de correo electrónico inválido']);
            return;
        }

        // Verificar duplicidad de código y DPI
        $existe = Empleado::verificarEmpleadoExistente($_POST['empleado_codigo'], $_POST['empleado_dpi'] ?? null);
        if ($existe['codigo_existe']) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe un empleado con ese código']);
            return;
        }
        if ($existe['dpi_existe']) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe un empleado con ese DPI']);
            return;
        }

        try {
            $empleado = new Empleado([
                'empleado_codigo' => $_POST['empleado_codigo'],
                'empleado_nombres' => $_POST['empleado_nombres'],
                'empleado_apellidos' => $_POST['empleado_apellidos'],
                'empleado_dpi' => $_POST['empleado_dpi'] ?? '',
                'empleado_puesto' => $_POST['empleado_puesto'] ?? '',
                'empleado_departamento' => $_POST['empleado_departamento'] ?? '',
                'empleado_fecha_ingreso' => $_POST['empleado_fecha_ingreso'] ?? '',
                'empleado_telefono' => $_POST['empleado_telefono'] ?? '',
                'empleado_correo' => $_POST['empleado_correo'] ?? '',
                'empleado_direccion' => $_POST['empleado_direccion'] ?? '',
                'empleado_situacion' => 1
            ]);
            
            $empleado->crear();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Empleado registrado correctamente']);
            
        } catch (Exception $e) {
            error_log("Error al guardar empleado: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Modificar Empleado
    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['empleado_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        // Validar campos requeridos
        $campos = [
            'empleado_codigo', 'empleado_nombres', 'empleado_apellidos'
        ];

        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "Falta el campo $campo"]);
                return;
            }
        }

        // Validaciones específicas
        if (strlen($_POST['empleado_codigo']) > 20) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Código demasiado largo']);
            return;
        }

        if (strlen($_POST['empleado_nombres']) > 100) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Nombres demasiado largos']);
            return;
        }

        if (strlen($_POST['empleado_apellidos']) > 100) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Apellidos demasiado largos']);
            return;
        }

        // Validar correo si se proporciona
        if (!empty($_POST['empleado_correo']) && !filter_var($_POST['empleado_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Formato de correo electrónico inválido']);
            return;
        }

        try {
            $empleado = Empleado::find($id);

            if (!$empleado) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Empleado no encontrado']);
                return;
            }

            // Verificar duplicidad (excluyendo el registro actual)
            $existe = Empleado::verificarEmpleadoExistente($_POST['empleado_codigo'], $_POST['empleado_dpi'] ?? null, $id);
            if ($existe['codigo_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Código ya registrado por otro empleado']);
                return;
            }
            if ($existe['dpi_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'DPI ya registrado por otro empleado']);
                return;
            }

            // Usar método personalizado
            $datosActualizacion = [
                'empleado_codigo' => $_POST['empleado_codigo'],
                'empleado_nombres' => $_POST['empleado_nombres'],
                'empleado_apellidos' => $_POST['empleado_apellidos'],
                'empleado_dpi' => $_POST['empleado_dpi'] ?? '',
                'empleado_puesto' => $_POST['empleado_puesto'] ?? '',
                'empleado_departamento' => $_POST['empleado_departamento'] ?? '',
                'empleado_fecha_ingreso' => $_POST['empleado_fecha_ingreso'] ?? '',
                'empleado_telefono' => $_POST['empleado_telefono'] ?? '',
                'empleado_correo' => $_POST['empleado_correo'] ?? '',
                'empleado_direccion' => $_POST['empleado_direccion'] ?? ''
            ];

            $resultado = $empleado->actualizarEmpleadoPersonalizado($datosActualizacion);

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Empleado actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => $resultado['mensaje']]);
            }

        } catch (Exception $e) {
            error_log("Error al modificar empleado: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Eliminar Empleado (lógico)
    public static function eliminarAPI()
    {
        getHeadersApi();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no válido']);
            return;
        }

        try {
            $empleado = Empleado::find($id);
            if (!$empleado) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Empleado no encontrado']);
                return;
            }

            $resultado = $empleado->eliminarLogico();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Empleado eliminado correctamente']);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar el empleado']);
            }
            
        } catch (Exception $e) {
            error_log("Error al eliminar empleado: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Buscar Empleados
    public static function buscarAPI()
    {
        getHeadersApi();

        $criterio = $_GET['criterio'] ?? '';
        $valor = $_GET['valor'] ?? '';

        try {
            $empleados = Empleado::buscarEmpleados($criterio, $valor);
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $empleados
            ]);
            
        } catch (Exception $e) {
            error_log("Error al buscar empleados: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar empleados', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener Estadísticas
    public static function obtenerEstadisticasAPI()
    {
        getHeadersApi();

        try {
            $estadisticas = Empleado::obtenerEstadisticasEmpleados();
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $estadisticas
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener estadísticas', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener Empleados por Departamento
    public static function obtenerPorDepartamentoAPI()
    {
        getHeadersApi();

        $departamento = $_GET['departamento'] ?? '';

        if (!$departamento) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Departamento no especificado']);
            return;
        }

        try {
            $empleados = Empleado::obtenerPorDepartamento($departamento);
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $empleados
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener empleados por departamento: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener empleados', 'detalle' => $e->getMessage()]);
        }
    }
}