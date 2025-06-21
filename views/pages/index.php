<!-- Hero Section con Video - App de Dotación Ejército de Guatemala -->
<section id="hero" class="position-relative overflow-hidden text-center">
    <!-- Video de fondo -->
    <video class="position-absolute w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
        <source src="video/ejercito-bg.mp4" type="video/mp4">
        Tu navegador no soporta videos HTML5.
    </video>

    <!-- Capa oscura -->
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-75"></div>

    <!-- Contenido central -->
    <div class="container position-relative py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold text-white mb-4">Industria Militar</h1>
                <p class="lead text-white-50 mb-5 fs-4">Ministerio de la Defensa Nacional <br> Sistema de Dotación</p>
                <a href="#dotaciones" class="btn btn-danger btn-lg px-5 py-3 rounded-pill shadow">Ver Dotaciones</a>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Dotaciones -->
<section id="dotaciones" class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 text-center">
            <div class="col">
                <h2 class="fw-bold display-5 text-dark">Dotaciones Disponibles</h2>
                <p class="text-muted fs-5">Gestiona tu dotación de manera rápida y sencilla</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-boot fa-4x text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-3">Botas</h5>
                        <p class="card-text text-muted fs-5">Calzado táctico de alta resistencia para personal operativo.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-tshirt fa-4x text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-3">Camisas</h5>
                        <p class="card-text text-muted fs-5">Uniformes reglamentarios en distintas tallas y modelos.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-tshirt fa-4x text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-3">Playeras</h5>
                        <p class="card-text text-muted fs-5">Playeras institucionales para entrenamiento y actividades varias.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <h3 class="fw-bold mb-3 display-6">¿Necesitas ayuda con tu dotación?</h3>
        <p class="lead mb-4 fs-4">Contáctanos al siguiente número</p>
        <h1 class="display-4 fw-bold text-danger">+502 3706-2621</h1>
    </div>
</section>

<!-- Smooth Scrolling JS (opcional) -->
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
