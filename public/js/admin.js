/* Admin Layout Logic - OmniPOS */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminRoot = document.getElementById('admin-root');

    if (sidebarToggle && adminRoot) {
        sidebarToggle.addEventListener('click', () => {
            adminRoot.classList.toggle('collapsed');
            // Guardar estado en localStorage para persistencia
            localStorage.setItem('sidebar_collapsed', adminRoot.classList.contains('collapsed'));
        });

        // Restaurar estado
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            adminRoot.classList.add('collapsed');
        }
    }

    // 2. Flash Messages Auto-hide
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            setTimeout(() => msg.remove(), 500);
        }, 5000);
    });
});

// 3. Submenu Toggle (Global)
function toggleSubmenu(el) {
    const submenu = el.nextElementSibling;
    const icon = el.querySelector('.fa-chevron-right');

    if (submenu && submenu.classList.contains('submenu-wrap')) {
        const isVisible = submenu.style.display === 'block';
        submenu.style.display = isVisible ? 'none' : 'block';
        if (icon) icon.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(90deg)';
    }
}

/**
 * Global Modal Helpers
 */
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'flex';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
}

/**
 * Finance Modal Helpers
 */
function openFinancePaymentModal(type, id, max, actionUrl) {
    const prefix = type.toLowerCase(); // 'cxc' or 'cxp'
    const idInput = document.getElementById(prefix + '-id');
    const form = document.getElementById('payment-form');
    const amountInput = document.getElementById('payment-amount');
    const maxText = document.getElementById('max-amount');

    if (idInput) idInput.value = id;
    if (form) form.action = actionUrl + '?id=' + id;
    if (amountInput) amountInput.value = max.toFixed(2);
    if (maxText) maxText.innerText = '$' + max.toFixed(2);

    openModal('payment-modal');
}
