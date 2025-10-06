// Enhanced Admin Dashboard JavaScript
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

    // Sidebar toggle for mobile
    const sidebar = document.querySelector('.sidebar');
    const toggleSidebar = document.createElement('button');
    toggleSidebar.className = 'btn btn-primary d-md-none position-fixed';
    toggleSidebar.style = 'top: 10px; left: 10px; z-index: 1001;';
    toggleSidebar.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.appendChild(toggleSidebar);

    toggleSidebar.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    // Form submission with loading state
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                submitButton.disabled = true;
            }
        });
    });

    // Confirmation dialogs for destructive actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.dataset.action || 'delete this item';
            if (confirm(`Are you sure you want to ${action}? This action cannot be undone.`)) {
                // If confirmed, submit the form
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });
    });

    // Real-time data refresh
    function refreshData() {
        // This would typically make AJAX calls to update dashboard data
        console.log('Refreshing dashboard data...');
    }

    // Refresh every 5 minutes
    setInterval(refreshData, 300000);

    // Theme switcher
    const themeSwitcher = document.getElementById('themeSwitcher');
    if (themeSwitcher) {
        themeSwitcher.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            // Save preference to localStorage
            localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
        });
    }

    // Load saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length > 2) {
                // In a real implementation, this would make an AJAX call to search
                console.log('Searching for:', query);
                // Show search results container
                if (searchResults) {
                    searchResults.classList.remove('d-none');
                }
            } else {
                // Hide search results container
                if (searchResults) {
                    searchResults.classList.add('d-none');
                }
            }
        });
    }

    // Chart initialization functions
    window.initUserChart = function(data) {
        // Implementation would go here
        console.log('Initializing user chart with data:', data);
    };

    window.initBookingChart = function(data) {
        // Implementation would go here
        console.log('Initializing booking chart with data:', data);
    };

    // Export functionality
    const exportButtons = document.querySelectorAll('.btn-export');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const format = this.dataset.format || 'csv';
            const table = this.dataset.table || 'data';
            
            showNotification(`Exporting ${table} as ${format.toUpperCase()}...`, 'success');
            
            // In a real implementation, this would trigger the export process
            setTimeout(() => {
                showNotification(`${table} exported successfully as ${format.toUpperCase()}!`, 'success');
            }, 2000);
        });
    });

    console.log('Enhanced Admin Dashboard JavaScript loaded');
});