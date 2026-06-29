<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kantine_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->date('end_date')->nullable();
            $table->string('name');
            $table->string('type', 20);
            $table->string('bundesland', 2);
            $table->unsignedSmallInteger('year');
            $table->timestamps();

            $table->index(['bundesland', 'year']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kantine_holidays');
    }
};
