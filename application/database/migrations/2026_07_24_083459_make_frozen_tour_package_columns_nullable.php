<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every column the current admin/agency tour package form actually
     * writes on store()/update() (App\Traits\TourService), plus the
     * framework-managed id/timestamps. Anything on tour_packages outside
     * this list that is NOT NULL with no default - or still carries an
     * ON UPDATE current_timestamp() clause - is a leftover from the
     * pre-departures form (person_capability, price, flexible_date,
     * tour_start, tour_end, discount) or a counter this codebase never
     * wrote at all (booking_person). Either way, an insert through the
     * new form has nothing to give MySQL's strict mode for that column,
     * so it fails. This migration nulls out the constraint wherever it
     * finds it, on whatever the live schema actually looks like, instead
     * of hardcoding column types we can't currently inspect.
     */
    private const WRITTEN_BY_FORM = [
        'id', 'user_id', 'user_type', 'title', 'address', 'description',
        'day_nights', 'duration_nights', 'category_id', 'latitude', 'longitude',
        'city', 'state', 'country', 'zip_code', 'features', 'exclusions',
        'destination_overview', 'highlights', 'itinerary', 'group_size_min',
        'group_size_max', 'guide_language', 'age_range_min', 'age_range_max',
        'intensity', 'status', 'created_at', 'updated_at',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        $columns = DB::select(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
             FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ?',
            ['tour_packages']
        );

        foreach ($columns as $column) {
            $name = $column->COLUMN_NAME;

            if (in_array($name, self::WRITTEN_BY_FORM, true)) {
                continue;
            }

            $extra = strtolower($column->EXTRA ?? '');
            if (str_contains($extra, 'auto_increment')) {
                continue;
            }

            $notNullNoDefault = $column->IS_NULLABLE === 'NO' && $column->COLUMN_DEFAULT === null;
            $hasOnUpdateClause = str_contains($extra, 'on update');

            if (!$notNullNoDefault && !$hasOnUpdateClause) {
                continue;
            }

            $isTextOrBlob = (bool) preg_match('/(blob|text|json|geometry)/i', $column->COLUMN_TYPE);

            $sql = sprintf('ALTER TABLE `tour_packages` MODIFY `%s` %s NULL', $name, $column->COLUMN_TYPE);
            if (!$isTextOrBlob) {
                $sql .= ' DEFAULT NULL';
            }

            DB::statement($sql);
        }
    }

    public function down(): void
    {
        // Not reversible: we don't know each column's original default, and
        // tour_start's previous ON UPDATE current_timestamp() was actively
        // corrupting data on every unrelated edit, so there is nothing safe
        // to restore it to.
    }
};
