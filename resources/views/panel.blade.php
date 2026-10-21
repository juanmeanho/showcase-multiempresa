<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    <main class="mx-auto max-w-4xl rounded-xl bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold text-gray-900">
            Panel de Showcase
        </h1>

        <p class="mt-2 text-gray-600">
            Sesión iniciada como {{ auth()->user()->email }}.
        </p>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button class="rounded-lg bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                Cerrar sesión
            </button>
        </form>
    </main>
</body>
</html>