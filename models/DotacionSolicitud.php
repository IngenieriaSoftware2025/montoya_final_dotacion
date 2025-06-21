<?php

namespace Model;

class DotacionSolicitud extends ActiveRecord
{
    // Nombre de la tabla en la BD
    public static $tabla = 'dotacion_solicitud';

    // Columnas que se van a mapear a la BD (SIN fecha para INSERT)
    public static $columnasDB = [
        'solicitud_codigo',
        'empleado_id',
        'solicitud_fecha',
        'solicitud_estado',
        'solicitud_observaciones',
        'solicitud_situacion'
    ];

    public static $idTabla = 'solicitud_id';

    // Propiedades
    public $solicitud_id;
    public $solicitud_codigo;
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
        $this->solicitud_codigo = $args['solicitud_codigo'] ?? '';
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->solicitud_fecha = $args['solicitud_fecha'] ?? date('Y-m-d');
        $this->solicitud_estado = $args['solicitud_estado'] ?? 'PENDIENTE';
        $this->solicitud_observaciones = $args['solicitud_observaciones'] ?? '';
        $this->solicitud_fecha_aprobacion = $args['solicitud_fecha_aprobacion'] ?? null;
        $this->solicitud_aprobado_por = $args['solicitud_aprobado_por'] ?? null;
        $this->solicitud_situacion = $args['solicitud_situacion'] ?? 1;
        $this->solicitud_fecha_registro = $args['solicitud_fecha_registro'] ?? date('Y-m-d');
    }

    // Verificar si existe una solicitud con el mismo código
    public static function verificarExistente($codigo, $excluirId = null)
    {
        $codigo = self::sanitizarCadena($codigo);
        $condicion = "solicitud_codigo = '$codigo' AND solicitud_situacion = 1";

        if ($excluirId) {
            $condicion .= " AND solicitud_id != " . intval($excluirId);
        }

        $sql = "SELECT COUNT(*) as existe FROM " . self::$tabla . " WHERE $condicion";
        $resultado = self::fetchArray($sql);
        
        return ['codigo_existe' => $resultado[0]['existe'] > 0];
    }

    // Obtener todas las solicitudes activas con información del empleado
    public static function obtenerActivas()
    {
        $sql = "SELECT 
                    ds.solicitud_id,
                    ds.solicitud_codigo,
                    ds.empleado_id,
                    e.empleado_nombre,
                    e.empleado_apellido,
                    ds.solicitud_fecha,
                    ds.solicitud_estado,
                    ds.solicitud_observaciones,
                    ds.solicitud_fecha_aprobacion,
                    ds.solicitud_aprobado_por
                FROM " . self::$tabla . " ds
                INNER JOIN empleado e ON ds.empleado_id = e.empleado_id
                WHERE ds.solicitud_situacion = 1 
                ORDER BY ds.solicitud_fecha DESC, ds.solicitud_codigo ASC";
        return self::fetchArray($sql);
    }

    // Obtener solicitudes por estado
    public static function obtenerPorEstado($estado)
    {
        $estado = self::sanitizarCadena($estado);
        $sql = "SELECT 
                    ds.solicitud_id,
                    ds.solicitud_codigo,
                    ds.empleado_id,
                    e.empleado_nombre,
                    e.empleado_apellido,
                    ds.solicitud_fecha,
                    ds.solicitud_estado,
                    ds.solicitud_observaciones
                FROM " . self::$tabla . " ds
                INNER JOIN empleado e ON ds.empleado_id = e.empleado_id
                WHERE ds.solicitud_estado = '$estado' AND ds.solicitud_situacion = 1 
                ORDER BY ds.solicitud_fecha DESC";
        return self::fetchArray($sql);
    }

    // Actualizar datos básicos de la solicitud
    public function actualizarDatos($codigo, $empleado_id, $fecha, $observaciones)
    {
        // Sanitizar datos
        $codigo = htmlspecialchars(trim($codigo), ENT_QUOTES, 'UTF-8');
        $observaciones = htmlspecialchars(trim($observaciones), ENT_QUOTES, 'UTF-8');
        $empleado_id = intval($empleado_id);
        $fecha = htmlspecialchars(trim($fecha), ENT_QUOTES, 'UTF-8');
        
        $sql = "UPDATE " . self::$tabla . " 
                SET solicitud_codigo = " . self::$db->quote($codigo) . ",
                    empleado_id = " . $empleado_id . ",
                    solicitud_fecha = " . self::$db->quote($fecha) . ",
                    solicitud_observaciones = " . self::$db->quote($observaciones) . "
                WHERE solicitud_id = " . intval($this->solicitud_id);
        
        $resultado = self::$db->exec($sql);
        
        return [
            'resultado' => $resultado > 0
        ];
    }

    // Actualizar estado de la solicitud
    public function actualizarEstado($estado, $aprobado_por = null)
    {
        $estado = self::sanitizarCadena($estado);
        $fecha_aprobacion = null;
        
        if (in_array($estado, ['APROBADA', 'RECHAZADA'])) {
            $fecha_aprobacion = date('Y-m-d');
        }
        
        $sql = "UPDATE " . self::$tabla . " 
                SET solicitud_estado = " . self::$db->quote($estado);
        
        if ($fecha_aprobacion) {
            $sql .= ", solicitud_fecha_aprobacion = " . self::$db->quote($fecha_aprobacion);
        }
        
        if ($aprobado_por) {
            $sql .= ", solicitud_aprobado_por = " . intval($aprobado_por);
        }
        
        $sql .= " WHERE solicitud_id = " . intval($this->solicitud_id);
        
        $resultado = self::$db->exec($sql);
        
        return [
            'resultado' => $resultado > 0
        ];
    }

    // Eliminar lógicamente (cambiar situación a 0)
    public function eliminarLogico()
    {
        $sql = "UPDATE " . self::$tabla . " 
                SET solicitud_situacion = 0 
                WHERE solicitud_id = " . intval($this->solicitud_id);
        
        $resultado = self::$db->exec($sql);
        
        return [
            'resultado' => $resultado > 0
        ];
    }

    // Generar código automático para nueva solicitud
    public static function generarCodigo()
    {
        $fecha = date('Ymd');
        $sql = "SELECT COUNT(*) as total FROM " . self::$tabla . " 
                WHERE solicitud_fecha = CURDATE() AND solicitud_situacion = 1";
        $resultado = self::fetchArray($sql);
        $consecutivo = ($resultado[0]['total'] + 1);
        
        return "SOL-" . $fecha . "-" . str_pad($consecutivo, 3, '0', STR_PAD_LEFT);
    }

    // Obtener empleados activos para el select
    public static function obtenerEmpleados()
    {
        $sql = "SELECT 
                    empleado_id, 
                    CONCAT(empleado_nombre, ' ', empleado_apellido) as empleado_completo,
                    empleado_nombre,
                    empleado_apellido
                FROM empleado 
                WHERE empleado_situacion = 1 
                ORDER BY empleado_nombre ASC, empleado_apellido ASC";
        return self::fetchArray($sql);
    }

    // Sanear cadena de entrada
    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}