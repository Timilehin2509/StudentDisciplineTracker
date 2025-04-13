<?php
require_once 'includes/functions.php';

// Redirect to appropriate dashboard if already logged in
if (isLoggedIn()) {
    redirectToDashboard();
}
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="hero-content">
                    <img src="img/babcock-logo.png" alt="Babcock University Logo" class="hero-logo mb-3" onerror="this.onerror=null; this.src=''; this.style.display='none';">
                    <h1 class="hero-title">Babcock University</h1>
                    <h2 class="hero-subtitle">Student Disciplinary Record Management System</h2>
                    <p class="lead mb-4">A comprehensive system to manage student disciplinary records with role-based access for administrators, staff, and students.</p>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn btn-gold btn-lg me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to System
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="section-title">Key Features</h2>
                <p class="section-subtitle">Our system offers advanced functionality for effective disciplinary management</p>
                <div class="section-divider"><span><i class="fas fa-school"></i></span></div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="fas fa-user-shield"></i>
                    <h3>Admin Dashboard</h3>
                    <p>Complete management of student and staff records with comprehensive tracking of disciplinary cases.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Incident Reporting</h3>
                    <p>Easy-to-use interface for staff to report student incidents with detailed information and supporting documents.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="fas fa-file-alt"></i>
                    <h3>Student Records</h3>
                    <p>Students can view their disciplinary records with transparent status tracking for all incidents.</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Analytics & Reports</h3>
                    <p>Comprehensive analytics and reporting tools for administrators to track trends and patterns.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="fas fa-user-lock"></i>
                    <h3>Secure Access</h3>
                    <p>Role-based access controls ensuring that users can only access information relevant to their role.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <i class="fas fa-laptop"></i>
                    <h3>Responsive Design</h3>
                    <p>Access the system from any device with a fully responsive design that works on desktop and mobile.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="section-title">About Babcock University</h2>
                <div class="section-divider"><span><i class="fas fa-university"></i></span></div>
            </div>
        </div>
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="about-content">
                    <p class="lead">Excellence. Virtue. Service.</p>
                    <p>Babcock University is a private Christian co-educational Nigerian university owned and operated by the Seventh-day Adventist Church in Nigeria.</p>
                    <p>The university is located equidistant between Ibadan and Lagos at Ilishan-Remo in Ogun State, Nigeria.</p>
                    <p>The Disciplinary Record Management System supports the university's commitment to maintaining high standards of conduct and educational excellence.</p>
                    <div class="mt-4">
                        <a href="#" class="btn btn-outline-primary">Learn More About Babcock</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="about-image text-center">
                    <div class="about-image-wrapper">
                        <i class="fas fa-university" style="font-size: 150px; color: var(--babcock-blue);"></i>
                    </div>
                    <div class="about-stats mt-4">
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-item">
                                    <h3>1959</h3>
                                    <p>Founded</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h3>10,000+</h3>
                                    <p>Students</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h3>50+</h3>
                                    <p>Programs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="section-title">What Users Say</h2>
                <div class="section-divider"><span><i class="fas fa-quote-right"></i></span></div>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <p>"The disciplinary system has greatly improved our record keeping and case management efficiency. We can now track incidents, follow up on cases, and ensure fair treatment for all students."</p>
                                </div>
                                <div class="testimonial-user">
                                    <i class="fas fa-user-circle testimonial-avatar"></i>
                                    <div class="testimonial-info">
                                        <h5>Dr. James Wilson</h5>
                                        <span>Dean of Student Affairs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <p>"As a staff member, I appreciate how easy it is to submit incident reports and track their progress. The system has streamlined our workflow and made communication much more effective."</p>
                                </div>
                                <div class="testimonial-user">
                                    <i class="fas fa-user-circle testimonial-avatar"></i>
                                    <div class="testimonial-info">
                                        <h5>Mrs. Elizabeth Taylor</h5>
                                        <span>Hall Administrator</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <p>"The transparency of the system helps me stay informed about my status and any disciplinary actions. It's reassuring to have clear information about the process and expectations."</p>
                                </div>
                                <div class="testimonial-user">
                                    <i class="fas fa-user-circle testimonial-avatar"></i>
                                    <div class="testimonial-info">
                                        <h5>Daniel Okafor</h5>
                                        <span>Computer Science Student</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5" style="background-color: var(--babcock-blue); color: var(--babcock-white);">
    <div class="container text-center">
        <h2 class="mb-4">Ready to access the system?</h2>
        <p class="lead mb-4">Login with your credentials to access your personalized dashboard.</p>
        <div class="cta-buttons">
            <a href="login.php" class="btn btn-gold btn-lg me-2">
                <i class="fas fa-sign-in-alt me-2"></i>Log In Now
            </a>
            <a href="#features" class="btn btn-outline-light btn-lg">
                <i class="fas fa-info-circle me-2"></i>Learn More
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
