const csrf = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');

async function loadOrders() {
    const res = await fetch('/api/orders');
    const orders = await res.json();
    const body = document.getElementById('ordersTableBody');

    if (!orders.length) {
        body.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-gray-500">
                    No hay órdenes guardadas
                </td>
            </tr>`;
        return;
    }

    body.innerHTML = orders.map(order => `
        <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-3">${order.bitfinex_id}</td>
            <td class="px-6 py-3 font-medium">${order.symbol}</td>
            <td class="px-6 py-3">$${Number(order.price).toFixed(2)}</td>
            <td class="px-6 py-3">${order.amount}</td>
            <td class="px-6 py-3">
                <span class="badge">${order.status}</span>
            </td>
            <td class="px-6 py-3">${order.type}</td>
            <td class="px-6 py-3 text-center">
                <button onclick='openEditModal(${JSON.stringify(order)})'
                    class="link-blue">Editar</button>
                <button onclick="deleteOrder(${order.id})"
                    class="link-red">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

async function syncOrders() {
    const btn = document.getElementById('syncBtn');
    const summary = document.getElementById('summary');

    btn.disabled = true;
    btn.innerText = 'Sincronizando...';

    const res = await fetch('/api/orders/sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        }
    });

    const data = await res.json();
    summary.classList.remove('hidden');
    summary.innerText =
        `Sincronización exitosa: ${data.ordenes_nuevas_guardadas} nuevas,
         ${data.ordenes_ignoradas} duplicadas`;

    btn.innerText = '🔄 Sincronizar Bitfinex';
    btn.disabled = false;

    loadOrders();
}

function openEditModal(order) {
    editId.value = order.id;
    editPrice.value = order.price;
    editAmount.value = order.amount;
    editStatus.value = order.status;
    editModal.classList.remove('hidden');
}

function closeModal() {
    editModal.classList.add('hidden');
}

async function updateOrder() {
    const id = editId.value;

    await fetch(`/api/orders/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
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
    if (!id) {
        loadOrders(); // Si está vacío, recarga toda la lista
        return;
    }

    try {
        const res = await fetch(`/api/orders/${id}`);
        
        if (res.status === 404) {
            alert("Orden no encontrada en la base de datos");
            return;
        }

        const order = await res.json();
        const body = document.getElementById('ordersTableBody');

        // Mostramos solo la orden encontrada en la tabla
        body.innerHTML = `
            <tr class="border-b bg-blue-50">
                <td class="px-6 py-3">${order.bitfinex_id}</td>
                <td class="px-6 py-3 font-medium">${order.symbol}</td>
                <td class="px-6 py-3">$${Number(order.price).toFixed(2)}</td>
                <td class="px-6 py-3">${order.amount}</td>
                <td class="px-6 py-3"><span class="badge">${order.status}</span></td>
                <td class="px-6 py-3">${order.type}</td>
                <td class="px-6 py-3 text-center">
                    <button onclick='openEditModal(${JSON.stringify(order)})' class="link-blue">Editar</button>
                    <button onclick="deleteOrder(${order.id})" class="link-red">Eliminar</button>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="text-center py-2">
                    <button onclick="loadOrders()" class="text-blue-500 underline text-sm">Ver todas las órdenes</button>
                </td>
            </tr>`;
    } catch (error) {
        console.error("Error al buscar:", error);
    }
}

window.onload = loadOrders;


