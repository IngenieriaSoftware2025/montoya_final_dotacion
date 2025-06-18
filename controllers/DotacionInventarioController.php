<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\DotacionInventario;

class DotacionInventarioController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('dotacioninventario/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT di.*, td.tipo_dotacion_nombre, t.talla_nombre, t.talla_descripcion
                    FROM mrml_dotacion_inventario di
                    JOIN mrml_tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                    JOIN mrml_talla t ON di.talla_id = t.talla_id
                    WHERE di.dotacion_inv_situacion = 1 
                    ORDER BY td.tipo_dotacion_nombre, t.talla_id ASC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario obtenido correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el inventario',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarDisponibleAPI()
    {
        $tipoDotacionId = $_GET['tipo_dotacion_id'] ?? null;
        $tallaId = $_GET['talla_id'] ?? null;

        try {
            $data = DotacionInventario::obtenerInventarioDisponible($tipoDotacionId, $tallaId);

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario disponible obtenido',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener inventario disponible',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['tipo_dotacion_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de dotación es obligatorio']);
            return;
        }

        if (empty($_POST['talla_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La talla es obligatoria']);
            return;
        }

        if (empty($_POST['dotacion_inv_cantidad_inicial']) || !is_numeric($_POST['dotacion_inv_cantidad_inicial']) || $_POST['dotacion_inv_cantidad_inicial'] <= 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La cantidad inicial debe ser un número mayor a 0']);
            return;
        }

        if (!empty($_POST['dotacion_inv_precio_unitario']) && (!is_numeric($_POST['dotacion_inv_precio_unitario']) || $_POST['dotacion_inv_precio_unitario'] < 0)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El precio unitario debe ser un número mayor o igual a 0']);
            return;
        }

        try {
            $cantidadInicial = intval($_POST['dotacion_inv_cantidad_inicial']);
            
            $inventario = new DotacionInventario([
                'tipo_dotacion_id' => intval($_POST['tipo_dotacion_id']),
                'talla_id' => intval($_POST['talla_id']),
                'dotacion_inv_marca' => trim($_POST['dotacion_inv_marca'] ?? ''),
                'dotacion_inv_modelo' => trim($_POST['dotacion_inv_modelo'] ?? ''),
                'dotacion_inv_color' => trim($_POST['dotacion_inv_color'] ?? ''),
                'dotacion_inv_material' => trim($_POST['dotacion_inv_material'] ?? ''),
                'dotacion_inv_cantidad_inicial' => $cantidadInicial,
                'dotacion_inv_cantidad_actual' => $cantidadInicial, // Al crear, actual = inicial
                'dotacion_inv_cantidad_minima' => intval($_POST['dotacion_inv_cantidad_minima'] ?? 5),
                'dotacion_inv_precio_unitario' => floatval($_POST['dotacion_inv_precio_unitario'] ?? 0),
                'dotacion_inv_proveedor' => trim($_POST['dotacion_inv_proveedor'] ?? ''),
                'dotacion_inv_fecha_ingreso' => $_POST['dotacion_inv_fecha_ingreso'] ?? null,
                'dotacion_inv_fecha_vencimiento' => $_POST['dotacion_inv_fecha_vencimiento'] ?? null,
                'dotacion_inv_observaciones' => trim($_POST['dotacion_inv_observaciones'] ?? ''),
                'dotacion_inv_situacion' => 1
            ]);

            $inventario->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Inventario registrado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['dotacion_inv_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        // Validaciones básicas
        if (empty($_POST['tipo_dotacion_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de dotación es obligatorio']);
            return;
        }

        if (empty($_POST['talla_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La talla es obligatoria']);
            return;
        }

        if (empty($_POST['dotacion_inv_cantidad_actual']) || !is_numeric($_POST['dotacion_inv_cantidad_actual']) || $_POST['dotacion_inv_cantidad_actual'] < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La cantidad actual debe ser un número mayor o igual a 0']);
            return;
        }

        try {
            $inventario = DotacionInventario::find($id);

            if (!$inventario) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Inventario no encontrado']);
                return;
            }

            $inventario->sincronizar([
                'tipo_dotacion_id' => intval($_POST['tipo_dotacion_id']),
                'talla_id' => intval($_POST['talla_id']),
                'dotacion_inv_marca' => trim($_POST['dotacion_inv_marca'] ?? ''),
                'dotacion_inv_modelo' => trim($_POST['dotacion_inv_modelo'] ?? ''),
                'dotacion_inv_color' => trim($_POST['dotacion_inv_color'] ?? ''),
                'dotacion_inv_material' => trim($_POST['dotacion_inv_material'] ?? ''),
                'dotacion_inv_cantidad_actual' => intval($_POST['dotacion_inv_cantidad_actual']),
                'dotacion_inv_cantidad_minima' => intval($_POST['dotacion_inv_cantidad_minima'] ?? 5),
                'dotacion_inv_precio_unitario' => floatval($_POST['dotacion_inv_precio_unitario'] ?? 0),
                'dotacion_inv_proveedor' => trim($_POST['dotacion_inv_proveedor'] ?? ''),
                'dotacion_inv_fecha_vencimiento' => $_POST['dotacion_inv_fecha_vencimiento'] ?? null,
                'dotacion_inv_observaciones' => trim($_POST['dotacion_inv_observaciones'] ?? ''),
                'dotacion_inv_situacion' => 1
            ]);

            $resultado = $inventario->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Inventario actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar el inventario']);
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
            $inventario = DotacionInventario::find($id);
            if (!$inventario) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Inventario no encontrado']);
                return;
            }

            $inventario->sincronizar(['dotacion_inv_situacion' => 0]);
            $inventario->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Inventario eliminado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}