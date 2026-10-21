<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar negocio | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-6 py-4">
            <div>
                <p class="text-sm font-medium text-indigo-600">SHOWCASE</p>
                <h1 class="text-xl font-bold">Editar negocio</h1>
            </div>
            <a href="{{ route('admin.businesses.index') }}"
               class="text-sm font-medium text-gray-600 hover:text-indigo-600">
                Volver a negocios
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-6 py-8">
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Revisa los siguientes errores:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.businesses.update', $business) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold">Datos del negocio</h2>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium">Nombre</label>
                        <input id="name" name="name" required
                               value="{{ old('name', $business->name) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label for="slug" class="mb-1 block text-sm font-medium">Slug</label>
                        <input id="slug" name="slug" required
                               value="{{ old('slug', $business->slug) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label for="archetype" class="mb-1 block text-sm font-medium">Arquetipo</label>
                        <select id="archetype" name="archetype" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            @foreach ([
                                'barber' => 'Barbería',
                                'spa' => 'Spa',
                                'nails' => 'Uñas',
                                'academy' => 'Academia',
                            ] as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('archetype', $business->archetype) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="zone" class="mb-1 block text-sm font-medium">Zona</label>
                        <input id="zone" name="zone"
                               value="{{ old('zone', $business->zone) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label for="whatsapp" class="mb-1 block text-sm font-medium">WhatsApp</label>
                        <input id="whatsapp" name="whatsapp"
                               value="{{ old('whatsapp', $business->whatsapp) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label for="primary_color" class="mb-1 block text-sm font-medium">Color principal</label>
                        <input id="primary_color" name="primary_color" type="color" required
                               value="{{ old('primary_color', $business->primary_color ?: '#4f46e5') }}"
                               class="h-10 w-full rounded-lg border border-gray-300 px-2 py-1">
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold">Propietario</h2>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="owner_name" class="mb-1 block text-sm font-medium">Nombre</label>
                        <input id="owner_name" name="owner_name" required
                               value="{{ old('owner_name', $owner?->name) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label for="owner_email" class="mb-1 block text-sm font-medium">Correo electrónico</label>
                        <input id="owner_email" name="owner_email" type="email" required
                               value="{{ old('owner_email', $owner?->email) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label for="owner_password" class="mb-1 block text-sm font-medium">
                            {{ $owner ? 'Nueva contraseña (opcional)' : 'Contraseña' }}
                        </label>
                        <input id="owner_password" name="owner_password" type="password"
                               minlength="12" autocomplete="new-password"
                               {{ $owner ? '' : 'required' }}
                               class="w-full rounded-lg border border-gray-300 px-3 py-2"
                               placeholder="{{ $owner ? 'Dejar vacía para no cambiarla' : 'Mínimo 12 caracteres' }}">
                    </div>

                    <div>
                        <label for="owner_password_confirmation"
                               class="mb-1 block text-sm font-medium">Confirmar contraseña</label>
                        <input id="owner_password_confirmation"
                               name="owner_password_confirmation" type="password"
                               minlength="12" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2"
                               placeholder="Repite la nueva contraseña">
                    </div>
                </div>

                <p class="mt-3 text-xs text-gray-500">
                    La contraseña actual no se puede recuperar para mostrarla.
                    Si deseas cambiarla, introduce una nueva y confírmala.
                </p>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.businesses.index') }}"
                   class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </main>
</body>
</html>