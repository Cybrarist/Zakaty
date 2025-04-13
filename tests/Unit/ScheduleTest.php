<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use Tests\TestCase;

class ScheduleTest extends TestCase
{

    public function testGetRefreshAllExchangePricesCommandIsScheduledDaily()
    {
        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

        $events = collect($schedule->events())->filter(function ($event) {
            return Str::contains($event->command, 'exchange:refresh-all');
        });

        $this->assertNotEmpty($events, 'The RefreshAllExchangePricesCommand is not scheduled');

        $events->each(function ($event) {
            $this->assertEquals('0 12 * * *', $event->expression, 'Command is not scheduled for 12:00 daily');
        });
    }

}
