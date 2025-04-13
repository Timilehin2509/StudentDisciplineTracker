<ul class="navbar-nav me-auto">
    <li class="nav-item <?php echo (getCurrentPage() == 'dashboard.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/admin/dashboard.php">
            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
        </a>
    </li>
    <li class="nav-item <?php echo (getCurrentPage() == 'students.php' || getCurrentPage() == 'student_edit.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/admin/students.php">
            <i class="fas fa-user-graduate me-1"></i>Students
        </a>
    </li>
    <li class="nav-item <?php echo (getCurrentPage() == 'staff.php' || getCurrentPage() == 'staff_edit.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/admin/staff.php">
            <i class="fas fa-user-tie me-1"></i>Staff
        </a>
    </li>
    <li class="nav-item <?php echo (getCurrentPage() == 'incidents.php' || getCurrentPage() == 'incident_view.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/admin/incidents.php">
            <i class="fas fa-exclamation-triangle me-1"></i>Incidents
        </a>
    </li>
    <li class="nav-item <?php echo (getCurrentPage() == 'reports.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="/admin/reports.php">
            <i class="fas fa-chart-bar me-1"></i>Reports
        </a>
    </li>
</ul>
