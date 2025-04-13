<?php

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // manage family
            $table->string('role')->default('user');

            // Money Zakah.
            $table->unsignedBigInteger('total_money_usd')->default(0);
            $table->unsignedBigInteger('total_gold_usd')->default(0);
            $table->unsignedBigInteger('total_silver_usd')->default(0);
            $table->unsignedBigInteger('total_pay_money')->default(0);
            $table->date('next_money_zakah_date')->nullable()->default(null);

            $table->json('settings')->nullable();

            $table->foreignIdFor(Currency::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
