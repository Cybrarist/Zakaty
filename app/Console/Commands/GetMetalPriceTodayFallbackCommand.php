<?php

namespace App\Console\Commands;

use App\Models\ExchangePrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GetMetalPriceTodayFallbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metal:fallback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'in case gold and silver api fails we run the following';


    private const float TROY_OUNCE_TO_GRAM = 31.1035;


    private const float PURITY_22K= 0.91667;
    private const float PURITY_21K= 0.875;
    private const float PURITY_20K= 0.83333;
    private const float PURITY_18K= 0.75;
    private const float PURITY_16K= 0.66667;
    private const float PURITY_14K= 0.58333;
    private const float PURITY_10K= 0.41667;


    /**
     * Execute the console command.
     */
    public function handle()
    {

        $check_if_gold_fetched = ExchangePrice::whereDate('updated_at', today())
            ->where('value' , '>' , 0)
            ->whereLike('name' , 'gold_%')
            ->count();

        $check_if_silver_fetched = ExchangePrice::whereDate('updated_at', today())
            ->where('value' , '>' , 0)
            ->whereLike('name' , 'silver_%')
            ->count();

        if ($check_if_silver_fetched > 0 && $check_if_gold_fetched > 0){
            return;
        }


        $response = Http::withUserAgent("Mozilla/5.0 (Linux; Android 7.1; LG-H930 Build/NRD90M) AppleWebKit/602.42 (KHTML, like Gecko)  Chrome/49.0.1450.386 Mobile Safari/536.3")
        ->get("https://data-asg.goldprice.org/dbXRates/USD")
        ->json();


        $gram_gold_price = $response['items'][0]['xauPrice'] / self::TROY_OUNCE_TO_GRAM;
        $gram_silver_price = $response['items'][0]['xagPrice'] / self::TROY_OUNCE_TO_GRAM;

        $values=[
            [
                "name"=>"gold_price_24",
                "value"=> $gram_gold_price
            ],
            [
                "name"=>"gold_price_22",
                "value"=> $gram_gold_price * self::PURITY_22K
            ],
            [
                "name"=>"gold_price_21",
                "value"=> $gram_gold_price * self::PURITY_21K
            ],
            [
                "name"=>"gold_price_20",
                "value"=> $gram_gold_price * self::PURITY_20K
            ],
            [
                "name"=>"gold_price_18",
                "value"=> $gram_gold_price * self::PURITY_18K
            ],
            [
                "name"=>"gold_price_16",
                "value"=> $gram_gold_price * self::PURITY_16K
            ],
            [
                "name"=>"gold_price_14",
                "value"=> $gram_gold_price * self::PURITY_14K
            ],
            [
                "name"=>"gold_price_10",
                "value"=> $gram_gold_price * self::PURITY_10K
            ],[
                "name"=>"silver_price_24",
                "value"=> $gram_silver_price
            ],
            [
                "name"=>"silver_price_22",
                "value"=> $gram_silver_price * self::PURITY_22K
            ],
            [
                "name"=>"silver_price_21",
                "value"=> $gram_silver_price * self::PURITY_21K
            ],
            [
                "name"=>"silver_price_20",
                "value"=> $gram_silver_price * self::PURITY_20K
            ],
            [
                "name"=>"silver_price_18",
                "value"=> $gram_silver_price * self::PURITY_18K
            ],
            [
                "name"=>"silver_price_16",
                "value"=> $gram_silver_price * self::PURITY_16K
            ],
            [
                "name"=>"silver_price_14",
                "value"=> $gram_silver_price * self::PURITY_14K
            ],
            [
                "name"=>"silver_price_10",
                "value"=> $gram_silver_price * self::PURITY_10K
            ],
        ];


        foreach ($values as $value)
            ExchangePrice::updateOrCreate([
                "name" => $value["name"]
            ],[
                "value" => $value["value"]
            ]);
    }
}
