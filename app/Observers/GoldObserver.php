<?php

namespace App\Observers;

use App\Helpers\ZakahHelper;
use App\Models\Gold;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoldObserver
{
    /**
     * Handle the Gold "created" event.
//     */
    public function saved(Gold $gold): void
    {
        $user_to_update = (Auth::id() == $gold->user_id) ? Auth::user() : User::findOrFail($gold->user_id);
        ZakahHelper::update_gold_zakah_data_for_user($user_to_update);
        ZakahHelper::set_amount_and_date_for_zakah($user_to_update);

    }

    /**
     * Handle the Gold "deleted" event.
     */
    public function deleted(Gold $gold): void
    {
        $user_to_update = (Auth::id() == $gold->user_id) ? Auth::user() : User::findOrFail($gold->user_id);
        ZakahHelper::update_gold_zakah_data_for_user($user_to_update);
        ZakahHelper::set_amount_and_date_for_zakah($user_to_update);
    }
}
