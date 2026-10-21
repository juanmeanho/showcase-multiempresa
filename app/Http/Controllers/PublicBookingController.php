<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    public function show(Business $business): View
    {
        abort_unless($business->status === 'active', 404);

        $services = Service::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $employees = User::query()
            ->where('business_id', $business->id)
            ->where('role', 'staff')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('public.booking', compact(
            'business',
            'services',
            'employees'
        ));
    }

    public function availability(
        Request $request,
        Business $business
    ): JsonResponse {
        abort_unless($business->status === 'active', 404);

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'service_id' => ['required', 'integer'],
            'employee_id' => ['required', 'integer'],
        ]);

        $service = Service::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->findOrFail($validated['service_id']);

        $employee = User::query()
            ->where('business_id', $business->id)
            ->where('role', 'staff')
            ->where('is_active', true)
            ->findOrFail($validated['employee_id']);

        $date = Carbon::parse($validated['date']);
        $dayOfWeek = $date->dayOfWeek;

        $settings = $this->agendaSettings($business);

        $interval = max(1, (int) $settings->slot_interval_minutes);
        $closingTime = $settings->closing_time;

        $activeSlots = DB::table('employee_availability_slots')
            ->where('business_id', $business->id)
            ->where('employee_id', $employee->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->pluck('start_time')
            ->map(fn ($time) => substr((string) $time, 0, 8))
            ->all();

        $available = [];

        foreach ($activeSlots as $startTime) {
            $start = Carbon::parse($validated['date'].' '.$startTime);
            $end = $start->copy()->addMinutes($service->duration_minutes);
            $endTime = $end->format('H:i:s');

            if ($endTime > $closingTime) {
                continue;
            }

            if ($date->isToday() && $start->lessThanOrEqualTo(now())) {
                continue;
            }

            if (! $this->hasRequiredSlots(
                $start,
                (int) $service->duration_minutes,
                $interval,
                $activeSlots
            )) {
                continue;
            }

            if ($this->hasAppointmentOverlap(
                $business,
                $employee,
                $validated['date'],
                $startTime,
                $endTime
            )) {
                continue;
            }

            $available[] = [
                'start_time' => substr($startTime, 0, 5),
                'end_time' => substr($endTime, 0, 5),
            ];
        }

        return response()->json([
            'slots' => $available,
        ]);
    }

    public function store(
        Request $request,
        Business $business
    ): RedirectResponse {
        abort_unless($business->status === 'active', 404);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'service_id' => ['required', 'integer'],
            'employee_id' => ['required', 'integer'],
            'appointment_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'start_time' => ['required', 'date_format:H:i'],
        ]);

        $service = Service::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->findOrFail($validated['service_id']);

        $employee = User::query()
            ->where('business_id', $business->id)
            ->where('role', 'staff')
            ->where('is_active', true)
            ->findOrFail($validated['employee_id']);

        $date = Carbon::parse($validated['appointment_date']);
        $start = Carbon::parse(
            $validated['appointment_date'].' '.$validated['start_time']
        );
        $startTime = $start->format('H:i:s');

        $settings = $this->agendaSettings($business);
        $interval = max(1, (int) $settings->slot_interval_minutes);
        $end = $start->copy()->addMinutes($service->duration_minutes);
        $endTime = $end->format('H:i:s');

        if ($date->isToday() && $start->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages([
                'start_time' => 'Selecciona un horario futuro.',
            ]);
        }

        abort_if(
            ! $business->reservations_enabled,
            403,
            'Las reservas están suspendidas para este negocio.'
        );


        $appointment = DB::transaction(function () use (
            $business,
            $service,
            $employee,
            $validated,
            $date,
            $start,
            $startTime,
            $endTime,
            $interval
        ) {
            // Bloquea los slots del empleado y día para reducir reservas simultáneas.
            $slots = DB::table('employee_availability_slots')
                ->where('business_id', $business->id)
                ->where('employee_id', $employee->id)
                ->where('day_of_week', $date->dayOfWeek)
                ->lockForUpdate()
                ->get(['start_time', 'is_active']);

            $activeSlots = $slots
                ->where('is_active', true)
                ->pluck('start_time')
                ->map(fn ($time) => substr((string) $time, 0, 8))
                ->all();

            if (! in_array($startTime, $activeSlots, true)) {
                throw ValidationException::withMessages([
                    'start_time' => 'Ese horario ya no está disponible.',
                ]);
            }

            if (! $this->hasRequiredSlots(
                $start,
                (int) $service->duration_minutes,
                $interval,
                $activeSlots
            )) {
                throw ValidationException::withMessages([
                    'start_time' => 'No hay disponibilidad suficiente para la duración del servicio.',
                ]);
            }

            if ($this->hasAppointmentOverlap(
                $business,
                $employee,
                $validated['appointment_date'],
                $startTime,
                $endTime,
                true
            )) {
                throw ValidationException::withMessages([
                    'start_time' => 'Ese horario acaba de ser reservado. Selecciona otro.',
                ]);
            }

            return Appointment::create([
                'business_id' => $business->id,
                'service_id' => $service->id,
                'employee_id' => $employee->id,
                'customer_name' => trim($validated['customer_name']),
                'customer_phone' => trim($validated['customer_phone']),
                'appointment_date' => $validated['appointment_date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'service_name' => $service->name,
                'service_price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'status' => 'pending',
            ]);
        });

        $whatsapp = preg_replace('/\D+/', '', $business->whatsapp ?? '');

        if ($whatsapp === '') {
            return redirect()
                ->route('booking.show', $business->slug)
                ->with(
                    'success',
                    'La cita quedó registrada, pero el negocio todavía no tiene un número de WhatsApp configurado.'
                );
        }

        $message = implode("\n", [
            'Hola, quiero confirmar mi cita.',
            '',
            'Negocio: '.$business->name,
            'Cliente: '.$appointment->customer_name,
            'Teléfono: '.$appointment->customer_phone,
            'Servicio: '.$appointment->service_name,
            'Especialista: '.$employee->name,
            'Fecha: '.$date->format('d/m/Y'),
            'Horario: '.substr($startTime, 0, 5).' - '.substr($endTime, 0, 5),
            'Precio: '.$appointment->service_price,
            'Referencia de reserva: #'.$appointment->id,
        ]);

        return redirect()->away(
            'https://wa.me/'.$whatsapp.'?text='.urlencode($message)
        );
    }

    private function agendaSettings(Business $business): object
    {
        return DB::table('business_agenda_settings')
            ->where('business_id', $business->id)
            ->first() ?? (object) [
                'slot_interval_minutes' => 60,
                'opening_time' => '08:00:00',
                'closing_time' => '18:00:00',
            ];
    }

    private function hasRequiredSlots(
        Carbon $start,
        int $duration,
        int $interval,
        array $activeSlots
    ): bool {
        for ($offset = 0; $offset < $duration; $offset += $interval) {
            $requiredTime = $start->copy()
                ->addMinutes($offset)
                ->format('H:i:s');

            if (! in_array($requiredTime, $activeSlots, true)) {
                return false;
            }
        }

        return true;
    }

    private function hasAppointmentOverlap(
        Business $business,
        User $employee,
        string $date,
        string $startTime,
        string $endTime,
        bool $lock = false
    ): bool {
        $query = Appointment::query()
            ->where('business_id', $business->id)
            ->where('employee_id', $employee->id)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->exists();
    }
}