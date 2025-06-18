<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\DotacionEntrega;
use Model\DotacionEntregaDetalle;
use Model\DotacionInventario;
use Model\DotacionSolicitud;
use Model\DotacionControlAnual;

class DotacionEntregaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('dotacionentrega/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT de.*, e.empleado_nombres, e.empleado_apellidos, e.empleado_puesto
                    FROM mrml_dotacion_entrega de
                    JOIN mrml_empleado e ON de.empleado_id = e.empleado_id
                    WHERE de.entrega_situacion = 1 
                    ORDER BY de.entrega_fecha DESC";
            $data = self::fetchArray($sql);

            // Agregar detalle a cada entrega
            foreach ($data as &$entrega) {
                $entregaObj = new DotacionEntrega(['entrega_id' => $entrega['entrega_id']]);
                $entrega['detalle'] = $entregaObj->obtenerDetalle();
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Entregas obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las entregas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['empleado_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El empleado es obligatorio']);
            return;
        }

        // Validar que haya al menos un detalle
        $detalles = json_decode($_POST['detalles'] ?? '[]', true);
        if (empty($detalles)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe agregar al menos un artículo a entregar']);
            return;
        }

        $empleadoId = intval($_POST['empleado_id']);
        $año = intval($_POST['entrega_año'] ?? date('Y'));

        // Verificar límite anual de entregas
        if (!DotacionEntrega::verificarLimiteAnual($empleadoId, $año)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El empleado ya ha alcanzado el límite de 3 entregas por año']);
            return;
        }

        // Validar disponibilidad de inventario para cada detalle
        foreach ($detalles as $detalle) {
            if (empty($detalle['dotacion_inv_id'])) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID de inventario obligatorio']);
                return;
            }

            $cantidad = intval($detalle['cantidad'] ?? 1);
            if ($cantidad <= 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'La cantidad debe ser mayor a 0']);
                return;
            }

            // Verificar stock actual del inventario específico
            $inventario = DotacionInventario::find($detalle['dotacion_inv_id']);
            if (!$inventario || $inventario->dotacion_inv_cantidad_actual < $cantidad) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay suficiente stock en el inventario seleccionado']);
                return;
            }
        }

        try {
            // Crear entrega
            $entrega = new DotacionEntrega([
                'empleado_id' => $empleadoId,
                'solicitud_id' => !empty($_POST['solicitud_id']) ? intval($_POST['solicitud_id']) : null,
                'entrega_fecha' => $_POST['entrega_fecha'] ?? null,
                'entrega_año' => $año,
                'entrega_observaciones' => trim($_POST['entrega_observaciones'] ?? ''),
                'entrega_entregado_por' => trim($_POST['entrega_entregado_por'] ?? ''),
                'entrega_recibido_por' => trim($_POST['entrega_recibido_por'] ?? ''),
                'entrega_situacion' => 1
            ]);

            $resultado = $entrega->crear();
            
            if (!$resultado['resultado']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear la entrega']);
                return;
            }

            $entregaId = $resultado['id'];

            // Crear detalles de entrega y actualizar inventario
            foreach ($detalles as $detalle) {
                $inventario = DotacionInventario::find($detalle['dotacion_inv_id']);
                
                $entregaDetalle = new DotacionEntregaDetalle([
                    'entrega_id' => $entregaId,
                    'dotacion_inv_id' => intval($detalle['dotacion_inv_id']),
                    'entrega_det_cantidad' => intval($detalle['cantidad']),
                    'entrega_det_precio_unitario' => $inventario->dotacion_inv_precio_unitario ?? 0,
                    'entrega_det_observaciones' => trim($detalle['observaciones'] ?? ''),
                    'entrega_det_situacion' => 1
                ]);

                $resultadoDetalle = $entregaDetalle->crear();

                if (!$resultadoDetalle['resultado']) {
                    http_response_code(400);
                    echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear detalle de entrega']);
                    return;
                }

                // Actualizar stock del inventario
                DotacionInventario::actualizarStock($detalle['dotacion_inv_id'], $detalle['cantidad']);
            }

            // Si la entrega está asociada a una solicitud, marcarla como entregada
            if (!empty($_POST['solicitud_id'])) {
                $solicitud = DotacionSolicitud::find($_POST['solicitud_id']);
                if ($solicitud) {
                    $solicitud->sincronizar(['solicitud_estado' => 'ENTREGADA']);
                    $solicitud->actualizar();
                }
            }

            // Actualizar control anual
            DotacionControlAnual::actualizarControl($empleadoId, $año);

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Entrega registrada correctamente',
                'entrega_id' => $entregaId
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar entrega', 'detalle' => $e->getMessage()]);
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
            $entrega = DotacionEntrega::find($id);
            if (!$entrega) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Entrega no encontrada']);
                return;
            }

            // Obtener detalles antes de eliminar para restaurar stock
            $detalles = $entrega->obtenerDetalle();

            $entrega->sincronizar(['entrega_situacion' => 0]);
            $entrega->actualizar();

            // Eliminar detalles
            $deleteDetalles = "UPDATE mrml_dotacion_entrega_detalle SET entrega_det_situacion = 0 WHERE entrega_id = " . intval($id);
            self::$db->exec($deleteDetalles);

            // Restaurar stock del inventario
            foreach ($detalles as $detalle) {
                $sqlRestaurar = "UPDATE mrml_dotacion_inventario 
                               SET dotacion_inv_cantidad_actual = dotacion_inv_cantidad_actual + " . intval($detalle['entrega_det_cantidad']) . "
                               WHERE dotacion_inv_id = " . intval($detalle['dotacion_inv_id']);
                self::$db->exec($sqlRestaurar);
            }

            // Actualizar control anual
            DotacionControlAnual::actualizarControl($entrega->empleado_id, $entrega->entrega_año);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Entrega eliminada correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function verificarLimiteAPI()
    {
        $empleadoId = $_GET['empleado_id'] ?? null;
        $año = $_GET['año'] ?? date('Y');

        if (!$empleadoId) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de empleado requerido']);
            return;
        }

        try {
            $puedeRecibir = DotacionEntrega::verificarLimiteAnual($empleadoId, $año);
            
            $sql = "SELECT COUNT(*) as entregas_realizadas
                    FROM mrml_dotacion_entrega 
                    WHERE empleado_id = " . intval($empleadoId) . "
                    AND entrega_año = " . intval($año) . "
                    AND entrega_situacion = 1";
            
            $resultado = self::fetchArray($sql);
            $entregasRealizadas = $resultado[0]['entregas_realizadas'] ?? 0;

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Verificación realizada',
                'puede_recibir' => $puedeRecibir,
                'entregas_realizadas' => $entregasRealizadas,
                'entregas_disponibles' => 3 - $entregasRealizadas
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar límite',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}