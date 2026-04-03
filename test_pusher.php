<?php
require __DIR__.'/vendor/autoload.php';

use Pusher\Pusher;

if (file_exists(__DIR__.'/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$app_id = $_ENV['PUSHER_APP_ID'] ?? '';
$app_key = $_ENV['PUSHER_APP_KEY'] ?? '';
$app_secret = $_ENV['PUSHER_APP_SECRET'] ?? '';
$app_cluster = $_ENV['PUSHER_APP_CLUSTER'] ?? 'ap1';

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
