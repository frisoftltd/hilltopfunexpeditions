<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks how many abandoned-checkout reminders a status-0 booking has
 * received (0 = none, 1 = first reminder sent, 2 = second/final reminder
 * sent), so the reminder cron doesn't re-send the same stage every run.
 * See SendAbandonedBookingReminders.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_stage')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropColumn('reminder_stage');
        });
    }
};
