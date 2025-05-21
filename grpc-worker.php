<?php

use User\UserServiceInterface;
use Spiral\RoadRunner\GRPC\Invoker;
use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\Worker;
use grpc\GetUserDetails\GetUserDetailsServiceInterface;
use grpc\Register\RegisterServiceInterface;
use grpc\userRegistrationFormData\UserRegistrationFormDataServiceInterface;
use App\Grpc\Controllers\RegisterUserController;
use App\Grpc\Controllers\UserDetailsController;
use App\Grpc\Controllers\RegistrationFormDataController;
use App\Grpc\Services\CommonFunctions;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$server = new Server(new Invoker(), [
    'debug' => false, 
]);

$server->registerService(RegisterServiceInterface::class, new RegisterUserController(new CommonFunctions));
$server->registerService(GetUserDetailsServiceInterface::class, new UserDetailsController(new CommonFunctions));
$server->registerService(UserRegistrationFormDataServiceInterface::class, new RegistrationFormDataController(new CommonFunctions));

$server->serve(Worker::create());