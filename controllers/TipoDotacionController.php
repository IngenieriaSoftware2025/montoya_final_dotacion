<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\TipoDotacion;
use MVC\Router;

class TipoDotacionController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('TipoDotacion/index', []);
    }

    // API: Guardar Tipo de Dotación
    public static function guardarAPI()
    {
        getHeadersApi();

        $campos = [
            'tipo_dotacion_nombre', 'tipo_dotacion_descripcion'
        ];

        // Validar campos requeridos
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "El campo $campo es requerido"]);
                return;
            }
        }

        // Validar longitud del nombre
        if (strlen($_POST['tipo_dotacion_nombre']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre del tipo de dotación es demasiado corto']);
            return;
        }

        // Verificar duplicidad de nombre
        $existe = TipoDotacion::verificarExistente($_POST['tipo_dotacion_nombre']);
        if ($existe['nombre_existe']) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe un tipo de dotación con ese nombre']);
            return;
        }

        try {
            $tipoDotacion = new TipoDotacion([
                'tipo_dotacion_nombre' => $_POST['tipo_dotacion_nombre'],
                'tipo_dotacion_descripcion' => $_POST['tipo_dotacion_descripcion'],
                'tipo_dotacion_situacion' => 1
                // NO incluimos fecha - que la BD la maneje
            ]);
            
            $tipoDotacion->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación registrado correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Modificar Tipo de Dotación
    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['tipo_dotacion_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        $campos = [
            'tipo_dotacion_nombre', 'tipo_dotacion_descripcion'
        ];

        // Validar campos requeridos
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "El campo $campo es requerido"]);
                return;
            }
        }

        // Validar longitud del nombre
        if (strlen($_POST['tipo_dotacion_nombre']) < 2) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre del tipo de dotación es demasiado corto']);
            return;
        }

        try {
            $tipoDotacion = TipoDotacion::find($id);

            if (!$tipoDotacion) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no encontrado']);
                return;
            }

            // Verificar duplicidad de nombre (excluyendo el registro actual)
            $existe = TipoDotacion::verificarExistente($_POST['tipo_dotacion_nombre'], $id);
            if ($existe['nombre_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro tipo de dotación con ese nombre']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO (evita problemas con fecha)
            $resultado = $tipoDotacion->actualizarDatos(
                $_POST['tipo_dotacion_nombre'], 
                $_POST['tipo_dotacion_descripcion']
            );

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

    // API: Eliminar Tipo de Dotación (eliminación lógica)
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
            $tipoDotacion = TipoDotacion::find($id);
            
            if (!$tipoDotacion) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Tipo de dotación no encontrado']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO (evita problemas con fecha)
            $resultado = $tipoDotacion->eliminarLogico();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Tipo de dotación eliminado correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar el tipo de dotación']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener todos los tipos de dotación activos
    public static function obtenerActivosAPI()
    {
        getHeadersApi();
        
        try {
            $tiposDotacion = TipoDotacion::obtenerActivos();
            
            // Verificar si hay datos
            if (!empty($tiposDotacion)) {
                echo json_encode(['codigo' => 1, 'datos' => $tiposDotacion]);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay tipos de dotación registrados', 'datos' => []]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener datos', 'detalle' => $e->getMessage()]);
        }
    }
}