<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Talla;

class TallaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('talla/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $condiciones = ["talla_situacion = 1"];
            $where = implode(" AND ", $condiciones);
            $sql = "SELECT * FROM mrml_talla WHERE $where ORDER BY talla_tipo ASC, talla_id ASC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tallas obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las tallas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarPorTipoAPI()
    {
        $tipo = $_GET['tipo'] ?? '';
        
        if (empty($tipo)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de talla requerido']);
            return;
        }

        try {
            $data = Talla::obtenerTallasPorTipo($tipo);

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tallas obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las tallas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['talla_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre de la talla es obligatorio']);
            return;
        }

        if (empty($_POST['talla_descripcion'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La descripción de la talla es obligatoria']);
            return;
        }

        if (empty($_POST['talla_tipo'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de talla es obligatorio']);
            return;
        }

        if (!in_array($_POST['talla_tipo'], ['CALZADO', 'ROPA'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de talla debe ser CALZADO o ROPA']);
            return;
        }

        try {
            // Verificar si ya existe la combinación nombre + tipo
            $existe = self::fetchArray("SELECT COUNT(*) as count FROM mrml_talla 
                                       WHERE talla_nombre = '" . trim($_POST['talla_nombre']) . "' 
                                       AND talla_tipo = '" . trim($_POST['talla_tipo']) . "' 
                                       AND talla_situacion = 1");
            
            if ($existe[0]['count'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe una talla con ese nombre y tipo']);
                return;
            }

            $talla = new Talla([
                'talla_nombre' => trim($_POST['talla_nombre']),
                'talla_descripcion' => trim($_POST['talla_descripcion']),
                'talla_tipo' => trim($_POST['talla_tipo']),
                'talla_situacion' => 1
            ]);

            $resultado = $talla->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Talla registrada correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['talla_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        // Validaciones básicas
        if (empty($_POST['talla_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre de la talla es obligatorio']);
            return;
        }

        if (empty($_POST['talla_descripcion'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La descripción de la talla es obligatoria']);
            return;
        }

        if (empty($_POST['talla_tipo'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de talla es obligatorio']);
            return;
        }

        if (!in_array($_POST['talla_tipo'], ['CALZADO', 'ROPA'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de talla debe ser CALZADO o ROPA']);
            return;
        }

        try {
            $talla = Talla::find($id);

            if (!$talla) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Talla no encontrada']);
                return;
            }

            // Verificar duplicidad excluyendo el registro actual
            $existe = self::fetchArray("SELECT COUNT(*) as count FROM mrml_talla 
                                       WHERE talla_nombre = '" . trim($_POST['talla_nombre']) . "' 
                                       AND talla_tipo = '" . trim($_POST['talla_tipo']) . "' 
                                       AND talla_situacion = 1 
                                       AND talla_id != " . intval($id));
            
            if ($existe[0]['count'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otra talla con ese nombre y tipo']);
                return;
            }

            $talla->sincronizar([
                'talla_nombre' => trim($_POST['talla_nombre']),
                'talla_descripcion' => trim($_POST['talla_descripcion']),
                'talla_tipo' => trim($_POST['talla_tipo']),
                'talla_situacion' => 1
            ]);

            $resultado = $talla->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Talla actualizada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar la talla']);
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
            $talla = Talla::find($id);
            if (!$talla) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Talla no encontrada']);
                return;
            }

            // Verificar si la talla está siendo usada en inventario
            $enUso = self::fetchArray("SELECT COUNT(*) as count FROM mrml_dotacion_inventario 
                                      WHERE talla_id = " . intval($id) . " AND dotacion_inv_situacion = 1");
            
            if ($enUso[0]['count'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se puede eliminar la talla porque está siendo utilizada en el inventario']);
                return;
            }

            $talla->sincronizar(['talla_situacion' => 0]);
            $resultado = $talla->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Talla eliminada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar la talla']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}