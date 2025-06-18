<?php

namespace Model;

class DotacionSolicitud extends ActiveRecord 
{
    public static $tabla = 'mrml_dotacion_solicitud';

    public static $columnasDB = [
        'empleado_id',
        'solicitud_fecha',
        'solicitud_estado',
        'solicitud_observaciones',
        'solicitud_fecha_aprobacion',
        'solicitud_aprobado_por',
        'solicitud_situacion'
    ];

    public static $idTabla = 'solicitud_id';

    // Propiedades
    public $solicitud_id;
    public $empleado_id;
    public $solicitud_fecha;
    public $solicitud_estado;
    public $solicitud_observaciones;
    public $solicitud_fecha_aprobacion;
    public $solicitud_aprobado_por;
    public $solicitud_situacion;
    public $solicitud_fecha_registro;

    public function __construct($args = [])
    {
        $this->solicitud_id = $args['solicitud_id'] ?? null;
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->solicitud_fecha = $args['solicitud_fecha'] ?? null;
        $this->solicitud_estado = $args['solicitud_estado'] ?? 'PENDIENTE';
        $this->solicitud_observaciones = $args['solicitud_observaciones'] ?? '';
        $this->solicitud_fecha_aprobacion = $args['solicitud_fecha_aprobacion'] ?? null;
        $this->solicitud_aprobado_por = $args['solicitud_aprobado_por'] ?? null;
        $this->solicitud_situacion = $args['solicitud_situacion'] ?? 1;
        $this->solicitud_fecha_registro = $args['solicitud_fecha_registro'] ?? null;
    }

    public static function getEstadosDisponibles()
    {
        return [
            'PENDIENTE' => 'Pendiente',
            'APROBADA' => 'Aprobada',
            'RECHAZADA' => 'Rechazada',
            'ENTREGADA' => 'Entregada'
        ];
    }

    public function obtenerDetalle()
    {
        $sql = "SELECT sdd.*, td.tipo_dotacion_nombre, t.talla_nombre, t.talla_descripcion
                FROM mrml_dotacion_solicitud_detalle sdd
                JOIN mrml_tipo_dotacion td ON sdd.tipo_dotacion_id = td.tipo_dotacion_id
                JOIN mrml_talla t ON sdd.talla_id = t.talla_id
                WHERE sdd.solicitud_id = {$this->solicitud_id} AND sdd.solicitud_det_situacion = 1 
                ORDER BY sdd.solicitud_detalle_id ASC";
        return self::fetchArray($sql);
    }

    public static function eliminarSolicitud($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET solicitud_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}
