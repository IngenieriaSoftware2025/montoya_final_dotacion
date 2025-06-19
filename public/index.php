<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\AplicacionController;
use MVC\Router;
use Controllers\AppController;
use Controllers\DotacionInventarioController;
use Controllers\EmpleadoController;
use Controllers\LoginController;
use Controllers\TipoDotacionController;
use Controllers\TallaController; 
use Controllers\UsuarioController;
use Controllers\DotacionSolicitudController;
use Controllers\DotacionEntregaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

$router->get('/login', [LoginController::class,'renderizarPagina']);
$router->post('/login/iniciar', [LoginController::class,'login']);
$router->get('/logout', [LoginController::class,'logout']);

// RUTAS TIPO DOTACIÓN
$router->get('/tipodotacion', [TipoDotacionController::class, 'renderizarPagina']);
$router->post('/tipodotacion/guardarAPI', [TipoDotacionController::class, 'guardarAPI']);
$router->get('/tipodotacion/buscarAPI', [TipoDotacionController::class, 'buscarAPI']);
$router->post('/tipodotacion/modificarAPI', [TipoDotacionController::class, 'modificarAPI']);
$router->get('/tipodotacion/eliminar', [TipoDotacionController::class, 'eliminarAPI']);

// RUTAS TALLA - AGREGAR ESTAS RUTAS QUE FALTAN
$router->get('/talla', [TallaController::class, 'renderizarPagina']);
$router->post('/talla/guardarAPI', [TallaController::class, 'guardarAPI']);
$router->get('/talla/buscarAPI', [TallaController::class, 'buscarAPI']);
$router->get('/talla/buscarPorTipoAPI', [TallaController::class, 'buscarPorTipoAPI']); // ESTA RUTA FALTABA!
$router->post('/talla/modificarAPI', [TallaController::class, 'modificarAPI']);
$router->get('/talla/eliminar', [TallaController::class, 'eliminarAPI']);

// RUTAS USUARIO
$router->get('/usuario', [UsuarioController::class, 'renderizarPagina']);
$router->post('/usuario/guardarAPI', [UsuarioController::class, 'guardarAPI']);
$router->get('/usuario/buscarAPI', [UsuarioController::class, 'buscarAPI']);
$router->post('/usuario/modificarAPI', [UsuarioController::class, 'modificarAPI']);
$router->get('/usuario/eliminar', [UsuarioController::class, 'eliminarAPI']);

// RUTAS APLICACIÓN
$router->get('/aplicacion', [AplicacionController::class, 'renderizarPagina']);
$router->post('/aplicacion/guardarAPI', [AplicacionController::class, 'guardarAPI']);
$router->get('/aplicacion/buscarAPI', [AplicacionController::class, 'buscarAPI']);
$router->post('/aplicacion/modificarAPI', [AplicacionController::class, 'modificarAPI']); // FALTABA ESTA
$router->get('/aplicacion/eliminar', [AplicacionController::class, 'eliminarAPI']);

// RUTAS EMPLEADO
$router->get('/empleado', [EmpleadoController::class, 'renderizarPagina']);
$router->get('/empleado/buscarAPI', [EmpleadoController::class, 'buscarAPI']);
$router->post('/empleado/guardarAPI', [EmpleadoController::class, 'guardarAPI']);
$router->post('/empleado/modificarAPI', [EmpleadoController::class, 'modificarAPI']);
$router->get('/empleado/eliminar', [EmpleadoController::class, 'eliminarAPI']);

// RUTAS DOTACIÓN INVENTARIO
$router->get('/dotacioninventario', [DotacionInventarioController::class, 'renderizarPagina']);
$router->get('/dotacioninventario/buscarAPI', [DotacionInventarioController::class, 'buscarAPI']);
$router->get('/dotacioninventario/buscarDisponibleAPI', [DotacionInventarioController::class, 'buscarDisponibleAPI']); // NUEVA
$router->post('/dotacioninventario/guardarAPI', [DotacionInventarioController::class, 'guardarAPI']);
$router->post('/dotacioninventario/modificarAPI', [DotacionInventarioController::class, 'modificarAPI']);
$router->get('/dotacioninventario/eliminar', [DotacionInventarioController::class, 'eliminarAPI']);

// RUTAS DOTACIÓN SOLICITUD
$router->get('/dotacionsolicitud', [DotacionSolicitudController::class, 'renderizarPagina']);
$router->get('/dotacionsolicitud/buscarAPI', [DotacionSolicitudController::class, 'buscarAPI']);
$router->post('/dotacionsolicitud/guardarAPI', [DotacionSolicitudController::class, 'guardarAPI']);
$router->post('/dotacionsolicitud/aprobarAPI', [DotacionSolicitudController::class, 'aprobarAPI']);
$router->post('/dotacionsolicitud/rechazarAPI', [DotacionSolicitudController::class, 'rechazarAPI']);
$router->get('/dotacionsolicitud/eliminar', [DotacionSolicitudController::class, 'eliminarAPI']);

// Agregar estas rutas adicionales a tu index.php:



// RUTAS DOTACIÓN SOLICITUD
$router->get('/dotacionsolicitud', [DotacionSolicitudController::class, 'renderizarPagina']);
$router->get('/dotacionsolicitud/buscarAPI', [DotacionSolicitudController::class, 'buscarAPI']);
$router->post('/dotacionsolicitud/guardarAPI', [DotacionSolicitudController::class, 'guardarAPI']);
$router->post('/dotacionsolicitud/aprobarAPI', [DotacionSolicitudController::class, 'aprobarAPI']);
$router->post('/dotacionsolicitud/rechazarAPI', [DotacionSolicitudController::class, 'rechazarAPI']);
$router->get('/dotacionsolicitud/eliminar', [DotacionSolicitudController::class, 'eliminarAPI']);

// RUTAS DOTACIÓN ENTREGA
$router->get('/dotacionentrega', [DotacionEntregaController::class, 'renderizarPagina']);
$router->get('/dotacionentrega/buscarAPI', [DotacionEntregaController::class, 'buscarAPI']);
$router->post('/dotacionentrega/guardarAPI', [DotacionEntregaController::class, 'guardarAPI']);
$router->get('/dotacionentrega/verificarLimiteAPI', [DotacionEntregaController::class, 'verificarLimiteAPI']);
$router->get('/dotacionentrega/eliminar', [DotacionEntregaController::class, 'eliminarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();