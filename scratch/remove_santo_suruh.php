<?php

$files = [
    'resources/views/layouts/mitra.blade.php',
    'resources/views/mitra/dashboard.blade.php',
    'resources/views/customer/partials/mobile-modals.blade.php',
    'resources/views/customer/partials/customer-modals.blade.php',
    'resources/views/customer/index.blade.php',
    'resources/views/public/index.blade.php',
];

foreach ($files as $f) {
    $full = __DIR__.'/../'.$f;
    if (file_exists($full)) {
        $c = file_get_contents($full);
        $c = str_replace('Santo Suruh', 'SayaBantu', $c);
        file_put_contents($full, $c);
        echo "Cleaned {$f}\n";
    }
}
