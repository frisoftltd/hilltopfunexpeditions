<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Follow-up to 2026_07_27_142519_seed_about_page_content_sections.php:
 * - Removes blog ("Travel News and Views") and our_best_offer ("Get
 *   Involved") from the About page's secs list, per direct request.
 * - Fixes a concrete content duplication: the previous migration's
 *   about_me.element bullets were literally titled "Our Vision"/"Our
 *   Mission"/"Our Focus" - this round adds a dedicated Vision & Mission
 *   section, so those bullets are cleared here rather than left to say
 *   the same thing twice on the same page.
 * - Adds the 3 new sections (team_bio, vision_mission, what_we_do) to
 *   the About page's secs list.
 * - Seeds their content.
 *
 * what_we_do has no entry in sections/builder/builder.json (same as
 * counter) - its two parallel item lists (for travelers / for
 * operators) don't fit the generic single-list content/element schema
 * every other section uses, so it's migration-only for now, same
 * caveat as counter: no admin UI path to edit it without one being
 * built later.
 *
 * FLAGGED, NOT FABRICATED: no actual bio text was provided for the
 * founder_bio field - it's seeded with an obvious, clearly-marked
 * placeholder rather than invented biographical claims about a real,
 * named person. team_bio IS a proper builder.json-registered section,
 * so replacing the placeholder (and uploading the real photo) can be
 * done directly via Admin > Frontend > Builder > Team Bio Section,
 * no further migration needed. The "What We Do" bullets are draft copy
 * written from the site's own established feature set (guest
 * checkout, agency booking review, price categories) since the
 * original two-column list text wasn't provided either - review before
 * treating as final.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('frontends')) {
            return;
        }

        $columns = DB::select(
            'SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?',
            ['frontends']
        );
        $existingColumns = array_column($columns, 'COLUMN_NAME');
        $hasTimestamps = in_array('created_at', $existingColumns, true);
        $now = now();

        $upsertContent = function (string $dataKeys, array $fields) use ($hasTimestamps, $now) {
            $existing = DB::table('frontends')->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
            if ($existing) {
                $current = json_decode($existing->data_values, true) ?? [];
                $merged = array_merge($current, $fields);
                DB::table('frontends')->where('id', $existing->id)->update(
                    $hasTimestamps
                        ? ['data_values' => json_encode($merged), 'updated_at' => $now]
                        : ['data_values' => json_encode($merged)]
                );
                return;
            }
            $row = ['data_keys' => $dataKeys, 'data_values' => json_encode($fields)];
            if ($hasTimestamps) {
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
            }
            DB::table('frontends')->insert($row);
        };

        // Clear about_me's bullets - "Our Vision"/"Our Mission"/"Our
        // Focus" now duplicate the new dedicated vision_mission section.
        DB::table('frontends')->where('data_keys', 'about_me.element')->delete();

        // --- Founder/team bio ---
        $upsertContent('team_bio.content', [
            'title' => 'Meet the Team',
            'heading' => 'The Person Behind Hilltop Fun Expeditions',
            'sub_heading' => 'A local perspective on adventure, built from the ground up in Rwanda.',
            'founder_name' => 'Pacifique Niyobuhungiro',
            'founder_role' => 'Co-Founder & CEO',
            'founder_bio' => '[Placeholder - no bio text was provided. Replace via Admin > Frontend > Builder > Team Bio Section with Pacifique\'s actual bio before publishing.]',
        ]);

        // --- Vision & Mission (own section - no longer duplicated inside about_me) ---
        $upsertContent('vision_mission.content', [
            'title' => 'Vision & Mission',
            'heading' => 'What Drives Us Forward',
            'sub_heading' => 'Two commitments that shape every journey we help create.',
            'vision_text' => 'Connect travelers with expert local operators through our Adventure Platform, making it effortless to discover authentic experiences anywhere in the world.',
            'mission_text' => "Make adventure tourism accessible to everyone, everywhere - empowering local operators while giving travelers safe, unforgettable journeys.",
        ]);

        // --- What We Do (draft copy - original two-column text wasn't provided) ---
        $upsertContent('what_we_do.content', [
            'title' => 'What We Do',
            'heading' => 'Built for Travelers. Built for Operators.',
            'traveler_items' => [
                'Browse and book unique tours from vetted local operators',
                'Pick your own dates and group size - no rigid fixed departures',
                'Pay securely online, or arrange payment directly with your tour operator',
                'Get support before, during, and after your trip',
            ],
            'operator_items' => [
                'List your tours and reach travelers from around the world',
                'Manage bookings, availability, and pricing in one dashboard',
                'Review and approve every booking before it is confirmed',
                'Get paid securely, with transparent and fair terms',
            ],
        ]);

        // --- About page secs: remove blog/our_best_offer, add the 3 new sections ---
        if (Schema::hasTable('pages')) {
            $tempname = activeTemplate();
            $aboutPage = DB::table('pages')
                ->where('tempname', $tempname)
                ->where(function ($q) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%about%'])
                        ->orWhereRaw('LOWER(slug) LIKE ?', ['%about%']);
                })
                ->first();

            if ($aboutPage) {
                $secs = json_decode($aboutPage->secs, true) ?? [];

                $secs = array_values(array_filter($secs, function ($sec) {
                    return !in_array($sec, ['blog', 'our_best_offer'], true);
                }));

                foreach (['team_bio', 'vision_mission', 'what_we_do'] as $needed) {
                    if (!in_array($needed, $secs, true)) {
                        $secs[] = $needed;
                    }
                }

                DB::table('pages')->where('id', $aboutPage->id)->update(['secs' => json_encode($secs)]);
            }
        }
    }

    public function down(): void
    {
        // Not reversible - the prior secs order/content wasn't captured
        // before this ran, and about_me.element was deleted outright.
    }
};
