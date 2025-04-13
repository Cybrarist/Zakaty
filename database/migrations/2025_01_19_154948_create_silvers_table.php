<?php

use App\Enums\KaratEnum;
use App\Enums\PreciousMetalColorEnum;
use App\Enums\WeightEnum;
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
        Schema::create('silvers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name', 255)->nullable();
            $table->unsignedInteger('weight')->default(0);
            $table->unsignedInteger('usd_amount')->default(0);
            $table->string('weight_unit')->default(WeightEnum::Gram->value);
            $table->unsignedTinyInteger('karat')->default(KaratEnum::Karat24->value);
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->text('images')->nullable();
            $table->text('notes')->nullable();

            $table->foreignIdFor(User::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('silvers');
    }
};
