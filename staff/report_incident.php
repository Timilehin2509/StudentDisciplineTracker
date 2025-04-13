<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is staff
requireStaff();

// Get all students for selection
$students = [];
$stmt = $conn->prepare("SELECT id, student_number, name FROM students ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Handle form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = sanitize($_POST['type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $date_of_incidence = sanitize($_POST['date_of_incidence'] ?? '');
    $student_ids = $_POST['students'] ?? [];
    $supporting_documents = '';
    
    // Validate inputs
    if (empty($type) || empty($description) || empty($date_of_incidence) || empty($student_ids)) {
        $error = 'Please fill in all required fields and select at least one student.';
    } else {
        // Handle file uploads
        $uploadedFiles = [];
        
        if (!empty($_FILES['supporting_documents']['name'][0])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/StudentDisciplineTracker/uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Process each uploaded file
            foreach ($_FILES['supporting_documents']['name'] as $key => $name) {
                if ($_FILES['supporting_documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['supporting_documents']['tmp_name'][$key];
                    $fileName = basename($name);
                    $newFileName = time() . '_' . $fileName;
                    $targetFilePath = $uploadDir . $newFileName;
                    
                    // Move the uploaded file
                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        $uploadedFiles[] = $targetFilePath;
                    }
                }
            }
        }
        
        // Convert uploaded files to comma-separated string
        if (!empty($uploadedFiles)) {
            $supporting_documents = implode(',', $uploadedFiles);
        }
        
        // Insert incident
        $stmt = $conn->prepare("
            INSERT INTO incidents 
            (type, description, date_of_incidence, date_reported, status, supporting_documents, reporter_id)
            VALUES (?, ?, ?, CURDATE(), 'Open', ?, ?)
        ");
        $stmt->bind_param("ssssi", $type, $description, $date_of_incidence, $supporting_documents, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $incident_id = $conn->insert_id;
            
            // Insert student-incident relationships
            $stmt = $conn->prepare("
                INSERT INTO incident_students (incident_id, student_id)
                VALUES (?, ?)
            ");
            
            foreach ($student_ids as $student_id) {
                $stmt->bind_param("ii", $incident_id, $student_id);
                $stmt->execute();
            }
            
            $success = 'Incident reported successfully.';
        } else {
            $error = 'Error reporting incident: ' . $conn->error;
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-header mb-4">
    <h1 class="dashboard-title">Report New Incident</h1>
    <p class="text-muted">Submit a new disciplinary incident report with student information</p>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <h4 class="alert-heading">Incident Reported Successfully!</h4>
        <p><?php echo $success; ?></p>
        <hr>
        <p class="mb-0">
            <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            <a href="report_incident.php" class="btn btn-secondary">Report Another Incident</a>
        </p>
    </div>
<?php else: ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form id="incident-report-form" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">Incident Type *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select incident type...</option>
                            <option value="Academic Dishonesty">Academic Dishonesty</option>
                            <option value="Attendance Issues">Attendance Issues</option>
                            <option value="Behavioral Problems">Behavioral Problems</option>
                            <option value="Dress Code Violation">Dress Code Violation</option>
                            <option value="Property Damage">Property Damage</option>
                            <option value="Physical Altercation">Physical Altercation</option>
                            <option value="Verbal Misconduct">Verbal Misconduct</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select an incident type.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="date_of_incidence" class="form-label">Date of Incident *</label>
                        <input type="date" class="form-control" id="date_of_incidence" name="date_of_incidence" required max="<?php echo date('Y-m-d'); ?>">
                        <div class="invalid-feedback">Please select a valid date (not in the future).</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    <div class="invalid-feedback">Please provide a description of the incident.</div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Students Involved *</label>
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="student-search" class="form-label">Search Students</label>
                                <input type="text" class="form-control" id="student-search" placeholder="Type student name or number...">
                            </div>
                            
                            <div id="student-list-container">
                                <ul id="student-list" class="list-group">
                                    <li class="list-group-item">Type at least 2 characters to search</li>
                                </ul>
                            </div>
                            
                            <div class="mt-3">
                                <label class="form-label">Selected Students</label>
                                <div id="selected-students" class="d-flex flex-wrap">
                                    <!-- Selected students will appear here -->
                                </div>
                                <div id="student-error" class="text-danger" style="display: none;">
                                    Please select at least one student.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="supporting_documents" class="form-label">Supporting Documents (Optional)</label>
                    <input class="form-control" type="file" id="supporting_documents" name="supporting_documents[]" multiple>
                    <div class="form-text">Upload any relevant files that support this incident report (max 5MB per file).</div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
