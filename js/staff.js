/**
 * Staff-specific JavaScript for the Student Disciplinary Record Management System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle the incident report form
    function setupIncidentReportForm() {
        const reportForm = document.getElementById('incident-report-form');
        
        if (reportForm) {
            // Setup student selection with search functionality
            const studentSearchInput = document.getElementById('student-search');
            const studentList = document.getElementById('student-list');
            const selectedStudentsContainer = document.getElementById('selected-students');
            
            if (studentSearchInput && studentList && selectedStudentsContainer) {
                // Track selected students to avoid duplicates
                const selectedStudents = new Set();
                
                studentSearchInput.addEventListener('keyup', function() {
                    const searchValue = this.value.toLowerCase();
                    
                    if (searchValue.length < 2) {
                        studentList.innerHTML = '<li class="list-group-item">Type at least 2 characters to search</li>';
                        return;
                    }
                    
                    // Create request object for student search
                    const request = new XMLHttpRequest();
                    request.open('GET', `/api/students.php?search=${searchValue}`);
                    
                    request.onload = function() {
                        if (this.status >= 200 && this.status < 400) {
                            // Success
                            const response = JSON.parse(this.response);
                            
                            if (response.students.length > 0) {
                                studentList.innerHTML = '';
                                
                                response.students.forEach(function(student) {
                                    // Skip already selected students
                                    if (selectedStudents.has(student.id)) {
                                        return;
                                    }
                                    
                                    const listItem = document.createElement('li');
                                    listItem.className = 'list-group-item student-item';
                                    listItem.setAttribute('data-id', student.id);
                                    listItem.setAttribute('data-name', student.name);
                                    listItem.textContent = `${student.name} (${student.student_number})`;
                                    
                                    listItem.addEventListener('click', function() {
                                        const studentId = this.getAttribute('data-id');
                                        const studentName = this.getAttribute('data-name');
                                        
                                        // Add to selected students
                                        selectedStudents.add(studentId);
                                        
                                        // Create selected student badge
                                        const badge = document.createElement('div');
                                        badge.className = 'badge bg-primary selected-student-badge me-2 mb-2';
                                        badge.setAttribute('data-id', studentId);
                                        badge.innerHTML = `
                                            ${studentName}
                                            <input type="hidden" name="students[]" value="${studentId}">
                                            <button type="button" class="btn-close btn-close-white ms-2" aria-label="Remove"></button>
                                        `;
                                        
                                        // Add remove functionality
                                        const removeButton = badge.querySelector('.btn-close');
                                        removeButton.addEventListener('click', function() {
                                            badge.remove();
                                            selectedStudents.delete(studentId);
                                        });
                                        
                                        selectedStudentsContainer.appendChild(badge);
                                        
                                        // Clear search
                                        studentSearchInput.value = '';
                                        studentList.innerHTML = '<li class="list-group-item">Type at least 2 characters to search</li>';
                                    });
                                    
                                    studentList.appendChild(listItem);
                                });
                            } else {
                                studentList.innerHTML = '<li class="list-group-item">No students found</li>';
                            }
                        } else {
                            // Error
                            studentList.innerHTML = '<li class="list-group-item text-danger">Error loading students</li>';
                        }
                    };
                    
                    request.onerror = function() {
                        studentList.innerHTML = '<li class="list-group-item text-danger">Connection error</li>';
                    };
                    
                    request.send();
                });
            }
            
            // Handle form submission
            reportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate that at least one student is selected
                const selectedStudents = document.querySelectorAll('input[name="students[]"]');
                if (selectedStudents.length === 0) {
                    const alertElement = document.createElement('div');
                    alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alertElement.setAttribute('role', 'alert');
                    alertElement.innerHTML = `
                        Please select at least one student for this incident.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    reportForm.prepend(alertElement);
                    return;
                }
                
                // Create request object
                const formData = new FormData(reportForm);
                const request = new XMLHttpRequest();
                request.open('POST', '/api/incidents.php');
                
                request.onload = function() {
                    if (this.status >= 200 && this.status < 400) {
                        // Success
                        const response = JSON.parse(this.response);
                        if (response.success) {
                            // Show success message
                            reportForm.innerHTML = `
                                <div class="alert alert-success">
                                    <h4 class="alert-heading">Incident Reported Successfully!</h4>
                                    <p>Your incident report has been submitted and is now awaiting review by an administrator.</p>
                                    <hr>
                                    <p class="mb-0">
                                        <a href="/staff/dashboard.php" class="btn btn-primary">Return to Dashboard</a>
                                        <a href="/staff/report_incident.php" class="btn btn-secondary">Report Another Incident</a>
                                    </p>
                                </div>
                            `;
                        } else {
                            // Show error message
                            const alertElement = document.createElement('div');
                            alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                            alertElement.setAttribute('role', 'alert');
                            alertElement.innerHTML = `
                                Error: ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            reportForm.prepend(alertElement);
                        }
                    } else {
                        // Error
                        const alertElement = document.createElement('div');
                        alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                        alertElement.setAttribute('role', 'alert');
                        alertElement.innerHTML = `
                            A server error occurred. Please try again later.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        reportForm.prepend(alertElement);
                    }
                };
                
                request.onerror = function() {
                    const alertElement = document.createElement('div');
                    alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alertElement.setAttribute('role', 'alert');
                    alertElement.innerHTML = `
                        A connection error occurred. Please check your internet connection and try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    reportForm.prepend(alertElement);
                };
                
                request.send(formData);
            });
        }
    }
    
    // Call the function to set up incident report form
    setupIncidentReportForm();
    
    // Function to filter incidents by status
    function setupIncidentFilter() {
        const statusFilter = document.getElementById('status-filter');
        const incidentRows = document.querySelectorAll('.incident-row');
        
        if (statusFilter && incidentRows.length > 0) {
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                
                incidentRows.forEach(function(row) {
                    const rowStatus = row.getAttribute('data-status');
                    
                    if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    }
    
    // Call the function to set up incident filter
    setupIncidentFilter();
});
