<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\DotacionControlAnual;

class DotacionReporteController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('dotacionreporte/index', []);
    }

    public static function reporteAnualAPI()
    {
        $año = $_GET['año'] ?? date('Y');

        try {
            $data = DotacionControlAnual::obtenerReporteAnual($año);

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reporte obtenido correctamente',
                'data' => $data,
                'año' => $año
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el reporte',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function reporteInventarioAPI()
    {
        try {
            $sql = "SELECT di.*, td.tipo_dotacion_nombre, t.talla_nombre,
                           CASE 
                               WHEN di.dotacion_inv_cantidad_actual <= di.dotacion_inv_cantidad_minima 
                               THEN 'STOCK_BAJO'
                               WHEN di.dotacion_inv_cantidad_actual = 0 
                               THEN 'SIN_STOCK'
                               ELSE 'DISPONIBLE'
                           END AS estado_stock
                    FROM mrml_dotacion_inventario di
                    JOIN mrml_tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                    JOIN mrml_talla t ON di.talla_id = t.talla_id
                    WHERE di.dotacion_inv_situacion = 1
                    ORDER BY td.tipo_dotacion_nombre, t.talla_id ASC";
            
            $data = self::fetchArray($sql);

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reporte de inventario obtenido',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el reporte de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function reporteEntregasPorTallaAPI()
    {
        $año = $_GET['año'] ?? date('Y');

        try {
            $sql = "SELECT t.talla_nombre, td.tipo_dotacion_nombre, 
                           SUM(ded.entrega_det_cantidad) as total_entregado,
                           COUNT(DISTINCT de.empleado_id) as empleados_beneficiados
                    FROM mrml_dotacion_entrega_detalle ded
                    JOIN mrml_dotacion_entrega de ON ded.entrega_id = de.entrega_id
                    JOIN mrml_dotacion_inventario di ON ded.dotacion_inv_id = di.dotacion_inv_id
                    JOIN mrml_tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                    JOIN mrml_talla t ON di.talla_id = t.talla_id
                    WHERE de.entrega_año = " . intval($año) . "
                    AND de.entrega_situacion = 1 
                    AND ded.entrega_det_situacion = 1
                    GROUP BY t.talla_id, t.talla_nombre, td.tipo_dotacion_id, td.tipo_dotacion_nombre
                    ORDER BY td.tipo_dotacion_nombre, t.talla_id ASC";
            
            $data = self::fetchArray($sql);

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reporte de entregas por talla obtenido',
                'data' => $data,
                'año' => $año
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el reporte de entregas por talla',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}