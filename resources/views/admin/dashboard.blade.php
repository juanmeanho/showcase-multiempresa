
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div>
                <p class="text-sm font-medium text-indigo-600">SHOWCASE</p>
                <h1 class="text-xl font-bold text-gray-900">Administración global</h1>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">
        <p class="text-gray-600">
            Bienvenido, {{ auth()->user()->name }}.
        </p>

        <section class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Negocios</h2>
            <p class="mt-2 text-sm text-gray-500">
                Desde aquí podrás crear negocios, asignar propietarios y administrar su estado.
            </p>

            <div class="mt-6">
                <a href="{{ route('admin.businesses.index') }}"
                class="inline-flex rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Gestionar negocios
                </a>
            </div>
        </section>
    </main>
</body>
</html>
