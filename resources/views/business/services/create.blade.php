
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo servicio | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    <main class="mx-auto max-w-2xl rounded-xl bg-white p-8 shadow-sm">
        <a href="{{ route('business.services.index') }}"
           class="text-sm text-gray-500 hover:text-gray-900">
            ← Volver a servicios
        </a>

        <h1 class="mt-3 text-2xl font-bold text-gray-900">
            Nuevo servicio
        </h1>

        <p class="mt-1 text-gray-600">
            Registra un servicio para tu negocio.
        </p>

        @if ($errors->any())
            <div class="mt-6 rounded-lg bg-red-50 p-4 text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" enctype="multipart/form-data" action="{{ route('business.services.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-gray-700">
                    Nombre del servicio *
                </label>
                <input id="name" name="name" type="text" required maxlength="255"
                       value="{{ old('name') }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-gray-900 focus:outline-none">
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-medium text-gray-700">
                    Descripción
                </label>
                <textarea id="description" name="description" rows="3" maxlength="2000"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-gray-900 focus:outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="price" class="mb-1 block text-sm font-medium text-gray-700">
                        Precio *
                    </label>
                    <input id="price" name="price" type="number" required min="0"
                           max="9999999999.99" step="0.01" value="{{ old('price', '0.00') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-gray-900 focus:outline-none">
                </div>

                <div>
                    <label for="duration_minutes" class="mb-1 block text-sm font-medium text-gray-700">
                        Duración (minutos) *
                    </label>
                    <input id="duration_minutes" name="duration_minutes" type="number"
                           required min="1" max="65535"
                           value="{{ old('duration_minutes', 30) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-gray-900 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium">
                    Imagen del servicio
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="mt-1 block w-full"
                >

                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1 text-sm text-gray-500">
                    Formatos JPG, PNG o WEBP. Máximo 4 MB.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <a href="{{ route('business.services.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-gray-900 px-5 py-2 text-white hover:bg-gray-700">
                    Guardar servicio
                </button>
            </div>
        </form>
    </main>
</body>
</html>
