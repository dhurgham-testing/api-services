<?php

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
        Schema::create('personal_access_token_abilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_access_token_id')->constrained()->onDelete('cascade');
            $table->foreignId('ability_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['personal_access_token_id', 'ability_id'], 'pat_ability_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_token_abilities');
    }
};
