<?php

namespace App\Console\Commands;

use App\Helpers\CacheHelper;
use App\Helpers\ZakahHelper;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateAllPricesToUsdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zakah:update-usd-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all Prices to USD';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $gold_prices= CacheHelper::get_gold_prices();
        $silver_prices= CacheHelper::get_silver_prices();

        User::with(['silver','money','gold' , 'money.currency'])
            ->get()
            ->each(function ($user) use ($gold_prices, $silver_prices) {

                $user->money->each(function ($single_money_account) {
                    $single_money_account->updateQuietly([
                        'usd_amount' =>  $single_money_account->amount  / $single_money_account->currency->rate
                    ]);
                });


                $user->gold->each(function ($gold_account) use ($gold_prices) {
                    $gold_account->updateQuietly([
                        'usd_amount' => $gold_account->weight_in_grams *  $gold_prices['gold_price_' . $gold_account->karat->value]
                    ]);
                });

                $user->silver->each(function ($silver_account) use ($silver_prices) {
                    $silver_account->updateQuietly([
                        'usd_amount' => $silver_account->weight_in_grams *  $silver_prices['silver_price_' . $silver_account->karat->value]
                    ]);
                });


                Artisan::call('zakah:refresh',[
                    'user' => $user->id,
                ]);


            });


    }
}
