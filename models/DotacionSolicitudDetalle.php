<?php

namespace Model;

class DotacionSolicitudDetalle extends ActiveRecord 
{
    public static $tabla = 'mrml_dotacion_solicitud_detalle';

    public static $columnasDB = [
        'solicitud_id',
        'tipo_dotacion_id',
        'talla_id',
        'solicitud_det_cantidad',
        'solicitud_det_observaciones',
        'solicitud_det_situacion'
    ];

    public static $idTabla = 'solicitud_detalle_id';

    // Propiedades
    public $solicitud_detalle_id;
    public $solicitud_id;
    public $tipo_dotacion_id;
    public $talla_id;
    public $solicitud_det_cantidad;
    public $solicitud_det_observaciones;
    public $solicitud_det_situacion;

    public function __construct($args = [])
    {
        $this->solicitud_detalle_id = $args['solicitud_detalle_id'] ?? null;
        $this->solicitud_id = $args['solicitud_id'] ?? null;
        $this->tipo_dotacion_id = $args['tipo_dotacion_id'] ?? null;
        $this->talla_id = $args['talla_id'] ?? null;
        $this->solicitud_det_cantidad = $args['solicitud_det_cantidad'] ?? 1;
        $this->solicitud_det_observaciones = $args['solicitud_det_observaciones'] ?? '';
        $this->solicitud_det_situacion = $args['solicitud_det_situacion'] ?? 1;
    }

    public static function eliminarDetalle($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET solicitud_det_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}