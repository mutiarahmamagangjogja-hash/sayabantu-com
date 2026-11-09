<?php

use App\Models\Mitra;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== USERS ===\n";
foreach (User::all() as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: {$u->role} | Phone: {$u->phone}\n";
}

echo "\n=== MITRAS ===\n";
foreach (Mitra::all() as $m) {
    echo "ID: {$m->id} | User ID: {$m->user_id} | Name: {$m->name} | Status: {$m->status} | Layanan: {$m->layanan_utama}\n";
}

echo "\n=== ORDERS ===\n";
foreach (Order::all() as $o) {
    echo "ID: {$o->id} | Code: {$o->code} | Status: {$o->status} | Customer: {$o->customer_name} | Mitra: {$o->mitra_name} | Layanan: {$o->layanan}\n";
}
