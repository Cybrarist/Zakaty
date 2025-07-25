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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("name");
            $table->date('date');
            $table->string("type");
            $table->unsignedInteger("amount");
            $table->text('notes')->nullable();

            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Money::class)->constrained();
            $table->foreignIdFor(User::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
