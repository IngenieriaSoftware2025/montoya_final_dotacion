<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Dotaciones - Ministerio de la Defensa</title>
<style>
/* Variables */
:root {
    --primary-color: #dc3545; /* rojo militar */
    --primary-dark: #a71d2a;
    --secondary-color: #343a40; /* gris militar */
    --text-color: #ffffff;
    --navbar-bg: linear-gradient(135deg, #1a1a2e, #16213e);
    --hover-color: #ffc107;
}

/* Body */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-image: url("<?= asset('images/fondo_pantalla.jpg') ?>");
    background-repeat: no-repeat;
    background-size: cover;
    background-attachment: fixed;
}


/* Navbar */
.navbar-custom {
    background: var(--navbar-bg);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    position: sticky;
    top: 0;
    z-index: 999;
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.3rem;
    color: var(--text-color) !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.navbar-brand img {
    border-radius: 50%;
    width: 40px;
    height: 40px;
}

.navbar-nav .nav-link {
    color: var(--text-color) !important;
    font-weight: 500;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: var(--hover-color) !important;
    background: rgba(255, 193, 7, 0.05);
}

.navbar-nav .nav-link.active {
    color: var(--primary-color) !important;
}

/* Dropdown */
.dropdown-menu {
    background: rgba(26, 26, 46, 0.98);
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
}

.dropdown-item {
    color: var(--text-color);
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: rgba(255, 193, 7, 0.1);
    color: var(--hover-color);
}

/* Botón menú */
.btn-menu {
    background: var(--primary-color);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    padding: 0.5rem 1.2rem;
    transition: all 0.3s ease;
}

.btn-menu:hover {
    background: var(--primary-dark);
    color: #fff;
}

/* Hero Section */
#hero {
    position: relative;
    height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
}

#hero video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -2;
}

#hero::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(0, 212, 255, 0.15));
    z-index: -1;
}

#hero .display-3 {
    font-size: 3.5rem;
    font-weight: bold;
}

#hero p.lead {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
}

#hero a.btn-danger {
    font-size: 1.2rem;
    padding: 0.75rem 2rem;
    border-radius: 50px;
}

/* Cards de dotación */
.card {
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
}

.card .card-title {
    font-weight: bold;
    font-size: 1.3rem;
}

.card .card-text {
    font-size: 1rem;
    color: #6c757d;
}

.card i {
    font-size: 3rem;
    color: var(--primary-color);
}

/* Footer */
footer {
    font-size: 0.85rem;
    color: #aaa;
}

/* Barra de progreso */
.progress-bar {
    background-color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    #hero .display-3 {
        font-size: 2.5rem;
    }
    #hero p.lead {
        font-size: 1rem;
    }
}
</style>
</head>
<body>
   <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <span>Dotaciones</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Catálogos Base -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-wide-connected"></i> Catálogos Base
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/TipoDotacion"><i class="bi bi-tags"></i> Tipos de Dotación</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/Talla"><i class="bi bi-rulers"></i> Tallas</a></li>
                        </ul>
                    </li>

                    <!-- Inventario & Personal -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-boxes"></i> Inventario & Personal
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/DotacionInventario"><i class="bi bi-box-seam"></i> Inventario de Dotaciones</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/empleado"><i class="bi bi-person-badge"></i> Empleados</a></li>
                        </ul>
                    </li>

                    <!-- Gestión de Dotaciones -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-clipboard-check"></i> Gestión de Dotaciones
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/dotacionsolicitud"><i class="bi bi-clipboard-plus"></i> Solicitudes</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/dotacionentrega"><i class="bi bi-truck"></i> Entregas</a></li>
                        </ul>
                    </li>

                    <!-- Reportes -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-graph-up"></i> Reportes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/dotacionreporte"><i class="bi bi-file-earmark-bar-graph"></i> Reporte Anual</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/dotacionreporte/inventario"><i class="bi bi-boxes"></i> Estado de Inventario</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/dotacionreporte/Talla"><i class="bi bi-pie-chart"></i> Entregas por Talla</a></li>
                        </ul>
                    </li>

                    <!-- Admin -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-people"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/usuario"><i class="bi bi-person-gear"></i> Usuarios</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/aplicacion"><i class="bi bi-shield-check"></i> Aplicación</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/permiso"><i class="bi bi-shield-check"></i> Permiso</a></li>
                            <li><a class="dropdown-item" href="/montoya_final_dotacion_ingsoft/asignacion_permiso"><i class="bi bi-shield-check"></i> Asignar Permiso</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Botón cerrar sesión -->
                <a href="/montoya_final_dotacion_ingsoft/logout" class="btn btn-menu">
                    <i class="bi bi-box-arrow-right me-2"></i> CERRAR SESIÓN
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Barra del Progreso -->
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">
        
        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid " >
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                        Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>
</html>