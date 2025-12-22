/* POS Logic - OmniPOS */

let cart = [];
let consumptionType = 'dine_in';
let searchTimer = null;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function setConsumptionType(type) {
    consumptionType = type;
    const dineInBtn = document.getElementById('btn-dine-in');
    const takeawayBtn = document.getElementById('btn-takeaway');

    if (dineInBtn && takeawayBtn) {
        dineInBtn.classList.toggle('btn-primary', type === 'dine_in');
        dineInBtn.classList.toggle('btn-secondary', type !== 'dine_in');
        takeawayBtn.classList.toggle('btn-primary', type === 'takeaway');
        takeawayBtn.classList.toggle('btn-secondary', type !== 'takeaway');
    }
    renderCart();
}

function addToCart(product) {
    document.getElementById('mod-product-name').innerText = product.name;
    document.getElementById('mod-product-data').value = JSON.stringify(product);
    document.getElementById('mod-extras').value = '';
    document.getElementById('mod-removals').value = '';
    document.getElementById('modification-modal').style.display = 'flex';
}

function saveModifications() {
    const productData = document.getElementById('mod-product-data').value;
    if (!productData) return;

    const product = JSON.parse(productData);
    const extrasStr = document.getElementById('mod-extras').value;
    const removalsStr = document.getElementById('mod-removals').value;

    const modifications = {
        extras: extrasStr ? extrasStr.split(',').map(s => s.trim()) : [],
        removals: removalsStr ? removalsStr.split(',').map(s => s.trim()) : []
    };

    cart.push({
        id: product.id,
        name: product.name,
        price: parseFloat(product.price_usd),
        packaging_cost: parseFloat(product.packaging_cost || 0),
        quantity: 1,
        modifications: modifications
    });

    document.getElementById('modification-modal').style.display = 'none';
    renderCart();
}

async function searchProducts(query) {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(async () => {
        loadPage(1);
    }, 300);
}

async function searchClients(query) {
    if (query.length < 2) {
        document.getElementById('client-results').style.display = 'none';
        return;
    }
    try {
        const cleanUrl = BASE_URL.replace(/\/$/, '');
        const response = await fetch(`${cleanUrl}/sales/search-clients?q=${encodeURIComponent(query)}`);
        const clients = await response.json();
        const results = document.getElementById('client-results');
        results.innerHTML = '';
        results.style.display = 'block';

        clients.forEach(c => {
            const item = document.createElement('div');
            item.className = 'client-result-item';
            item.style.cssText = 'padding: 10px; cursor: pointer; border-bottom: 1px solid var(--border-subtle); font-size:0.9rem;';
            item.onclick = () => selectClient(c.id, c.name);
            item.innerHTML = `${escapeHtml(c.name)} (${escapeHtml(c.document_id)})`;
            results.appendChild(item);
        });
    } catch (e) {
        console.error('Error searching clients:', e);
    }
}

function selectClient(id, name) {
    document.getElementById('selected-client-id').value = id;
    document.getElementById('client-search').value = name;
    document.getElementById('client-results').style.display = 'none';
}

function renderCart() {
    const container = document.getElementById('cart-items');
    if (!container) return;

    container.innerHTML = '';
    let baseTotal = 0;
    let packagingTotal = 0;

    cart.forEach((item, index) => {
        baseTotal += item.price * item.quantity;
        if (consumptionType === 'takeaway') {
            packagingTotal += item.packaging_cost * item.quantity;
        }

        let modHtml = '';
        if (item.modifications.extras.length > 0 || item.modifications.removals.length > 0) {
            modHtml = `<div style="font-size: 0.75rem; color: #fbbf24;">
                ${item.modifications.extras.map(e => '+' + e).join(', ')} 
                ${item.modifications.removals.map(r => '-' + r).join(', ')}
            </div>`;
        }

        container.innerHTML += `
            <div class="cart-item">
                <div>
                    <div style="font-weight: 600;">${item.name}</div>
                    ${modHtml}
                    <div style="font-size: 0.8rem; color: var(--text-dim);">${item.quantity} x $${item.price.toFixed(2)}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <strong style="font-size: 1rem;">$${(item.price * item.quantity).toFixed(2)}</strong>
                    <button onclick="removeFromCart(${index})" class="btn-icon" style="color: #ef4444; background:none; border:none; cursor:pointer;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    const totalUsd = baseTotal + packagingTotal;
    const taxEstimate = totalUsd * 0.16;
    const finalTotal = totalUsd + taxEstimate;

    if (document.getElementById('subtotal-usd')) {
        document.getElementById('subtotal-usd').innerText = `$${totalUsd.toFixed(2)}`;
    }

    document.getElementById('total-usd').innerText = `$${finalTotal.toFixed(2)}`;
    document.getElementById('total-ves').innerText = `Bs ${(finalTotal * EXCHANGE_RATE).toFixed(2)}`;

    const packDisplay = document.getElementById('packaging-display');
    if (packDisplay) {
        if (packagingTotal > 0) {
            packDisplay.style.display = 'flex';
            document.getElementById('total-packaging').innerText = `$${packagingTotal.toFixed(2)}`;
        } else {
            packDisplay.style.display = 'none';
        }
    }

    const taxDisplay = document.getElementById('tax-amount');
    if (taxDisplay) {
        taxDisplay.innerText = `$${taxEstimate.toFixed(2)}`;
    }

    document.getElementById('cart-count').innerText = cart.length;
    updateChangeDisplay();
}

function updateChangeDisplay() {
    const totalEl = document.getElementById('total-usd');
    if (!totalEl) return;

    const total = parseFloat(totalEl.innerText.replace('$', ''));
    let paid = 0;
    document.querySelectorAll('.payment-amount').forEach(input => {
        if (input.value) {
            const checkbox = input.closest('.payment-method-row').querySelector('input[type="checkbox"]');
            const currency = checkbox.dataset.currency;
            if (currency === 'USD') paid += parseFloat(input.value);
            else {
                paid += parseFloat(input.value) / EXCHANGE_RATE;
            }
        }
    });
    const change = paid - total;
    const changeEl = document.getElementById('change-display');
    if (changeEl) {
        changeEl.innerText = change > 0 ? `Vuelto: $${change.toFixed(2)}` : '';
        changeEl.style.color = change >= 0 ? '#10b981' : '#ef4444';
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function openPaymentModal() {
    if (cart.length === 0) return alert('El carrito está vacío');
    document.getElementById('payment-modal').style.display = 'flex';
    updateChangeDisplay();
}

function toggleSupervisor(value) {
    const authDiv = document.getElementById('supervisor-auth');
    const methodsList = document.getElementById('payment-methods');
    if (value === 'credit') {
        authDiv.style.display = 'block';
        methodsList.style.display = 'none';
    } else {
        authDiv.style.display = 'none';
        methodsList.style.display = 'block';
    }
}

async function submitCheckout() {
    const paymentType = document.getElementById('payment-type').value;
    const supervisorCode = document.getElementById('supervisor-code').value;
    const payments = [];

    if (paymentType === 'cash') {
        document.querySelectorAll('.payment-method-row').forEach(row => {
            const checkbox = row.querySelector('input[type="checkbox"]');
            const amountInput = row.querySelector('.payment-amount');
            if (checkbox.checked && amountInput.value > 0) {
                payments.push({
                    method_id: checkbox.value,
                    amount: parseFloat(amountInput.value),
                    currency: checkbox.dataset.currency
                });
            }
        });
        if (payments.length === 0) return alert('Selecciona al menos un método de pago');
    }

    if (paymentType === 'credit' && !supervisorCode) {
        return alert('Se requiere la contraseña del supervisor para ventas a crédito');
    }

    // El backend recalculará basándose en items, pero enviamos esto para trazabilidad de UI
    const baseTotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const packagingTotal = consumptionType === 'takeaway' ? cart.reduce((sum, item) => sum + (item.packaging_cost * item.quantity), 0) : 0;
    const total = baseTotal + packagingTotal;

    const cleanUrl = BASE_URL.replace(/\/$/, '');
    const response = await fetch(`${cleanUrl}/sales/checkout`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            total: total,
            items: cart,
            client_id: document.getElementById('selected-client-id').value,
            payment_type: paymentType,
            payments: payments,
            supervisor_code: supervisorCode,
            consumption_type: consumptionType
        })
    });

    const result = await response.json();
    if (result.success) {
        showTicket(result.order_id);
    } else {
        alert('Error: ' + result.message);
    }
}

function showTicket(orderId) {
    const ticket = document.getElementById('ticket-content');
    if (!ticket) return;

    const total = parseFloat(document.getElementById('total-usd').innerText.replace('$', ''));
    let itemsHtml = '';
    cart.forEach(item => {
        let mods = '';
        if (item.modifications.extras.length) mods += '<br>+ ' + item.modifications.extras.join(', ');
        if (item.modifications.removals.length) mods += '<br>- ' + item.modifications.removals.join(', ');

        itemsHtml += `<div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #eee; padding-bottom: 5px;">
                        <span style="font-size:0.85rem; flex: 1;">${item.name} x${item.quantity}${mods}</span>
                        <span style="font-weight: 600;">$${(item.price * item.quantity).toFixed(2)}</span>
                      </div>`;
    });

    ticket.innerHTML = `
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 15px;">
            <h2 style="margin:0; font-size: 1.2rem;">OmniPOS</h2>
            <p style="margin: 5px 0; font-size: 0.8rem;">SaaS Restaurant Management</p>
            <small>Fecha: ${new Date().toLocaleString()}</small><br>
            <small>ID Orden: ${orderId.substring(0, 8).toUpperCase()}</small><br>
            <div style="margin-top: 10px; font-weight: 800; text-transform: uppercase; border: 1px solid #000; padding: 5px;">
                ${consumptionType === 'takeaway' ? 'PARA LLEVAR' : 'PARA EL LOCAL'}
            </div>
        </div>
        <div style="min-height: 100px;">
            ${itemsHtml}
        </div>
        <div style="border-top: 2px solid #000; margin-top: 20px; padding-top: 15px;">
            <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 900;">
                <span>TOTAL A PAGAR:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-top: 5px; color: #666;">
                <span>Contravalor VES:</span>
                <span>Bs ${(total * EXCHANGE_RATE).toFixed(2)}</span>
            </div>
        </div>
        <div style="text-align: center; margin-top: 30px; border-top: 1px dashed #aaa; padding-top: 10px;">
            <p style="font-size: 0.8rem; font-style: italic;">¡Muchas gracias por su preferencia!</p>
            <div style="font-size: 0.6rem; opacity: 0.5; margin-top: 10px;">Powered by OmniPOS Cloud</div>
        </div>
    `;
    document.getElementById('payment-modal').style.display = 'none';
    document.getElementById('ticket-modal').style.display = 'flex';
}

async function loadPage(page) {
    const query = document.getElementById('product-search').value;
    try {
        const cleanUrl = BASE_URL.replace(/\/$/, '');
        const response = await fetch(`${cleanUrl}/sales/search-products?q=${encodeURIComponent(query)}&page=${page}`);
        const result = await response.json();

        if (result.products) {
            renderProductGrid(result.products);
            TOTAL_PAGES = parseInt(result.totalPages) || 1;
        } else if (Array.isArray(result)) {
            renderProductGrid(result);
            TOTAL_PAGES = 1; // If it's a plain array, assume no pagination or single page
        } else {
            // Handle unexpected format, maybe clear grid or show error
            renderProductGrid([]);
            TOTAL_PAGES = 1;
            console.warn('Unexpected product search result format:', result);
        }

        CURRENT_PAGE = page;
        renderPagination();
    } catch (e) {
        console.error('Error loading products page:', e);
        // Optionally, clear the grid and pagination on error
        document.getElementById('products-container').innerHTML = '<p class="text-center text-danger">Error al cargar productos.</p>';
        TOTAL_PAGES = 1;
        CURRENT_PAGE = 1;
        renderPagination(); // Render empty pagination or just current page
    }
}

function renderProductGrid(products) {
    const container = document.getElementById('products-container');
    container.innerHTML = '';
    products.forEach(p => {
        const imgSrc = p.image_url ?
            (p.image_url.startsWith('http') ? p.image_url : BASE_URL + '/' + p.image_url.replace(/^\//, '')) :
            BASE_URL + '/img/no-image.png';

        const card = document.createElement('div');
        card.className = 'product-card glass-effect p-1-5 cursor-pointer hover-glow transition-all rounded-lg d-flex flex-column';
        card.onclick = () => addToCart(p);

        card.innerHTML = `
            <div class="aspect-square rounded-md mb-1-5 overflow-hidden bg-dim">
                <img src="${imgSrc}" onerror="this.onerror=null; this.src='${BASE_URL}/img/no-image.png'" class="w-full h-full object-cover">
            </div>
            <div class="name font-700 leading-tight text-sm line-clamp-2 mb-1">${escapeHtml(p.name)}</div>
            <div class="price text-primary font-800 mt-auto">$${parseFloat(p.price_usd).toFixed(2)}</div>
        `;
        container.appendChild(card);
    });
}

function renderPagination() {
    const container = document.getElementById('pos-pagination');
    if (!container || TOTAL_PAGES <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    let html = `<nav class="pagination-premium d-flex align-center gap-05 bg-glass-dark p-05 rounded-full border-bright">`;

    if (CURRENT_PAGE > 1) {
        html += `<button onclick="loadPage(${CURRENT_PAGE - 1})" class="btn-pagination" title="Anterior">
                    <i class="fa fa-chevron-left text-xs"></i>
                 </button>`;
    }

    for (let i = 1; i <= TOTAL_PAGES; i++) {
        html += `<button onclick="loadPage(${i})" class="btn-pagination ${i === CURRENT_PAGE ? 'active' : ''}">
                    ${i}
                 </button>`;
    }

    if (CURRENT_PAGE < TOTAL_PAGES) {
        html += `<button onclick="loadPage(${CURRENT_PAGE + 1})" class="btn-pagination" title="Siguiente">
                    <i class="fa fa-chevron-right text-xs"></i>
                 </button>`;
    }

    html += `</nav>`;
    container.innerHTML = html;
}

// Initial render of pagination
document.addEventListener('DOMContentLoaded', () => {
    if (typeof TOTAL_PAGES !== 'undefined') {
        renderPagination();
    }
});
