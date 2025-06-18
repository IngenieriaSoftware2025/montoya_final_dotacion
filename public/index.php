<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\AplicacionController;
use MVC\Router;
use Controllers\AppController;
use Controllers\DotacionInventarioController;
use Controllers\EmpleadoController;
use Controllers\LoginController;
use Controllers\PermisoController;
use Controllers\TallaController;
use Controllers\TipoDotacionController;
use Controllers\UsuarioController;


$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// ===== RUTAS PRINCIPALES =====
$router->get('/', [AppController::class, 'index']);

// ===== AUTENTICACIÓN =====
$router->get('/login', [LoginController::class, 'renderizarPagina']);
$router->post('/login/iniciar', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// ===== CATÁLOGOS BASE =====

// Tipos de Dotación
$router->get('/tipodotacion', [TipoDotacionController::class, 'renderizarPagina']);
$router->post('/tipodotacion/guardarAPI', [TipoDotacionController::class, 'guardarAPI']);
$router->get('/tipodotacion/buscarAPI', [TipoDotacionController::class, 'buscarAPI']);
$router->post('/tipodotacion/modificarAPI', [TipoDotacionController::class, 'modificarAPI']);
$router->get('/tipodotacion/eliminar', [TipoDotacionController::class, 'eliminarAPI']);

// Tallas
$router->get('/talla', [TallaController::class, 'renderizarPagina']);
$router->get('/talla/buscarAPI', [TallaController::class, 'buscarAPI']);
$router->get('/talla/buscarPorTipoAPI', [TallaController::class, 'buscarPorTipoAPI']);
$router->post('/talla/guardarAPI', [TallaController::class, 'guardarAPI']);
$router->post('/talla/modificarAPI', [TallaController::class, 'modificarAPI']);
$router->get('/talla/eliminar', [TallaController::class, 'eliminarAPI']);



// Empleados
$router->get('/empleado', [EmpleadoController::class, 'renderizarPagina']);
$router->get('/empleado/buscarAPI', [EmpleadoController::class, 'buscarAPI']);
$router->post('/empleado/guardarAPI', [EmpleadoController::class, 'guardarAPI']);
$router->post('/empleado/modificarAPI', [EmpleadoController::class, 'modificarAPI']);
$router->get('/empleado/eliminar', [EmpleadoController::class, 'eliminarAPI']);


// Usuarios
$router->get('/usuario', [UsuarioController::class, 'renderizarPagina']);
$router->post('/usuario/guardarAPI', [UsuarioController::class, 'guardarAPI']);
$router->get('/usuario/buscarAPI', [UsuarioController::class, 'buscarAPI']);
$router->post('/usuario/modificarAPI', [UsuarioController::class, 'modificarAPI']);
$router->get('/usuario/eliminar', [UsuarioController::class, 'eliminarAPI']);

// ===== INVENTARIO =====

// Inventario de Dotaciones
$router->get('/dotacioninventario', [DotacionInventarioController::class, 'renderizarPagina']);
$router->get('/dotacioninventario/buscarAPI', [DotacionInventarioController::class, 'buscarAPI']);
$router->post('/dotacioninventario/guardarAPI', [DotacionInventarioController::class, 'guardarAPI']);
$router->post('/dotacioninventario/modificarAPI', [DotacionInventarioController::class, 'modificarAPI']);
$router->get('/dotacioninventario/eliminar', [DotacionInventarioController::class, 'eliminarAPI']);
$router->get('/dotacioninventario/buscarDisponibleAPI', [DotacionInventarioController::class, 'buscarDisponibleAPI']);


// ===== APLICACIONES (PARA PERMISOS) =====
$router->get('/aplicacion', [AplicacionController::class, 'renderizarPagina']);
$router->post('/aplicacion/guardarAPI', [AplicacionController::class, 'guardarAPI']);
$router->get('/aplicacion/buscarAPI', [AplicacionController::class, 'buscarAPI']);
$router->post('/aplicacion/modificarAPI', [AplicacionController::class, 'modificarAPI']);
$router->get('/aplicacion/eliminar', [AplicacionController::class, 'eliminarAPI']); 

$router->get('/permiso', [PermisoController::class, 'renderizarPagina']);
$router->post('/permiso/guardarAPI', [permisoController::class, 'guardarAPI']);
$router->get('/permiso/buscarAPI', [permisoController::class, 'buscarAPI']);
$router->post('/permiso/eliminarAPI', [permisoController::class, 'eliminarAPI']);
$router->post('/permiso/cambiarEstadoAPI', [permisoController::class, 'cambiarEstadoAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();