<?php

$data = json_decode(file_get_contents('storage/app/admin_realtime_state.json'), true);
echo 'Keys: '.implode(', ', array_keys($data)).PHP_EOL;
echo 'Recent orders count: '.count($data['recent_orders'] ?? []).PHP_EOL;
echo 'Recent activities count: '.count($data['recent_activities'] ?? []).PHP_EOL;
echo 'Orders detail count: '.count($data['orders_detail'] ?? []).PHP_EOL;
foreach ($data['orders_detail'] ?? [] as $k => $od) {
    echo " - Order detail {$k}: {$od['code']} | {$od['customer']} | {$od['mitra']}\n";
}
