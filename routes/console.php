<?php

use App\Console\Commands\RefreshAllExchangePricesCommand;
use App\Jobs\SendZakahReminderBeforeThirtyDaysJob;
use App\Jobs\SendZakahReminderOnPaymentDay;

Schedule::command(RefreshAllExchangePricesCommand::class)->dailyAt('12:00');


Schedule::job(SendZakahReminderBeforeThirtyDaysJob::class)->dailyAt('12:00');
Schedule::job(SendZakahReminderOnPaymentDay::class)->dailyAt('12:00');
