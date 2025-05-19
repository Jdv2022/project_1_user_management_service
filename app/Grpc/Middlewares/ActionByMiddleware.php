<?php

namespace App\Grpc\Middlewares;

use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\ActionByUserService;

class ActionByMiddleware {

    public function initializeActionByUser(int $id): void {
        app()->instance(ActionByUserService::class, new ActionByUserService($id));
    }

}
