<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\DotacionInventario;
use Model\TipoDotacion;
use Model\Talla;
use MVC\Router;

class DotacionInventarioController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('DotacionInventario/index', []);
    }

    // API: Obtener Inventario (COPIANDO LA ESTRATEGIA QUE FUNCIONA)
    public static function obtenerInventarioAPI()
    {
        getHeadersApi();

        try {
            // Usando la misma estrategia del InventarioController que funciona
            $inventarios = self::fetchArray("
                SELECT 
                    i.dotacion_inv_id,
                    i.dotacion_inv_codigo,
                    i.tipo_dotacion_id,
                    i.talla_id,
                    i.dotacion_inv_marca,
                    i.dotacion_inv_modelo,
                    i.dotacion_inv_color,
                    i.dotacion_inv_material,
                    i.dotacion_inv_cantidad_inicial,
                    i.dotacion_inv_cantidad_actual,
                    i.dotacion_inv_cantidad_minima,
                    i.dotacion_inv_precio_unitario,
                    i.dotacion_inv_proveedor,
                    i.dotacion_inv_situacion,
                    td.tipo_dotacion_nombre,
                    t.talla_codigo
                FROM dotacion_inventario i
                INNER JOIN tipo_dotacion td ON i.tipo_dotacion_id = td.tipo_dotacion_id
                INNER JOIN talla t ON i.talla_id = t.talla_id
                WHERE i.dotacion_inv_situacion = 1
                ORDER BY i.dotacion_inv_codigo ASC
            ");
            
            error_log("=== USANDO ESTRATEGIA EXITOSA ===");
            error_log("Registros encontrados: " . count($inventarios));
            
            if (!empty($inventarios)) {
                error_log("Primer registro: " . print_r($inventarios[0], true));
            }
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $inventarios
            ]);
            
        } catch (Exception $e) {
            error_log("❌ ERROR: " . $e->getMessage());
            
            // Fallback: consulta sin JOINs como en el proyecto exitoso
            try {
                error_log("🔄 Intentando fallback...");
                
                $inventarioSimple = self::fetchArray("
                    SELECT 
                        dotacion_inv_id,
                        dotacion_inv_codigo,
                        tipo_dotacion_id,
                        talla_id,
                        dotacion_inv_marca,
                        dotacion_inv_modelo,
                        dotacion_inv_cantidad_inicial,
                        dotacion_inv_cantidad_actual,
                        dotacion_inv_precio_unitario,
                        dotacion_inv_situacion
                    FROM dotacion_inventario
                    WHERE dotacion_inv_situacion = 1
                    ORDER BY dotacion_inv_codigo ASC
                ");
                
                // Agregar nombres manualmente
                foreach ($inventarioSimple as &$item) {
                    // Obtener tipo de dotación
                    $tipoResult = self::fetchArray("SELECT tipo_dotacion_nombre FROM tipo_dotacion WHERE tipo_dotacion_id = " . intval($item['tipo_dotacion_id']));
                    $item['tipo_dotacion_nombre'] = !empty($tipoResult) ? $tipoResult[0]['tipo_dotacion_nombre'] : 'Sin tipo';
                    
                    // Obtener talla
                    $tallaResult = self::fetchArray("SELECT talla_codigo FROM talla WHERE talla_id = " . intval($item['talla_id']));
                    $item['talla_codigo'] = !empty($tallaResult) ? $tallaResult[0]['talla_codigo'] : 'Sin talla';
                    
                    // Completar campos faltantes
                    $item['dotacion_inv_color'] = $item['dotacion_inv_color'] ?? '';
                    $item['dotacion_inv_material'] = $item['dotacion_inv_material'] ?? '';
                    $item['dotacion_inv_proveedor'] = $item['dotacion_inv_proveedor'] ?? '';
                    $item['dotacion_inv_cantidad_minima'] = $item['dotacion_inv_cantidad_minima'] ?? 5;
                }
                
                echo json_encode([
                    'codigo' => 1, 
                    'mensaje' => 'Éxito (modo fallback)', 
                    'datos' => $inventarioSimple
                ]);
                
            } catch (Exception $e2) {
                error_log("❌ También falló el fallback: " . $e2->getMessage());
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0, 
                    'mensaje' => 'Error al obtener los datos', 
                    'detalle' => $e->getMessage()
                ]);
            }
        }
    }

    // API: Obtener tipos de dotación (copiando estrategia exitosa)
    public static function obtenerTiposDotacionAPI()
    {
        getHeadersApi();

        try {
            $tipos = self::fetchArray("
                SELECT 
                    tipo_dotacion_id, 
                    tipo_dotacion_nombre,
                    tipo_dotacion_descripcion
                FROM tipo_dotacion 
                WHERE tipo_dotacion_situacion = 1 
                ORDER BY tipo_dotacion_nombre ASC
            ");
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $tipos
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener tipos de dotación: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'codigo' => 0, 
                'mensaje' => 'Error al obtener tipos de dotación', 
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // API: Obtener tallas (copiando estrategia exitosa)
    public static function obtenerTallasAPI()
    {
        getHeadersApi();

        try {
            $tallas = self::fetchArray("
                SELECT 
                    talla_id, 
                    talla_codigo,
                    talla_descripcion
                FROM talla 
                WHERE talla_situacion = 1 
                ORDER BY talla_codigo ASC
            ");
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $tallas
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener tallas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'codigo' => 0, 
                'mensaje' => 'Error al obtener tallas', 
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // API: Guardar Inventario (copiando validaciones exitosas)
    public static function guardarAPI()
    {
        getHeadersApi();

        $campos = [
            'dotacion_inv_codigo', 'tipo_dotacion_id', 'talla_id', 
            'dotacion_inv_cantidad_inicial', 'dotacion_inv_cantidad_actual'
        ];

        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "Falta el campo $campo"]);
                return;
            }
        }

        // Validaciones específicas
        if (strlen($_POST['dotacion_inv_codigo']) > 20) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Código demasiado largo (máximo 20 caracteres)']);
            return;
        }

        if (!is_numeric($_POST['dotacion_inv_cantidad_inicial']) || $_POST['dotacion_inv_cantidad_inicial'] < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Cantidad inicial inválida']);
            return;
        }

        if (!is_numeric($_POST['dotacion_inv_cantidad_actual']) || $_POST['dotacion_inv_cantidad_actual'] < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Cantidad actual inválida']);
            return;
        }

        // Validar que el tipo de dotación existe y está activo
        $tipoValido = self::fetchArray("SELECT tipo_dotacion_id FROM tipo_dotacion WHERE tipo_dotacion_id = " . intval($_POST['tipo_dotacion_id']) . " AND tipo_dotacion_situacion = 1");
        if (empty($tipoValido)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no válido']);
            return;
        }

        // Validar que la talla existe y está activa
        $tallaValida = self::fetchArray("SELECT talla_id FROM talla WHERE talla_id = " . intval($_POST['talla_id']) . " AND talla_situacion = 1");
        if (empty($tallaValida)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Talla no válida']);
            return;
        }

        // Verificar duplicidad de código
        $existe = DotacionInventario::verificarCodigoExistente($_POST['dotacion_inv_codigo']);
        if ($existe['codigo_existe']) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Código ya registrado']);
            return;
        }

        // Crear inventario
        try {
            $inventario = new DotacionInventario([
                'dotacion_inv_codigo' => $_POST['dotacion_inv_codigo'],
                'tipo_dotacion_id' => $_POST['tipo_dotacion_id'],
                'talla_id' => $_POST['talla_id'],
                'dotacion_inv_marca' => $_POST['dotacion_inv_marca'] ?? '',
                'dotacion_inv_modelo' => $_POST['dotacion_inv_modelo'] ?? '',
                'dotacion_inv_color' => $_POST['dotacion_inv_color'] ?? '',
                'dotacion_inv_material' => $_POST['dotacion_inv_material'] ?? '',
                'dotacion_inv_cantidad_inicial' => $_POST['dotacion_inv_cantidad_inicial'],
                'dotacion_inv_cantidad_actual' => $_POST['dotacion_inv_cantidad_actual'],
                'dotacion_inv_cantidad_minima' => $_POST['dotacion_inv_cantidad_minima'] ?? 5,
                'dotacion_inv_precio_unitario' => $_POST['dotacion_inv_precio_unitario'] ?? 0,
                'dotacion_inv_proveedor' => $_POST['dotacion_inv_proveedor'] ?? '',
                'dotacion_inv_observaciones' => $_POST['dotacion_inv_observaciones'] ?? '',
                'dotacion_inv_situacion' => 1
            ]);
            $inventario->crear();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Producto agregado al inventario correctamente']);
            
        } catch (Exception $e) {
            error_log("Error al guardar inventario: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Modificar Inventario (corregido para evitar errores de columnas)
    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['dotacion_inv_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        // Validar campos requeridos
        $campos = [
            'dotacion_inv_codigo', 'tipo_dotacion_id', 'talla_id', 
            'dotacion_inv_cantidad_inicial', 'dotacion_inv_cantidad_actual'
        ];

        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "Falta el campo $campo"]);
                return;
            }
        }

        // Validaciones específicas (mismas que en guardar)
        if (strlen($_POST['dotacion_inv_codigo']) > 20) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Código demasiado largo']);
            return;
        }

        if (!is_numeric($_POST['dotacion_inv_cantidad_inicial']) || $_POST['dotacion_inv_cantidad_inicial'] < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Cantidad inicial inválida']);
            return;
        }

        if (!is_numeric($_POST['dotacion_inv_cantidad_actual']) || $_POST['dotacion_inv_cantidad_actual'] < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Cantidad actual inválida']);
            return;
        }

        try {
            $inventario = DotacionInventario::find($id);

            if (!$inventario) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Producto no encontrado']);
                return;
            }

            // Verificar duplicidad (excluyendo el registro actual)
            $existe = DotacionInventario::verificarCodigoExistente($_POST['dotacion_inv_codigo'], $id);
            if ($existe['codigo_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Código ya registrado por otro producto']);
                return;
            }

            // USAR MÉTODO PERSONALIZADO en lugar de sincronizar
            $datosActualizacion = [
                'dotacion_inv_codigo' => $_POST['dotacion_inv_codigo'],
                'tipo_dotacion_id' => $_POST['tipo_dotacion_id'],
                'talla_id' => $_POST['talla_id'],
                'dotacion_inv_marca' => $_POST['dotacion_inv_marca'] ?? '',
                'dotacion_inv_modelo' => $_POST['dotacion_inv_modelo'] ?? '',
                'dotacion_inv_color' => $_POST['dotacion_inv_color'] ?? '',
                'dotacion_inv_material' => $_POST['dotacion_inv_material'] ?? '',
                'dotacion_inv_cantidad_inicial' => $_POST['dotacion_inv_cantidad_inicial'],
                'dotacion_inv_cantidad_actual' => $_POST['dotacion_inv_cantidad_actual'],
                'dotacion_inv_cantidad_minima' => $_POST['dotacion_inv_cantidad_minima'] ?? 5,
                'dotacion_inv_precio_unitario' => $_POST['dotacion_inv_precio_unitario'] ?? 0,
                'dotacion_inv_proveedor' => $_POST['dotacion_inv_proveedor'] ?? '',
                'dotacion_inv_observaciones' => $_POST['dotacion_inv_observaciones'] ?? ''
            ];

            $resultado = $inventario->actualizarInventarioPersonalizado($datosActualizacion);

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Producto actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => $resultado['mensaje']]);
            }

        } catch (Exception $e) {
            error_log("Error al modificar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Eliminar Inventario (lógico) - corregido
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
            $inventario = DotacionInventario::find($id);
            if (!$inventario) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Producto no encontrado']);
                return;
            }

            // Usar método específico para eliminación lógica
            $resultado = $inventario->eliminarLogico();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Producto eliminado del inventario correctamente']);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar el producto']);
            }
            
        } catch (Exception $e) {
            error_log("Error al eliminar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Actualizar Stock
    public static function actualizarStockAPI()
    {
        getHeadersApi();

        $id = $_POST['dotacion_inv_id'] ?? null;
        $nuevaCantidad = $_POST['nueva_cantidad'] ?? null;

        if (!$id || !is_numeric($nuevaCantidad) || $nuevaCantidad < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID y cantidad válida son requeridos']);
            return;
        }

        try {
            $inventario = DotacionInventario::find($id);
            if (!$inventario) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Producto no encontrado']);
                return;
            }

            $inventario->sincronizar(['dotacion_inv_cantidad_actual' => intval($nuevaCantidad)]);
            $resultado = $inventario->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Stock actualizado correctamente']);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar el stock']);
            }

        } catch (Exception $e) {
            error_log("Error al actualizar stock: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al actualizar stock', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener productos con stock bajo
    public static function obtenerStockBajoAPI()
    {
        getHeadersApi();

        try {
            $stockBajo = self::fetchArray("
                SELECT 
                    dotacion_inv_id,
                    dotacion_inv_codigo,
                    dotacion_inv_cantidad_actual,
                    dotacion_inv_cantidad_minima
                FROM dotacion_inventario
                WHERE dotacion_inv_situacion = 1 
                AND dotacion_inv_cantidad_actual <= dotacion_inv_cantidad_minima
                ORDER BY dotacion_inv_cantidad_actual ASC
            ");
            
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Éxito', 
                'datos' => $stockBajo
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener stock bajo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener stock bajo', 'detalle' => $e->getMessage()]);
        }
    }
}