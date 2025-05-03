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
        Schema::create('zakah_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("name")->nullable();
            $table->unsignedBigInteger("amount");
            $table->unsignedBigInteger("usd_amount");
            $table->string("type");
            $table->string("status");
            $table->string("payment_method")->nullable();

            $table->date('paid_at')->nullable();

            $table->text("notes")->nullable();
            $table->text("images")->nullable();

            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zakah_payments');
    }
};
