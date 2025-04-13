<?php

namespace App\Observers;

use App\Mail\WelcomeMail;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //todo enable currency to be usd
        $user->updateQuietly([
            "settings" => [
                "consider_jeweleries_in_zakah" => 1,
                "enable_top_navbar" => 1
            ],
            "currency_id" => $user->currency_id ?? Currency::where('code', 'USD')->first()->id,
        ]);

        Mail::send(new WelcomeMail($user));

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
