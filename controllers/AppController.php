<?php

namespace Controllers;

use MVC\Router;

class AppController 
{
    public static function index(Router $router)
    {   // Mostrar la página de inicio
        $router->render('pages/index', []);
    }
}