<?php
// ==============================================================
// DotacionSolicitudController.php


namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\DotacionSolicitud;
use Model\DotacionSolicitudDetalle;
use Model\DotacionInventario;

class DotacionSolicitudController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('dotacionsolicitud/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT ds.*, e.empleado_nombres, e.empleado_apellidos, e.empleado_puesto
                    FROM mrml_dotacion_solicitud ds
                    JOIN mrml_empleado e ON ds.empleado_id = e.empleado_id
                    WHERE ds.solicitud_situacion = 1 
                    ORDER BY ds.solicitud_fecha DESC";
            $data = self::fetchArray($sql);

            // Agregar detalle a cada solicitud
            foreach ($data as &$solicitud) {
                $solicitudObj = new DotacionSolicitud(['solicitud_id' => $solicitud['solicitud_id']]);
                $solicitud['detalle'] = $solicitudObj->obtenerDetalle();
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Solicitudes obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las solicitudes',
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
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe agregar al menos un artículo solicitado']);
            return;
        }

        // Validar disponibilidad de stock para cada detalle
        foreach ($detalles as $detalle) {
            if (empty($detalle['tipo_dotacion_id']) || empty($detalle['talla_id'])) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación y talla son obligatorios']);
                return;
            }

            $cantidad = intval($detalle['cantidad'] ?? 1);
            if ($cantidad <= 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'La cantidad debe ser mayor a 0']);
                return;
            }

            // Verificar stock disponible
            $stockDisponible = DotacionInventario::verificarStockDisponible(
                $detalle['tipo_dotacion_id'], 
                $detalle['talla_id'], 
                $cantidad
            );

            if (!$stockDisponible) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay suficiente stock disponible para uno de los artículos solicitados']);
                return;
            }
        }

        try {
            // Crear solicitud
            $solicitud = new DotacionSolicitud([
                'empleado_id' => intval($_POST['empleado_id']),
                'solicitud_fecha' => $_POST['solicitud_fecha'] ?? null,
                'solicitud_estado' => 'PENDIENTE',
                'solicitud_observaciones' => trim($_POST['solicitud_observaciones'] ?? ''),
                'solicitud_situacion' => 1
            ]);

            $resultado = $solicitud->crear();
            
            if (!$resultado['resultado']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear la solicitud']);
                return;
            }

            $solicitudId = $resultado['id'];

            // Crear detalles de solicitud
            foreach ($detalles as $detalle) {
                $solicitudDetalle = new DotacionSolicitudDetalle([
                    'solicitud_id' => $solicitudId,
                    'tipo_dotacion_id' => intval($detalle['tipo_dotacion_id']),
                    'talla_id' => intval($detalle['talla_id']),
                    'solicitud_det_cantidad' => intval($detalle['cantidad']),
                    'solicitud_det_observaciones' => trim($detalle['observaciones'] ?? ''),
                    'solicitud_det_situacion' => 1
                ]);

                $resultadoDetalle = $solicitudDetalle->crear();

                if (!$resultadoDetalle['resultado']) {
                    http_response_code(400);
                    echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear detalle de solicitud']);
                    return;
                }
            }

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Solicitud registrada correctamente',
                'solicitud_id' => $solicitudId
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar solicitud', 'detalle' => $e->getMessage()]);
        }
    }

    public static function aprobarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['solicitud_id'] ?? null;
        $aprobadoPor = $_POST['aprobado_por'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de solicitud no proporcionado']);
            return;
        }

        try {
            $solicitud = DotacionSolicitud::find($id);

            if (!$solicitud) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solicitud no encontrada']);
                return;
            }

            if ($solicitud->solicitud_estado != 'PENDIENTE') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solo se pueden aprobar solicitudes pendientes']);
                return;
            }

            $solicitud->sincronizar([
                'solicitud_estado' => 'APROBADA',
                'solicitud_fecha_aprobacion' => date('Y-m-d'),
                'solicitud_aprobado_por' => $aprobadoPor
            ]);

            $resultado = $solicitud->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Solicitud aprobada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo aprobar la solicitud']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al aprobar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function rechazarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['solicitud_id'] ?? null;
        $observaciones = trim($_POST['observaciones'] ?? '');

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de solicitud no proporcionado']);
            return;
        }

        try {
            $solicitud = DotacionSolicitud::find($id);

            if (!$solicitud) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solicitud no encontrada']);
                return;
            }

            if ($solicitud->solicitud_estado != 'PENDIENTE') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solo se pueden rechazar solicitudes pendientes']);
                return;
            }

            $solicitud->sincronizar([
                'solicitud_estado' => 'RECHAZADA',
                'solicitud_observaciones' => $observaciones
            ]);

            $resultado = $solicitud->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Solicitud rechazada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo rechazar la solicitud']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al rechazar', 'detalle' => $e->getMessage()]);
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
            $solicitud = DotacionSolicitud::find($id);
            if (!$solicitud) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solicitud no encontrada']);
                return;
            }

            // Solo permitir eliminar solicitudes pendientes o rechazadas
            if (!in_array($solicitud->solicitud_estado, ['PENDIENTE', 'RECHAZADA'])) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se puede eliminar una solicitud aprobada o entregada']);
                return;
            }

            $solicitud->sincronizar(['solicitud_situacion' => 0]);
            $solicitud->actualizar();

            // Eliminar también los detalles
            $deleteDetalles = "UPDATE mrml_dotacion_solicitud_detalle SET solicitud_det_situacion = 0 WHERE solicitud_id = " . intval($id);
            self::$db->exec($deleteDetalles);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Solicitud eliminada correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}