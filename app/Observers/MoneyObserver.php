<?php

namespace App\Observers;

use App\Helpers\ZakahHelper;
use App\Models\Money;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MoneyObserver
{
    /**
     * Handle the Money "created" event.
     */
    public function saved(Money $money): void
    {
        $user_to_update = (Auth::id() == $money->user_id) ? Auth::user() : User::findOrFail($money->user_id);
        ZakahHelper::update_money_zakah_data_for_user($user_to_update);
        ZakahHelper::set_amount_and_date_for_zakah($user_to_update);
    }

    /**
     * Handle the Money "deleted" event.
     */
    public function deleted(Money $money): void
    {
        $user_to_update = (Auth::id() == $money->user_id) ? Auth::user() : User::findOrFail($money->user_id);
        ZakahHelper::update_money_zakah_data_for_user($user_to_update);
        ZakahHelper::set_amount_and_date_for_zakah($user_to_update);
    }
}
