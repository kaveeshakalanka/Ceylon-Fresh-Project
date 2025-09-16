// Cart functionality
function updateQuantity(id) {
    var quantity = document.getElementById('qty_' + id).value;
    console.log('Updating quantity for ID:', id, 'to:', quantity);
    if (quantity < 1) quantity = 1;
    window.location.href = 'cart.php?action=update&id=' + id + '&qty=' + quantity;
}

function removeItem(id) {
    console.log('Removing item with ID:', id);
    if (confirm('Are you sure you want to remove this item?')) {
        window.location.href = 'cart.php?action=remove&id=' + id;
    }
}

function clearCart() {
    console.log('Clearing cart');
    if (confirm('Are you sure you want to clear the entire cart?')) {
        window.location.href = 'cart.php?action=clear';
    }
}

// Admin functionality
function toggleEditForm(userId) {
    const form = document.getElementById('edit-form-' + userId);
    if (form) {
        form.classList.toggle('active');
    }
}


// Popup messages
function showPopup(message, type = 'success', duration = 3000) {
    const existingPopup = document.getElementById('popup');
    if (existingPopup) {
        existingPopup.remove();
    }

    const popup = document.createElement('div');
    popup.id = 'popup';
    popup.className = `popup show ${type}`;
    
    let icon = 'fas fa-check-circle';
    if (type === 'error') icon = 'fas fa-exclamation-circle';
    if (type === 'info') icon = 'fas fa-info-circle';
    
    popup.innerHTML = `<i class="${icon}"></i> ${message}`;
    
    document.body.appendChild(popup);
    
    setTimeout(function() {
        if (popup) {
            popup.classList.remove('show');
            setTimeout(function() {
                if (popup.parentNode) {
                    popup.parentNode.removeChild(popup);
                }
            }, 500);
        }
    }, duration);
}

function initPopupMessages() {
    const popup = document.getElementById('popup');
    if (popup) {
        setTimeout(function() {
            if (popup) {
                popup.classList.remove('show');
                setTimeout(function() {
                    if (popup.parentNode) {
                        popup.parentNode.removeChild(popup);
                    }
                }, 500);
            }
        }, 3000);
    }
}


// Form validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
    return phoneRegex.test(phone);
}

function validateRequiredFields(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    return isValid;
}


// Utility functions
function formatCurrency(amount, currency = 'Rs.') {
    return `${currency} ${parseFloat(amount).toFixed(2)}`;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}


// Mobile menu functionality
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    const hamburger = document.querySelector('.hamburger');
    
    if (navLinks && hamburger) {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
    }
}

function closeMobileMenuOnClickOutside() {
    document.addEventListener('click', function(event) {
        const navLinks = document.querySelector('.nav-links');
        const hamburger = document.querySelector('.hamburger');
        
        if (navLinks && hamburger && 
            !navLinks.contains(event.target) && 
            !hamburger.contains(event.target)) {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });
}


// Initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Ceylon Fresh JavaScript loaded successfully');
    
    initPopupMessages();
    closeMobileMenuOnClickOutside();
    
    const hamburger = document.querySelector('.hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileMenu);
    }
    
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            smoothScrollTo(targetId);
        });
    });
    
    const forms = document.querySelectorAll('.validate-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateRequiredFields(this.id)) {
                e.preventDefault();
                showPopup('Please fill in all required fields.', 'error');
            }
        });
    });
});


// Export functions for global use
window.updateQuantity = updateQuantity;
window.removeItem = removeItem;
window.clearCart = clearCart;
window.toggleEditForm = toggleEditForm;
window.showPopup = showPopup;
window.validateEmail = validateEmail;
window.validatePhone = validatePhone;
window.validateRequiredFields = validateRequiredFields;
window.formatCurrency = formatCurrency;
window.smoothScrollTo = smoothScrollTo;
window.toggleMobileMenu = toggleMobileMenu;