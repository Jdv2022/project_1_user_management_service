<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\DeleteTeam\DeleteTeamResponse;
use grpc\DeleteTeam\DeleteTeamRequest;
use grpc\DeleteTeam\DeleteTeamServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\DeleteTeamHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserTeam;
use App\Models\UserDetail;
use App\Models\UserDetailUserTeam;

class DeleteTeamHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");

		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$init = new ActionByMiddleware();
		$init->initializeActionByUser(1, 'test');

		$this->artisan('migrate');
		$this->artisan('app:setup-environment');

		UserDetail::customInsert([
			[
				'id' => 1,
				'first_name' => 'John',
				'middle_name' => 'A',
				'last_name' => 'Doe',
				'email' => 'QKt2j@example.com',
				'phone' => '1234567890',
				'address' => '123 Main St',
				'date_of_birth' => '1990-01-01',
				'gender' => 'M',
				'profile_image_url' => 'https://example.com/profile.jpg',
				'profile_image_name' => 'profile.jpg',
				'user_id' => 1
			]
		]);
		UserTeam::customInsert(
			[
				'team_name' => 'Team 1',
				'description' => 'Team 1 description: test'
			]
		);
		UserDetailUserTeam::customInsert(
			[
				'user_team_id' => 1,
				'user_detail_id' => 1
			]
			);
	}

    public function test_delete_team(): void {
		Log::info("DeleteTeamHandlerTest running...");
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$in = new DeleteTeamRequest();
		$in->setActionByUserId(1);
		$in->setTeamId(1);
		$in->setUserId(1);
		$in->setTimezone('test');

		$ctx = $this->createMock(ContextInterface::class);
		$deleteTeamHandler = new DeleteTeamHandler(new CommonFunctions());
		$result = $deleteTeamHandler->deleteTeam($ctx, $in);

		$this->assertInstanceOf(DeleteTeamResponse::class, $result);
		$this->assertTrue($result->getResult());
    }

}
