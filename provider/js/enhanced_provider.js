// Enhanced Provider Dashboard JavaScript
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

    // Service management enhancements
    const serviceForm = document.getElementById('serviceForm');
    if (serviceForm) {
        // Image preview for service images
        const imageInputs = serviceForm.querySelectorAll('input[type="file"][name^="images"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const previewId = this.dataset.preview;
                    const preview = document.getElementById(previewId);
                    if (preview) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.classList.remove('d-none');
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                }
            });
        });
        
        // Dynamic pricing options
        const priceTypeSelect = serviceForm.querySelector('select[name="price_type"]');
        const durationField = serviceForm.querySelector('.duration-field');
        if (priceTypeSelect && durationField) {
            priceTypeSelect.addEventListener('change', function() {
                if (this.value === 'hourly') {
                    durationField.classList.remove('d-none');
                } else {
                    durationField.classList.add('d-none');
                }
            });
        }
        
        // Tag input enhancement
        const tagInput = serviceForm.querySelector('input[name="tags"]');
        if (tagInput) {
            tagInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const tag = this.value.trim().replace(/,/g, '');
                    if (tag) {
                        // Add tag to display
                        const tagContainer = this.closest('.tag-container');
                        if (tagContainer) {
                            const tagElement = document.createElement('span');
                            tagElement.className = 'badge bg-primary me-2';
                            tagElement.innerHTML = `${tag} <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove"></button>`;
                            tagContainer.querySelector('.tags-display').appendChild(tagElement);
                            
                            // Add remove functionality
                            tagElement.querySelector('button').addEventListener('click', function() {
                                tagElement.remove();
                            });
                            
                            // Clear input
                            this.value = '';
                        }
                    }
                }
            });
        }
        
        // Form submission
        serviceForm.addEventListener('submit', function(e) {
            // Show loading state
            const submitButton = serviceForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                submitButton.disabled = true;
            }
        });
    }

    // Availability management
    const availabilityForm = document.getElementById('availabilityForm');
    if (availabilityForm) {
        // Add time slot button
        const addSlotButton = document.getElementById('addTimeSlot');
        if (addSlotButton) {
            addSlotButton.addEventListener('click', function() {
                const slotContainer = document.getElementById('timeSlotsContainer');
                if (slotContainer) {
                    const slotCount = slotContainer.querySelectorAll('.time-slot').length;
                    const newSlot = document.createElement('div');
                    newSlot.className = 'time-slot row mb-3';
                    newSlot.innerHTML = `
                        <div class="col-md-3">
                            <select name="day_${slotCount}" class="form-control">
                                <option value="0">Sunday</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="time" name="start_${slotCount}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <input type="time" name="end_${slotCount}" class="form-control" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-slot">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    slotContainer.appendChild(newSlot);
                    
                    // Add remove functionality
                    newSlot.querySelector('.remove-slot').addEventListener('click', function() {
                        newSlot.remove();
                    });
                }
            });
        }
        
        // Remove slot functionality
        const removeSlotButtons = availabilityForm.querySelectorAll('.remove-slot');
        removeSlotButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.time-slot').remove();
            });
        });
    }

    // Booking management
    const bookingStatusButtons = document.querySelectorAll('.btn-booking-status');
    bookingStatusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const status = this.dataset.status;
            
            // Show confirmation
            if (confirm(`Are you sure you want to change this booking to ${status}?`)) {
                // In a real implementation, this would make an AJAX call to update the booking status
                console.log('Updating booking status:', bookingId, status);
                showNotification(`Booking status updated to ${status}`, 'success');
                
                // Update UI
                const statusBadge = document.querySelector(`.booking-status-badge[data-booking-id="${bookingId}"]`);
                if (statusBadge) {
                    statusBadge.className = `booking-status-badge status-${status}`;
                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                }
            }
        });
    });

    // Earnings chart filtering
    const earningsFilter = document.getElementById('earningsFilter');
    if (earningsFilter) {
        earningsFilter.addEventListener('change', function() {
            const period = this.value;
            // In a real implementation, this would reload the chart with data for the selected period
            console.log('Filtering earnings by period:', period);
            showNotification(`Showing earnings for ${period}`, 'info');
        });
    }

    // Review response
    const reviewResponseForms = document.querySelectorAll('.review-response-form');
    reviewResponseForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            const responseText = this.querySelector('textarea').value;
            
            if (responseText.trim().length < 5) {
                showNotification('Please provide a detailed response (at least 5 characters)', 'warning');
                return;
            }
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                submitButton.disabled = true;
            }
            
            // In a real implementation, this would submit the response via AJAX
            setTimeout(() => {
                showNotification('Response sent successfully!', 'success');
                // Update UI to show the response
                const responseContainer = this.closest('.review-item').querySelector('.review-response');
                if (responseContainer) {
                    responseContainer.innerHTML = `<strong>Your response:</strong> ${responseText}`;
                }
                this.remove();
            }, 1500);
        });
    });

    console.log('Enhanced Provider Dashboard JavaScript loaded');
});