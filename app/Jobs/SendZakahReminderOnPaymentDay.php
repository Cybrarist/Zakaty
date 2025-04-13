<?php

namespace App\Jobs;

use App\Mail\SendZakahReminderMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendZakahReminderOnPaymentDay implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $users = User::whereDate('next_money_zakah_date', '=', today())->get();

        foreach ($users as $user) {
            Mail::to($user)
                ->queue(new SendZakahReminderMail($user, 'Today'));
        }
    }
}
