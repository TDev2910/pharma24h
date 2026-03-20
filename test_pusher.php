<?php
require __DIR__.'/vendor/autoload.php';

use Pusher\Pusher;

$app_id = '2129330';
$app_key = '6cac0c642eb827f1dc56';
$app_secret = 'b5cd5e745717ae48c219';
$app_cluster = 'ap1';

$pusher = new Pusher(
    $app_key,
    $app_secret,
    $app_id,
    [
        'cluster' => $app_cluster,
        'useTLS' => true
    ]
);

try {
    echo "Attempting to trigger event...\n";
    $pusher->trigger('test-channel', 'test-event', ['message' => 'hello world']);
    echo "Event triggered successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
