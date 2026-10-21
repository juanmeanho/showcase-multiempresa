<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('archetype', 30);
            $table->string('zone')->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('primary_color', 20)->default('#111827');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['status', 'archetype']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
