<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Collapses tour_packages.status from four values down to the two the
 * system actually has any use for: 1 = Active (visible/bookable),
 * 0 = Inactive (hidden from search). Confirmed via SiteController::
 * tourPackageList()'s whereIn('status', [1,2,3]) that 1/2/3 were already
 * being treated as identically visible - nothing anywhere ever sets
 * status to 2 or 3 in the current codebase (no scheduled command, no
 * departure-aware transition logic - that was fully removed along with
 * the departures model, not merely broken). Any row still showing 2 or 3
 * is a frozen leftover value from before that removal; this is a pure
 * label fix, not a behavior change, since those rows were already fully
 * visible and bookable.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        DB::table('tour_packages')->whereIn('status', [2, 3])->update(['status' => 1]);
    }

    public function down(): void
    {
        // Not reversible - the original 2/3 values carried no meaning
        // distinguishable from 1 by the time this ran, so there is
        // nothing to restore them to.
    }
};
