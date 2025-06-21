<?php 
require_once __DIR__ . '/../includes/app.php';
use MVC\Router;
use Controllers\AppController;
use Controllers\DotacionInventarioController;
use Controllers\EmpleadoController;
use Controllers\TallaController;
use Controllers\TipoDotacionController;


$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//Tipos de Dotacion
$router->get('/TipoDotacion', [TipoDotacionController::class, 'renderizarPagina']);
$router->get('/TipoDotacion/obtenerActivosAPI', [TipoDotacionController::class, 'obtenerActivosAPI']);
$router->post('/TipoDotacion/guardarAPI', [TipoDotacionController::class, 'guardarAPI']);
$router->post('/TipoDotacion/modificarAPI', [TipoDotacionController::class, 'modificarAPI']);
$router->get('/TipoDotacion/eliminarAPI', [TipoDotacionController::class, 'eliminarAPI']); 

// Tipos de Dotacion

$router->get('/Talla', [TallaController::class, 'renderizarPagina']);
$router->get('/Talla/obtenerActivasAPI', [TallaController::class, 'obtenerActivasAPI']);
$router->post('/Talla/guardarAPI', [TallaController::class, 'guardarAPI']);
$router->post('/Talla/modificarAPI', [TallaController::class, 'modificarAPI']);
$router->get('/Talla/eliminarAPI', [TallaController::class, 'eliminarAPI']);

// Rutas para DotacionInventario
$router->get('/DotacionInventario', [DotacionInventarioController::class, 'renderizarPagina']);
$router->get('/DotacionInventario/obtenerInventarioAPI', [DotacionInventarioController::class, 'obtenerInventarioAPI']);
$router->get('/DotacionInventario/obtenerTiposDotacionAPI', [DotacionInventarioController::class, 'obtenerTiposDotacionAPI']);
$router->get('/DotacionInventario/obtenerTallasAPI', [DotacionInventarioController::class, 'obtenerTallasAPI']);
$router->get('/DotacionInventario/obtenerStockBajoAPI', [DotacionInventarioController::class, 'obtenerStockBajoAPI']);
$router->post('/DotacionInventario/guardarAPI', [DotacionInventarioController::class, 'guardarAPI']);
$router->post('/DotacionInventario/modificarAPI', [DotacionInventarioController::class, 'modificarAPI']);
$router->post('/DotacionInventario/actualizarStockAPI', [DotacionInventarioController::class, 'actualizarStockAPI']);
$router->get('/DotacionInventario/eliminarAPI', [DotacionInventarioController::class, 'eliminarAPI']);


$router->get('/Empleado', [EmpleadoController::class, 'renderizarPagina']);

// API: Obtener todos los empleados activos
$router->get('/Empleado/obtenerEmpleadosAPI', [EmpleadoController::class, 'obtenerEmpleadosAPI']);

// API: Guardar nuevo empleado
$router->post('/Empleado/guardarAPI', [EmpleadoController::class, 'guardarAPI']);

// API: Modificar empleado existente
$router->post('/Empleado/modificarAPI', [EmpleadoController::class, 'modificarAPI']);

// API: Eliminar empleado (eliminación lógica)
$router->get('/Empleado/eliminarAPI', [EmpleadoController::class, 'eliminarAPI']);

// API: Buscar empleados por criterios específicos
$router->get('/Empleado/buscarAPI', [EmpleadoController::class, 'buscarAPI']);

// API: Obtener estadísticas de empleados
$router->get('/Empleado/obtenerEstadisticasAPI', [EmpleadoController::class, 'obtenerEstadisticasAPI']);

// API: Obtener empleados por departamento
$router->get('/Empleado/obtenerPorDepartamentoAPI', [EmpleadoController::class, 'obtenerPorDepartamentoAPI']);


$router->get('/DotacionInventario/diagnosticarCamposBlobAPI', [DotacionInventarioController::class, 'diagnosticarCamposBlobAPI']);
// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();