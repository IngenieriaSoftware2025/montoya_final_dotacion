<?php

namespace Model;

class DotacionEntrega extends ActiveRecord 
{
    public static $tabla = 'mrml_dotacion_entrega';

    public static $columnasDB = [
        'empleado_id',
        'solicitud_id',
        'entrega_fecha',
        'entrega_año',
        'entrega_observaciones',
        'entrega_entregado_por',
        'entrega_recibido_por',
        'entrega_situacion'
    ];

    public static $idTabla = 'entrega_id';

    // Propiedades
    public $entrega_id;
    public $empleado_id;
    public $solicitud_id;
    public $entrega_fecha;
    public $entrega_año;
    public $entrega_observaciones;
    public $entrega_entregado_por;
    public $entrega_recibido_por;
    public $entrega_situacion;
    public $entrega_fecha_registro;

    public function __construct($args = [])
    {
        $this->entrega_id = $args['entrega_id'] ?? null;
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->solicitud_id = $args['solicitud_id'] ?? null;
        $this->entrega_fecha = $args['entrega_fecha'] ?? null;
        $this->entrega_año = $args['entrega_año'] ?? date('Y');
        $this->entrega_observaciones = $args['entrega_observaciones'] ?? '';
        $this->entrega_entregado_por = $args['entrega_entregado_por'] ?? '';
        $this->entrega_recibido_por = $args['entrega_recibido_por'] ?? '';
        $this->entrega_situacion = $args['entrega_situacion'] ?? 1;
        $this->entrega_fecha_registro = $args['entrega_fecha_registro'] ?? null;
    }

    public function obtenerDetalle()
    {
        $sql = "SELECT edd.*, di.dotacion_inv_marca, di.dotacion_inv_modelo, 
                       td.tipo_dotacion_nombre, t.talla_nombre
                FROM mrml_dotacion_entrega_detalle edd
                JOIN mrml_dotacion_inventario di ON edd.dotacion_inv_id = di.dotacion_inv_id
                JOIN mrml_tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                JOIN mrml_talla t ON di.talla_id = t.talla_id
                WHERE edd.entrega_id = {$this->entrega_id} AND edd.entrega_det_situacion = 1 
                ORDER BY edd.entrega_detalle_id ASC";
        return self::fetchArray($sql);
    }

    public static function verificarLimiteAnual($empleadoId, $año = null)
    {
        $año = $año ?? date('Y');
        
        $sql = "SELECT COUNT(*) as entregas_realizadas
                FROM mrml_dotacion_entrega 
                WHERE empleado_id = " . intval($empleadoId) . "
                AND entrega_año = " . intval($año) . "
                AND entrega_situacion = 1";
        
        $resultado = self::fetchArray($sql);
        $entregasRealizadas = $resultado[0]['entregas_realizadas'] ?? 0;
        
        return $entregasRealizadas < 3; // Máximo 3 entregas por año
    }

    public static function eliminarEntrega($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET entrega_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}
