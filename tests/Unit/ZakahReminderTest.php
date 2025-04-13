<?php

namespace Tests\Unit;

use App\Enums\MoneyTypeEnum;
use App\Jobs\SendZakahReminderBeforeThirtyDaysJob;
use App\Jobs\SendZakahReminderOnPaymentDay;
use App\Mail\SendZakahReminderMail;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ZakahReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $currencies = [
            [
                'name' => 'USD',
                'code' => 'USD',
                'rate' => 1,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }

        // set the min nisab
        Cache::put('today_minimum_nisab', 50);

        User::factory()->create();

    }

    public function test_zakah_reminder_running_before_one_month(): void
    {
        $user = User::first();

        $user->update([
            'next_money_zakah_date' => today()->addMonth(),
        ]);

        $money_to_create = [
            [
                'name' => 'First',
                'amount' => 36700,
                'usd_amount' => 36700,
                'currency_id' => 1,
                'type' => MoneyTypeEnum::Cash->value
            ],
            [
                'name' => 'Second',
                'amount' => 36700,
                'usd_amount' => 36700,
                'currency_id' => 1,
                'type' => MoneyTypeEnum::Cash->value
            ],
        ];

        foreach ($money_to_create as $money) {
            $user->money()->create($money);
        }




        Mail::fake();

        $job = (new SendZakahReminderBeforeThirtyDaysJob)->withFakeQueueInteractions();

        $job->handle();

        Mail::assertQueued(SendZakahReminderMail::class);
    }

    public function test_zakah_reminder_running_on_payment_day(): void
    {
        $user = User::first();

        $user->update([
            'next_money_zakah_date' => today(),
        ]);

        $money_to_create = [
            [
                'name' => 'First',
                'amount' => 36700,
                'currency_id' => 1,
                'type' => MoneyTypeEnum::Cash->value
            ],
            [
                'name' => 'Second',
                'amount' => 367,
                'currency_id' => 1,
                'type' => MoneyTypeEnum::Cash->value
            ],
        ];

        foreach ($money_to_create as $money) {
            $user->money()->create($money);
        }

        Mail::fake();

        $job = (new SendZakahReminderOnPaymentDay)->withFakeQueueInteractions();

        $job->handle();

        Mail::assertQueued(SendZakahReminderMail::class);
    }


}
