// Enhanced Customer Dashboard JavaScript
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

    // Favorite toggle functionality
    const favoriteButtons = document.querySelectorAll('.btn-favorite');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const serviceId = this.dataset.serviceId;
            const isFavorited = this.classList.contains('favorited');
            
            // Toggle visual state
            this.classList.toggle('favorited');
            const icon = this.querySelector('i');
            if (isFavorited) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                showNotification('Removed from favorites', 'info');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                showNotification('Added to favorites', 'success');
            }
            
            // In a real implementation, this would make an AJAX call to update the backend
            console.log('Toggling favorite status for service:', serviceId);
        });
    });

    // Booking form enhancements
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        // Date picker initialization
        const dateInput = bookingForm.querySelector('input[type="date"]');
        if (dateInput) {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
        }
        
        // Time slot selection
        const timeSelect = bookingForm.querySelector('select[name="time"]');
        if (timeSelect) {
            // Populate time slots (example: 9AM to 5PM in 30-minute intervals)
            for (let hour = 9; hour <= 17; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    const option = document.createElement('option');
                    option.value = timeString;
                    option.textContent = timeString;
                    timeSelect.appendChild(option);
                }
            }
        }
        
        // Form validation
        bookingForm.addEventListener('submit', function(e) {
            const date = dateInput.value;
            const time = timeSelect.value;
            
            if (!date || !time) {
                e.preventDefault();
                showNotification('Please select both date and time', 'warning');
                return;
            }
            
            // Show loading state
            const submitButton = bookingForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...';
                submitButton.disabled = true;
            }
        });
    }

    // Service search and filtering
    const serviceFilters = document.querySelectorAll('.service-filter');
    serviceFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            // In a real implementation, this would filter the services
            console.log('Filtering services by:', this.value);
            showNotification('Filtering services...', 'info');
        });
    });

    // Review submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        const ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
        const ratingDisplay = document.getElementById('ratingDisplay');
        
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (ratingDisplay) {
                    ratingDisplay.textContent = `${this.value} star${this.value > 1 ? 's' : ''}`;
                }
            });
        });
        
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = reviewForm.querySelector('input[name="rating"]:checked');
            const reviewText = reviewForm.querySelector('textarea[name="review"]').value;
            
            if (!rating) {
                showNotification('Please select a rating', 'warning');
                return;
            }
            
            if (reviewText.trim().length < 10) {
                showNotification('Please provide a detailed review (at least 10 characters)', 'warning');
                return;
            }
            
            // Show loading state
            const submitButton = reviewForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                submitButton.disabled = true;
            }
            
            // In a real implementation, this would submit the review via AJAX
            setTimeout(() => {
                showNotification('Review submitted successfully!', 'success');
                reviewForm.reset();
                if (ratingDisplay) {
                    ratingDisplay.textContent = '';
                }
                if (submitButton) {
                    submitButton.innerHTML = 'Submit Review';
                    submitButton.disabled = false;
                }
            }, 1500);
        });
    }

    // Profile image upload preview
    const profileImageInput = document.getElementById('profileImage');
    const profileImagePreview = document.getElementById('profileImagePreview');
    if (profileImageInput && profileImagePreview) {
        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    console.log('Enhanced Customer Dashboard JavaScript loaded');
});