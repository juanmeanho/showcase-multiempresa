
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear negocio | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">
    <main class="mx-auto max-w-4xl px-6 py-8">
        <a href="{{ route('admin.businesses.index') }}"
           class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
            ← Volver a negocios
        </a>

        <div class="mt-5">
            <h1 class="text-2xl font-bold">Crear negocio</h1>
            <p class="mt-1 text-sm text-gray-500">
                Registra el negocio y crea la cuenta de su propietario.
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Revisa los siguientes campos:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.businesses.store') }}"
              class="mt-6 space-y-6">
            @csrf

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Datos del negocio</h2>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-1.5 block text-sm font-medium">Nombre del negocio *</label>
                        <input id="name" name="name" value="{{ old('name') }}" required
                               maxlength="255" placeholder="Ej. Barbería Central"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="slug" class="mb-1.5 block text-sm font-medium">Slug único *</label>
                        <input id="slug" name="slug" value="{{ old('slug') }}" required
                               pattern="[A-Za-z0-9_-]+" maxlength="100"
                               placeholder="barberia-central"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-indigo-500 focus:outline-none">
                        <p class="mt-1 text-xs text-gray-500">Identificador sin espacios; debe ser único.</p>
                    </div>

                    <div>
                        <label for="archetype" class="mb-1.5 block text-sm font-medium">Arquetipo *</label>
                        <select id="archetype" name="archetype" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5">
                            <option value="">Seleccionar...</option>
                            <option value="barber" @selected(old('archetype') === 'barber')>Barbería</option>
                            <option value="spa" @selected(old('archetype') === 'spa')>Spa</option>
                            <option value="nails" @selected(old('archetype') === 'nails')>Estudio de uñas</option>
                            <option value="academy" @selected(old('archetype') === 'academy')>Academia</option>
                        </select>
                    </div>

                    <div>
                        <label for="zone" class="mb-1.5 block text-sm font-medium">Zona o ubicación</label>
                        <input id="zone" name="zone" value="{{ old('zone') }}"
                               maxlength="255" placeholder="Ej. Maturín"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    </div>

                    <div>
                        <label for="whatsapp" class="mb-1.5 block text-sm font-medium">WhatsApp</label>
                        <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                               maxlength="30" placeholder="Ej. +58..."
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    </div>

                    <div>
                        <label for="primary_color" class="mb-1.5 block text-sm font-medium">Color principal *</label>
                        <input id="primary_color" name="primary_color" type="color"
                               value="{{ old('primary_color', '#111827') }}" required
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Cuenta del propietario</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Se creará un usuario independiente para administrar este negocio.
                </p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="owner_name" class="mb-1.5 block text-sm font-medium">Nombre *</label>
                        <input id="owner_name" name="owner_name" value="{{ old('owner_name') }}"
                               required maxlength="255"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    </div>

                    <div>
                        <label for="owner_email" class="mb-1.5 block text-sm font-medium">Correo electrónico *</label>
                        <input id="owner_email" name="owner_email" type="email"
                               value="{{ old('owner_email') }}" required maxlength="255"
                               autocomplete="off"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    </div>

                    <div>
                        <label for="owner_password" class="mb-1.5 block text-sm font-medium">Contraseña inicial *</label>
                        <input id="owner_password" name="owner_password" type="password"
                               required minlength="12" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5">
                        <p class="mt-1 text-xs text-gray-500">Mínimo 12 caracteres.</p>
                    </div>

                    <div>
                        <label for="owner_password_confirmation" class="mb-1.5 block text-sm font-medium">Confirmar contraseña *</label>
                        <input id="owner_password_confirmation" name="owner_password_confirmation"
                               type="password" required minlength="12" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    </div>
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.businesses.index') }}"
                   class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Crear negocio
                </button>
            </div>
        </form>
    </main>
</body>
</html>
