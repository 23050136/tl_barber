// TL Barber - Main JavaScript

// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
        }
    });
});

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('notification-toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.className = 'notification-toast ' + type;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Booking Steps Navigation
function goToStep(stepNumber) {
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        if (index + 1 < stepNumber) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (index + 1 === stepNumber) {
            step.classList.add('active');
            step.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
}

// Time Slot Selection
function selectTimeSlot(element) {
    if (element.classList.contains('disabled')) return;
    
    // Remove previous selection
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    // Add selection to clicked element
    element.classList.add('selected');
    
    // Update hidden input if exists
    const timeInput = document.getElementById('selected_time');
    if (timeInput) {
        timeInput.value = element.dataset.time;
    }
}

// Date Picker (if using native date input)
function updateAvailableSlots() {
    const dateInput = document.getElementById('booking_date');
    const barberId = document.getElementById('barber_id')?.value;
    
    if (!dateInput || !barberId) return;
    
    const selectedDate = dateInput.value;
    if (!selectedDate) return;
    
    // Fetch available slots via AJAX
    fetch(`api/get-available-slots.php?date=${selectedDate}&barber_id=${barberId}`)
        .then(response => response.json())
        .then(data => {
            updateTimeSlotsDisplay(data.available_slots, data.booked_slots);
        })
        .catch(error => {
            console.error('Error fetching slots:', error);
        });
}

function updateTimeSlotsDisplay(availableSlots, bookedSlots) {
    const timeSlots = document.querySelectorAll('.time-slot');
    timeSlots.forEach(slot => {
        const slotTime = slot.dataset.time;
        if (bookedSlots.includes(slotTime)) {
            slot.classList.add('disabled');
            slot.classList.remove('selected');
        } else if (availableSlots.includes(slotTime)) {
            slot.classList.remove('disabled');
        }
    });
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
    });
    
    return isValid;
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Rating Stars
function setRating(rating) {
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
    
    const ratingInput = document.getElementById('rating');
    if (ratingInput) {
        ratingInput.value = rating;
    }
}

