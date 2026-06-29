<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kantine_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bundesland', 2)->default('NW');
            $table->boolean('monday_open')->default(true);
            $table->boolean('tuesday_open')->default(true);
            $table->boolean('wednesday_open')->default(true);
            $table->boolean('thursday_open')->default(true);
            $table->boolean('friday_open')->default(true);
            $table->boolean('saturday_open')->default(false);
            $table->boolean('sunday_open')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kantine_settings');
    }
};
