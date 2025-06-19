<?php
// ========================================
// MODELO: TipoDotacion.php
// ========================================

namespace Model;

class TipoDotacion extends ActiveRecord 
{
    public static $tabla = 'tipo_dotacion';

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
                FROM tipo_dotacion 
                WHERE tipo_dotacion_situacion = 1 
                ORDER BY tipo_dotacion_nombre ASC";
        return self::fetchArray($sql);
    }
}

// ========================================
// MODELO: Talla.php
// ========================================

class Talla extends ActiveRecord 
{
    public static $tabla = 'talla';

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

    public static function obtenerTallasPorTipo($tipo)
    {
        $sql = "SELECT talla_id, talla_nombre, talla_descripcion 
                FROM talla 
                WHERE talla_tipo = ? AND talla_situacion = 1 
                ORDER BY talla_id ASC";
        return self::fetchArray($sql, [$tipo]);
    }
}

// ========================================
// MODELO: Empleado.php
// ========================================

class Empleado extends ActiveRecord 
{
    public static $tabla = 'empleado';

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
        $condDpi = "empleado_dpi = ? AND empleado_situacion = 1";
        $condCorreo = "empleado_correo = ? AND empleado_situacion = 1";
        $paramsDpi = [$dpi];
        $paramsCorreo = [$correo];

        if ($excluirId) {
            $condDpi .= " AND empleado_id != ?";
            $condCorreo .= " AND empleado_id != ?";
            $paramsDpi[] = $excluirId;
            $paramsCorreo[] = $excluirId;
        }

        $sqlDpi = "SELECT COUNT(*) as count FROM empleado WHERE $condDpi";
        $sqlCorreo = "SELECT COUNT(*) as count FROM empleado WHERE $condCorreo";

        $resDpi = self::fetchArray($sqlDpi, $paramsDpi);
        $resCorreo = self::fetchArray($sqlCorreo, $paramsCorreo);

        return [
            'dpi_existe' => ($resDpi[0]['count'] ?? 0) > 0,
            'correo_existe' => ($resCorreo[0]['count'] ?? 0) > 0
        ];
    }
}

// ========================================
// MODELO: DotacionInventario.php
// ========================================

class DotacionInventario extends ActiveRecord 
{
    public static $tabla = 'dotacion_inventario';

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
    }

    public static function obtenerInventarioDisponible($tipoDotacionId = null, $tallaId = null)
    {
        $condiciones = "di.dotacion_inv_situacion = 1 AND di.dotacion_inv_cantidad_actual > 0";
        $params = [];
        
        if ($tipoDotacionId) {
            $condiciones .= " AND di.tipo_dotacion_id = ?";
            $params[] = $tipoDotacionId;
        }
        
        if ($tallaId) {
            $condiciones .= " AND di.talla_id = ?";
            $params[] = $tallaId;
        }

        $sql = "SELECT di.*, td.tipo_dotacion_nombre, t.talla_nombre, t.talla_descripcion
                FROM dotacion_inventario di
                JOIN tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                JOIN talla t ON di.talla_id = t.talla_id
                WHERE $condiciones
                ORDER BY td.tipo_dotacion_nombre, t.talla_id ASC";
        
        return self::fetchArray($sql, $params);
    }

    public static function verificarStockDisponible($tipoDotacionId, $tallaId, $cantidadSolicitada)
    {
        $sql = "SELECT SUM(dotacion_inv_cantidad_actual) as stock_total
                FROM dotacion_inventario 
                WHERE tipo_dotacion_id = ? AND talla_id = ? AND dotacion_inv_situacion = 1";
        
        $resultado = self::fetchArray($sql, [$tipoDotacionId, $tallaId]);
        $stockTotal = $resultado[0]['stock_total'] ?? 0;
        
        return $stockTotal >= $cantidadSolicitada;
    }

    public static function actualizarStock($inventarioId, $cantidadEntregada)
    {
        $sql = "UPDATE dotacion_inventario 
                SET dotacion_inv_cantidad_actual = dotacion_inv_cantidad_actual - ?
                WHERE dotacion_inv_id = ?";
        
        return self::$db->prepare($sql)->execute([$cantidadEntregada, $inventarioId]);
    }
}

// ========================================
// MODELO: DotacionSolicitud.php
// ========================================

class DotacionSolicitud extends ActiveRecord 
{
    public static $tabla = 'dotacion_solicitud';

    public static $columnasDB = [
        'empleado_id',
        'solicitud_fecha',
        'solicitud_estado',
        'solicitud_observaciones',
        'solicitud_fecha_aprobacion',
        'solicitud_aprobado_por',
        'solicitud_situacion'
    ];

    public static $idTabla = 'solicitud_id';

    public $solicitud_id;
    public $empleado_id;
    public $solicitud_fecha;
    public $solicitud_estado;
    public $solicitud_observaciones;
    public $solicitud_fecha_aprobacion;
    public $solicitud_aprobado_por;
    public $solicitud_situacion;

    public function __construct($args = [])
    {
        $this->solicitud_id = $args['solicitud_id'] ?? null;
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->solicitud_fecha = $args['solicitud_fecha'] ?? null;
        $this->solicitud_estado = $args['solicitud_estado'] ?? 'PENDIENTE';
        $this->solicitud_observaciones = $args['solicitud_observaciones'] ?? '';
        $this->solicitud_fecha_aprobacion = $args['solicitud_fecha_aprobacion'] ?? null;
        $this->solicitud_aprobado_por = $args['solicitud_aprobado_por'] ?? null;
        $this->solicitud_situacion = $args['solicitud_situacion'] ?? 1;
    }

    public function obtenerDetalle()
    {
        $sql = "SELECT sdd.*, td.tipo_dotacion_nombre, t.talla_nombre, t.talla_descripcion
                FROM dotacion_solicitud_detalle sdd
                JOIN tipo_dotacion td ON sdd.tipo_dotacion_id = td.tipo_dotacion_id
                JOIN talla t ON sdd.talla_id = t.talla_id
                WHERE sdd.solicitud_id = ? AND sdd.solicitud_det_situacion = 1 
                ORDER BY sdd.solicitud_detalle_id ASC";
        return self::fetchArray($sql, [$this->solicitud_id]);
    }
}

// ========================================
// MODELO: DotacionEntrega.php
// ========================================

class DotacionEntrega extends ActiveRecord 
{
    public static $tabla = 'dotacion_entrega';

    public static $columnasDB = [
        'empleado_id',
        'solicitud_id',
        'entrega_fecha',
        'entrega_año',
        'entrega_observaciones',
        'entrega_entregado_por',
        'entrega_recibido_por',
        'entrega_situacion'
    ];

    public static $idTabla = 'entrega_id';

    public $entrega_id;
    public $empleado_id;
    public $solicitud_id;
    public $entrega_fecha;
    public $entrega_año;
    public $entrega_observaciones;
    public $entrega_entregado_por;
    public $entrega_recibido_por;
    public $entrega_situacion;

    public function __construct($args = [])
    {
        $this->entrega_id = $args['entrega_id'] ?? null;
        $this->empleado_id = $args['empleado_id'] ?? null;
        $this->solicitud_id = $args['solicitud_id'] ?? null;
        $this->entrega_fecha = $args['entrega_fecha'] ?? null;
        $this->entrega_año = $args['entrega_año'] ?? date('Y');
        $this->entrega_observaciones = $args['entrega_observaciones'] ?? '';
        $this->entrega_entregado_por = $args['entrega_entregado_por'] ?? '';
        $this->entrega_recibido_por = $args['entrega_recibido_por'] ?? '';
        $this->entrega_situacion = $args['entrega_situacion'] ?? 1;
    }

    public function obtenerDetalle()
    {
        $sql = "SELECT edd.*, di.dotacion_inv_marca, di.dotacion_inv_modelo, 
                       td.tipo_dotacion_nombre, t.talla_nombre
                FROM dotacion_entrega_detalle edd
                JOIN dotacion_inventario di ON edd.dotacion_inv_id = di.dotacion_inv_id
                JOIN tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
                JOIN talla t ON di.talla_id = t.talla_id
                WHERE edd.entrega_id = ? AND edd.entrega_det_situacion = 1 
                ORDER BY edd.entrega_detalle_id ASC";
        return self::fetchArray($sql, [$this->entrega_id]);
    }

    public static function verificarLimiteAnual($empleadoId, $año = null)
    {
        $año = $año ?? date('Y');
        
        $sql = "SELECT COUNT(*) as entregas_realizadas
                FROM dotacion_entrega 
                WHERE empleado_id = ? AND entrega_año = ? AND entrega_situacion = 1";
        
        $resultado = self::fetchArray($sql, [$empleadoId, $año]);
        $entregasRealizadas = $resultado[0]['entregas_realizadas'] ?? 0;
        
        return $entregasRealizadas < 3; // Máximo 3 entregas por año
    }
}

// ========================================
// CONTROLADOR: TipoDotacionController.php
// ========================================

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\TipoDotacion;

class TipoDotacionController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('tipodotacion/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT * FROM tipo_dotacion WHERE tipo_dotacion_situacion = 1 ORDER BY tipo_dotacion_nombre ASC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tipos de dotación obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los tipos de dotación',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if (empty($_POST['tipo_dotacion_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre del tipo de dotación es obligatorio']);
            return;
        }

        if (strlen($_POST['tipo_dotacion_nombre']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre debe tener al menos 2 caracteres']);
            return;
        }

        try {
            $tipoDotacion = new TipoDotacion([
                'tipo_dotacion_nombre' => trim($_POST['tipo_dotacion_nombre']),
                'tipo_dotacion_descripcion' => trim($_POST['tipo_dotacion_descripcion'] ?? ''),
                'tipo_dotacion_situacion' => 1
            ]);

            $tipoDotacion->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación registrado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['tipo_dotacion_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        if (empty($_POST['tipo_dotacion_nombre']) || strlen($_POST['tipo_dotacion_nombre']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre debe tener al menos 2 caracteres']);
            return;
        }

        try {
            $tipoDotacion = TipoDotacion::find($id);

            if (!$tipoDotacion) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no encontrado']);
                return;
            }

            $tipoDotacion->sincronizar([
                'tipo_dotacion_nombre' => trim($_POST['tipo_dotacion_nombre']),
                'tipo_dotacion_descripcion' => trim($_POST['tipo_dotacion_descripcion'] ?? ''),
                'tipo_dotacion_situacion' => 1
            ]);

            $resultado = $tipoDotacion->actualizar();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar el tipo de dotación']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function eliminarAPI()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no válido']);
            return;
        }

        try {
            $tipoDotacion = TipoDotacion::find($id);
            if (!$tipoDotacion) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no encontrado']);
                return;
            }

            $tipoDotacion->sincronizar(['tipo_dotacion_situacion' => 0]);
            $tipoDotacion->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación eliminado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}

// ========================================
// CONTROLADOR: DotacionEntregaController.php
// ========================================

class DotacionEntregaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('dotacionentrega/index', []);
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT de.*, e.empleado_nombres, e.empleado_apellidos, e.empleado_puesto
                    FROM dotacion_entrega de
                    JOIN empleado e ON de.empleado_id = e.empleado_id
                    WHERE de.entrega_situacion = 1 
                    ORDER BY de.entrega_fecha DESC";
            $data = self::fetchArray($sql);

            // Agregar detalle a cada entrega
            foreach ($data as &$entrega) {
                $entregaObj = new DotacionEntrega(['entrega_id' => $entrega['entrega_id']]);
                $entrega['detalle'] = $entregaObj->obtenerDetalle();
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Entregas obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las entregas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['empleado_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El empleado es obligatorio']);
            return;
        }

        // Validar que haya al menos un detalle
        $detalles = json_decode($_POST['detalles'] ?? '[]', true);
        if (empty($detalles)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe agregar al menos un artículo a entregar']);
            return;
        }

        $empleadoId = intval($_POST['empleado_id']);
        $año = intval($_POST['entrega_año'] ?? date('Y'));

        // Verificar límite anual de entregas
        if (!DotacionEntrega::verificarLimiteAnual($empleadoId, $año)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El empleado ya ha alcanzado el límite de 3 entregas por año']);
            return;
        }

        // Validar disponibilidad de inventario para cada detalle
        foreach ($detalles as $detalle) {
            if (empty($detalle['dotacion_inv_id'])) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID de inventario obligatorio']);
                return;
            }

            $cantidad = intval($detalle['cantidad'] ?? 1);
            if ($cantidad <= 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'La cantidad debe ser mayor a 0']);
                return;
            }

            // Verificar stock actual del inventario específico
            $inventario = DotacionInventario::find($detalle['dotacion_inv_id']);
            if (!$inventario || $inventario->dotacion_inv_cantidad_actual < $cantidad) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay suficiente stock en el inventario seleccionado']);
                return;
            }
        }

        try {
            self::$db->beginTransaction();

            // Crear entrega
            $entrega = new DotacionEntrega([
                'empleado_id' => $empleadoId,
                'solicitud_id' => !empty($_POST['solicitud_id']) ? intval($_POST['solicitud_id']) : null,
                'entrega_fecha' => $_POST['entrega_fecha'] ?? null,
                'entrega_año' => $año,
                'entrega_observaciones' => trim($_POST['entrega_observaciones'] ?? ''),
                'entrega_entregado_por' => trim($_POST['entrega_entregado_por'] ?? ''),
                'entrega_recibido_por' => trim($_POST['entrega_recibido_por'] ?? ''),
                'entrega_situacion' => 1
            ]);

            $resultado = $entrega->crear();
            
            if (!$resultado['resultado']) {
                throw new Exception('Error al crear la entrega');
            }

            $entregaId = $resultado['id'];

            // Crear detalles de entrega y actualizar inventario
            foreach ($detalles as $detalle) {
                $inventario = DotacionInventario::find($detalle['dotacion_inv_id']);
                
                // Insertar detalle
                $sql = "INSERT INTO dotacion_entrega_detalle 
                        (entrega_id, dotacion_inv_id, entrega_det_cantidad, entrega_det_precio_unitario, entrega_det_observaciones, entrega_det_situacion) 
                        VALUES (?, ?, ?, ?, ?, 1)";
                
                $stmt = self::$db->prepare($sql);
                $stmt->execute([
                    $entregaId,
                    $detalle['dotacion_inv_id'],
                    $detalle['cantidad'],
                    $inventario->dotacion_inv_precio_unitario ?? 0,
                    trim($detalle['observaciones'] ?? '')
                ]);

                // Actualizar stock del inventario
                DotacionInventario::actualizarStock($detalle['dotacion_inv_id'], $detalle['cantidad']);
            }

            // Si la entrega está asociada a una solicitud, marcarla como entregada
            if (!empty($_POST['solicitud_id'])) {
                $sqlSolicitud = "UPDATE dotacion_solicitud SET solicitud_estado = 'ENTREGADA' WHERE solicitud_id = ?";
                self::$db->prepare($sqlSolicitud)->execute([$_POST['solicitud_id']]);
            }

            self::$db->commit();

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Entrega registrada correctamente',
                'entrega_id' => $entregaId
            ]);

        } catch (Exception $e) {
            self::$db->rollBack();
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar entrega', 'detalle' => $e->getMessage()]);
        }
    }

    public static function eliminarAPI()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no válido']);
            return;
        }

        try {
            self::$db->beginTransaction();

            $entrega = DotacionEntrega::find($id);
            if (!$entrega) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Entrega no encontrada']);
                return;
            }

            // Obtener detalles antes de eliminar para restaurar stock
            $detalles = $entrega->obtenerDetalle();

            // Eliminar entrega
            $entrega->sincronizar(['entrega_situacion' => 0]);
            $entrega->actualizar();

            // Eliminar detalles
            $sqlDetalles = "UPDATE dotacion_entrega_detalle SET entrega_det_situacion = 0 WHERE entrega_id = ?";
            self::$db->prepare($sqlDetalles)->execute([$id]);

            // Restaurar stock del inventario
            foreach ($detalles as $detalle) {
                $sqlRestaurar = "UPDATE dotacion_inventario 
                               SET dotacion_inv_cantidad_actual = dotacion_inv_cantidad_actual + ?
                               WHERE dotacion_inv_id = ?";
                self::$db->prepare($sqlRestaurar)->execute([
                    $detalle['entrega_det_cantidad'],
                    $detalle['dotacion_inv_id']
                ]);
            }

            self::$db->commit();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Entrega eliminada correctamente']);
            
        } catch (Exception $e) {
            self::$db->rollBack();
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function verificarLimiteAPI()
    {
        $empleadoId = $_GET['empleado_id'] ?? null;
        $año = $_GET['año'] ?? date('Y');

        if (!$empleadoId) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de empleado requerido']);
            return;
        }

        try {
            $puedeRecibir = DotacionEntrega::verificarLimiteAnual($empleadoId, $año);
            
            $sql = "SELECT COUNT(*) as entregas_realizadas
                    FROM dotacion_entrega 
                    WHERE empleado_id = ? AND entrega_año = ? AND entrega_situacion = 1";
            
            $resultado = self::fetchArray($sql, [$empleadoId, $año]);
            $entregasRealizadas = $resultado[0]['entregas_realizadas'] ?? 0;

            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Verificación realizada',
                'puede_recibir' => $puedeRecibir,
                'entregas_realizadas' => $entregasRealizadas,
                'entregas_disponibles' => 3 - $entregasRealizadas
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar límite',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}