<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\RegistrationResponse as BaseRegistrationResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterResponse extends BaseRegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Auth::user();
        If($user) {
            if (! $user->hasRole('storage_manager')) {
                $user->assignRole('storage_manager');
            }
            if (auth()->check()) {
                event(new Registered(auth()->user()));
            }
        }
        
        // ✅ Redirect to Laravel's built-in email verification notice page
        return redirect()->route('verification.notice');
    }
}
