<?php
namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\TipoDotacion;

class TipoDotacionController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('tipodotacion/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $condiciones = ["tipo_dotacion_situacion = 1"];
            $where = implode(" AND ", $condiciones);
            $sql = "SELECT * FROM mrml_tipo_dotacion WHERE $where ORDER BY tipo_dotacion_nombre ASC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tipos de dotación obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los tipos de dotación',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['tipo_dotacion_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre del tipo de dotación es obligatorio']);
            return;
        }

        if (strlen($_POST['tipo_dotacion_nombre']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre debe tener al menos 2 caracteres']);
            return;
        }

        try {
            $tipoDotacion = new TipoDotacion([
                'tipo_dotacion_nombre' => trim($_POST['tipo_dotacion_nombre']),
                'tipo_dotacion_descripcion' => trim($_POST['tipo_dotacion_descripcion'] ?? ''),
                'tipo_dotacion_situacion' => 1
            ]);

            $tipoDotacion->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación registrado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['tipo_dotacion_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        // Validaciones básicas
        if (empty($_POST['tipo_dotacion_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre del tipo de dotación es obligatorio']);
            return;
        }

        if (strlen($_POST['tipo_dotacion_nombre']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre debe tener al menos 2 caracteres']);
            return;
        }

        try {
            $tipoDotacion = TipoDotacion::find($id);

            if (!$tipoDotacion) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no encontrado']);
                return;
            }

            $tipoDotacion->sincronizar([
                'tipo_dotacion_nombre' => trim($_POST['tipo_dotacion_nombre']),
                'tipo_dotacion_descripcion' => trim($_POST['tipo_dotacion_descripcion'] ?? ''),
                'tipo_dotacion_situacion' => 1
            ]);

            $resultado = $tipoDotacion->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar el tipo de dotación']);
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
            $tipoDotacion = TipoDotacion::find($id);
            if (!$tipoDotacion) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no encontrado']);
                return;
            }

            $tipoDotacion->sincronizar(['tipo_dotacion_situacion' => 0]);
            $tipoDotacion->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación eliminado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}