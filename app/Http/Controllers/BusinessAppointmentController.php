<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class BusinessAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user->business && $user->business->status === 'active',
            403
        );

        $query = Appointment::query()
            ->where('business_id', $user->business_id)
            ->with(['service', 'employee'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time');

        // Filtrar por fecha.
        $query->when(
            $request->filled('date'),
            fn ($q) => $q->whereDate(
                'appointment_date',
                $request->input('date')
            )
        );

        // Filtrar por estado.
        $query->when(
            $request->filled('status'),
            fn ($q) => $q->where(
                'status',
                $request->input('status')
            )
        );

        // Buscar por cliente, teléfono o servicio.
        $query->when(
            $request->filled('search'),
            function ($q) use ($request) {
                $search = trim($request->input('search'));

                $q->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('service_name', 'like', "%{$search}%");
                });
            }
        );

        $appointments = $query
            ->paginate(15)
            ->withQueryString();

        return view('business.appointments.index', [
            'appointments' => $appointments,
            'filters' => $request->only(['date', 'status', 'search']),
        ]);
    }


    public function updateStatus(Request $request, int $appointment): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user->business && $user->business->status === 'active',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['pending', 'confirmed', 'cancelled', 'completed']),
            ],
        ]);

        $appointmentRecord = Appointment::query()
            ->where('business_id', $user->business_id)
            ->findOrFail($appointment);

        $appointmentRecord->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('business.appointments.index', $request->query())
            ->with('success', 'El estado de la cita se actualizó correctamente.');
    }

}
