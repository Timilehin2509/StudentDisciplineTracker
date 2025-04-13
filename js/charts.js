/**
 * Charts JavaScript for the Student Disciplinary Record Management System
 * Uses Chart.js for creating analytics visualizations
 */

document.addEventListener('DOMContentLoaded', function() {
    // Load total incidents analytics
    function loadTotalIncidents() {
        const totalIncidentsElement = document.getElementById('total-incidents');
        
        if (totalIncidentsElement) {
            // Create request object
            const request = new XMLHttpRequest();
            request.open('GET', '/api/analytics.php?metric=total');
            
            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    // Success
                    const response = JSON.parse(this.response);
                    totalIncidentsElement.textContent = response.total || 0;
                } else {
                    // Error
                    totalIncidentsElement.textContent = 'Error';
                    console.error('Server error loading total incidents');
                }
            };
            
            request.onerror = function() {
                totalIncidentsElement.textContent = 'Error';
                console.error('Connection error loading total incidents');
            };
            
            request.send();
        }
    }
    
    // Load incidents by type chart
    function loadIncidentsByType() {
        const chartCanvas = document.getElementById('incidents-by-type-chart');
        
        if (chartCanvas) {
            // Create request object
            const request = new XMLHttpRequest();
            request.open('GET', '/api/analytics.php?metric=by-type');
            
            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    // Success
                    const response = JSON.parse(this.response);
                    
                    // Create the chart
                    const ctx = chartCanvas.getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels || [],
                            datasets: [{
                                label: 'Number of Incidents',
                                data: response.data || [],
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(199, 199, 199, 0.7)',
                                    'rgba(83, 102, 255, 0.7)'
                                ],
                                borderColor: [
                                    'rgb(255, 99, 132)',
                                    'rgb(54, 162, 235)',
                                    'rgb(255, 206, 86)',
                                    'rgb(75, 192, 192)',
                                    'rgb(153, 102, 255)',
                                    'rgb(255, 159, 64)',
                                    'rgb(199, 199, 199)',
                                    'rgb(83, 102, 255)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Error
                    console.error('Server error loading incidents by type');
                    chartCanvas.parentNode.innerHTML = '<div class="alert alert-danger">Error loading chart data</div>';
                }
            };
            
            request.onerror = function() {
                console.error('Connection error loading incidents by type');
                chartCanvas.parentNode.innerHTML = '<div class="alert alert-danger">Connection error loading chart data</div>';
            };
            
            request.send();
        }
    }
    
    // Load incident trend chart
    function loadIncidentTrend() {
        const chartCanvas = document.getElementById('incident-trend-chart');
        
        if (chartCanvas) {
            // Create request object
            const request = new XMLHttpRequest();
            request.open('GET', '/api/analytics.php?metric=trend');
            
            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    // Success
                    const response = JSON.parse(this.response);
                    
                    // Create the chart
                    const ctx = chartCanvas.getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels || [],
                            datasets: [{
                                label: 'Incidents',
                                data: response.data || [],
                                backgroundColor: 'rgba(0, 51, 102, 0.2)',
                                borderColor: 'rgba(0, 51, 102, 1)',
                                borderWidth: 2,
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Error
                    console.error('Server error loading incident trend');
                    chartCanvas.parentNode.innerHTML = '<div class="alert alert-danger">Error loading chart data</div>';
                }
            };
            
            request.onerror = function() {
                console.error('Connection error loading incident trend');
                chartCanvas.parentNode.innerHTML = '<div class="alert alert-danger">Connection error loading chart data</div>';
            };
            
            request.send();
        }
    }
    
    // Load all charts on the reports page
    const reportsPage = document.getElementById('reports-page');
    if (reportsPage) {
        loadTotalIncidents();
        loadIncidentsByType();
        loadIncidentTrend();
    }
    
    // Load dashboard summary charts
    const dashboardSummary = document.getElementById('dashboard-summary');
    if (dashboardSummary) {
        loadTotalIncidents();
    }
});
