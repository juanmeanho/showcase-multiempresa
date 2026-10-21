
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business->name }} | Showcase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">
    <main class="mx-auto max-w-5xl">
        <header class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div>
                <p class="text-sm font-medium text-gray-500">SHOWCASE</p>

                <h1 class="mt-1 text-2xl font-bold text-gray-900">
                    {{ $business->name }}
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    {{ ucfirst($business->archetype) }}
                    @if ($business->zone)
                        · {{ $business->zone }}
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                    Cerrar sesión
                </button>
            </form>
        </header>

        <section class="mt-6">
            <h2 class="text-lg font-semibold text-gray-900">
                Administración del negocio
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Gestiona tu equipo, servicios y citas desde un mismo lugar.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <article class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900">Empleados</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Administra las personas que trabajan en tu negocio.
                    </p>
                    <p class="mt-4 text-sm text-gray-400">
                        <a href="{{ route('business.employees.index') }}"
                        class="mt-4 inline-flex rounded-lg bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-700">
                            Administrar empleados
                        </a>
                    </p>
                </article>

                
                <article class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900">Servicios</h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Configura los servicios, sus precios y duración.
                    </p>

                    <a href="{{ route('business.services.index') }}"
                    class="mt-4 inline-flex rounded-lg bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-700">
                        Administrar servicios
                    </a>
                </article>

                <article class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900">Agenda</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Organiza los horarios y las citas de tus clientes.
                    </p>

                    <a href="{{ route('business.appointments.index') }}"
                    class="mt-4 inline-flex rounded-lg bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-700">
                        Ver citas
                    </a>
                </article>


                <article class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900">Reserva pública</h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Permite que tus clientes consulten tus servicios y soliciten
                        citas en línea desde una página pública de tu negocio.
                    </p>

                    
                    <a href="{{ route('booking.show', ['business' => $business->slug]) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-4 inline-flex rounded-lg bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-700">
                        Ver página pública
                    </a>

                    <p class="mt-3 break-all text-xs text-gray-500">
                        Enlace para compartir:
                        {{ route('booking.show', ['business' => $business->slug]) }}
                    </p>

                </article>

            </div>
        </section>
    </main>
</body>
</html>
