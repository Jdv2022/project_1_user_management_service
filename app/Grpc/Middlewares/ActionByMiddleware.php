<?php

namespace App\Grpc\Middlewares;

use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\ActionByUserService;

// This class is for setting up the actionActionByUserServiceByUser class
// ActionByUserService is used for populating common table columns
class ActionByMiddleware {

    public function initializeActionByUser(int $id, string $tz = 'undefined'): void {
        app()->instance(ActionByUserService::class, new ActionByUserService($id, $tz));
    }

}
