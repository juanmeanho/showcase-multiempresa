<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class BusinessServiceController extends Controller
{
    private function authorizeOwner(Request $request): void
    {
        abort_unless(
            $request->user()->isOwner(),
            403,
            'Solo el propietario puede administrar servicios.'
        );

        abort_unless(
            $request->user()->business?->status === 'active',
            403,
            'El negocio no está activo.'
        );
    }

    private function findService(Request $request, int $id): Service
    {
        return $request->user()
            ->business
            ->services()
            ->whereKey($id)
            ->firstOrFail();
    }

    public function index(Request $request): View
    {
        $this->authorizeOwner($request);

        $services = $request->user()
            ->business
            ->services()
            ->latest()
            ->paginate(10);

        return view('business.services.index', compact('services'));
    }

    public function create(Request $request): View
    {
        $this->authorizeOwner($request);

        return view('business.services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeOwner($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:65535'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $imagePath = $request->file('image')?->store('services', 'public');

        unset($validated['image']);

        $request->user()->business->services()->create([
            ...$validated,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()
            ->route('business.services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Request $request, int $service): View
    {
        $this->authorizeOwner($request);

        $service = $this->findService($request, $service);

        return view('business.services.edit', compact('service'));
    }

    public function update(Request $request, int $service): RedirectResponse
    {
        $this->authorizeOwner($request);

        $service = $this->findService($request, $service);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:65535'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('services', 'public');

            $oldImagePath = $service->image_path;

            $validated['image_path'] = $newImagePath;

            $service->update($validated);

            if ($oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            $service->update($validated);
        }

        return redirect()
            ->route('business.services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function toggleStatus(Request $request, int $service): RedirectResponse
    {
        $this->authorizeOwner($request);

        $service = $this->findService($request, $service);

        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        return redirect()
            ->route('business.services.index')
            ->with(
                'success',
                $service->is_active
                    ? 'Servicio activado correctamente.'
                    : 'Servicio desactivado correctamente.'
            );
    }
}
