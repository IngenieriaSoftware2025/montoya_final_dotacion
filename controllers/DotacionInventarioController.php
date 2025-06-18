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

    public static function guardarAPI()
    {
        getHeadersApi();

        $validacion = self::validarCampos($_POST);
        if ($validacion !== true) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => $validacion]);
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
                'dotacion_inv_cantidad_actual' => $cantidadInicial,
                'dotacion_inv_cantidad_minima' => intval($_POST['dotacion_inv_cantidad_minima'] ?? 5),
                'dotacion_inv_precio_unitario' => floatval($_POST['dotacion_inv_precio_unitario'] ?? 0),
                'dotacion_inv_proveedor' => trim($_POST['dotacion_inv_proveedor'] ?? ''),
                'dotacion_inv_fecha_ingreso' => $_POST['dotacion_inv_fecha_ingreso'] ?? null,
                'dotacion_inv_fecha_vencimiento' => $_POST['dotacion_inv_fecha_vencimiento'] ?? null,
                'dotacion_inv_observaciones' => trim($_POST['dotacion_inv_observaciones'] ?? ''),
                'dotacion_inv_situacion' => 1
            ]);

            $resultado = $inventario->crear();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Inventario registrado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear el inventario']);
            }

        } catch (Exception $e) {
            error_log("Error en guardarAPI: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function buscarAPI()
    {
        try {
            $data = self::fetchArray("
                SELECT
                    ina.dotacion_inv_id,
                    ina.tipo_dotacion_id,
                    ina.talla_id,
                    ina.dotacion_inv_marca,
                    ina.dotacion_inv_modelo,
                    ina.dotacion_inv_color,
                    ina.dotacion_inv_material,
                    ina.dotacion_inv_cantidad_inicial,
                    ina.dotacion_inv_cantidad_actual,
                    ina.dotacion_inv_cantidad_minima,
                    ina.dotacion_inv_precio_unitario,
                    ina.dotacion_inv_proveedor,
                    ina.dotacion_inv_fecha_ingreso,
                    ina.dotacion_inv_fecha_vencimiento,
                    ina.dotacion_inv_observaciones,
                    ina.dotacion_inv_situacion,
                    ina.dotacion_inv_fecha_registro,
                    ta.talla_nombre,
                    do.tipo_dotacion_nombre
                FROM mrml_dotacion_inventario ina
                INNER JOIN mrml_talla ta ON ta.talla_id = ina.talla_id
                INNER JOIN mrml_tipo_dotacion do ON do.tipo_dotacion_id = ina.tipo_dotacion_id
                WHERE ina.dotacion_inv_situacion = 1
                ORDER BY do.tipo_dotacion_nombre ASC, ta.talla_nombre ASC
            ");

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario obtenido correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            error_log("Error en buscarAPI: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el inventario',
                'detalle' => $e->getMessage()
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
            error_log("Error en buscarDisponibleAPI: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener inventario disponible',
                'detalle' => $e->getMessage()
            ]);
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

        $validacion = self::validarCampos($_POST, false);
        if ($validacion !== true) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => $validacion]);
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
            error_log("Error en modificarAPI: " . $e->getMessage());
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

            // Verificar si el inventario está siendo usado en entregas
            $enUso = self::fetchArray("SELECT COUNT(*) as count FROM mrml_dotacion_entrega_detalle 
                                      WHERE dotacion_inv_id = " . intval($id) . " AND entrega_det_situacion = 1");
            
            if ($enUso[0]['count'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se puede eliminar el inventario porque está siendo utilizado en entregas']);
                return;
            }

            $inventario->sincronizar(['dotacion_inv_situacion' => 0]);
            $resultado = $inventario->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Inventario eliminado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar el inventario']);
            }

        } catch (Exception $e) {
            error_log("Error en eliminarAPI: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // Función para validar campos
    private static function validarCampos($datos, $esCreacion = true)
    {
        // Validaciones básicas
        if (empty($datos['tipo_dotacion_id'])) {
            return 'El tipo de dotación es obligatorio';
        }

        if (empty($datos['talla_id'])) {
            return 'La talla es obligatoria';
        }

        if (empty($datos['dotacion_inv_marca'])) {
            return 'La marca es obligatoria';
        }

        if (empty($datos['dotacion_inv_modelo'])) {
            return 'El modelo es obligatorio';
        }

        if ($esCreacion) {
            if (empty($datos['dotacion_inv_cantidad_inicial']) || !is_numeric($datos['dotacion_inv_cantidad_inicial']) || $datos['dotacion_inv_cantidad_inicial'] <= 0) {
                return 'La cantidad inicial debe ser un número mayor a 0';
            }
        } else {
            if (empty($datos['dotacion_inv_cantidad_actual']) || !is_numeric($datos['dotacion_inv_cantidad_actual']) || $datos['dotacion_inv_cantidad_actual'] < 0) {
                return 'La cantidad actual debe ser un número mayor o igual a 0';
            }
        }

        if (!empty($datos['dotacion_inv_precio_unitario']) && (!is_numeric($datos['dotacion_inv_precio_unitario']) || $datos['dotacion_inv_precio_unitario'] < 0)) {
            return 'El precio unitario debe ser un número mayor o igual a 0';
        }

        // Validar que el tipo de dotación existe
        $tipoExiste = self::fetchArray("SELECT COUNT(*) as count FROM mrml_tipo_dotacion 
                                       WHERE tipo_dotacion_id = " . intval($datos['tipo_dotacion_id']) . " 
                                       AND tipo_dotacion_situacion = 1");
        
        if ($tipoExiste[0]['count'] == 0) {
            return 'El tipo de dotación seleccionado no es válido';
        }

        // Validar que la talla existe
        $tallaExiste = self::fetchArray("SELECT COUNT(*) as count FROM mrml_talla 
                                        WHERE talla_id = " . intval($datos['talla_id']) . " 
                                        AND talla_situacion = 1");
        
        if ($tallaExiste[0]['count'] == 0) {
            return 'La talla seleccionada no es válida';
        }

        return true;
    }
}