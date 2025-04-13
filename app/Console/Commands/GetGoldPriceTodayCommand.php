<?php

namespace App\Console\Commands;

use App\Models\ExchangePrice;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetGoldPriceTodayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gold:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Get Today's Gold Price";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response= Http::withHeader("x-access-token" , config('settings.precious_metal_price_api_key'))
            ->get("https://www.goldapi.io/api/XAU/USD")->json();

        if (Arr::exists($response , "error")){
            Log::error("Something Wrong Happened Getting Gold Prices for date" . today());
            return;
        }

        $wanted_settings=[
            [
                "name"=>"gold_price_24",
                "api_key"=>"price_gram_24k"
            ],
            [
                "name"=>"gold_price_22",
                "api_key"=>"price_gram_22k"
            ],
            [
                "name"=>"gold_price_21",
                "api_key"=>"price_gram_21k"
            ],
            [
                "name"=>"gold_price_20",
                "api_key"=>"price_gram_20k"
            ],
            [
                "name"=>"gold_price_18",
                "api_key"=>"price_gram_18k"
            ],
            [
                "name"=>"gold_price_16",
                "api_key"=>"price_gram_16k"
            ],
            [
                "name"=>"gold_price_14",
                "api_key"=>"price_gram_14k"
            ],
            [
                "name"=>"gold_price_10",
                "api_key"=>"price_gram_10k"
            ],
        ];


        foreach ($wanted_settings as $setting)
            ExchangePrice::updateOrCreate([
                "name" => $setting["name"]
            ],[
                "value" => $response[$setting["api_key"]]
            ]);


        Cache::forget("gold_prices");
    }
}
