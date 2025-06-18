<?php

namespace Model;

class Empleado extends ActiveRecord 
{
    public static $tabla = 'mrml_empleado';

    public static $columnasDB = [
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
        $this->empleado_nombres = $args['empleado_nombres'] ?? '';
        $this->empleado_apellidos = $args['empleado_apellidos'] ?? '';
        $this->empleado_dpi = $args['empleado_dpi'] ?? '';
        $this->empleado_puesto = $args['empleado_puesto'] ?? '';
        $this->empleado_departamento = $args['empleado_departamento'] ?? '';
        $this->empleado_fecha_ingreso = $args['empleado_fecha_ingreso'] ?? null;
        $this->empleado_telefono = $args['empleado_telefono'] ?? '';
        $this->empleado_correo = $args['empleado_correo'] ?? '';
        $this->empleado_direccion = $args['empleado_direccion'] ?? '';
        $this->empleado_situacion = $args['empleado_situacion'] ?? 1;
        $this->empleado_fecha_registro = $args['empleado_fecha_registro'] ?? null;
    }

    public static function verificarDpiCorreoExistente($dpi, $correo, $excluirId = null)
    {
        $dpi = self::sanitizarCadena($dpi);
        $correo = self::sanitizarCadena($correo);

        $condDpi = "empleado_dpi = '$dpi' AND empleado_situacion = 1";
        $condCorreo = "empleado_correo = '$correo' AND empleado_situacion = 1";

        if ($excluirId) {
            $condDpi .= " AND empleado_id != " . intval($excluirId);
            $condCorreo .= " AND empleado_id != " . intval($excluirId);
        }

        $sqlDpi = "SELECT COUNT(*) as count FROM mrml_empleado WHERE $condDpi";
        $sqlCorreo = "SELECT COUNT(*) as count FROM mrml_empleado WHERE $condCorreo";

        $resDpi = self::fetchArray($sqlDpi);
        $resCorreo = self::fetchArray($sqlCorreo);

        return [
            'dpi_existe' => ($resDpi[0]['count'] ?? 0) > 0,
            'correo_existe' => ($resCorreo[0]['count'] ?? 0) > 0
        ];
    }

    public static function obtenerEmpleadosActivos()
    {
        $sql = "SELECT empleado_id, (empleado_nombres || ' ' || empleado_apellidos) AS nombre_completo 
                FROM mrml_empleado 
                WHERE empleado_situacion = 1 
                ORDER BY empleado_nombres ASC";
        return self::fetchArray($sql);
    }

    public static function eliminarEmpleado($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET empleado_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    private static function sanitizarCadena($valor)
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
}