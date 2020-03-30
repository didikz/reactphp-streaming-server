<?php

define('public_path', realpath('../public/'));

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/VideoStreaming.php';

use React\Http\Server;
use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

$loop = Factory::create();

$filesystem = new VideoStreaming(Filesystem::create($loop));

$server = new Server($filesystem);

$socket = New \React\Socket\Server('127.0.0.1:8000', $loop);

$server->on('error', function (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
