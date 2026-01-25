<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Check DB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-bold mb-4">📦 Estado de la Base de Datos</h1>

    <p class="mb-2"><strong>Total registros:</strong> {{ $total }}</p>
    <p class="mb-6 text-sm text-gray-600"><strong>DB:</strong> {{ $db_path }}</p>

    <table class="w-full border text-sm">
        <thead class="bg-gray-200">
            <tr>
                @foreach($orders->first()?->getAttributes() ?? [] as $key => $value)
                    <th class="border px-2 py-1">{{ $key }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr class="hover:bg-gray-50">
                    @foreach($order->getAttributes() as $value)
                        <td class="border px-2 py-1">{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
