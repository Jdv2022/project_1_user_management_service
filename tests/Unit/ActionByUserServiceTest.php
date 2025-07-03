<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Middlewares\ActionByMiddleware;
use Illuminate\Support\Facades\Redis;
use Log;
use App\Grpc\Services\ActionByUserService;

class ActionByUserServiceTest extends TestCase {
	public function test_action_by_user_service() {
		Log::info("test_action_by_user_service");
		$userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

		$userId = 1;
        $redisKey = 'user_' . $userId;
        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$actionByUserService = new ActionByUserService(1, 'UTC');

		$authUser = $actionByUserService->authUser();
		$tz = $actionByUserService->getUserTimeZone();

		$this->assertEquals($authUser['id'], $userId);
		$this->assertEquals($tz, 'UTC');
		$this->assertIsArray($authUser);
	}
}
