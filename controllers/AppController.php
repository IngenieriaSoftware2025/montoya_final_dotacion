<?php

namespace Controllers;

use MVC\Router;

class AppController 
{
    public static function index(Router $router)
    {
        // Verificar si el usuario está autenticado
        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            // Si está autenticado, mostrar la página de inicio
            $router->render('pages/index', []);
        } else {
            // Si no está autenticado, redirigir al login
            header('Location: /montoya_final_dotacion_ingsoft/login');
            exit;
        }
    }

    public static function inicio(Router $router)
    {
        // Verificar autenticación
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /montoya_final_dotacion_ingsoft/login');
            exit;
        }

        // Mostrar la página de inicio
        $router->render('pages/index', []);
    }
}