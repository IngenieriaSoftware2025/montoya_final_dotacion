<?php
namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Empleado;

class EmpleadoController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('empleado/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $condiciones = ["empleado_situacion = 1"];
            $where = implode(" AND ", $condiciones);
            $sql = "SELECT * FROM mrml_empleado WHERE $where ORDER BY empleado_nombres ASC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Empleados obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los empleados',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['empleado_nombres'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los nombres son obligatorios']);
            return;
        }

        if (empty($_POST['empleado_apellidos'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los apellidos son obligatorios']);
            return;
        }

        if (strlen($_POST['empleado_nombres']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los nombres deben tener al menos 2 caracteres']);
            return;
        }

        if (strlen($_POST['empleado_apellidos']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los apellidos deben tener al menos 2 caracteres']);
            return;
        }

        // Validar DPI
        if (!empty($_POST['empleado_dpi']) && strlen($_POST['empleado_dpi']) != 13) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI debe tener 13 dígitos']);
            return;
        }

        // Validar correo
        if (!empty($_POST['empleado_correo']) && !filter_var($_POST['empleado_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Correo inválido']);
            return;
        }

        // Verificar duplicidad
        if (!empty($_POST['empleado_dpi']) || !empty($_POST['empleado_correo'])) {
            $existe = Empleado::verificarDpiCorreoExistente($_POST['empleado_dpi'] ?? '', $_POST['empleado_correo'] ?? '');
            if ($existe['dpi_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'DPI ya registrado']);
                return;
            }
            if ($existe['correo_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Correo ya registrado']);
                return;
            }
        }

        try {
            $empleado = new Empleado([
                'empleado_nombres' => trim($_POST['empleado_nombres']),
                'empleado_apellidos' => trim($_POST['empleado_apellidos']),
                'empleado_dpi' => trim($_POST['empleado_dpi'] ?? ''),
                'empleado_puesto' => trim($_POST['empleado_puesto'] ?? ''),
                'empleado_departamento' => trim($_POST['empleado_departamento'] ?? ''),
                'empleado_fecha_ingreso' => $_POST['empleado_fecha_ingreso'] ?? null,
                'empleado_telefono' => trim($_POST['empleado_telefono'] ?? ''),
                'empleado_correo' => trim($_POST['empleado_correo'] ?? ''),
                'empleado_direccion' => trim($_POST['empleado_direccion'] ?? ''),
                'empleado_situacion' => 1
            ]);

            $empleado->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Empleado registrado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['empleado_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        // Validaciones básicas (igual que en guardar)
        if (empty($_POST['empleado_nombres'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los nombres son obligatorios']);
            return;
        }

        if (empty($_POST['empleado_apellidos'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los apellidos son obligatorios']);
            return;
        }

        if (strlen($_POST['empleado_nombres']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los nombres deben tener al menos 2 caracteres']);
            return;
        }

        if (strlen($_POST['empleado_apellidos']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los apellidos deben tener al menos 2 caracteres']);
            return;
        }

        // Validar DPI
        if (!empty($_POST['empleado_dpi']) && strlen($_POST['empleado_dpi']) != 13) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI debe tener 13 dígitos']);
            return;
        }

        // Validar correo
        if (!empty($_POST['empleado_correo']) && !filter_var($_POST['empleado_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Correo inválido']);
            return;
        }

        try {
            $empleado = Empleado::find($id);

            if (!$empleado) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Empleado no encontrado']);
                return;
            }

            // Verificar duplicidad (excluyendo el empleado actual)
            if (!empty($_POST['empleado_dpi']) || !empty($_POST['empleado_correo'])) {
                $existe = Empleado::verificarDpiCorreoExistente($_POST['empleado_dpi'] ?? '', $_POST['empleado_correo'] ?? '', $id);
                if ($existe['dpi_existe']) {
                    http_response_code(400);
                    echo json_encode(['codigo' => 0, 'mensaje' => 'DPI ya registrado por otro empleado']);
                    return;
                }
                if ($existe['correo_existe']) {
                    http_response_code(400);
                    echo json_encode(['codigo' => 0, 'mensaje' => 'Correo ya registrado por otro empleado']);
                    return;
                }
            }

            $empleado->sincronizar([
                'empleado_nombres' => trim($_POST['empleado_nombres']),
                'empleado_apellidos' => trim($_POST['empleado_apellidos']),
                'empleado_dpi' => trim($_POST['empleado_dpi'] ?? ''),
                'empleado_puesto' => trim($_POST['empleado_puesto'] ?? ''),
                'empleado_departamento' => trim($_POST['empleado_departamento'] ?? ''),
                'empleado_fecha_ingreso' => $_POST['empleado_fecha_ingreso'] ?? null,
                'empleado_telefono' => trim($_POST['empleado_telefono'] ?? ''),
                'empleado_correo' => trim($_POST['empleado_correo'] ?? ''),
                'empleado_direccion' => trim($_POST['empleado_direccion'] ?? ''),
                'empleado_situacion' => 1
            ]);

            $resultado = $empleado->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Empleado actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar el empleado']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function eliminarAPI()
    {
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

            $empleado->sincronizar(['empleado_situacion' => 0]);
            $empleado->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Empleado eliminado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}