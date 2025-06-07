<?php

use User\UserServiceInterface;
use Spiral\RoadRunner\GRPC\Invoker;
use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\Worker;
use grpc\GetUserDetails\GetUserDetailsServiceInterface;
use grpc\Register\RegisterServiceInterface;
use grpc\userRegistrationFormData\UserRegistrationFormDataServiceInterface;
use grpc\getUsers\GetUsersServiceInterface;
use grpc\userClockIn\UserClockInServiceInterface;
use grpc\getAttendance\GetAttendanceInterface;
use App\Grpc\Handlers\RegisterUserHandler;
use App\Grpc\Handlers\UserDetailsHandler;
use App\Grpc\Handlers\RegistrationFormDataHandler;
use App\Grpc\Handlers\UsersHandler;
use App\Grpc\Handlers\UserClockInHandler;
use App\Grpc\Handlers\GetAttendanceHandler;
use App\Grpc\Services\CommonFunctions;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$server = new Server(new Invoker(), [
    'debug' => false, 
]);

$server->registerService(RegisterServiceInterface::class, new RegisterUserHandler(new CommonFunctions));
$server->registerService(GetUserDetailsServiceInterface::class, new UserDetailsHandler(new CommonFunctions));
$server->registerService(UserRegistrationFormDataServiceInterface::class, new RegistrationFormDataHandler(new CommonFunctions));
$server->registerService(GetUsersServiceInterface::class, new UsersHandler(new CommonFunctions));
$server->registerService(UserClockInServiceInterface::class, new UserClockInHandler(new CommonFunctions));
$server->registerService(GetAttendanceInterface::class, new GetAttendanceHandler(new CommonFunctions));

$server->serve(Worker::create());