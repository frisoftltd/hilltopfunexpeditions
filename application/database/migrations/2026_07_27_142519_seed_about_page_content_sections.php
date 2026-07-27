<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Wires content into the 3 reusable About-page sections identified in the
 * About-page investigation: counter.blade.php (stats bar), our_best_offer.
 * blade.php (2 CTA cards), about_me.blade.php + why_choose_us.blade.php
 * (Our Story / Core Values). All content lives in the generic `frontends`
 * table (App\Models\Frontend), keyed by data_keys ('{section}.content' /
 * '{section}.element') - the same store every section on every page reads
 * from via getContent(), not a per-page field.
 *
 * counter has no entry in sections/builder/builder.json at all, so there
 * is no admin UI path to manage its content - this migration is the only
 * way to set it, not just a convenient one. The other three sections do
 * have admin forms (Admin > Frontend > Builder), so this is a choice for
 * speed/consistency with how other content has been seeded this session,
 * not a requirement.
 *
 * IMPORTANT CAVEAT: about_me/why_choose_us content is global, not
 * About-page-scoped - if the homepage (or any other page) also has these
 * same section types in its own secs list, this will change what shows
 * there too, not just on About. Couldn't verify either way without DB
 * access. If that turns out to be true, this needs two new distinct
 * section templates instead of repurposing shared ones.
 *
 * .content rows for about_me/why_choose_us/our_best_offer are
 * update-if-exists (preserving whatever image fields are already set -
 * this migration never touches images), insert-with-null-images
 * otherwise (getImage() falls back to a generic placeholder for a
 * null/missing path, so this doesn't break rendering, per the "or
 * current placeholders" instruction). .element rows (the bullet/stat
 * items) are delete-then-insert, since those have no images to lose and
 * old placeholder bullets shouldn't linger mixed in with the new ones.
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

        $replaceElements = function (string $dataKeys, array $elements) use ($hasTimestamps, $now) {
            DB::table('frontends')->where('data_keys', $dataKeys)->delete();
            foreach ($elements as $fields) {
                $row = ['data_keys' => $dataKeys, 'data_values' => json_encode($fields)];
                if ($hasTimestamps) {
                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                }
                DB::table('frontends')->insert($row);
            }
        };

        // --- Stats bar (counter.element) - 4 items, no .content row (the
        // section has no heading of its own, just the stat cards) ---
        $replaceElements('counter.element', [
            ['counter_number' => '500', 'counter_text' => '+', 'counter_heading' => 'Organized Adventures'],
            ['counter_number' => '200', 'counter_text' => '+', 'counter_heading' => 'Trusted Operators'],
            ['counter_number' => '24', 'counter_text' => '/7', 'counter_heading' => 'Customer Support'],
            ['counter_number' => '100', 'counter_text' => '%', 'counter_heading' => 'Best Price Guarantee'],
        ]);

        // --- CTA cards (our_best_offer.content) ---
        $upsertContent('our_best_offer.content', [
            'title' => 'Get Involved',
            'heading' => "Whether You're Exploring or Hosting",
            'sub_heading' => 'Two ways to be part of the Hilltop Fun Expeditions community.',
            'left_discount_title' => 'For Travelers',
            'left_heading' => 'Ready to Start Your Next Expedition?',
            'left_button_name' => 'Explore Tours',
            'left_button_url' => route('browse'),
            'right_discount_title' => 'For Tour Operators',
            'right_heading' => 'Are You a Tour Operator?',
            'right_button_name' => 'Partner With Us',
            'right_button_url' => route('agency.register'),
        ]);

        // --- Our Story (about_me.content + about_me.element) ---
        $upsertContent('about_me.content', [
            'title' => 'Our Story',
            'heading' => 'Connecting Travelers & Local Experts',
            'sub_heading' => "Hilltop Fun Expeditions is the Organized Adventure Platform connecting travelers and travel agents. Located in Kigali, Rwanda, our global travel experts are available online 24/7. We bridge the gap between thrill-seekers and trusted, vetted local tour companies - because the best travel experiences come from passionate local guides, not massive corporations.",
        ]);
        $replaceElements('about_me.element', [
            ['title' => 'Our Vision', 'description' => 'Connect travelers with expert local operators through our Adventure Platform.'],
            ['title' => 'Our Mission', 'description' => 'Make adventure tourism accessible to everyone, everywhere.'],
            ['title' => 'Our Focus', 'description' => 'Empowering local operators while giving travelers safe, authentic experiences.'],
        ]);

        // --- Core Values (why_choose_us.content + why_choose_us.element) ---
        $upsertContent('why_choose_us.content', [
            'title' => 'Core Values',
            'heading' => 'What We Stand For',
            'sub_heading' => 'The principles that guide every trip we help create.',
        ]);
        $replaceElements('why_choose_us.element', [
            ['title' => 'Adventure & Exploration', 'description' => 'Life is meant to be lived outdoors - we champion bold, unforgettable experiences.'],
            ['title' => 'Community First', 'description' => 'Supporting local economies and ensuring fair compensation for our partners.'],
            ['title' => 'Safety & Trust', 'description' => 'Rigorous safety standards so every traveler has peace of mind.'],
        ]);

        // --- Add these 4 section templates to the About page's secs list ---
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
                foreach (['about_me', 'why_choose_us', 'counter', 'our_best_offer'] as $needed) {
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
        // Not reversible to "no content" without knowing what (if
        // anything) was there before - this only ever merged into or
        // replaced rows, never captured a full prior snapshot.
    }
};
