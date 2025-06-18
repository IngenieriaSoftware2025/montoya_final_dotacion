<?php

namespace Model;

class DotacionEntregaDetalle extends ActiveRecord 
{
    public static $tabla = 'mrml_dotacion_entrega_detalle';

    public static $columnasDB = [
        'entrega_id',
        'dotacion_inv_id',
        'entrega_det_cantidad',
        'entrega_det_precio_unitario',
        'entrega_det_observaciones',
        'entrega_det_situacion'
    ];

    public static $idTabla = 'entrega_detalle_id';

    // Propiedades
    public $entrega_detalle_id;
    public $entrega_id;
    public $dotacion_inv_id;
    public $entrega_det_cantidad;
    public $entrega_det_precio_unitario;
    public $entrega_det_observaciones;
    public $entrega_det_situacion;

    public function __construct($args = [])
    {
        $this->entrega_detalle_id = $args['entrega_detalle_id'] ?? null;
        $this->entrega_id = $args['entrega_id'] ?? null;
        $this->dotacion_inv_id = $args['dotacion_inv_id'] ?? null;
        $this->entrega_det_cantidad = $args['entrega_det_cantidad'] ?? 1;
        $this->entrega_det_precio_unitario = $args['entrega_det_precio_unitario'] ?? 0;
        $this->entrega_det_observaciones = $args['entrega_det_observaciones'] ?? '';
        $this->entrega_det_situacion = $args['entrega_det_situacion'] ?? 1;
    }

    public static function eliminarDetalle($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET entrega_det_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}