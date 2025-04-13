<?php

namespace App\Observers;

use App\Helpers\ZakahHelper;
use App\Models\Silver;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SilverObserver
{
    public function saved(Silver $silver): void
    {
        $user_to_update = (Auth::id() == $silver->user_id) ? Auth::user() : User::findOrFail($silver->user_id);
        ZakahHelper::update_silver_zakah_data_for_user($user_to_update);
        ZakahHelper::set_amount_and_date_for_zakah($user_to_update);
    }

    /**
     * Handle the Silver "deleted" event.
     */
    public function deleted(Silver $silver): void
    {
        $user_to_update = (Auth::id() == $silver->user_id) ? Auth::user() : User::findOrFail($silver->user_id);
        ZakahHelper::update_silver_zakah_data_for_user($user_to_update);
        ZakahHelper::set_amount_and_date_for_zakah($user_to_update);
    }
}
