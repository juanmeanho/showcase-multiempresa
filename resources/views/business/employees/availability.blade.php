
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilidad | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    @php
        $days = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    @endphp

    <main class="mx-auto max-w-5xl space-y-6">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('business.employees.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-900">
                    ← Volver a empleados
                </a>

                <h1 class="mt-2 text-2xl font-bold text-gray-900">
                    Disponibilidad de {{ $employee->name }}
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Activa o desactiva los horarios de cada día.
                </p>
            </div>
        </header>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @foreach ($days as $dayNumber => $dayName)
            <section class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="mb-4 font-semibold text-gray-900">
                    {{ $dayName }}
                </h2>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                    @forelse ($slots->get($dayNumber, collect()) as $slot)
                        <div class="flex items-center justify-between gap-2 rounded-lg border p-3
                            {{ $slot->is_active
                                ? 'border-green-200 bg-green-50'
                                : 'border-gray-200 bg-gray-50' }}">

                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ substr($slot->start_time, 0, 5) }}
                                </p>

                                <p class="text-xs {{ $slot->is_active
                                    ? 'text-green-700'
                                    : 'text-gray-500' }}">
                                    {{ $slot->is_active ? 'Activo' : 'Inactivo' }}
                                </p>
                            </div>

                            <form method="POST"
                                  action="{{ route(
                                      'business.employees.availability.toggle',
                                      [$employee->id, $slot->id]
                                  ) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="rounded-md border border-gray-300 bg-white px-2 py-1 text-xs hover:bg-gray-100">
                                    {{ $slot->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">
                            No hay slots configurados para este día.
                        </p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </main>
</body>
</html>
