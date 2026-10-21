
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar empleado | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    <main class="mx-auto max-w-2xl">
        <a href="{{ route('business.employees.index') }}"
           class="text-sm text-gray-500 hover:text-gray-900">
            ← Volver a empleados
        </a>

        <section class="mt-4 rounded-xl bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">Editar empleado</h1>

            @if ($errors->any())
                <div class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('business.employees.update', $employee->id) }}"
                  class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">
                        Nombre completo
                    </label>
                    <input id="name" name="name"
                           value="{{ old('name', $employee->name) }}"
                           required maxlength="255"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
                        Correo electrónico
                    </label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email', $employee->email) }}"
                           required maxlength="255"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
                        Nueva contraseña (opcional)
                    </label>
                    <input id="password" name="password" type="password"
                           minlength="12" autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    <p class="mt-1 text-xs text-gray-500">
                        Déjala vacía para conservar la contraseña actual.
                    </p>
                </div>

                <div>
                    <label for="password_confirmation"
                           class="mb-1 block text-sm font-medium text-gray-700">
                        Confirmar nueva contraseña
                    </label>
                    <input id="password_confirmation" name="password_confirmation"
                           type="password" minlength="12" autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('business.employees.index') }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="rounded-lg bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
