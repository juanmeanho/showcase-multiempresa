
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-4 sm:p-6">
    <main class="mx-auto max-w-7xl rounded-xl bg-white p-5 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('business.dashboard', auth()->user()->business) }}"
                   class="text-sm text-gray-500 hover:text-gray-900">
                    ← Volver al panel
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900">
                    Citas
                </h1>

                <p class="mt-1 text-gray-600">
                    Consulta y administra las citas de tu negocio.
                </p>
            </div>
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

        {{-- Filtros --}}
        <form method="GET"
              action="{{ route('business.appointments.index') }}"
              class="mt-6 grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2 lg:grid-cols-4">

            <div>
                <label for="search" class="mb-1 block text-sm font-medium text-gray-700">
                    Buscar
                </label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Cliente, teléfono o servicio"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500"
                >
            </div>

            <div>
                <label for="date" class="mb-1 block text-sm font-medium text-gray-700">
                    Fecha de la cita
                </label>
                <input
                    type="date"
                    name="date"
                    id="date"
                    value="{{ $filters['date'] ?? '' }}"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500"
                >
            </div>

            <div>
                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
                    Estado
                </label>
                <select
                    name="status"
                    id="status"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500"
                >
                    <option value="">Todos los estados</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>
                        Pendiente
                    </option>
                    <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>
                        Confirmada
                    </option>
                    <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>
                        Cancelada
                    </option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>
                        Completada
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                >
                    Filtrar
                </button>

                <a
                    href="{{ route('business.appointments.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Limpiar
                </a>
            </div>
        </form>

        {{-- Tabla --}}
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-left text-sm">
                <thead class="border-b bg-gray-50 text-gray-600">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3">Fecha y hora</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Servicio</th>
                        <th class="px-4 py-3">Especialista</th>
                        <th class="whitespace-nowrap px-4 py-3">Importe</th>
                        <th class="px-4 py-3">Estado</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-4">
                                <p class="font-medium text-gray-900">
                                    {{ $appointment->appointment_date->format('d/m/Y') }}
                                </p>
                                <p class="mt-1 text-gray-500">
                                    {{ substr($appointment->start_time, 0, 5) }}
                                    –
                                    {{ substr($appointment->end_time, 0, 5) }}
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                <p class="font-medium text-gray-900">
                                    {{ $appointment->customer_name }}
                                </p>
                                <a
                                    href="tel:{{ $appointment->customer_phone }}"
                                    class="mt-1 inline-block text-gray-500 hover:text-blue-700"
                                >
                                    {{ $appointment->customer_phone }}
                                </a>
                            </td>

                            <td class="px-4 py-4">
                                <p class="font-medium text-gray-900">
                                    {{ $appointment->service_name }}
                                </p>
                                <p class="mt-1 text-gray-500">
                                    {{ $appointment->duration_minutes }} min
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                {{ $appointment->employee?->name ?? 'Sin asignar' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 font-medium text-gray-900">
                                {{ number_format((float) $appointment->service_price, 2, ',', '.') }}
                            </td>

                            <td class="px-4 py-4">
                                @switch($appointment->status)
                                    @case('pending')
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800">
                                            Pendiente
                                        </span>
                                        @break

                                    @case('confirmed')
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                            Confirmada
                                        </span>
                                        @break

                                    @case('cancelled')
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                            Cancelada
                                        </span>
                                        @break

                                    @case('completed')
                                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                            Completada
                                        </span>
                                        @break
                                @endswitch

                                <form
                                    method="POST"
                                    action="{{ route('business.appointments.update-status', $appointment->id) }}"
                                    class="mt-3 flex flex-col gap-2"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        aria-label="Cambiar estado de la cita"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-gray-500 focus:ring-gray-500"
                                    >
                                        <option value="pending" @selected($appointment->status === 'pending')>
                                            Pendiente
                                        </option>
                                        <option value="confirmed" @selected($appointment->status === 'confirmed')>
                                            Confirmada
                                        </option>
                                        <option value="cancelled" @selected($appointment->status === 'cancelled')>
                                            Cancelada
                                        </option>
                                        <option value="completed" @selected($appointment->status === 'completed')>
                                            Completada
                                        </option>
                                    </select>

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white hover:bg-gray-700"
                                    >
                                        Actualizar estado
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                No se encontraron citas con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    </main>
</body>
</html>
