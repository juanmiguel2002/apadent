<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddAdminCCToMails
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
    public function handle(object $event)
    {
        // Obtener los correos de los usuarios con el rol "Admin"
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->pluck('email')->toArray();

        // Agregar a los administradores en copia (CC)
        if (!empty($admins)) {
            foreach ($admins as $adminEmail) {
                $event->message->addCc($adminEmail);
            }
        }
    }
}
