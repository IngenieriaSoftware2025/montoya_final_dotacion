<?php
namespace Model;

class TipoDotacion extends ActiveRecord 
{
    public static $tabla = 'mrml_tipo_dotacion';

    public static $columnasDB = [
        'tipo_dotacion_nombre',
        'tipo_dotacion_descripcion',
        'tipo_dotacion_situacion'
    ];

    public static $idTabla = 'tipo_dotacion_id';

  
    public $tipo_dotacion_id;
    public $tipo_dotacion_nombre;
    public $tipo_dotacion_descripcion;
    public $tipo_dotacion_situacion;
    public $tipo_dotacion_fecha_registro;

    public function __construct($args = [])
    {
        $this->tipo_dotacion_id = $args['tipo_dotacion_id'] ?? null;
        $this->tipo_dotacion_nombre = $args['tipo_dotacion_nombre'] ?? '';
        $this->tipo_dotacion_descripcion = $args['tipo_dotacion_descripcion'] ?? '';
        $this->tipo_dotacion_situacion = $args['tipo_dotacion_situacion'] ?? 1;
        $this->tipo_dotacion_fecha_registro = $args['tipo_dotacion_fecha_registro'] ?? null;
    }

    public static function obtenerTiposActivos()
    {
        $sql = "SELECT tipo_dotacion_id, tipo_dotacion_nombre 
                FROM mrml_tipo_dotacion 
                WHERE tipo_dotacion_situacion = 1 
                ORDER BY tipo_dotacion_nombre ASC";
        return self::fetchArray($sql);
    }

    public static function eliminarTipoDotacion($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET tipo_dotacion_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}
