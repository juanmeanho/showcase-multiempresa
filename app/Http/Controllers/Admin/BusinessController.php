<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(): View
    {
        $businesses = Business::with('owners')
            ->latest()
            ->paginate(10);

        return view('admin.businesses.index', compact('businesses'));
    }

    public function create(): View
    {
        return view('admin.businesses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                'unique:businesses,slug',
            ],
            'archetype' => [
                'required',
                Rule::in(['barber', 'spa', 'nails', 'academy']),
            ],
            'zone' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'owner_password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
            ],
        ], [
            'slug.alpha_dash' => 'El slug solo puede contener letras, números, guiones y guiones bajos.',
            'slug.unique' => 'Ese slug ya está siendo utilizado.',
            'owner_email.unique' => 'Ya existe un usuario con ese correo.',
            'owner_password.confirmed' => 'Las contraseñas del propietario no coinciden.',
            'owner_password.min' => 'La contraseña debe tener al menos 12 caracteres.',
        ]);

        DB::transaction(function () use ($validated) {
            $business = Business::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'archetype' => $validated['archetype'],
                'zone' => $validated['zone'] ?? null,
                'whatsapp' => $validated['whatsapp'] ?? null,
                'primary_color' => $validated['primary_color'],
                'status' => 'active',
            ]);

            User::create([
                'name' => $validated['owner_name'],
                'email' => strtolower($validated['owner_email']),
                'password' => $validated['owner_password'],
                'business_id' => $business->id,
                'role' => 'owner',
            ]);
        });

        return redirect()
            ->route('admin.businesses.index')
            ->with(
                'success',
                'El negocio y su propietario fueron creados correctamente.'
            );
    }

    public function toggleStatus(Business $business): RedirectResponse
    {
        $business->update([
            'status' => $business->status === 'active'
                ? 'suspended'
                : 'active',
        ]);

        $message = $business->status === 'active'
            ? 'El negocio fue reactivado correctamente.'
            : 'El negocio fue suspendido correctamente.';

        return redirect()
            ->route('admin.businesses.index')
            ->with('success', $message);
    }
}
