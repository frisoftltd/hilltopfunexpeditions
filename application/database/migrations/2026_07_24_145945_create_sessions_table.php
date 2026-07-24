<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Needed for SESSION_DRIVER=database. Session expiry (both read() and gc())
 * is compared entirely in PHP (Carbon::now()->getTimestamp() against the
 * last_activity column written the same way) - unlike the file driver, it
 * never touches filemtime(), so it can't be thrown off by any gap between
 * the host OS's filesystem clock and the app's configured timezone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
