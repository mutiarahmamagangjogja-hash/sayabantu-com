<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Http\Controllers\ChatController;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

$c = new ChatController;

// Check getConversations for role customer & mitra
$req = Request::create('/api/orders/kebutuhan', 'GET');
$res = app()->handle($req);
echo 'API Orders Kebutuhan: '.$res->getContent()."\n";
