<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessPanelController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $business = $user->business;

        abort_unless(
            $business && $business->status === 'active',
            403,
            'No tienes un negocio activo asignado.'
        );

        return redirect()->route('business.dashboard', $business);
    }

    public function show(Request $request, Business $business): View
    {
        $this->authorizeBusiness($request, $business);

        return view('admin.businesses.dashboard', compact('business'));
    }

    private function authorizeBusiness(
        Request $request,
        Business $business
    ): void {
        abort_if(
            $business->status !== 'active',
            403,
            'Este negocio está suspendido.'
        );

        $hasAccess = $business->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        abort_unless(
            $hasAccess,
            403,
            'No tienes acceso a este negocio.'
        );
    }

    public function toggleReservations(
        Request $request,
        Business $business
    ): RedirectResponse {
        $this->authorizeBusiness($request, $business);

        $business->reservations_enabled = ! $business->reservations_enabled;
        $business->save();

        return back()->with(
            'success',
            $business->reservations_enabled
                ? 'Las reservas se han reanudado.'
                : 'Las reservas se han suspendido.'
        );
    }
}
