<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per agency-owned booking that reaches a commission-triggering
 * event: deducted automatically at payment-credit time (status 0 - paid/
 * collected), or recorded as owed at reservation-confirm time for
 * pay-on-arrival bookings (status 1 - owed, since no money moved through
 * the platform to deduct from). commission_rate snapshots gs()->commission_rate
 * at creation time so later rate changes don't rewrite historical figures.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->foreignId('tour_booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('booking_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
