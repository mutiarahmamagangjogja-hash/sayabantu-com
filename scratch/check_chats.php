<?php

use App\Models\ChatMessage;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$msgs = ChatMessage::all();
echo 'Total ChatMessages: '.$msgs->count()."\n";
foreach ($msgs as $m) {
    echo "ID: {$m->id} | OrderCode: '{$m->order_code}' | SenderType: {$m->sender_type} | SenderName: {$m->sender_name} | Type: {$m->message_type} | Msg: ".substr($m->message, 0, 40)."\n";
}
