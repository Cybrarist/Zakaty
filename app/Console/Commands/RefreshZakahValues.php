<?php

namespace App\Console\Commands;

use App\Helpers\ZakahHelper;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshZakahValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zakah:refresh {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Zakah values for user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user= User::firstWhere('id',$this->argument('user'));

        ZakahHelper::update_money_zakah_data_for_user($user);
        ZakahHelper::update_gold_zakah_data_for_user($user);
        ZakahHelper::update_silver_zakah_data_for_user($user);
        ZakahHelper::set_amount_and_date_for_zakah($user);

    }
}
