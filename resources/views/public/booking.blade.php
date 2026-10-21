<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reservar | {{ $business->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
          rel="stylesheet">

    <style>
        :root {
            --accent: {{ match($business->archetype) {
                'spa' => '#f472b6',
                'nails' => '#fb7185',
                'academy' => '#a855f7',
                default => '#d4af37',
            } }};
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #020617;
            color: #f1f5f9;
        }

        .font-luxury { font-family: 'Playfair Display', serif; }

        .accent-text { color: var(--accent); }
        .accent-bg { background: var(--accent); color: #020617; }
        .accent-border { border-color: var(--accent); }

        .service-card {
            transition: border-color .2s, background .2s, transform .2s;
        }

        .service-card.selected {
            border-color: var(--accent);
            background: rgba(255,255,255,.09);
            box-shadow: 0 0 0 1px var(--accent);
        }

        .time-button.selected {
            background: var(--accent);
            color: #020617;
            border-color: var(--accent);
            font-weight: 800;
        }

        .time-button:disabled {
            opacity: .3;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .form-field {
            width: 100%;
            background: #020617;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 12px;
            color: #f8fafc;
            outline: none;
        }

        .form-field:focus {
            border-color: var(--accent);
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { scrollbar-width: none; }

        .primary-button {
            background: var(--accent);
            color: #020617;
            transition: opacity .2s, transform .2s;
        }

        .primary-button:hover { opacity: .9; }
        .primary-button:active { transform: scale(.99); }
        .primary-button:disabled { opacity: .4; cursor: not-allowed; }

        .flatpickr-calendar {
            border: 1px solid #334155;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .4);
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #020617;
            font-weight: 800;
        }

        .flatpickr-day.today {
            border-color: var(--accent);
        }

        .flatpickr-day:hover {
            background: #334155;
            border-color: #334155;
            color: #fff;
        }

        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month,
        .flatpickr-weekdays,
        span.flatpickr-weekday {
            color: #f8fafc;
        }
    </style>
</head>

<body class="min-h-screen">

<header class="sticky top-0 z-30 border-b border-slate-800 bg-slate-950/95 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
        <a href="{{ route('booking.show', $business->slug) }}"
           class="flex min-w-0 items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                 style="background:var(--accent);color:#020617">
                @php
                    $businessIcon = match ($business->archetype) {
                        'spa' => 'fa-spa',
                        'nails' => 'fa-hand-sparkles',
                        'academy' => 'fa-graduation-cap',
                        default => 'fa-scissors',
                    };
                @endphp
                <i class="fa-solid {{ $businessIcon }} text-lg"></i>
            </div>

            <div class="min-w-0">
                <h1 class="truncate text-sm font-extrabold text-white sm:text-base">
                    {{ $business->name }}
                </h1>
                <p class="text-xs text-slate-400">
                    <i class="fa-solid fa-calendar-check mr-1 accent-text"></i>
                    Reservas en línea
                </p>
            </div>
        </a>

        <span class="hidden rounded-full border border-slate-700 px-3 py-1.5 text-xs text-slate-300 sm:inline-flex">
            <i class="fa-solid fa-circle-check mr-1.5 accent-text"></i>
            Agenda disponible
        </span>
    </div>
</header>

<main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-300">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-200">
            <p class="mb-2 font-bold">Revisa los datos de la reserva:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mb-8 overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-900 to-slate-950 p-5 sm:p-8">
        <div class="grid gap-6 sm:grid-cols-[1fr_auto] sm:items-center">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-[.22em] accent-text">
                    Tu próxima experiencia comienza aquí
                </p>

                <h2 class="font-luxury text-3xl leading-tight text-white sm:text-4xl">
                    Reserva tu cita
                    <span class="block accent-text">en pocos pasos.</span>
                </h2>

                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-400">
                    Elige el servicio, encuentra un horario disponible y envía los detalles de tu reserva por WhatsApp.
                </p>

                @if ($business->zone)
                    <p class="mt-4 text-sm text-slate-300">
                        <i class="fa-solid fa-location-dot mr-2 accent-text"></i>
                        {{ $business->zone }}
                    </p>
                @endif
            </div>

            <div class="hidden h-24 w-24 items-center justify-center rounded-full border sm:flex"
                 style="border-color:var(--accent);background:rgba(255,255,255,.03)">
                <i class="fa-solid {{ $businessIcon }} text-4xl accent-text"></i>
            </div>
        </div>
    </section>

    @if (! $business->reservations_enabled)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-5 text-center">
            <p class="font-semibold text-amber-900">
                Reservas suspendidas
            </p>

            <p class="mt-1 text-sm text-amber-800">
                En este momento no estamos aceptando nuevas reservas.
            </p>
        </div>
    @else
        {{-- Aquí conserva el formulario actual de reserva --}}
            <form id="booking-form"
                method="POST"
                action="{{ route('booking.store', $business->slug) }}">
                @csrf

                <div class="grid items-start gap-6 lg:grid-cols-[1.25fr_.75fr]">

                    <div class="space-y-6">

                        <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl accent-bg font-extrabold">1</span>
                                <div>
                                    <h3 class="font-bold text-white">Selecciona tu servicio</h3>
                                    <p class="text-xs text-slate-400">Elige el servicio que deseas reservar.</p>
                                </div>
                            </div>

                            @if ($services->isEmpty())
                                <p class="rounded-xl border border-slate-700 p-4 text-sm text-slate-400">
                                    Actualmente no hay servicios disponibles para reservar.
                                </p>
                            @else
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($services as $service)
                                        <button type="button"
                                                class="service-card group overflow-hidden rounded-2xl border border-slate-700 bg-slate-950 text-left"
                                                data-service-id="{{ $service->id }}"
                                                data-service-name="{{ $service->name }}"
                                                data-service-price="{{ $service->price }}"
                                                data-service-duration="{{ $service->duration_minutes }}"
                                                aria-pressed="false">

                                            @if ($service->image_path)
                                                <img
                                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($service->image_path) }}"
                                                    alt="{{ $service->name }}"
                                                    loading="lazy"
                                                    class="h-36 w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                                            @else
                                                <div class="flex h-28 items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950">
                                                    <i class="fa-solid {{ $businessIcon }} text-3xl accent-text"></i>
                                                </div>
                                            @endif

                                            <div class="p-3.5">
                                                <div class="flex items-start justify-between gap-2">
                                                    <h4 class="text-sm font-bold text-white">
                                                        {{ $service->name }}
                                                    </h4>
                                                    <i class="fa-regular fa-circle accent-text service-check mt-0.5"></i>
                                                </div>

                                                @if ($service->description)
                                                    <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-400">
                                                        {{ $service->description }}
                                                    </p>
                                                @endif

                                                <div class="mt-3 flex items-center justify-between gap-2">
                                                    <span class="text-base font-extrabold accent-text">
                                                        {{ number_format((float) $service->price, 2, ',', '.') }}
                                                    </span>
                                                    <span class="text-xs text-slate-400">
                                                        <i class="fa-regular fa-clock mr-1"></i>
                                                        {{ $service->duration_minutes }} min
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            <input type="hidden" name="service_id" id="service_id" value="{{ old('service_id') }}">
                        </section>

                        <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl accent-bg font-extrabold">2</span>
                                <div>
                                    <h3 class="font-bold text-white">Especialista y horario</h3>
                                    <p class="text-xs text-slate-400">Los horarios dependen del día y del especialista.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="employee_id" class="mb-1.5 block text-xs font-semibold text-slate-300">
                                        Especialista
                                    </label>
                                    <select id="employee_id" name="employee_id" required class="form-field">
                                        <option value="">Selecciona un especialista</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                @selected(old('employee_id') == $employee->id)>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($employees->isEmpty())
                                        <p class="mt-2 text-xs text-amber-300">
                                            No hay especialistas activos disponibles para reservar.
                                        </p>
                                    @endif
                                </div>


                        <div>
                            <label for="appointment_date" class="mb-1.5 block text-xs font-semibold text-slate-300">
                                Fecha de la cita
                            </label>

                            <div class="relative">
                                <i class="fa-regular fa-calendar-days pointer-events-none absolute left-4 top-1/2 z-10 -translate-y-1/2 accent-text"></i>

                                <input
                                    type="text"
                                    id="appointment_date"
                                    name="appointment_date"
                                    value="{{ old('appointment_date', now()->format('Y-m-d')) }}"
                                    required
                                    readonly
                                    placeholder="Selecciona una fecha"
                                    class="form-field cursor-pointer pl-11 pr-4"
                                    autocomplete="off">
                            </div>

                            <p class="mt-2 text-xs text-slate-500">
                                <i class="fa-solid fa-calendar-check mr-1 accent-text"></i>
                                Selecciona el día de tu cita en el calendario.
                            </p>
                        </div>


                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <label class="text-xs font-semibold text-slate-300">
                                            Horarios disponibles
                                        </label>
                                        <span id="availability-hint" class="text-right text-xs text-slate-500">
                                            Selecciona servicio, especialista y fecha
                                        </span>
                                    </div>

                                    <div id="time-slots" class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                                        <p class="col-span-full rounded-xl border border-dashed border-slate-700 p-4 text-center text-xs text-slate-500">
                                            Los horarios aparecerán aquí.
                                        </p>
                                    </div>

                                    <input type="hidden"
                                        id="start_time"
                                        name="start_time"
                                        value="{{ old('start_time') }}"
                                        required>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-6 lg:sticky lg:top-24">
                        <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl accent-bg font-extrabold">3</span>
                                <div>
                                    <h3 class="font-bold text-white">Tus datos</h3>
                                    <p class="text-xs text-slate-400">Para identificar tu reserva.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="customer_name" class="mb-1.5 block text-xs font-semibold text-slate-300">
                                        Nombre y apellido
                                    </label>
                                    <input
                                        type="text"
                                        id="customer_name"
                                        name="customer_name"
                                        maxlength="255"
                                        required
                                        autocomplete="name"
                                        value="{{ old('customer_name') }}"
                                        placeholder="Ej. María González"
                                        class="form-field">
                                </div>

                                <div>
                                    <label for="customer_phone" class="mb-1.5 block text-xs font-semibold text-slate-300">
                                        Número de teléfono
                                    </label>
                                    <input
                                        type="tel"
                                        id="customer_phone"
                                        name="customer_phone"
                                        maxlength="30"
                                        required
                                        autocomplete="tel"
                                        value="{{ old('customer_phone') }}"
                                        placeholder="Ej. +58 412 1234567"
                                        class="form-field">
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
                            <h3 class="mb-4 flex items-center gap-2 font-bold text-white">
                                <i class="fa-solid fa-receipt accent-text"></i>
                                Resumen de tu cita
                            </h3>

                            <div class="space-y-3 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-slate-400">Servicio</span>
                                    <span id="summary-service" class="text-right font-semibold text-slate-200">Por seleccionar</span>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-slate-400">Precio</span>
                                    <span id="summary-price" class="font-bold accent-text">—</span>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-slate-400">Especialista</span>
                                    <span id="summary-employee" class="text-right text-slate-200">Por seleccionar</span>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-slate-400">Fecha</span>
                                    <span id="summary-date" class="text-right text-slate-200">Por seleccionar</span>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-slate-400">Hora</span>
                                    <span id="summary-time" class="font-bold text-slate-200">Por seleccionar</span>
                                </div>
                            </div>

                            <div class="my-5 border-t border-slate-800"></div>

                            <button
                                id="submit-booking"
                                type="submit"
                                @disabled($services->isEmpty() || $employees->isEmpty())
                                class="primary-button flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3.5 text-sm font-extrabold">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                                <span>Confirmar y enviar por WhatsApp</span>
                            </button>

                            <p class="mt-3 text-center text-xs leading-5 text-slate-500">
                                Tu cita se registra en el sistema antes de abrir WhatsApp. El mensaje se enviará cuando lo confirmes desde esa aplicación.
                            </p>
                        </section>
                    </aside>
                </div>
            </form>
    @endif


</main>

<footer class="mt-8 border-t border-slate-900 px-4 py-5 text-center text-xs text-slate-500">
    <span class="font-semibold text-slate-400">{{ $business->name }}</span>
    <span class="mx-1">·</span>
    Reservas en línea
</footer>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>

    flatpickr("#appointment_date", {
        locale: "es",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        defaultDate: document.getElementById("appointment_date").value,
        minDate: "today",
        disableMobile: true,
        allowInput: false,
        monthSelectorType: "static",

        // Deshabilitar sábados y domingos
        disable: [
            function (date) {
                return date.getDay() === 0 || date.getDay() === 6;
            }
        ],

        onChange: function () {
            updateSummary();
            loadAvailability();
        }
    });

    const serviceCards = [...document.querySelectorAll('.service-card')];
    const serviceInput = document.getElementById('service_id');
    const employeeInput = document.getElementById('employee_id');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('start_time');
    const slotsContainer = document.getElementById('time-slots');
    const hint = document.getElementById('availability-hint');
    const submitButton = document.getElementById('submit-booking');

    const availabilityUrl = @json(route('booking.availability', $business->slug));
    const currency = @json($business->archetype === 'barber' ? '$' : '$');

    function selectedServiceCard() {
        return serviceCards.find(card => card.dataset.serviceId === serviceInput.value);
    }

    function updateSummary() {
        const card = selectedServiceCard();
        const employee = employeeInput.options[employeeInput.selectedIndex];

        document.getElementById('summary-service').textContent =
            card ? card.dataset.serviceName : 'Por seleccionar';

        document.getElementById('summary-price').textContent =
            card ? currency + Number(card.dataset.servicePrice).toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '—';

        document.getElementById('summary-employee').textContent =
            employee && employee.value ? employee.textContent.trim() : 'Por seleccionar';

        document.getElementById('summary-date').textContent =
            dateInput.value
                ? new Date(dateInput.value + 'T12:00:00').toLocaleDateString('es-VE')
                : 'Por seleccionar';

        document.getElementById('summary-time').textContent =
            timeInput.value || 'Por seleccionar';
    }

    function clearSelectedTime() {
        timeInput.value = '';
        document.getElementById('summary-time').textContent = 'Por seleccionar';
    }

    function renderEmptySlots(message) {
        slotsContainer.replaceChildren();

        const paragraph = document.createElement('p');
        paragraph.className =
            'col-span-full rounded-xl border border-dashed border-slate-700 p-4 text-center text-xs text-slate-500';
        paragraph.textContent = message;

        slotsContainer.appendChild(paragraph);
    }

    async function loadAvailability() {
        clearSelectedTime();
        updateSummary();

        if (!serviceInput.value || !employeeInput.value || !dateInput.value) {
            renderEmptySlots('Selecciona servicio, especialista y fecha.');
            hint.textContent = 'Faltan datos';
            return;
        }

        renderEmptySlots('Consultando disponibilidad...');
        hint.textContent = 'Consultando...';

        const params = new URLSearchParams({
            service_id: serviceInput.value,
            employee_id: employeeInput.value,
            date: dateInput.value
        });

        try {
            const response = await fetch(availabilityUrl + '?' + params.toString(), {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('No se pudo consultar la disponibilidad.');
            }

            const data = await response.json();
            slotsContainer.replaceChildren();

            if (!data.slots || data.slots.length === 0) {
                renderEmptySlots('No hay horarios disponibles para esta selección.');
                hint.textContent = 'Sin horarios';
                return;
            }

            data.slots.forEach(slot => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className =
                    'time-button rounded-xl border border-slate-700 bg-slate-950 px-2 py-3 text-sm transition hover:border-slate-400';
                button.textContent = slot.start_time;

                button.addEventListener('click', () => {
                    document.querySelectorAll('.time-button').forEach(item => {
                        item.classList.remove('selected');
                        item.setAttribute('aria-pressed', 'false');
                    });

                    button.classList.add('selected');
                    button.setAttribute('aria-pressed', 'true');
                    timeInput.value = slot.start_time;
                    updateSummary();
                });

                slotsContainer.appendChild(button);
            });

            hint.textContent = data.slots.length + ' horario(s) disponible(s)';
        } catch (error) {
            renderEmptySlots('No se pudo consultar la disponibilidad. Inténtalo de nuevo.');
            hint.textContent = 'Error de consulta';
        }
    }

    serviceCards.forEach(card => {
        card.addEventListener('click', () => {
            serviceCards.forEach(item => {
                item.classList.remove('selected');
                item.setAttribute('aria-pressed', 'false');

                const check = item.querySelector('.service-check');
                if (check) {
                    check.classList.remove('fa-circle-check');
                    check.classList.add('fa-circle');
                }
            });

            card.classList.add('selected');
            card.setAttribute('aria-pressed', 'true');

            const check = card.querySelector('.service-check');
            if (check) {
                check.classList.remove('fa-circle');
                check.classList.add('fa-circle-check');
            }

            serviceInput.value = card.dataset.serviceId;
            updateSummary();
            loadAvailability();
        });
    });

    employeeInput.addEventListener('change', () => {
        updateSummary();
        loadAvailability();
    });

    dateInput.addEventListener('change', () => {
        updateSummary();
        loadAvailability();
    });

    document.getElementById('booking-form').addEventListener('submit', event => {
        if (!serviceInput.value || !employeeInput.value ||
            !dateInput.value || !timeInput.value) {
            event.preventDefault();
            alert('Selecciona un servicio, especialista, fecha y horario disponible.');
        }
    });

    const oldServiceId = @json((string) old('service_id', ''));
    if (oldServiceId && serviceCards.some(card => card.dataset.serviceId === oldServiceId)) {
        const previousCard = serviceCards.find(card => card.dataset.serviceId === oldServiceId);
        previousCard.click();
    } else {
        updateSummary();
        renderEmptySlots('Selecciona un servicio, especialista y fecha para ver los horarios.');
    }

    if (employeeInput.value && dateInput.value && serviceInput.value) {
        loadAvailability();
    }
</script>

</body>
</html>