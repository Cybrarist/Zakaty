<?php

use App\Models\Currency;
use App\Models\Money;
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
        Schema::create('realestates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            //General Fields
            $table->string("name")->nullable();
            $table->string("type")->nullable();
            $table->text('coordinates')->nullable();
            $table->string("method_of_ownership");
            $table->string("ownership_reason");
            $table->text('notes')->nullable();
            $table->text("images")->nullable();
            $table->text("documents")->nullable();
            $table->unsignedInteger("value")->nullable();
            $table->unsignedInteger("usd_value")->nullable();

            //selling Fields
            $table->date('decided_to_sell_at')->nullable();
            $table->date('sold_at')->nullable();

            //Renting Fields
            $table->unsignedInteger("rent_amount")->nullable();
            $table->unsignedInteger("rent_usd_amount")->nullable();
            $table->string("rent_type")->nullable();
            $table->foreignIdFor(Money::class)->nullable()->constrained();


            $table->date('zakah_at')->nullable();

            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Currency::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realestates');
    }
};
