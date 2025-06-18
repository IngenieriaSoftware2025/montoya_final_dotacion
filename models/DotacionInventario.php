<?php

namespace Model;

class DotacionInventario extends ActiveRecord 
{
    public static $tabla = 'mrml_dotacion_inventario';

    public static $columnasDB = [
        'tipo_dotacion_id',
        'talla_id',
        'dotacion_inv_marca',
        'dotacion_inv_modelo',
        'dotacion_inv_color',
        'dotacion_inv_material',
        'dotacion_inv_cantidad_inicial',
        'dotacion_inv_cantidad_actual',
        'dotacion_inv_cantidad_minima',
        'dotacion_inv_precio_unitario',
        'dotacion_inv_proveedor',
        'dotacion_inv_fecha_ingreso',
        'dotacion_inv_fecha_vencimiento',
        'dotacion_inv_observaciones',
        'dotacion_inv_situacion'
    ];

    public static $idTabla = 'dotacion_inv_id';

    // Propiedades
    public $dotacion_inv_id;
    public $tipo_dotacion_id;
    public $talla_id;
    public $dotacion_inv_marca;
    public $dotacion_inv_modelo;
    public $dotacion_inv_color;
    public $dotacion_inv_material;
    public $dotacion_inv_cantidad_inicial;
    public $dotacion_inv_cantidad_actual;
    public $dotacion_inv_cantidad_minima;
    public $dotacion_inv_precio_unitario;
    public $dotacion_inv_proveedor;
    public $dotacion_inv_fecha_ingreso;
    public $dotacion_inv_fecha_vencimiento;
    public $dotacion_inv_observaciones;
    public $dotacion_inv_situacion;
    public $dotacion_inv_fecha_registro;

    public function __construct($args = [])
    {
        $this->dotacion_inv_id = $args['dotacion_inv_id'] ?? null;
        $this->tipo_dotacion_id = $args['tipo_dotacion_id'] ?? null;
        $this->talla_id = $args['talla_id'] ?? null;
        $this->dotacion_inv_marca = $args['dotacion_inv_marca'] ?? '';
        $this->dotacion_inv_modelo = $args['dotacion_inv_modelo'] ?? '';
        $this->dotacion_inv_color = $args['dotacion_inv_color'] ?? '';
        $this->dotacion_inv_material = $args['dotacion_inv_material'] ?? '';
        $this->dotacion_inv_cantidad_inicial = $args['dotacion_inv_cantidad_inicial'] ?? 0;
        $this->dotacion_inv_cantidad_actual = $args['dotacion_inv_cantidad_actual'] ?? 0;
        $this->dotacion_inv_cantidad_minima = $args['dotacion_inv_cantidad_minima'] ?? 5;
        $this->dotacion_inv_precio_unitario = $args['dotacion_inv_precio_unitario'] ?? 0;
        $this->dotacion_inv_proveedor = $args['dotacion_inv_proveedor'] ?? '';
        $this->dotacion_inv_fecha_ingreso = $args['dotacion_inv_fecha_ingreso'] ?? null;
        $this->dotacion_inv_fecha_vencimiento = $args['dotacion_inv_fecha_vencimiento'] ?? null;
        $this->dotacion_inv_observaciones = $args['dotacion_inv_observaciones'] ?? '';
        $this->dotacion_inv_situacion = $args['dotacion_inv_situacion'] ?? 1;
        $this->dotacion_inv_fecha_registro = $args['dotacion_inv_fecha_registro'] ?? null;
    }

    public static function obtenerInventarioDisponible($tipoDotacionId = null, $tallaId = null)
    {
        $condiciones = "di.dotacion_inv_situacion = 1 AND di.dotacion_inv_cantidad_actual > 0";
        
        if ($tipoDotacionId) {
            $condiciones .= " AND di.tipo_dotacion_id = " . intval($tipoDotacionId);
        }
        
        if ($tallaId) {
            $condiciones .= " AND di.talla_id = " . intval($tallaId);
        }

        $sql = "SELECT di.*, td.tipo_dotacion_nombre, t.talla_nombre, t.talla_descripcion
                FROM mrml_dotacion_inventario di
                JOIN mrml_tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                JOIN mrml_talla t ON di.talla_id = t.talla_id
                WHERE $condiciones
                ORDER BY td.tipo_dotacion_nombre, t.talla_id ASC";
        
        return self::fetchArray($sql);
    }

    public static function verificarStockDisponible($tipoDotacionId, $tallaId, $cantidadSolicitada)
    {
        $sql = "SELECT SUM(dotacion_inv_cantidad_actual) as stock_total
                FROM mrml_dotacion_inventario 
                WHERE tipo_dotacion_id = " . intval($tipoDotacionId) . "
                AND talla_id = " . intval($tallaId) . "
                AND dotacion_inv_situacion = 1";
        
        $resultado = self::fetchArray($sql);
        $stockTotal = $resultado[0]['stock_total'] ?? 0;
        
        return $stockTotal >= $cantidadSolicitada;
    }

    public static function actualizarStock($inventarioId, $cantidadEntregada)
    {
        $sql = "UPDATE mrml_dotacion_inventario 
                SET dotacion_inv_cantidad_actual = dotacion_inv_cantidad_actual - " . intval($cantidadEntregada) . "
                WHERE dotacion_inv_id = " . intval($inventarioId);
        
        return self::$db->exec($sql);
    }

    public static function eliminarInventario($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET dotacion_inv_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}

