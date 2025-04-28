// Signup Form Enhancements for MMU Hostel Management System

document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const strengthIndicator = document.getElementById('password-strength');
    
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            if (strengthIndicator) {
                updatePasswordStrength(this.value, strengthIndicator);
            }
        });
    }
    
    // Password confirmation match indicator
    if (passwordField && confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            checkPasswordMatch(passwordField.value, this.value);
        });
    }
    
    // Profile picture preview
    const profilePicInput = document.getElementById('profile_pic');
    const previewContainer = document.getElementById('profile-preview-container');
    
    if (profilePicInput && previewContainer) {
        profilePicInput.addEventListener('change', function() {
            showImagePreview(this, previewContainer);
        });
    }
    
    // Form section navigation
    const formSections = document.querySelectorAll('.form-section');
    const nextButtons = document.querySelectorAll('.btn-next');
    const prevButtons = document.querySelectorAll('.btn-prev');
    
    // If multi-step form is used
    if (formSections.length > 1 && nextButtons.length > 0) {
        setupMultiStepForm(formSections, nextButtons, prevButtons);
    }
    
    // Form validation enhancement
    const signupForm = document.querySelector('form');
    if (signupForm) {
        signupForm.addEventListener('submit', function(event) {
            const isValid = validateForm(this);
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});

// Password strength checker
function updatePasswordStrength(password, indicator) {
    let strength = 0;
    
    // Add points for various password characteristics
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]+/)) strength += 1;
    if (password.match(/[A-Z]+/)) strength += 1;
    if (password.match(/[0-9]+/)) strength += 1;
    if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;
    
    // Update visual indicator
    switch (strength) {
        case 0:
        case 1:
            indicator.className = 'progress-bar bg-danger';
            indicator.style.width = '20%';
            indicator.textContent = 'Weak';
            break;
        case 2:
        case 3:
            indicator.className = 'progress-bar bg-warning';
            indicator.style.width = '60%';
            indicator.textContent = 'Medium';
            break;
        case 4:
        case 5:
            indicator.className = 'progress-bar bg-success';
            indicator.style.width = '100%';
            indicator.textContent = 'Strong';
            break;
    }
}

// Check if passwords match
function checkPasswordMatch(password, confirmPassword) {
    const matchIndicator = document.getElementById('password-match');
    if (!matchIndicator) return;
    
    if (password === confirmPassword && password !== '') {
        matchIndicator.className = 'text-success';
        matchIndicator.textContent = 'Passwords match';
    } else {
        matchIndicator.className = 'text-danger';
        matchIndicator.textContent = 'Passwords do not match';
    }
}

// Profile picture preview
function showImagePreview(input, previewContainer) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            let img = previewContainer.querySelector('img');
            
            if (!img) {
                img = document.createElement('img');
                img.className = 'profile-preview';
                previewContainer.appendChild(img);
            }
            
            img.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Multi-step form setup
function setupMultiStepForm(sections, nextButtons, prevButtons) {
    // Hide all sections except the first one
    for (let i = 1; i < sections.length; i++) {
        sections[i].style.display = 'none';
    }
    
    // Setup next buttons
    nextButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            if (validateSection(sections[index])) {
                sections[index].style.display = 'none';
                sections[index + 1].style.display = 'block';
                sections[index + 1].classList.add('fade-in');
                window.scrollTo(0, 0);
            }
        });
    });
    
    // Setup previous buttons
    prevButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            sections[index + 1].style.display = 'none';
            sections[index].style.display = 'block';
            sections[index].classList.add('fade-in');
            window.scrollTo(0, 0);
        });
    });
}

// Validate current form section
function validateSection(section) {
    const inputs = section.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            highlightInvalidField(input);
        } else {
            removeInvalidHighlight(input);
        }
    });
    
    return isValid;
}

// Full form validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            highlightInvalidField(field);
        } else {
            removeInvalidHighlight(field);
        }
    });
    
    // Check password match
    const password = form.querySelector('#password');
    const confirmPassword = form.querySelector('#confirm_password');
    
    if (password && confirmPassword && password.value !== confirmPassword.value) {
        isValid = false;
        highlightInvalidField(confirmPassword);
    }
    
    return isValid;
}

// Highlight invalid field
function highlightInvalidField(field) {
    field.classList.add('is-invalid');
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = 'This field is required';
    
    // Check if feedback already exists
    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
        field.parentNode.insertBefore(feedback, field.nextElementSibling);
    }
}

// Remove invalid highlight
function removeInvalidHighlight(field) {
    field.classList.remove('is-invalid');
    
    // Remove feedback message if exists
    if (field.nextElementSibling && field.nextElementSibling.classList.contains('invalid-feedback')) {
        field.parentNode.removeChild(field.nextElementSibling);
    }
}