/**
 * ProLedger - JavaScript Utilities
 */

// Format number to Rupiah (without Rp prefix, just the number)
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Parse Rupiah string to number
function parseRupiah(rupiah) {
    return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
}

// Format input as Rupiah on keyup
function formatRupiahInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
        input.value = formatRupiah(parseInt(value));
    }
}

// Auto-format all rupiah inputs
document.addEventListener('DOMContentLoaded', function () {
    const rupiahInputs = document.querySelectorAll('.rupiah-input');
    rupiahInputs.forEach(input => {
        input.addEventListener('keyup', function () {
            formatRupiahInput(this);
        });

        // Format on blur to ensure consistency
        input.addEventListener('blur', function () {
            formatRupiahInput(this);
        });
    });
});

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Close modal on outside click
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target.id);
    }
});

// Confirm delete
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
}

// Calculate modal preview
function calculateModalPreview() {
    const pagu = parseRupiah(document.getElementById('pagu')?.value || '0');
    const rab = parseRupiah(document.getElementById('rab')?.value || '0');
    const jumlahPemodal = parseInt(document.getElementById('jumlah_pemodal')?.value || '0');

    if (jumlahPemodal > 0) {
        const modalPasal = (pagu * 0.13) / jumlahPemodal;
        const modalBelanja = rab / jumlahPemodal;
        const totalModal = modalPasal + modalBelanja;

        const previewEl = document.getElementById('modal-preview');
        if (previewEl) {
            previewEl.innerHTML = `
                <div class="card card-glass mt-3">
                    <h4>Preview Perhitungan Modal per Pemodal</h4>
                    <div class="grid grid-3 mt-2">
                        <div>
                            <div class="text-gray">Modal Pasal (13%)</div>
                            <div class="stat-value" style="font-size: 1.25rem;">${formatRupiah(modalPasal)}</div>
                        </div>
                        <div>
                            <div class="text-gray">Modal Belanja</div>
                            <div class="stat-value" style="font-size: 1.25rem;">${formatRupiah(modalBelanja)}</div>
                        </div>
                        <div>
                            <div class="text-gray">Total Modal</div>
                            <div class="stat-value" style="font-size: 1.25rem; color: var(--primary);">${formatRupiah(totalModal)}</div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
}

// Auto-calculate on input change
document.addEventListener('DOMContentLoaded', function () {
    const calculationInputs = ['pagu', 'rab', 'jumlah_pemodal'];
    calculationInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', calculateModalPreview);
            input.addEventListener('change', calculateModalPreview);
        }
    });
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = 'var(--danger)';
        } else {
            field.style.borderColor = 'var(--gray-light)';
        }
    });

    return isValid;
}

// Show/hide loading spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.id = 'loading-spinner';
    spinner.className = 'modal active';
    spinner.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(spinner);
}

function hideLoading() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Multi-select helper
function toggleSelectAll(checkbox, targetClass) {
    const checkboxes = document.querySelectorAll(`.${targetClass}`);
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

// Date formatting
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

// Number animation
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        element.textContent = formatRupiah(Math.floor(progress * (end - start) + start));
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Initialize tooltips (if needed)
document.addEventListener('DOMContentLoaded', function () {
    // Add any initialization code here
    console.log('ProLedger initialized');
});
