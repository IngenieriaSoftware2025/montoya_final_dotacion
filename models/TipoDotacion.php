<?php

namespace Model;

class TipoDotacion extends ActiveRecord
{
    // Nombre de la tabla en la BD
    public static $tabla = 'tipo_dotacion';

    // Columnas que se van a mapear a la BD (SIN fecha para INSERT)
    public static $columnasDB = [
        'tipo_dotacion_nombre',
        'tipo_dotacion_descripcion',
        'tipo_dotacion_situacion'
        // NO incluimos tipo_dotacion_fecha_registro para que la BD la maneje
    ];

    public static $idTabla = 'tipo_dotacion_id';

    // Propiedades
    public $tipo_dotacion_id;
    public $tipo_dotacion_nombre;
    public $tipo_dotacion_descripcion;
    public $tipo_dotacion_situacion;
    public $tipo_dotacion_fecha_registro;

    public function __construct($args = [])
    {
        $this->tipo_dotacion_id = $args['tipo_dotacion_id'] ?? null;
        $this->tipo_dotacion_nombre = $args['tipo_dotacion_nombre'] ?? '';
        $this->tipo_dotacion_descripcion = $args['tipo_dotacion_descripcion'] ?? 'Sin descripción';
        $this->tipo_dotacion_situacion = $args['tipo_dotacion_situacion'] ?? 1;
        // NO asignamos fecha - que la BD la maneje automáticamente
    }

    // Verificar si existe un tipo de dotación con el mismo nombre
    public static function verificarExistente($nombre, $excluirId = null)
    {
        $nombre = self::sanitizarCadena($nombre);
        $condicion = "tipo_dotacion_nombre = '$nombre' AND tipo_dotacion_situacion = 1";

        if ($excluirId) {
            $condicion .= " AND tipo_dotacion_id != " . intval($excluirId);
        }

        $sql = "SELECT COUNT(*) as existe FROM " . self::$tabla . " WHERE $condicion";
        $resultado = self::fetchArray($sql);
        
        return ['nombre_existe' => $resultado[0]['existe'] > 0];
    }

    // Obtener todos los tipos de dotación activos (incluye fecha)
    public static function obtenerActivos()
    {
        $sql = "SELECT 
                    tipo_dotacion_id, 
                    tipo_dotacion_nombre, 
                    tipo_dotacion_descripcion,
                    tipo_dotacion_fecha_registro
                FROM " . self::$tabla . " 
                WHERE tipo_dotacion_situacion = 1 
                ORDER BY tipo_dotacion_nombre ASC";
        return self::fetchArray($sql);
    }

    // Actualizar solo nombre y descripción (sin tocar fecha)
    public function actualizarDatos($nombre, $descripcion)
    {
        // Sanitizar aquí directamente
        $nombre = htmlspecialchars(trim($nombre), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($descripcion), ENT_QUOTES, 'UTF-8');
        
        $sql = "UPDATE " . self::$tabla . " 
                SET tipo_dotacion_nombre = " . self::$db->quote($nombre) . ",
                    tipo_dotacion_descripcion = " . self::$db->quote($descripcion) . "
                WHERE tipo_dotacion_id = " . intval($this->tipo_dotacion_id);
        
        $resultado = self::$db->exec($sql);
        
        return [
            'resultado' => $resultado > 0
        ];
    }

    // Eliminar lógicamente (cambiar situación a 0)
    public function eliminarLogico()
    {
        $sql = "UPDATE " . self::$tabla . " 
                SET tipo_dotacion_situacion = 0 
                WHERE tipo_dotacion_id = " . intval($this->tipo_dotacion_id);
        
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