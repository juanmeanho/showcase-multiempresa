
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negocios | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div>
                <p class="text-sm font-medium text-indigo-600">SHOWCASE</p>
                <h1 class="text-xl font-bold">Administración global</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="text-sm font-medium text-gray-600 hover:text-indigo-600">
                Volver al panel
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Negocios</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Administra los negocios registrados en la plataforma.
                </p>
            </div>
            <a href="{{ route('admin.businesses.create') }}"
               class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                + Crear negocio
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Negocio</th>
                            <th class="px-5 py-3 font-semibold">Arquetipo</th>
                            <th class="px-5 py-3 font-semibold">Propietario</th>
                            <th class="px-5 py-3 font-semibold">Zona</th>
                            <th class="px-5 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($businesses as $business)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold">{{ $business->name }}</p>
                                    <a href="{{ url('/reservar/' . $business->slug) }}"
                                    target="_blank"
                                    class="mt-1 block text-xs text-blue-600 hover:underline">
                                        /reservar/{{ $business->slug }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    {{ [
                                        'barber' => 'Barbería',
                                        'spa' => 'Spa',
                                        'nails' => 'Uñas',
                                        'academy' => 'Academia',
                                    ][$business->archetype] ?? $business->archetype }}
                                </td>
                                <td class="px-5 py-4">
                                    @forelse ($business->owners as $owner)
                                        <p>{{ $owner->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $owner->email }}</p>
                                    @empty
                                        <span class="text-gray-400">Sin propietario</span>
                                    @endforelse
                                </td>
                                <td class="px-5 py-4">{{ $business->zone ?: '—' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $business->status === 'active'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-gray-100 text-gray-600' }}">
                                        {{ $business->status === 'active' ? 'Activo' : $business->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.businesses.edit', $business) }}"
                                        class="rounded-lg bg-indigo-100 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200">
                                            Editar
                                        </a>

                                        <form method="POST"
                                            action="{{ route('admin.businesses.toggle-status', $business) }}">
                                            @csrf
                                            @method('PATCH')

                                            @if ($business->status === 'active')
                                                <button type="submit"
                                                        class="rounded-lg bg-red-100 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-200"
                                                        onclick="return confirm('¿Seguro que deseas suspender este negocio?')">
                                                    Suspender
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="rounded-lg bg-green-100 px-3 py-2 text-sm font-medium text-green-700 hover:bg-green-200"
                                                        onclick="return confirm('¿Deseas reactivar este negocio?')">
                                                    Reactivar
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                                    Todavía no hay negocios registrados.
                                    <div class="mt-3">
                                        <a href="{{ route('admin.businesses.create') }}"
                                           class="font-semibold text-indigo-600 hover:text-indigo-800">
                                            Crear el primer negocio
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($businesses->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $businesses->links() }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>
