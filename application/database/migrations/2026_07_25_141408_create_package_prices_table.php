<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces departure_prices now that tours have no fixed departures -
 * one price per (package, category) instead of one per (departure, category).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_prices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('price_category_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['tour_package_id', 'price_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_prices');
    }
};
