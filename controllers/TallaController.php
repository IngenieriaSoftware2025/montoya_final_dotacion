<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Talla;
use MVC\Router;

class TallaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('Talla/index', []);
    }

    // API: Guardar Talla
    public static function guardarAPI()
    {
        getHeadersApi();

        $campos = [
            'talla_codigo', 'talla_descripcion'
        ];

        // Validar campos requeridos
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "El campo $campo es requerido"]);
                return;
            }
        }

        // Validar longitud del código
        if (strlen($_POST['talla_codigo']) < 1) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El código de la talla es requerido']);
            return;
        }

        if (strlen($_POST['talla_codigo']) > 10) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El código de la talla no puede exceder 10 caracteres']);
            return;
        }

        // Verificar duplicidad de código
        $existe = Talla::verificarExistente($_POST['talla_codigo']);
        if ($existe['codigo_existe']) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe una talla con ese código']);
            return;
        }

        try {
            $talla = new Talla([
                'talla_codigo' => $_POST['talla_codigo'],
                'talla_descripcion' => $_POST['talla_descripcion'],
                'talla_situacion' => 1
            ]);
            
            $talla->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Talla registrada correctamente']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Modificar Talla
    public static function modificarAPI()
    {
        getHeadersApi();
        
        $id = $_POST['talla_id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        $campos = [
            'talla_codigo', 'talla_descripcion'
        ];

        // Validar campos requeridos
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => "El campo $campo es requerido"]);
                return;
            }
        }

        // Validar longitud del código
        if (strlen($_POST['talla_codigo']) < 1) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El código de la talla es requerido']);
            return;
        }

        if (strlen($_POST['talla_codigo']) > 10) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El código de la talla no puede exceder 10 caracteres']);
            return;
        }

        try {
            $talla = Talla::find($id);

            if (!$talla) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Talla no encontrada']);
                return;
            }

            // Verificar duplicidad de código (excluyendo el registro actual)
            $existe = Talla::verificarExistente($_POST['talla_codigo'], $id);
            if ($existe['codigo_existe']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otra talla con ese código']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO
            $resultado = $talla->actualizarDatos(
                $_POST['talla_codigo'], 
                $_POST['talla_descripcion']
            );

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Talla actualizada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo actualizar la talla']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Eliminar Talla (eliminación lógica)
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
            $talla = Talla::find($id);
            
            if (!$talla) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Talla no encontrada']);
                return;
            }

            // USAR EL MÉTODO ESPECÍFICO DEL MODELO
            $resultado = $talla->eliminarLogico();

            if ($resultado['resultado']) {
                echo json_encode(['codigo' => 1, 'mensaje' => 'Talla eliminada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'No se pudo eliminar la talla']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // API: Obtener todas las tallas activas
    public static function obtenerActivasAPI()
    {
        getHeadersApi();
        
        try {
            $tallas = Talla::obtenerActivas();
            
            // Verificar si hay datos
            if (!empty($tallas)) {
                echo json_encode(['codigo' => 1, 'datos' => $tallas]);
            } else {
                echo json_encode(['codigo' => 0, 'mensaje' => 'No hay tallas registradas', 'datos' => []]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener datos', 'detalle' => $e->getMessage()]);
        }
    }
}