<?php

use User\UserServiceInterface;
use Spiral\RoadRunner\GRPC\Invoker;
use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\Worker;
use grpc\GetUserDetails\GetUserDetailsServiceInterface;
use grpc\GetUserDetails\RegisterServiceInterface;
use App\Grpc\Controllers\UserDetailsController;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$server = new Server(new Invoker(), [
    'debug' => false, 
]);

$server->registerService(GetUserDetailsServiceInterface::class, new UserDetailsController());

$server->serve(Worker::create());