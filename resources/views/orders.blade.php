<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Órdenes - Fenix</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 p-4 md:p-8 font-sans">

    <div class="max-w-6xl mx-auto bg-white shadow-2xl rounded-2xl p-4 md:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Historial de Órdenes</h1>
            <button onclick="syncOrders()" id="syncBtn"
                class="w-full md:w-auto bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-2 rounded-xl shadow-lg hover:scale-105 transition-transform duration-200">
                🔄 Sincronizar Bitfinex
            </button>
        </div>

        <div id="summary" class="hidden mb-4 p-4 bg-green-100 text-green-700 rounded border border-green-200"></div>

        <div class="flex flex-col sm:flex-row gap-2 mb-6">
            <input type="number" id="searchId" placeholder="Buscar por ID" class="border p-2 rounded w-full sm:w-64 focus:ring-2 focus:ring-blue-400 outline-none">
            <button onclick="searchOrder()" class="bg-gray-800 text-white px-6 py-2 rounded hover:bg-black transition-colors">
                🔍 Buscar
            </button>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-inner">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">ID Bitfinex</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Símbolo</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Precio</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Cantidad</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Estado</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 md:px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody" class="bg-white divide-y divide-gray-200">
                    </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Editar Orden</h3>

            <input type="hidden" id="editId">

            <div id="editFields" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                    <input type="number" id="editPrice" step="0.01" class="w-full border p-2 rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                    <input type="number" id="editAmount" step="0.00001" class="w-full border p-2 rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" id="editStatus" class="w-full border p-2 rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
            </div>

            <div id="fullDetails" class="mt-6 pt-4 border-t border-gray-200 hidden">
                <h4 class="text-xs font-bold text-blue-500 uppercase mb-3 tracking-wider">Metadatos del Sistema</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs uppercase">ID Interno</p>
                        <p id="viewId" class="font-mono font-bold text-gray-700"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase">Tipo</p>
                        <p id="viewType" class="font-semibold text-gray-700"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button onclick="closeModal()" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-semibold">Cerrar</button>
                <button onclick="updateOrder()" class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-semibold shadow-md">Guardar</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/orders.js') }}"></script>
</body>
</html>
