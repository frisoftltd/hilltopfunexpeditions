<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Run only after the code cutover (schema/models/services/controllers/
 * forms/details-widget/display-blade commits) has been deployed and
 * confirmed clean - package_prices already carries forward whatever
 * pricing these held. Child table first: departure_prices has a
 * cascading FK to tour_departures.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('departure_prices');
        Schema::dropIfExists('tour_departures');
    }

    public function down(): void
    {
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->unsignedInteger('seats_total');
            $table->unsignedInteger('seats_booked')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('departure_prices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('tour_departure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('price_category_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['tour_departure_id', 'price_category_id']);
        });
    }
};
