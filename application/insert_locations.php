<?php
/**
 * One-time script to insert Rwanda tour locations into the locations table.
 * Bypasses the Google Maps auto-fill (currently broken due to billing account issue).
 *
 * USAGE:
 * 1. Upload this file to /home/erteymdc/hilltopfunexpeditions.com/application/
 * 2. Run: php insert_locations.php
 * 3. Delete this file immediately after running.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$locations = [
    ['name' => 'Kigali',                     'location' => 'Kigali, Rwanda',              'lat' => -1.9441, 'lng' => 30.0619],
    ['name' => 'Volcanoes National Park',    'location' => 'Musanze, Rwanda',             'lat' => -1.4996, 'lng' => 29.5000],
    ['name' => 'Akagera National Park',      'location' => 'Kayonza, Rwanda',             'lat' => -1.8656, 'lng' => 30.7275],
    ['name' => 'Nyungwe National Park',      'location' => 'Nyamasheke/Rusizi, Rwanda',   'lat' => -2.5000, 'lng' => 29.2000],
    ['name' => 'Rubavu (Gisenyi)',           'location' => 'Rubavu, Rwanda',              'lat' => -1.7025, 'lng' => 29.2564],
    ['name' => 'Karongi (Kibuye)',           'location' => 'Karongi, Rwanda',             'lat' => -2.0600, 'lng' => 29.3467],
    ['name' => 'Nyanza',                     'location' => 'Nyanza, Rwanda',              'lat' => -2.3508, 'lng' => 29.7500],
    ['name' => 'Huye (Butare)',              'location' => 'Huye, Rwanda',                'lat' => -2.5967, 'lng' => 29.7392],
    ['name' => 'Lake Kivu',                  'location' => 'Lake Kivu, Rwanda',           'lat' => -1.9500, 'lng' => 29.2500],
];

$inserted = 0;
$skipped = 0;

foreach ($locations as $loc) {
    $exists = DB::table('locations')->where('name', $loc['name'])->exists();

    if ($exists) {
        echo "SKIPPED (already exists): {$loc['name']}\n";
        $skipped++;
        continue;
    }

    DB::table('locations')->insert([
        'name'       => $loc['name'],
        'location'   => $loc['location'],
        'image'      => '',
        'latitude'   => $loc['lat'],
        'longitude'  => $loc['lng'],
        'status'     => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "INSERTED: {$loc['name']}\n";
    $inserted++;
}

echo "\nDone. Inserted: {$inserted}, Skipped: {$skipped}\n";
