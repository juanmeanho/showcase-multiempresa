
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    <main class="mx-auto max-w-5xl">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('business.dashboard', auth()->user()->business) }}"
                   class="text-sm text-gray-500 hover:text-gray-900">
                    ← Volver al panel
                </a>
                <h1 class="mt-2 text-2xl font-bold text-gray-900">Empleados</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Administra el equipo de tu negocio.
                </p>
            </div>

            <a href="{{ route('business.employees.create') }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                + Nuevo empleado
            </a>
        </header>

        @if (session('success'))
            <div class="mt-5 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nombre</th>
                            <th class="px-5 py-3 font-medium">Correo</th>
                            <th class="px-5 py-3 font-medium">Estado</th>
                            <th class="px-5 py-3 text-right font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="px-5 py-4 font-medium text-gray-900">
                                    {{ $employee->name }}
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    {{ $employee->email }}
                                </td>
                                <td class="px-5 py-4">
                                    @if ($employee->is_active)
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                            Activo
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-medium text-gray-600">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('business.employees.edit', $employee->id) }}"
                                           class="font-medium text-blue-700 hover:underline">
                                            Editar
                                        </a>

                                        <a href="{{ route('business.employees.availability.index', $employee->id) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            Disponibilidad
                                        </a>

                                        <form method="POST"
                                              action="{{ route('business.employees.toggle-status', $employee->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="font-medium {{ $employee->is_active ? 'text-red-700' : 'text-green-700' }} hover:underline">
                                                {{ $employee->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                                    Todavía no tienes empleados registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($employees->hasPages())
                <div class="border-t border-gray-100 p-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>
