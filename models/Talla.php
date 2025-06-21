<?php

namespace Model;

class Talla extends ActiveRecord
{
    // Nombre de la tabla en la BD
    public static $tabla = 'talla';

    // Columnas que se van a mapear a la BD (SIN fecha para INSERT)
    public static $columnasDB = [
        'talla_codigo',
        'talla_descripcion',
        'talla_situacion'
    ];

    public static $idTabla = 'talla_id';

    // Propiedades
    public $talla_id;
    public $talla_codigo;
    public $talla_descripcion;
    public $talla_situacion;

    public function __construct($args = [])
    {
        $this->talla_id = $args['talla_id'] ?? null;
        $this->talla_codigo = $args['talla_codigo'] ?? '';
        $this->talla_descripcion = $args['talla_descripcion'] ?? 'Sin descripción';
        $this->talla_situacion = $args['talla_situacion'] ?? 1;
    }

    // Verificar si existe una talla con el mismo código
    public static function verificarExistente($codigo, $excluirId = null)
    {
        $codigo = self::sanitizarCadena($codigo);
        $condicion = "talla_codigo = '$codigo' AND talla_situacion = 1";

        if ($excluirId) {
            $condicion .= " AND talla_id != " . intval($excluirId);
        }

        $sql = "SELECT COUNT(*) as existe FROM " . self::$tabla . " WHERE $condicion";
        $resultado = self::fetchArray($sql);
        
        return ['codigo_existe' => $resultado[0]['existe'] > 0];
    }

    // Obtener todas las tallas activas
    public static function obtenerActivas()
    {
        $sql = "SELECT 
                    talla_id, 
                    talla_codigo, 
                    talla_descripcion
                FROM " . self::$tabla . " 
                WHERE talla_situacion = 1 
                ORDER BY talla_codigo ASC";
        return self::fetchArray($sql);
    }

    // Actualizar solo código y descripción (sin tocar situación)
    public function actualizarDatos($codigo, $descripcion)
    {
        // Sanitizar aquí directamente
        $codigo = htmlspecialchars(trim($codigo), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($descripcion), ENT_QUOTES, 'UTF-8');
        
        $sql = "UPDATE " . self::$tabla . " 
                SET talla_codigo = " . self::$db->quote($codigo) . ",
                    talla_descripcion = " . self::$db->quote($descripcion) . "
                WHERE talla_id = " . intval($this->talla_id);
        
        $resultado = self::$db->exec($sql);
        
        return [
            'resultado' => $resultado > 0
        ];
    }

    // Eliminar lógicamente (cambiar situación a 0)
    public function eliminarLogico()
    {
        $sql = "UPDATE " . self::$tabla . " 
                SET talla_situacion = 0 
                WHERE talla_id = " . intval($this->talla_id);
        
        $resultado = self::$db->exec($sql);
        
        return [
            'resultado' => $resultado > 0
        ];
    }

    // Sanear cadena de entrada
    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}