const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Elementos del Modal (Cache referencias)
const editModal = document.getElementById('editModal');
const editId = document.getElementById('editId');
const editPrice = document.getElementById('editPrice');
const editAmount = document.getElementById('editAmount');
const editStatus = document.getElementById('editStatus');
const fullDetails = document.getElementById('fullDetails');
const modalTitle = document.getElementById('modalTitle');

async function loadOrders() {
    const res = await fetch('/api/orders');
    const orders = await res.json();
    const body = document.getElementById('ordersTableBody');

    if (!orders.length) {
        body.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">No hay órdenes guardadas</td></tr>`;
        return;
    }

    body.innerHTML = orders.map(order => renderOrderRow(order)).join('');
}

function renderOrderRow(order) {
    return `
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-3">${order.bitfinex_id}</td>
            <td class="px-6 py-3 font-medium">${order.symbol}</td>
            <td class="px-6 py-3">$${Number(order.price).toFixed(2)}</td>
            <td class="px-6 py-3">${order.amount}</td>
            <td class="px-6 py-3"><span class="badge">${order.status}</span></td>
            <td class="px-6 py-3">${order.type}</td>
            <td class="px-6 py-3 text-center">
                <button onclick='viewOrderDetails(${JSON.stringify(order)})' class="text-green-600 hover:underline mr-2">Ver</button>
                <button onclick='openEditModal(${JSON.stringify(order)})' class="link-blue">Editar</button>
                <button onclick="deleteOrder(${order.id})" class="link-red ml-2">Eliminar</button>
            </td>
        </tr>
    `;
}

// MODO VER: Solo lectura + Información del Sistema
function viewOrderDetails(order) {
    fillModalData(order);
    
    if (modalTitle) modalTitle.innerText = 'Ficha Técnica: ' + order.bitfinex_id;
    
    // Configuración UI
    toggleInputs(true);
    document.querySelector('.btn-green').style.display = 'none';
    
    // Inyectar Metadatos
    document.getElementById('viewId').innerText = order.id;
    document.getElementById('viewType').innerText = order.type;
    document.getElementById('viewMtsCreate').innerText = order.mts_create || 'N/A';
    document.getElementById('viewCreatedAt').innerText = formatDate(order.created_at);
    document.getElementById('viewUpdatedAt').innerText = formatDate(order.updated_at);
    
    fullDetails.classList.remove('hidden');
    editModal.classList.remove('hidden');
}

// MODO EDITAR: Escritura permitida
function openEditModal(order) {
    fillModalData(order);
    
    if (modalTitle) modalTitle.innerText = 'Editar Orden';
    
    // Configuración UI
    toggleInputs(false);
    document.querySelector('.btn-green').style.display = 'block';
    
    fullDetails.classList.add('hidden'); // Ocultar datos de la BD
    editModal.classList.remove('hidden');
}

// Funciones de apoyo
function fillModalData(order) {
    editId.value = order.id;
    editPrice.value = order.price;
    editAmount.value = order.amount;
    editStatus.value = order.status;
}

function toggleInputs(disabled) {
    editPrice.disabled = disabled;
    editAmount.disabled = disabled;
    editStatus.disabled = disabled;
}

function closeModal() {
    editModal.classList.add('hidden');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('es-ES', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

// OPERACIONES API
async function syncOrders() {
    const btn = document.getElementById('syncBtn');
    const summary = document.getElementById('summary');
    btn.disabled = true;
    btn.innerText = 'Sincronizando...';

    const res = await fetch('/api/orders/sync', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });

    const data = await res.json();
    summary.classList.remove('hidden');
    summary.innerText = `Sincronización: ${data.ordenes_nuevas_guardadas} nuevas, ${data.ordenes_ignoradas} duplicadas`;
    
    btn.innerText = '🔄 Sincronizar Bitfinex';
    btn.disabled = false;
    loadOrders();
}

async function updateOrder() {
    const id = editId.value;
    await fetch(`/api/orders/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({
            price: editPrice.value,
            amount: editAmount.value,
            status: editStatus.value
        })
    });
    closeModal();
    loadOrders();
}

async function deleteOrder(id) {
    if (!confirm('¿Eliminar esta orden?')) return;
    await fetch(`/api/orders/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    });
    loadOrders();
}

async function searchOrder() {
    const id = document.getElementById('searchId').value;
    if (!id) { loadOrders(); return; }

    try {
        const res = await fetch(`/api/orders/${id}`);
        if (res.status === 404) { alert("Orden no encontrada"); return; }

        const order = await res.json();
        const body = document.getElementById('ordersTableBody');

        body.innerHTML = renderOrderRow(order) + `
            <tr>
                <td colspan="7" class="text-center py-2">
                    <button onclick="loadOrders()" class="text-blue-500 underline text-sm">Ver todas</button>
                </td>
            </tr>`;
    } catch (error) {
        console.error("Error al buscar:", error);
    }
}

window.onload = loadOrders;
