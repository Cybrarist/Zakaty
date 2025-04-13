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
        Schema::create('money', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("name", 255);
            $table->unsignedInteger("amount")->default(0);
            $table->unsignedInteger("usd_amount")->default(0);
            $table->string('type');
            $table->text("images")->nullable();
            $table->text("notes")->nullable();


            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Currency::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money');
    }
};
