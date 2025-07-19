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
use grpc\userClockOut\UserClockOutServiceInterface;
use grpc\TeamLists\TeamListsServiceInterface;
use grpc\getAttendance\GetAttendanceInterface;
use grpc\CreateShift\CreateShiftServiceInterface;
use grpc\AssignUserShift\AssignUserShiftServiceInterface;
use grpc\GetArchives\GetArchivesServiceInterface;
use grpc\AddArchive\AddArchiveServiceInterface;
use grpc\getLogs\GetLogsServiceInterface;
use grpc\Overview\OverviewServiceInterface;
use grpc\EditUserDetails\EditUserDetailsServiceInterface;
use grpc\CreateTeam\CreateTeamServiceInterface;
use grpc\EditTeam\EditTeamServiceInterface;
use grpc\TeamUsersLists\TeamUsersListsServiceInterface;
use grpc\SuggestedMember\SuggestedMemberServiceInterface;
use App\Grpc\Handlers\RegisterUserHandler;
use App\Grpc\Handlers\UserDetailsHandler;
use App\Grpc\Handlers\RegistrationFormDataHandler;
use App\Grpc\Handlers\UsersHandler;
use App\Grpc\Handlers\UserClockInHandler;
use App\Grpc\Handlers\UserClockOutHandler;
use App\Grpc\Handlers\GetAttendanceHandler;
use App\Grpc\Handlers\TeamListsHandler;
use App\Grpc\Handlers\CreateShiftHandler;
use App\Grpc\Handlers\AssignUserShiftHandler;
use App\Grpc\Handlers\GetArchivesHandler;
use App\Grpc\Handlers\AddArchiveHandler;
use App\Grpc\Handlers\GetLogsHandler;
use App\Grpc\Handlers\OverviewHandler;
use App\Grpc\Handlers\EditUserDetailsHandler;
use App\Grpc\Handlers\GetTeamUsersListsHandler;
use App\Grpc\Handlers\CreateTeamHandler;
use App\Grpc\Handlers\SuggestedMemberHandler;
use App\Grpc\Handlers\EditTeamHandler;
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
$server->registerService(UserClockOutServiceInterface::class, new UserClockOutHandler(new CommonFunctions));
$server->registerService(GetAttendanceInterface::class, new GetAttendanceHandler(new CommonFunctions));
$server->registerService(TeamListsServiceInterface::class, new TeamListsHandler(new CommonFunctions));
$server->registerService(TeamUsersListsServiceInterface::class, new GetTeamUsersListsHandler(new CommonFunctions));
$server->registerService(CreateTeamServiceInterface::class, new CreateTeamHandler(new CommonFunctions));
$server->registerService(CreateShiftServiceInterface::class, new CreateShiftHandler(new CommonFunctions));
$server->registerService(AssignUserShiftServiceInterface::class, new AssignUserShiftHandler(new CommonFunctions));
$server->registerService(GetArchivesServiceInterface::class, new GetArchivesHandler(new CommonFunctions));
$server->registerService(AddArchiveServiceInterface::class, new AddArchiveHandler(new CommonFunctions));
$server->registerService(GetLogsServiceInterface::class, new GetLogsHandler(new CommonFunctions));
$server->registerService(OverviewServiceInterface::class, new OverviewHandler(new CommonFunctions));
$server->registerService(EditUserDetailsServiceInterface::class, new EditUserDetailsHandler(new CommonFunctions));
$server->registerService(SuggestedMemberServiceInterface::class, new SuggestedMemberHandler(new CommonFunctions));
$server->registerService(EditTeamServiceInterface::class, new EditTeamHandler(new CommonFunctions));

$server->serve(Worker::create());