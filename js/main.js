/**
 * Main JavaScript file for the Student Disciplinary Record Management System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Form validation for all forms with the class 'needs-validation'
    var forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Function to handle confirmation dialogs
    function setupConfirmationDialogs() {
        document.querySelectorAll('.confirm-action').forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm(this.getAttribute('data-confirm-message') || 'Are you sure you want to proceed?')) {
                    e.preventDefault();
                }
            });
        });
    }
    
    // Call the function to set up confirmation dialogs
    setupConfirmationDialogs();
    
    // Function to handle dynamic form fields
    function setupDynamicFormFields() {
        // Add student button for incident reporting
        const addStudentBtn = document.getElementById('add-student-btn');
        const studentsContainer = document.getElementById('students-container');
        const studentTemplate = document.getElementById('student-template');
        
        if (addStudentBtn && studentsContainer && studentTemplate) {
            addStudentBtn.addEventListener('click', function() {
                const newIndex = document.querySelectorAll('.student-entry').length;
                const clone = studentTemplate.content.cloneNode(true);
                
                // Update the name and id attributes to use the new index
                clone.querySelectorAll('[name]').forEach(element => {
                    element.name = element.name.replace('[0]', '[' + newIndex + ']');
                    if (element.id) {
                        element.id = element.id.replace('0', newIndex);
                    }
                });
                
                // Add the remove button event listener
                const removeButton = clone.querySelector('.remove-student-btn');
                if (removeButton) {
                    removeButton.addEventListener('click', function() {
                        this.closest('.student-entry').remove();
                    });
                }
                
                studentsContainer.appendChild(clone);
            });
        }
    }
    
    // Call the function to set up dynamic form fields
    setupDynamicFormFields();
    
    // Function to handle search functionality
    function setupSearch() {
        const searchInput = document.getElementById('search-input');
        const searchableItems = document.querySelectorAll('.searchable-item');
        
        if (searchInput && searchableItems.length > 0) {
            searchInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                
                searchableItems.forEach(function(item) {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchValue)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }
    
    // Call the function to set up search functionality
    setupSearch();
    
    // Function to handle sorting of tables
    function setupTableSorting() {
        document.querySelectorAll('.sortable').forEach(function(table) {
            table.querySelectorAll('th').forEach(function(header, index) {
                if (header.classList.contains('sortable-header')) {
                    header.addEventListener('click', function() {
                        const tbody = table.querySelector('tbody');
                        const rows = Array.from(tbody.querySelectorAll('tr'));
                        const direction = this.classList.contains('ascending') ? -1 : 1;
                        
                        // Toggle sorting direction
                        if (this.classList.contains('ascending')) {
                            this.classList.remove('ascending');
                            this.classList.add('descending');
                        } else {
                            this.classList.remove('descending');
                            this.classList.add('ascending');
                        }
                        
                        // Clear other headers' sort classes
                        table.querySelectorAll('th.sortable-header').forEach(function(otherHeader) {
                            if (otherHeader !== header) {
                                otherHeader.classList.remove('ascending', 'descending');
                            }
                        });
                        
                        // Sort the rows
                        rows.sort(function(a, b) {
                            const cellA = a.querySelectorAll('td')[index].textContent.trim();
                            const cellB = b.querySelectorAll('td')[index].textContent.trim();
                            
                            // Try to convert to numbers if possible
                            const numA = parseFloat(cellA);
                            const numB = parseFloat(cellB);
                            
                            if (!isNaN(numA) && !isNaN(numB)) {
                                return direction * (numA - numB);
                            } else {
                                return direction * cellA.localeCompare(cellB);
                            }
                        });
                        
                        // Remove existing rows
                        rows.forEach(function(row) {
                            tbody.removeChild(row);
                        });
                        
                        // Add sorted rows
                        rows.forEach(function(row) {
                            tbody.appendChild(row);
                        });
                    });
                }
            });
        });
    }
    
    // Call the function to set up table sorting
    setupTableSorting();
});
