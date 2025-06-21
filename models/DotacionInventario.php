<?php

namespace Model;

use Exception;

class DotacionInventario extends ActiveRecord
{
    // Nombre de la tabla en la BD
    public static $tabla = 'dotacion_inventario';

    // Columnas que se van a mapear a la BD (CORREGIDAS)
    public static $columnasDB = [
        'dotacion_inv_codigo',
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
        'dotacion_inv_observaciones',
        'dotacion_inv_situacion'
    ];

    public static $idTabla = 'dotacion_inv_id';

    // Propiedades
    public $dotacion_inv_id;
    public $dotacion_inv_codigo;
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
    public $dotacion_inv_fecha_registro;
    public $dotacion_inv_observaciones;
    public $dotacion_inv_situacion;

    public function __construct($args = [])
    {
        $this->dotacion_inv_id = $args['dotacion_inv_id'] ?? null;
        $this->dotacion_inv_codigo = $args['dotacion_inv_codigo'] ?? '';
        $this->tipo_dotacion_id = $args['tipo_dotacion_id'] ?? '';
        $this->talla_id = $args['talla_id'] ?? '';
        $this->dotacion_inv_marca = $args['dotacion_inv_marca'] ?? '';
        $this->dotacion_inv_modelo = $args['dotacion_inv_modelo'] ?? '';
        $this->dotacion_inv_color = $args['dotacion_inv_color'] ?? '';
        $this->dotacion_inv_material = $args['dotacion_inv_material'] ?? '';
        $this->dotacion_inv_cantidad_inicial = $args['dotacion_inv_cantidad_inicial'] ?? 0;
        $this->dotacion_inv_cantidad_actual = $args['dotacion_inv_cantidad_actual'] ?? 0;
        $this->dotacion_inv_cantidad_minima = $args['dotacion_inv_cantidad_minima'] ?? 5;
        $this->dotacion_inv_precio_unitario = $args['dotacion_inv_precio_unitario'] ?? 0.00;
        $this->dotacion_inv_proveedor = $args['dotacion_inv_proveedor'] ?? '';
        $this->dotacion_inv_observaciones = $args['dotacion_inv_observaciones'] ?? '';
        $this->dotacion_inv_situacion = $args['dotacion_inv_situacion'] ?? 1;
    }

    // MÉTODO PERSONALIZADO PARA ACTUALIZAR (evita problemas con sincronizar)
    public function actualizarInventarioPersonalizado($datos)
    {
        try {
            // Construir UPDATE manualmente para Informix
            $campos = [];
            
            // Solo actualizar campos que existen y están permitidos
            $camposPermitidos = [
                'dotacion_inv_codigo',
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
                'dotacion_inv_observaciones'
            ];
            
            foreach ($datos as $campo => $valor) {
                if (in_array($campo, $camposPermitidos)) {
                    if ($valor === '' || $valor === null) {
                        // Para Informix, usar NULL directamente
                        $campos[] = "$campo = NULL";
                    } else {
                        // Sanitizar y escapar el valor
                        $valorLimpio = self::sanitizarCadena($valor);
                        
                        if (is_numeric($valor)) {
                            $campos[] = "$campo = " . $valor;
                        } else {
                            $campos[] = "$campo = '" . addslashes($valorLimpio) . "'";
                        }
                    }
                }
            }
            
            if (empty($campos)) {
                return ['resultado' => false, 'mensaje' => 'No hay campos válidos para actualizar'];
            }
            
            $sql = "UPDATE " . self::$tabla . " 
                    SET " . implode(', ', $campos) . "
                    WHERE dotacion_inv_id = " . intval($this->dotacion_inv_id);
            
            error_log("SQL UPDATE PERSONALIZADO: " . $sql);
            
            $resultado = self::$db->exec($sql);
            
            return [
                'resultado' => $resultado !== false,
                'mensaje' => $resultado !== false ? 'Producto actualizado correctamente' : 'No se pudo actualizar el producto'
            ];
            
        } catch (Exception $e) {
            error_log("Error en actualizarInventarioPersonalizado: " . $e->getMessage());
            return ['resultado' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // Método actualizar SOBRESCRITO para evitar problemas con BLOB y columnas inexistentes
    public function actualizar()
    {
        try {
            // Usar método personalizado en lugar del heredado
            $datos = [];
            
            // Construir array con valores actuales del objeto
            foreach (self::$columnasDB as $columna) {
                if (property_exists($this, $columna)) {
                    $datos[$columna] = $this->$columna;
                }
            }
            
            return $this->actualizarInventarioPersonalizado($datos);
            
        } catch (Exception $e) {
            error_log("Error en método actualizar: " . $e->getMessage());
            return ['resultado' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // Verificar código existente (copiando estrategia exitosa del Inventario)
    public static function verificarCodigoExistente($codigo, $excluirId = null)
    {
        $codigo = self::sanitizarCadena($codigo);

        $condCodigo = "dotacion_inv_codigo = '$codigo' AND dotacion_inv_situacion = 1";

        if ($excluirId) {
            $condCodigo .= " AND dotacion_inv_id != " . intval($excluirId);
        }

        $sql = "
            SELECT 
                (SELECT COUNT(*) FROM dotacion_inventario WHERE $condCodigo) AS codigo_existe
        ";

        $resultado = self::fetchArray($sql);
        return $resultado[0] ?? ['codigo_existe' => 0];
    }

    // Eliminar producto (lógico)
    public static function EliminarInventario($id)
    {
        $sql = "UPDATE " . self::$tabla . " SET dotacion_inv_situacion = 0 WHERE " . self::$idTabla . " = " . intval($id);
        return self::$db->exec($sql);
    }

    // Obtener inventario disponible
    public static function obtenerInventarioDisponible()
    {
        $sql = "
            SELECT 
                i.dotacion_inv_id,
                i.dotacion_inv_codigo,
                i.dotacion_inv_cantidad_actual,
                i.dotacion_inv_precio_unitario,
                td.tipo_dotacion_nombre,
                t.talla_codigo
            FROM dotacion_inventario i
            INNER JOIN tipo_dotacion td ON i.tipo_dotacion_id = td.tipo_dotacion_id
            INNER JOIN talla t ON i.talla_id = t.talla_id
            WHERE i.dotacion_inv_situacion = 1 
            AND i.dotacion_inv_cantidad_actual > 0
            ORDER BY td.tipo_dotacion_nombre ASC, t.talla_codigo ASC
        ";
        return self::fetchArray($sql);
    }

    // Obtener estadísticas del inventario
    public static function obtenerEstadisticasInventario()
    {
        $sql = "
            SELECT 
                COUNT(*) as total_productos,
                SUM(dotacion_inv_cantidad_actual) as stock_total,
                SUM(CASE WHEN dotacion_inv_cantidad_actual > 0 THEN dotacion_inv_cantidad_actual ELSE 0 END) as stock_disponible,
                SUM(CASE WHEN dotacion_inv_cantidad_actual <= dotacion_inv_cantidad_minima THEN 1 ELSE 0 END) as productos_stock_bajo,
                SUM(CASE WHEN dotacion_inv_cantidad_actual = 0 THEN 1 ELSE 0 END) as productos_sin_stock,
                AVG(dotacion_inv_precio_unitario) as precio_promedio
            FROM dotacion_inventario 
            WHERE dotacion_inv_situacion = 1
        ";
        $resultado = self::fetchArray($sql);
        return $resultado[0] ?? [];
    }

    // Buscar productos por criterios
    public static function buscarProductos($criterio = '', $valor = '')
    {
        $where = "i.dotacion_inv_situacion = 1";
        
        if ($criterio && $valor) {
            $valor = self::sanitizarCadena($valor);
            switch ($criterio) {
                case 'tipo':
                    $where .= " AND td.tipo_dotacion_nombre LIKE '%$valor%'";
                    break;
                case 'talla':
                    $where .= " AND t.talla_codigo LIKE '%$valor%'";
                    break;
                case 'codigo':
                    $where .= " AND i.dotacion_inv_codigo LIKE '%$valor%'";
                    break;
                case 'marca':
                    $where .= " AND i.dotacion_inv_marca LIKE '%$valor%'";
                    break;
                case 'modelo':
                    $where .= " AND i.dotacion_inv_modelo LIKE '%$valor%'";
                    break;
                case 'stock_bajo':
                    $where .= " AND i.dotacion_inv_cantidad_actual <= i.dotacion_inv_cantidad_minima";
                    break;
            }
        }

        $sql = "
            SELECT 
                i.*,
                td.tipo_dotacion_nombre,
                t.talla_codigo
            FROM dotacion_inventario i
            INNER JOIN tipo_dotacion td ON i.tipo_dotacion_id = td.tipo_dotacion_id
            INNER JOIN talla t ON i.talla_id = t.talla_id
            WHERE $where
            ORDER BY i.dotacion_inv_codigo ASC
        ";
        
        return self::fetchArray($sql);
    }

    // Obtener productos con stock bajo (como en el exitoso)
    public static function obtenerStockBajo()
    {
        $sql = "
            SELECT 
                i.dotacion_inv_id,
                i.dotacion_inv_codigo,
                i.dotacion_inv_cantidad_actual,
                i.dotacion_inv_cantidad_minima,
                td.tipo_dotacion_nombre,
                t.talla_codigo
            FROM dotacion_inventario i
            INNER JOIN tipo_dotacion td ON i.tipo_dotacion_id = td.tipo_dotacion_id
            INNER JOIN talla t ON i.talla_id = t.talla_id
            WHERE i.dotacion_inv_situacion = 1 
            AND i.dotacion_inv_cantidad_actual <= i.dotacion_inv_cantidad_minima
            ORDER BY i.dotacion_inv_cantidad_actual ASC
        ";
        return self::fetchArray($sql);
    }

    // Actualizar stock específicamente
    public function actualizarStock($nuevaCantidad)
    {
        try {
            $sql = "UPDATE " . self::$tabla . " 
                    SET dotacion_inv_cantidad_actual = " . intval($nuevaCantidad) . "
                    WHERE dotacion_inv_id = " . intval($this->dotacion_inv_id);
            
            $resultado = self::$db->exec($sql);
            
            return [
                'resultado' => $resultado !== false,
                'mensaje' => $resultado !== false ? 'Stock actualizado correctamente' : 'No se pudo actualizar el stock'
            ];
            
        } catch (Exception $e) {
            return [
                'resultado' => false,
                'mensaje' => 'Error al actualizar stock: ' . $e->getMessage()
            ];
        }
    }

    // Actualizar inventario completo (método de compatibilidad)
    public function actualizarInventario($datos)
    {
        return $this->actualizarInventarioPersonalizado($datos);
    }

    // Eliminar lógico
    public function eliminarLogico()
    {
        try {
            $sql = "UPDATE " . self::$tabla . " 
                    SET dotacion_inv_situacion = 0 
                    WHERE dotacion_inv_id = " . intval($this->dotacion_inv_id);
            
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

    // Validaciones
    public function validar()
    {
        $errores = [];

        // Validar código
        if (!$this->dotacion_inv_codigo || strlen($this->dotacion_inv_codigo) > 20) {
            $errores[] = 'El código es requerido y no puede exceder 20 caracteres';
        }

        // Validar tipo de dotación
        if (!$this->tipo_dotacion_id) {
            $errores[] = 'Debe seleccionar un tipo de dotación';
        }

        // Validar talla
        if (!$this->talla_id) {
            $errores[] = 'Debe seleccionar una talla';
        }

        // Validar cantidades
        if ($this->dotacion_inv_cantidad_inicial < 0) {
            $errores[] = 'La cantidad inicial no puede ser negativa';
        }

        if ($this->dotacion_inv_cantidad_actual < 0) {
            $errores[] = 'La cantidad actual no puede ser negativa';
        }

        if ($this->dotacion_inv_cantidad_actual > $this->dotacion_inv_cantidad_inicial) {
            $errores[] = 'La cantidad actual no puede ser mayor que la inicial';
        }

        // Validar precio si se proporciona
        if ($this->dotacion_inv_precio_unitario && $this->dotacion_inv_precio_unitario < 0) {
            $errores[] = 'El precio no puede ser negativo';
        }

        return $errores;
    }

    // Sanear cadena de entrada (copiando del exitoso)
    private static function sanitizarCadena($valor)
    {
        if ($valor === null || $valor === '') {
            return '';
        }
        $valor = str_replace("'", "''", trim($valor));
        
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }
}