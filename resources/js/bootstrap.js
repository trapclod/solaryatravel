// Bootstrap 5 bundle (includes Popper)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Auto-initialize tooltips and popovers on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => new bootstrap.Popover(el));

    // Auto-dismiss flash alerts after 5s
    document.querySelectorAll('.alert-auto-dismiss').forEach(el => {
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        }, 5000);
    });
});
