<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Handlers\BaseClassTest;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\ActionByUserService;
use Log;

class ActionByMiddlewareTest extends TestCase {
    public function test_user_initialization_for_saving_default_db_values() {
		Log::info("test_user_initialization_for_saving_default_db_values");
        $userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$baseClass = new BaseClassTest();
		$baseClass->initializeActionByUser(id: 1, tz: 'UTC');

		$authUser = app(ActionByUserService::class)->authUser();
		$tz = app(ActionByUserService::class)->getUserTimeZone();

		$this->assertEquals($authUser['id'], $userId);
		$this->assertEquals($tz, 'UTC');
		$this->assertIsArray($authUser);
	}

}
