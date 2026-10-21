<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAvailabilitySlot;
use App\Models\User;
use App\Services\EmployeeAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessEmployeeAvailabilityController extends Controller
{
    private function authorizeOwner(Request $request): void
    {
        abort_unless(
            $request->user()->isOwner(),
            403,
            'Solo el propietario puede administrar la disponibilidad.'
        );

        abort_unless(
            $request->user()->business?->status === 'active',
            403,
            'El negocio no está activo.'
        );
    }

    private function findEmployee(Request $request, int $id): User
    {
        return $request->user()->business->staff()
            ->whereKey($id)
            ->firstOrFail();
    }

    public function index(
        Request $request,
        int $employee,
        EmployeeAvailabilityService $availability
    ): View {
        $this->authorizeOwner($request);

        $business = $request->user()->business;
        $employee = $this->findEmployee($request, $employee);

        $availability->ensureSlotsExist($business, $employee);

        $slots = EmployeeAvailabilitySlot::query()
            ->where('business_id', $business->id)
            ->where('employee_id', $employee->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view(
            'business.employees.availability',
            compact('employee', 'slots')
        );
    }

    public function toggle(
        Request $request,
        int $employee,
        int $slot
    ): RedirectResponse {
        $this->authorizeOwner($request);

        $business = $request->user()->business;
        $employee = $this->findEmployee($request, $employee);

        $availabilitySlot = EmployeeAvailabilitySlot::query()
            ->where('business_id', $business->id)
            ->where('employee_id', $employee->id)
            ->whereKey($slot)
            ->firstOrFail();

        $availabilitySlot->update([
            'is_active' => ! $availabilitySlot->is_active,
        ]);

        return redirect()
            ->route('business.employees.availability.index', $employee->id)
            ->with('success', 'Disponibilidad actualizada correctamente.');
    }
}