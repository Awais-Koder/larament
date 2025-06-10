<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Mail\NewUserRegistered;
use Illuminate\Support\Facades\Mail;

class NotifySuperAdminOfNewUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Get all super_admins
        $admins = User::role('super_admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewUserRegistered($event->user));
        }
    }
}
