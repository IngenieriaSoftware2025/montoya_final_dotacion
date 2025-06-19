 <!-- Hero Section con Video -->
    <section id="inicio" class="hero-section">
        <!-- Video de fondo -->
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="video/techcell-bg.mp4" type="video/mp4">
            <source src="<?= asset('./images/logomovilcare.mp4') ?>" type="video/mp4">
            Tu navegador no soporta videos HTML5.
        </video>
        <div class="hero-overlay"></div>
        
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="hero-content">
                        <h1 class="hero-title">Industria Militar</h1>
                        <p class="hero-subtitle">Ministerio de la Defensa Nacional</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
                <!-- Venta -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-danger">Dotaciones</h4>
                        <p class="text-muted mb-3">tu dotacion de la forma mas facil</p>
                        <ul class="list-unstyled text-start text-primary">
                            <li><i class="fas fa-check text-success me-2 text-primary"></i>Botas</li>
                            <li><i class="fas fa-check text-success me-2 text-primary"></i>Camisas</li>
                            <li><i class="fas fa-check text-success me-2 text-primary"></i>PLayeras</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Call to Action -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <h3 class="mb-3">¿Necesitas ayuda con tu dotacion?</h3>
                    <p class="lead mb-4">Contáctanos 37062621</p>
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                        <h1>+502 3706-2621</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Smooth scrolling
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

        // Video autoplay fallback
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('.hero-video');
            if (video) {
                video.play().catch(function(error) {
                    console.log('Video autoplay failed:', error);
                });
            }
        });
    </script>