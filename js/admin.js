/**
 * Admin-specific JavaScript for the Student Disciplinary Record Management System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle student judgment forms
    function setupJudgmentForms() {
        const judgmentForms = document.querySelectorAll('.judgment-form');
        
        judgmentForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const studentId = form.getAttribute('data-student-id');
                const incidentId = form.getAttribute('data-incident-id');
                
                // Create request object
                const request = new XMLHttpRequest();
                request.open('POST', '/api/judgments.php');
                
                request.onload = function() {
                    if (this.status >= 200 && this.status < 400) {
                        // Success
                        const response = JSON.parse(this.response);
                        if (response.success) {
                            // Show success message
                            const alertElement = document.createElement('div');
                            alertElement.className = 'alert alert-success alert-dismissible fade show mt-3';
                            alertElement.setAttribute('role', 'alert');
                            alertElement.innerHTML = `
                                Judgment updated successfully.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            form.appendChild(alertElement);
                            
                            // Update the status badge if needed
                            const punishmentBadge = document.getElementById(`punishment-badge-${studentId}`);
                            if (punishmentBadge) {
                                const punishment = formData.get('punishment');
                                punishmentBadge.textContent = punishment;
                                
                                // Update the badge class
                                punishmentBadge.className = 'badge';
                                switch (punishment) {
                                    case 'No Punishment':
                                        punishmentBadge.classList.add('bg-success');
                                        break;
                                    case 'Suspension':
                                        punishmentBadge.classList.add('bg-warning');
                                        break;
                                    case 'Expulsion':
                                        punishmentBadge.classList.add('bg-danger');
                                        break;
                                    case 'Community Service':
                                        punishmentBadge.classList.add('bg-info');
                                        break;
                                    default:
                                        punishmentBadge.classList.add('bg-secondary');
                                }
                            }
                            
                            // Auto-dismiss the alert after 3 seconds
                            setTimeout(function() {
                                const alert = document.querySelector('.alert');
                                if (alert) {
                                    const bsAlert = new bootstrap.Alert(alert);
                                    bsAlert.close();
                                }
                            }, 3000);
                        } else {
                            // Show error message
                            const alertElement = document.createElement('div');
                            alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                            alertElement.setAttribute('role', 'alert');
                            alertElement.innerHTML = `
                                Error: ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            form.appendChild(alertElement);
                        }
                    } else {
                        // Error
                        console.error('Server error');
                    }
                };
                
                request.onerror = function() {
                    console.error('Connection error');
                };
                
                request.send(formData);
            });
        });
    }
    
    // Call the function to set up judgment forms
    setupJudgmentForms();
    
    // Handle incident status update
    function setupIncidentStatusUpdate() {
        const statusForm = document.getElementById('incident-status-form');
        
        if (statusForm) {
            statusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(statusForm);
                const incidentId = statusForm.getAttribute('data-incident-id');
                
                // Create request object
                const request = new XMLHttpRequest();
                request.open('POST', '/api/incidents.php');
                
                request.onload = function() {
                    if (this.status >= 200 && this.status < 400) {
                        // Success
                        const response = JSON.parse(this.response);
                        if (response.success) {
                            // Show success message
                            const alertElement = document.createElement('div');
                            alertElement.className = 'alert alert-success alert-dismissible fade show mt-3';
                            alertElement.setAttribute('role', 'alert');
                            alertElement.innerHTML = `
                                Incident status updated successfully.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            statusForm.appendChild(alertElement);
                            
                            // Update the status badge
                            const statusBadge = document.getElementById('status-badge');
                            if (statusBadge) {
                                const status = formData.get('status');
                                statusBadge.textContent = status;
                                
                                // Update the badge class
                                statusBadge.className = 'badge';
                                switch (status) {
                                    case 'Open':
                                        statusBadge.classList.add('bg-warning');
                                        break;
                                    case 'Investigate':
                                        statusBadge.classList.add('bg-info');
                                        break;
                                    case 'Closed':
                                        statusBadge.classList.add('bg-success');
                                        break;
                                    default:
                                        statusBadge.classList.add('bg-secondary');
                                }
                            }
                            
                            // Auto-dismiss the alert after 3 seconds
                            setTimeout(function() {
                                const alert = document.querySelector('.alert');
                                if (alert) {
                                    const bsAlert = new bootstrap.Alert(alert);
                                    bsAlert.close();
                                }
                            }, 3000);
                        } else {
                            // Show error message
                            const alertElement = document.createElement('div');
                            alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                            alertElement.setAttribute('role', 'alert');
                            alertElement.innerHTML = `
                                Error: ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            statusForm.appendChild(alertElement);
                        }
                    } else {
                        // Error
                        console.error('Server error');
                    }
                };
                
                request.onerror = function() {
                    console.error('Connection error');
                };
                
                request.send(formData);
            });
        }
    }
    
    // Call the function to set up incident status update
    setupIncidentStatusUpdate();
    
    // Function to handle student/staff record deletion
    function setupRecordDeletion() {
        const deleteButtons = document.querySelectorAll('.delete-record');
        
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const id = this.getAttribute('data-id');
                const type = this.getAttribute('data-type');
                
                if (confirm(`Are you sure you want to delete this ${type} record? This action cannot be undone.`)) {
                    // Create request object
                    const request = new XMLHttpRequest();
                    request.open('DELETE', `/api/${type}s.php?id=${id}`);
                    
                    request.onload = function() {
                        if (this.status >= 200 && this.status < 400) {
                            // Success
                            const response = JSON.parse(this.response);
                            if (response.success) {
                                // Remove the row from the table
                                const row = document.getElementById(`${type}-${id}`);
                                if (row) {
                                    row.remove();
                                }
                                
                                // Show success message
                                const tableContainer = document.querySelector(`.${type}-table-container`);
                                const alertElement = document.createElement('div');
                                alertElement.className = 'alert alert-success alert-dismissible fade show mt-3';
                                alertElement.setAttribute('role', 'alert');
                                alertElement.innerHTML = `
                                    ${type.charAt(0).toUpperCase() + type.slice(1)} record deleted successfully.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `;
                                tableContainer.prepend(alertElement);
                                
                                // Auto-dismiss the alert after 3 seconds
                                setTimeout(function() {
                                    const alert = document.querySelector('.alert');
                                    if (alert) {
                                        const bsAlert = new bootstrap.Alert(alert);
                                        bsAlert.close();
                                    }
                                }, 3000);
                            } else {
                                // Show error message
                                const tableContainer = document.querySelector(`.${type}-table-container`);
                                const alertElement = document.createElement('div');
                                alertElement.className = 'alert alert-danger alert-dismissible fade show mt-3';
                                alertElement.setAttribute('role', 'alert');
                                alertElement.innerHTML = `
                                    Error: ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `;
                                tableContainer.prepend(alertElement);
                            }
                        } else {
                            // Error
                            console.error('Server error');
                        }
                    };
                    
                    request.onerror = function() {
                        console.error('Connection error');
                    };
                    
                    request.send();
                }
            });
        });
    }
    
    // Call the function to set up record deletion
    setupRecordDeletion();
});
