<ul class="navbar-nav me-auto">
    <li class="nav-item <?php echo (getCurrentPage() == 'dashboard.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/StudentDisciplineTracker/staff/dashboard.php">
            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
        </a>
    </li>
    <li class="nav-item <?php echo (getCurrentPage() == 'report_incident.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/StudentDisciplineTracker/staff/report_incident.php">
            <i class="fas fa-plus-circle me-1"></i>Report Incident
        </a>
    </li>
</ul>
