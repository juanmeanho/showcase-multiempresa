<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\EmployeeAvailabilityService;
use Illuminate\Support\Facades\DB;

class BusinessEmployeeController extends Controller
{
    private function authorizeOwner(Request $request): void
    {
        abort_unless(
            $request->user()->isOwner(),
            403,
            'Solo el propietario puede administrar empleados.'
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

    public function index(Request $request): View
    {
        $this->authorizeOwner($request);

        $employees = $request->user()->business->staff()
            ->latest()
            ->paginate(10);

        return view('business.employees.index', compact('employees'));
    }

    public function create(Request $request): View
    {
        $this->authorizeOwner($request);

        return view('business.employees.create');
    }

    public function store(
        Request $request,
        EmployeeAvailabilityService $availability
    ): RedirectResponse {
        $this->authorizeOwner($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
            ],
        ]);

        $business = $request->user()->business;

        DB::transaction(function () use (
            $business,
            $validated,
            $availability
        ) {
            $employee = $business->staff()->create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'role' => 'staff',
                'is_active' => true,
            ]);

            $availability->ensureSlotsExist($business, $employee);
        });

        return redirect()
            ->route('business.employees.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Request $request, int $employee): View
    {
        $this->authorizeOwner($request);

        $employee = $this->findEmployee($request, $employee);

        return view('business.employees.edit', compact('employee'));
    }

    public function update(
        Request $request,
        int $employee
    ): RedirectResponse {
        $this->authorizeOwner($request);

        $employee = $this->findEmployee($request, $employee);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee->id),
            ],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $employee->update($data);

        return redirect()
            ->route('business.employees.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function toggleStatus(
        Request $request,
        int $employee
    ): RedirectResponse {
        $this->authorizeOwner($request);

        $employee = $this->findEmployee($request, $employee);

        $employee->update([
            'is_active' => ! $employee->is_active,
        ]);

        return redirect()
            ->route('business.employees.index')
            ->with(
                'success',
                $employee->is_active
                    ? 'Empleado activado correctamente.'
                    : 'Empleado desactivado correctamente.'
            );
    }
}
