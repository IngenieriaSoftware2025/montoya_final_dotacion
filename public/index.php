<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\AplicacionController;
use MVC\Router;
use Controllers\AppController;
use Controllers\EmpleadoController;
use Controllers\LoginController;
use Controllers\TipoDotacionController;
use Controllers\UsuarioController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

$router->get('/login', [LoginController::class,'renderizarPagina']);
$router->post('/login/iniciar', [LoginController::class,'login']);
$router->get('/logout', [LoginController::class,'logout']);


$router->get('/tipodotacion', [TipoDotacionController::class, 'renderizarPagina']);
$router->post('/tipodotacion/guardarAPI', [TipoDotacionController::class, 'guardarAPI']);
$router->get('/tipodotacion/buscarAPI', [TipoDotacionController::class, 'buscarAPI']);
$router->post('/tipodotacion/modificarAPI', [TipoDotacionController::class, 'modificarAPI']);
$router->get('/tipodotacion/eliminar', [TipoDotacionController::class, 'eliminarAPI']);


$router->get('/usuario', [UsuarioController::class, 'renderizarPagina']);
$router->post('/usuario/guardarAPI', [UsuarioController::class, 'guardarAPI']);
$router->get('/usuario/buscarAPI', [UsuarioController::class, 'buscarAPI']);
$router->post('/usuario/modificarAPI', [UsuarioController::class, 'modificarAPI']);
$router->get('/usuario/eliminar', [UsuarioController::class, 'eliminarAPI']);


$router->get('/aplicacion', [AplicacionController::class, 'renderizarPagina']);
$router->post('/aplicacion/guardarAPI', [AplicacionController::class, 'guardarAPI']);
$router->get('/aplicacion/buscarAPI', [AplicacionController::class, 'buscarAPI']);
$router->post('/aplicacion/eliminarAPI', [AplicacionController::class, 'eliminarAPI']);
$router->post('/aplicacion/cambiarEstadoAPI', [AplicacionController::class, 'cambiarEstadoAPI']);


$router->get('/empleado', [EmpleadoController::class, 'renderizarPagina']);
$router->get('/empleado/buscarAPI', [EmpleadoController::class, 'buscarAPI']);
$router->post('/empleado/guardarAPI', [EmpleadoController::class, 'guardarAPI']);
$router->post('/empleado/modificarAPI', [EmpleadoController::class, 'modificarAPI']);
$router->get('/empleado/eliminar', [EmpleadoController::class, 'eliminarAPI']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
