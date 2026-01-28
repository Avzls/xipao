import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize SweetAlert2 with default config
window.Swal = Swal;

// Toast configuration
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

// Helper functions for common alerts
window.showSuccess = (message) => {
    Toast.fire({
        icon: 'success',
        title: message
    });
};

window.showError = (message) => {
    Toast.fire({
        icon: 'error',
        title: message
    });
};

window.showWarning = (message) => {
    Toast.fire({
        icon: 'warning',
        title: message
    });
};

window.confirmDelete = (callback) => {
    Swal.fire({
        title: 'Yakin hapus data ini?',
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#8B0000',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
};
