<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is admin
requireAdmin();

// Get total counts for metrics
$totalIncidents = 0;

// Get total incidents count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM incidents");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalIncidents = $row['count'];
}
?>

<?php include '../includes/header.php'; ?>

<div id="reports-page">
    <div class="dashboard-header mb-4">
        <h1 class="dashboard-title">Reports & Analytics</h1>
        <p class="text-muted">View analytics and generate reports on disciplinary incidents.</p>
    </div>
    
    <!-- Metrics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="metric-value" id="total-incidents">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="metric-label">Total Incidents</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-chart-bar"></i>
                    <div class="metric-value" id="avg-incidents-month">
                        <?php 
                        // Calculate average incidents per month if we have incident data
                        if ($totalIncidents > 0) {
                            $stmt = $conn->prepare("
                                SELECT MIN(date_reported) as first_date,
                                MAX(date_reported) as last_date
                                FROM incidents
                            ");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $dates = $result->fetch_assoc();
                            
                            if ($dates['first_date'] && $dates['last_date']) {
                                $first = new DateTime($dates['first_date']);
                                $last = new DateTime($dates['last_date']);
                                $diff = $last->diff($first);
                                
                                // Calculate months (at least 1)
                                $months = max(1, ($diff->y * 12) + $diff->m + ($diff->d > 0 ? 1 : 0));
                                $average = round($totalIncidents / $months, 1);
                                echo $average;
                            } else {
                                echo "0";
                            }
                        } else {
                            echo "0";
                        }
                        ?>
                    </div>
                    <div class="metric-label">Avg Incidents/Month</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-users"></i>
                    <div class="metric-value" id="students-with-records">
                        <?php 
                        // Get count of unique students involved in incidents
                        $stmt = $conn->prepare("
                            SELECT COUNT(DISTINCT student_id) as count
                            FROM incident_students
                        ");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()) {
                            echo $row['count'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </div>
                    <div class="metric-label">Students with Records</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row">
        <!-- Incidents by Type Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Incidents by Type</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incidents-by-type-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Incident Trend Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Incident Trend (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incident-trend-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Reports -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detailed Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Incidents Summary Report</h6>
                                    <p class="card-text">Generate a summary report of all incidents with filters for date range and type.</p>
                                    <button class="btn btn-primary" disabled>
                                        <i class="fas fa-file-pdf me-1"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Student Disciplinary History</h6>
                                    <p class="card-text">Generate a report for a specific student showing their full disciplinary history.</p>
                                    <button class="btn btn-primary" disabled>
                                        <i class="fas fa-file-pdf me-1"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i> Report generation functionality will be available in a future update.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
