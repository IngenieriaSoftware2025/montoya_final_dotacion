<?php

namespace Model;

class DotacionControlAnual extends ActiveRecord 
{
    public static $tabla = 'mrml_dotacion_control_anual';

    public static $columnasDB = [
        'empleado_id',
        'control_año',
        'control_entregas_realizadas',
        'control_entregas_maximas',
        'control_fecha_primera_entrega',
        'control_fecha_ultima_entrega',
        'control_situacion'
    ];

    public static $idTabla = 'control_id';

    // Propiedades
    public $control_id;
    public $empleado_id;
    public $control_año;
    public $control_entregas_realizadas;
    public $control_entregas_maximas;
    public $control_fecha_primera_entrega;
    public $control_fecha_ultima_entrega;
    public $control_situacion;

    public function __construct($args = [])
    {
        $this->control_id = $args['control_id'] ?? null;
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->control_año = $args['control_año'] ?? date('Y');
        $this->control_entregas_realizadas = $args['control_entregas_realizadas'] ?? 0;
        $this->control_entregas_maximas = $args['control_entregas_maximas'] ?? 3;
        $this->control_fecha_primera_entrega = $args['control_fecha_primera_entrega'] ?? null;
        $this->control_fecha_ultima_entrega = $args['control_fecha_ultima_entrega'] ?? null;
        $this->control_situacion = $args['control_situacion'] ?? 1;
    }

    public static function actualizarControl($empleadoId, $año = null)
    {
        $año = $año ?? date('Y');
        
        // Verificar si existe el control para este empleado y año
        $sqlVerificar = "SELECT control_id FROM mrml_dotacion_control_anual 
                        WHERE empleado_id = " . intval($empleadoId) . " 
                        AND control_año = " . intval($año);
        
        $existe = self::fetchArray($sqlVerificar);
        
        // Contar entregas del año
        $sqlContar = "SELECT COUNT(*) as total, MIN(entrega_fecha) as primera, MAX(entrega_fecha) as ultima
                     FROM mrml_dotacion_entrega 
                     WHERE empleado_id = " . intval($empleadoId) . "
                     AND entrega_año = " . intval($año) . "
                     AND entrega_situacion = 1";
        
        $datos = self::fetchArray($sqlContar);
        $total = $datos[0]['total'] ?? 0;
        $primera = $datos[0]['primera'] ?? null;
        $ultima = $datos[0]['ultima'] ?? null;
        
        if (empty($existe)) {
            // Crear nuevo control
            $sqlInsertar = "INSERT INTO mrml_dotacion_control_anual 
                           (empleado_id, control_año, control_entregas_realizadas, control_fecha_primera_entrega, control_fecha_ultima_entrega)
                           VALUES (" . intval($empleadoId) . ", " . intval($año) . ", " . intval($total) . ", 
                           " . ($primera ? "'$primera'" : "NULL") . ", " . ($ultima ? "'$ultima'" : "NULL") . ")";
            self::$db->exec($sqlInsertar);
        } else {
            // Actualizar control existente
            $controlId = $existe[0]['control_id'];
            $sqlActualizar = "UPDATE mrml_dotacion_control_anual 
                             SET control_entregas_realizadas = " . intval($total) . ",
                                 control_fecha_primera_entrega = " . ($primera ? "'$primera'" : "NULL") . ",
                                 control_fecha_ultima_entrega = " . ($ultima ? "'$ultima'" : "NULL") . "
                             WHERE control_id = " . intval($controlId);
            self::$db->exec($sqlActualizar);
        }
    }

    public static function obtenerControlPorEmpleado($empleadoId, $año = null)
    {
        $año = $año ?? date('Y');
        
        $sql = "SELECT dca.*, e.empleado_nombres, e.empleado_apellidos
                FROM mrml_dotacion_control_anual dca
                JOIN mrml_empleado e ON dca.empleado_id = e.empleado_id
                WHERE dca.empleado_id = " . intval($empleadoId) . "
                AND dca.control_año = " . intval($año) . "
                AND dca.control_situacion = 1";
        
        $resultado = self::fetchArray($sql);
        return $resultado[0] ?? null;
    }

    public static function obtenerReporteAnual($año = null)
    {
        $año = $año ?? date('Y');
        
        $sql = "SELECT dca.*, e.empleado_nombres, e.empleado_apellidos, e.empleado_puesto, e.empleado_departamento,
                       (dca.control_entregas_maximas - dca.control_entregas_realizadas) as entregas_disponibles
                FROM mrml_dotacion_control_anual dca
                JOIN mrml_empleado e ON dca.empleado_id = e.empleado_id
                WHERE dca.control_año = " . intval($año) . "
                AND dca.control_situacion = 1
                AND e.empleado_situacion = 1
                ORDER BY e.empleado_nombres ASC";
        
        return self::fetchArray($sql);
    }

    public static function eliminarControl($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET control_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}