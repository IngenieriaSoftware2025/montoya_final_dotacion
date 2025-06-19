<?php

namespace Model;

class Talla extends ActiveRecord 
{
    public static $tabla = 'mrml_talla';

    public static $columnasDB = [
        'talla_nombre',
        'talla_descripcion',
        'talla_tipo',
        'talla_situacion'
    ];

    public static $idTabla = 'talla_id';

 
    public $talla_id;
    public $talla_nombre;
    public $talla_descripcion;
    public $talla_tipo;
    public $talla_situacion;

    public function __construct($args = [])
    {
        $this->talla_id = $args['talla_id'] ?? null;
        $this->talla_nombre = $args['talla_nombre'] ?? '';
        $this->talla_descripcion = $args['talla_descripcion'] ?? '';
        $this->talla_tipo = $args['talla_tipo'] ?? '';
        $this->talla_situacion = $args['talla_situacion'] ?? 1;
    }

    public static function obtenerTallasActivas()
    {
        $sql = "SELECT talla_id, talla_nombre, talla_descripcion, talla_tipo 
                FROM mrml_talla 
                WHERE talla_situacion = 1 
                ORDER BY talla_tipo ASC, talla_id ASC";
        return self::fetchArray($sql);
    }

    public static function obtenerTallasPorTipo($tipo)
    {
        $tipo = self::sanitizarCadena($tipo);
        $sql = "SELECT talla_id, talla_nombre, talla_descripcion 
                FROM mrml_talla 
                WHERE talla_tipo = '$tipo' AND talla_situacion = 1 
                ORDER BY talla_id ASC";
        return self::fetchArray($sql);
    }

    public static function eliminarTalla($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET talla_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}
