    </main>
    <footer class="text-light py-5 mt-5" style="background-color: var(--babcock-blue);">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <div class="footer-brand mb-4">
                        <img src="/img/babcock-logo.png" alt="Babcock University Logo" height="60" class="mb-3" onerror="this.onerror=null; this.src=''; this.style.display='none';">
                        <h5>Babcock University</h5>
                    </div>
                    <p class="mb-3">Student Disciplinary Record Management System</p>
                    <p class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Ilishan-Remo, Ogun State, Nigeria</p>
                    <p class="mb-0"><i class="fas fa-globe me-2"></i><a href="https://www.babcock.edu.ng" class="text-light">www.babcock.edu.ng</a></p>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-light"><i class="fas fa-home me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="/login.php" class="text-light"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
                        <?php if (isLoggedIn()): ?>
                            <?php if (isAdmin()): ?>
                                <li class="mb-2"><a href="/admin/dashboard.php" class="text-light"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                            <?php elseif (isStaff()): ?>
                                <li class="mb-2"><a href="/staff/dashboard.php" class="text-light"><i class="fas fa-tachometer-alt me-2"></i>Staff Dashboard</a></li>
                            <?php elseif (isStudent()): ?>
                                <li class="mb-2"><a href="/student/dashboard.php" class="text-light"><i class="fas fa-tachometer-alt me-2"></i>Student Dashboard</a></li>
                            <?php endif; ?>
                            <li class="mb-2"><a href="/logout.php" class="text-light"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-12">
                    <h5 class="mb-4">Contact Us</h5>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i>+234 (0) 8000000000</p>
                    <p class="mb-4"><i class="fas fa-envelope me-2"></i>info@babcock.edu.ng</p>
                    
                    <h5 class="mb-3">Follow Us</h5>
                    <div class="social-icons">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in fa-lg"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12">
                    <hr class="footer-divider">
                    <div class="d-md-flex justify-content-between align-items-center text-center text-md-start">
                        <p class="mb-md-0">&copy; <?php echo date('Y'); ?> Babcock University. All rights reserved.</p>
                        <div>
                            <a href="#" class="text-light me-3 small">Privacy Policy</a>
                            <a href="#" class="text-light me-3 small">Terms of Service</a>
                            <a href="#" class="text-light small">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Main JS -->
    <script src="/js/main.js"></script>
    
    <?php if (isAdmin()): ?>
    <!-- Admin JS -->
    <script src="/js/admin.js"></script>
    <!-- Charts JS -->
    <script src="/js/charts.js"></script>
    <?php elseif (isStaff()): ?>
    <!-- Staff JS -->
    <script src="/js/staff.js"></script>
    <?php elseif (isStudent()): ?>
    <!-- Student JS -->
    <script src="/js/student.js"></script>
    <?php endif; ?>
</body>
</html>
