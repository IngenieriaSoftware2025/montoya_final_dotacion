<?php

namespace Model;

class Empleado extends ActiveRecord
{
    // Nombre de la tabla en la BD
    public static $tabla = 'empleado';

    // Columnas que se van a mapear a la BD
    public static $columnasDB = [
        'empleado_codigo',
        'empleado_nombres',
        'empleado_apellidos',
        'empleado_dpi',
        'empleado_puesto',
        'empleado_departamento',
        'empleado_fecha_ingreso',
        'empleado_telefono',
        'empleado_correo',
        'empleado_direccion',
        'empleado_situacion'
    ];

    public static $idTabla = 'empleado_id';

    // Propiedades
    public $empleado_id;
    public $empleado_codigo;
    public $empleado_nombres;
    public $empleado_apellidos;
    public $empleado_dpi;
    public $empleado_puesto;
    public $empleado_departamento;
    public $empleado_fecha_ingreso;
    public $empleado_telefono;
    public $empleado_correo;
    public $empleado_direccion;
    public $empleado_situacion;
    public $empleado_fecha_registro;

    public function __construct($args = [])
    {
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->empleado_codigo = $args['empleado_codigo'] ?? '';
        $this->empleado_nombres = $args['empleado_nombres'] ?? '';
        $this->empleado_apellidos = $args['empleado_apellidos'] ?? '';
        $this->empleado_dpi = $args['empleado_dpi'] ?? '';
        $this->empleado_puesto = $args['empleado_puesto'] ?? '';
        $this->empleado_departamento = $args['empleado_departamento'] ?? '';
        $this->empleado_fecha_ingreso = $args['empleado_fecha_ingreso'] ?? '';
        $this->empleado_telefono = $args['empleado_telefono'] ?? '';
        $this->empleado_correo = $args['empleado_correo'] ?? '';
        $this->empleado_direccion = $args['empleado_direccion'] ?? '';
        $this->empleado_situacion = $args['empleado_situacion'] ?? 1;
    }

    // Verificar si ya existe un código o DPI
    public static function verificarEmpleadoExistente($codigo, $dpi = null, $excluirId = null)
    {
        $codigo = self::sanitizarCadena($codigo);
        $condiciones = [];

        // Verificar código
        $condCodigo = "empleado_codigo = '$codigo' AND empleado_situacion = 1";
        if ($excluirId) {
            $condCodigo .= " AND empleado_id != " . intval($excluirId);
        }
        $condiciones[] = "(SELECT COUNT(*) FROM empleado WHERE $condCodigo) AS codigo_existe";

        // Verificar DPI si se proporciona
        if ($dpi && trim($dpi) !== '') {
            $dpi = self::sanitizarCadena($dpi);
            $condDpi = "empleado_dpi = '$dpi' AND empleado_situacion = 1";
            if ($excluirId) {
                $condDpi .= " AND empleado_id != " . intval($excluirId);
            }
            $condiciones[] = "(SELECT COUNT(*) FROM empleado WHERE $condDpi) AS dpi_existe";
        } else {
            $condiciones[] = "0 AS dpi_existe";
        }

        $sql = "SELECT " . implode(', ', $condiciones);
        $resultado = self::fetchArray($sql);
        
        return [
            'codigo_existe' => ($resultado[0]['codigo_existe'] ?? 0) > 0,
            'dpi_existe' => ($resultado[0]['dpi_existe'] ?? 0) > 0
        ];
    }

    // Método personalizado para actualizar (evita problemas con Informix)
    public function actualizarEmpleadoPersonalizado($datos)
    {
        try {
            $campos = [];
            
            $camposPermitidos = [
                'empleado_codigo',
                'empleado_nombres',
                'empleado_apellidos',
                'empleado_dpi',
                'empleado_puesto',
                'empleado_departamento',
                'empleado_fecha_ingreso',
                'empleado_telefono',
                'empleado_correo',
                'empleado_direccion'
            ];
            
            foreach ($datos as $campo => $valor) {
                if (in_array($campo, $camposPermitidos)) {
                    if ($valor === '' || $valor === null) {
                        $campos[] = "$campo = NULL";
                    } else {
                        $valorLimpio = self::sanitizarCadena($valor);
                        $campos[] = "$campo = '" . addslashes($valorLimpio) . "'";
                    }
                }
            }
            
            if (empty($campos)) {
                return ['resultado' => false, 'mensaje' => 'No hay campos válidos para actualizar'];
            }
            
            $sql = "UPDATE " . self::$tabla . " 
                    SET " . implode(', ', $campos) . "
                    WHERE empleado_id = " . intval($this->empleado_id);
            
            error_log("SQL UPDATE EMPLEADO: " . $sql);
            
            $resultado = self::$db->exec($sql);
            
            return [
                'resultado' => $resultado !== false,
                'mensaje' => $resultado !== false ? 'Empleado actualizado correctamente' : 'No se pudo actualizar el empleado'
            ];
            
        } catch (Exception $e) {
            error_log("Error en actualizarEmpleadoPersonalizado: " . $e->getMessage());
            return ['resultado' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // Método actualizar sobrescrito
    public function actualizar()
    {
        try {
            $datos = [];
            
            foreach (self::$columnasDB as $columna) {
                if (property_exists($this, $columna)) {
                    $datos[$columna] = $this->$columna;
                }
            }
            
            return $this->actualizarEmpleadoPersonalizado($datos);
            
        } catch (Exception $e) {
            error_log("Error en método actualizar empleado: " . $e->getMessage());
            return ['resultado' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // Eliminar empleado (lógico)
    public function eliminarLogico()
    {
        try {
            $sql = "UPDATE " . self::$tabla . " 
                    SET empleado_situacion = 0 
                    WHERE empleado_id = " . intval($this->empleado_id);
            
            $resultado = self::$db->exec($sql);
            
            return [
                'resultado' => $resultado !== false
            ];
            
        } catch (Exception $e) {
            return [
                'resultado' => false
            ];
        }
    }

    // Obtener empleados por departamento
    public static function obtenerPorDepartamento($departamento)
    {
        $departamento = self::sanitizarCadena($departamento);
        $sql = "
            SELECT *
            FROM empleado
            WHERE empleado_departamento = '$departamento' 
            AND empleado_situacion = 1
            ORDER BY empleado_apellidos ASC, empleado_nombres ASC
        ";
        return self::fetchArray($sql);
    }

    // Buscar empleados por criterios
    public static function buscarEmpleados($criterio = '', $valor = '')
    {
        $where = "empleado_situacion = 1";
        
        if ($criterio && $valor) {
            $valor = self::sanitizarCadena($valor);
            switch ($criterio) {
                case 'nombres':
                    $where .= " AND (empleado_nombres LIKE '%$valor%' OR empleado_apellidos LIKE '%$valor%')";
                    break;
                case 'codigo':
                    $where .= " AND empleado_codigo LIKE '%$valor%'";
                    break;
                case 'dpi':
                    $where .= " AND empleado_dpi LIKE '%$valor%'";
                    break;
                case 'puesto':
                    $where .= " AND empleado_puesto LIKE '%$valor%'";
                    break;
                case 'departamento':
                    $where .= " AND empleado_departamento LIKE '%$valor%'";
                    break;
                case 'correo':
                    $where .= " AND empleado_correo LIKE '%$valor%'";
                    break;
            }
        }

        $sql = "
            SELECT *
            FROM empleado
            WHERE $where
            ORDER BY empleado_apellidos ASC, empleado_nombres ASC
        ";
        
        return self::fetchArray($sql);
    }

    // Obtener estadísticas de empleados
    public static function obtenerEstadisticasEmpleados()
    {
        $sql = "
            SELECT 
                COUNT(*) as total_empleados,
                COUNT(DISTINCT empleado_departamento) as total_departamentos,
                COUNT(DISTINCT empleado_puesto) as total_puestos,
                COUNT(CASE WHEN empleado_fecha_ingreso >= CURRENT - 30 UNITS DAY THEN 1 END) as nuevos_ultimo_mes
            FROM empleado 
            WHERE empleado_situacion = 1
        ";
        $resultado = self::fetchArray($sql);
        return $resultado[0] ?? [];
    }

    // Obtener empleados activos
    public static function obtenerEmpleadosActivos()
    {
        $sql = "
            SELECT 
                empleado_id,
                empleado_codigo,
                empleado_nombres,
                empleado_apellidos,
                empleado_puesto,
                empleado_departamento,
                empleado_telefono,
                empleado_correo
            FROM empleado
            WHERE empleado_situacion = 1
            ORDER BY empleado_apellidos ASC, empleado_nombres ASC
        ";
        return self::fetchArray($sql);
    }

    // Validaciones
    public function validar()
    {
        $errores = [];

        // Validar código
        if (!$this->empleado_codigo || strlen($this->empleado_codigo) > 20) {
            $errores[] = 'El código es requerido y no puede exceder 20 caracteres';
        }

        // Validar nombres
        if (!$this->empleado_nombres || strlen($this->empleado_nombres) > 100) {
            $errores[] = 'Los nombres son requeridos y no pueden exceder 100 caracteres';
        }

        // Validar apellidos
        if (!$this->empleado_apellidos || strlen($this->empleado_apellidos) > 100) {
            $errores[] = 'Los apellidos son requeridos y no pueden exceder 100 caracteres';
        }

        // Validar DPI (si se proporciona)
        if ($this->empleado_dpi && strlen($this->empleado_dpi) > 15) {
            $errores[] = 'El DPI no puede exceder 15 caracteres';
        }

        // Validar correo (si se proporciona)
        if ($this->empleado_correo && !filter_var($this->empleado_correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El formato del correo electrónico no es válido';
        }

        // Validar teléfono (si se proporciona)
        if ($this->empleado_telefono && strlen($this->empleado_telefono) > 15) {
            $errores[] = 'El teléfono no puede exceder 15 caracteres';
        }

        return $errores;
    }

    // Obtener nombre completo
    public function getNombreCompleto()
    {
        return trim($this->empleado_nombres . ' ' . $this->empleado_apellidos);
    }

    // Sanear cadena de entrada
    private static function sanitizarCadena($valor)
    {
        if ($valor === null || $valor === '') {
            return '';
        }
        
        $valor = str_replace("'", "''", trim($valor));
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }
}