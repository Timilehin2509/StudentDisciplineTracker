/**
 * Student-specific JavaScript for the Student Disciplinary Record Management System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Function to filter records by status
    function setupRecordFilter() {
        const statusFilter = document.getElementById('status-filter');
        const recordRows = document.querySelectorAll('.record-row');
        
        if (statusFilter && recordRows.length > 0) {
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                
                recordRows.forEach(function(row) {
                    const rowStatus = row.getAttribute('data-status');
                    
                    if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update count of displayed records
                updateRecordCount();
            });
        }
    }
    
    // Function to update record count
    function updateRecordCount() {
        const recordsTable = document.getElementById('records-table');
        const recordCount = document.getElementById('record-count');
        
        if (recordsTable && recordCount) {
            const visibleRows = recordsTable.querySelectorAll('tbody tr[style=""]').length;
            recordCount.textContent = visibleRows;
        }
    }
    
    // Call the function to set up record filter
    setupRecordFilter();
    
    // Initialize the record count on page load
    updateRecordCount();
});
