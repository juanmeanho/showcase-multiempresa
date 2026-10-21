
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    <main class="mx-auto max-w-6xl rounded-xl bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('business.dashboard', auth()->user()->business) }}"
                   class="text-sm text-gray-500 hover:text-gray-900">
                    ← Volver al panel
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900">
                    Servicios
                </h1>

                <p class="mt-1 text-gray-600">
                    Administra los servicios de tu negocio.
                </p>
            </div>

            <a href="{{ route('business.services.create') }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                + Nuevo servicio
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-lg bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-lg bg-red-50 p-4 text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Servicio</th>
                        <th class="px-4 py-3">Precio</th>
                        <th class="px-4 py-3">Duración</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($services as $service)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-medium text-gray-900">
                                    {{ $service->name }}
                                </p>
                                @if ($service->description)
                                    <p class="mt-1 max-w-md text-gray-500">
                                        {{ $service->description }}
                                    </p>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ number_format((float) $service->price, 2, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ $service->duration_minutes }} min
                            </td>

                            <td class="px-4 py-4">
                                @if ($service->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-3">
                                    <a href="{{ route('business.services.edit', $service) }}"
                                       class="font-medium text-blue-700 hover:text-blue-900">
                                        Editar
                                    </a>

                                    <form method="POST"
                                          action="{{ route('business.services.toggle-status', $service) }}"
                                          onsubmit="return confirm('¿Deseas cambiar el estado de este servicio?')">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="font-medium text-gray-700 hover:text-gray-950">
                                            {{ $service->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                Todavía no tienes servicios registrados.
                                <div class="mt-3">
                                    <a href="{{ route('business.services.create') }}"
                                       class="font-medium text-blue-700 hover:underline">
                                        Crear el primer servicio
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $services->links() }}
        </div>
    </main>
</body>
</html>
