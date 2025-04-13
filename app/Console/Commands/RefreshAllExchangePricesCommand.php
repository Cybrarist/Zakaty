<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshAllExchangePricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:refresh-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh All Exchange Prices along with gold sna silver';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('exchange:price');
        Artisan::call('gold:price');
        Artisan::call('silver:price');

        Artisan::call('zakah:update-usd-amounts');

    }
}
