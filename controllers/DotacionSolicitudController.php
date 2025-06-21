<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\DotacionSolicitud;
use MVC\Router;

class DotacionSolicitudController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('DotacionSolicitud/index', []);
    }

    // API: Guardar Solicitud
    public static function guardarAPI()
    {
        getHeadersApi();

        $campos = [
            'empleado_id', 'solicitud_fecha'
        ];

        // Validar campos requeridos
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "El campo $campo es requerido"]);
                return;
            }
        }

        // Validar empleado
        if (!is_numeric($_POST['empleado_id']) || $_POST['empleado_id'] <= 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar un empleado válido']);
            return;
        }

        // Validar fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['solicitud_fecha'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Formato de fecha inválido']);
            return;
        }

        try {
            // Generar código automático
            $codigo = DotacionSolicitud::generarCodigo();
            
            $solicitud = new DotacionSolicitud([
                'solicitud_codigo' => $codigo,
                'empleado_id' => $_POST['empleado_id'],
                'solicitud_fecha' => $_POST['solicitud_fecha'],
                'solicitud_observaciones' => $_POST['solicitud_observaciones'] ?? '',
                'solicitud_estado' => 'PENDIENTE',
                'solicitud_situacion' => 1
            ]);
            
            $solicitud->crear();
            echo json_encode([
                'codigo' => 1, 
                'mensaje' => 'Solicitud registrada correctamente',
                'solicitud_codigo' => $codigo
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Modificar Solicitud
    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['solicitud_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        $campos = [
            'solicitud_codigo', 'empleado_id', 'solicitud_fecha'
        ];

        // Validar campos requeridos
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "El campo $campo es requerido"]);
                return;
            }
        }

        // Validar empleado
        if (!is_numeric($_POST['empleado_id']) || $_POST['empleado_id'] <= 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar un empleado válido']);
            return;
        }

        // Validar fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['solicitud_fecha'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Formato de fecha inválido']);
            return;
        }

        // Validar longitud del código
        if (strlen($_POST['solicitud_codigo']) < 5) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El código de solicitud es muy corto']);
            return;
        }

        if (strlen($_POST['solicitud_codigo']) > 20) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El código de solicitud no puede exceder 20 caracteres']);
            return;
        }

        try {
            $solicitud = DotacionSolicitud::find($id);

            if (!$solicitud) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solicitud no encontrada']);
                return;
            }

            // Verificar que la solicitud esté en estado PENDIENTE para poder modificarla
            if ($solicitud->solicitud_estado !== 'PENDIENTE') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solo se pueden modificar solicitudes en estado PENDIENTE']);
                return;
            }

            // Verificar duplicidad de código (excluyendo el registro actual)
            $existe = DotacionSolicitud::verificarExistente($_POST['solicitud_codigo'], $id);
            if ($existe['codigo_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otra solicitud con ese código']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO
            $resultado = $solicitud->actualizarDatos(
                $_POST['solicitud_codigo'],
                $_POST['empleado_id'],
                $_POST['solicitud_fecha'],
                $_POST['solicitud_observaciones'] ?? ''
            );

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Solicitud actualizada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar la solicitud']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Cambiar Estado de Solicitud
    public static function cambiarEstadoAPI()
    {
        getHeadersApi();
        
        $id = $_POST['solicitud_id'] ?? null;
        $estado = $_POST['solicitud_estado'] ?? null;

        if (!$id || !$estado) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID y estado son requeridos']);
            return;
        }

        // Validar estados permitidos
        $estados_validos = ['PENDIENTE', 'APROBADA', 'RECHAZADA', 'ENTREGADA'];
        if (!in_array($estado, $estados_validos)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Estado no válido']);
            return;
        }

        try {
            $solicitud = DotacionSolicitud::find($id);
            
            if (!$solicitud) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solicitud no encontrada']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO
            $aprobado_por = $_POST['aprobado_por'] ?? null;
            $resultado = $solicitud->actualizarEstado($estado, $aprobado_por);

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Estado actualizado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar el estado']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al cambiar estado', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Eliminar Solicitud (eliminación lógica)
    public static function eliminarAPI()
    {
        getHeadersApi();
        
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no válido']);
            return;
        }

        try {
            $solicitud = DotacionSolicitud::find($id);
            
            if (!$solicitud) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solicitud no encontrada']);
                return;
            }

            // Verificar que la solicitud esté en estado PENDIENTE para poder eliminarla
            if ($solicitud->solicitud_estado !== 'PENDIENTE') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solo se pueden eliminar solicitudes en estado PENDIENTE']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO
            $resultado = $solicitud->eliminarLogico();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Solicitud eliminada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar la solicitud']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener todas las solicitudes activas
    public static function obtenerActivasAPI()
    {
        getHeadersApi();
        
        try {
            $solicitudes = DotacionSolicitud::obtenerActivas();
            
            // Verificar si hay datos
            if (!empty($solicitudes)) {
                echo json_encode(['codigo' => 1, 'datos' => $solicitudes]);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay solicitudes registradas', 'datos' => []]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener datos', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener solicitudes por estado
    public static function obtenerPorEstadoAPI()
    {
        getHeadersApi();
        
        $estado = $_GET['estado'] ?? 'PENDIENTE';
        
        try {
            $solicitudes = DotacionSolicitud::obtenerPorEstado($estado);
            
            if (!empty($solicitudes)) {
                echo json_encode(['codigo' => 1, 'datos' => $solicitudes]);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => "No hay solicitudes en estado: $estado", 'datos' => []]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener datos', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener empleados para el select
    public static function obtenerEmpleadosAPI()
    {
        getHeadersApi();
        
        try {
            $empleados = DotacionSolicitud::obtenerEmpleados();
            
            if (!empty($empleados)) {
                echo json_encode(['codigo' => 1, 'datos' => $empleados]);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay empleados registrados', 'datos' => []]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener empleados', 'detalle' => $e->getMessage()]);
        }
    }
}