<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\EditTeam\EditTeamResponse;
use grpc\EditTeam\EditTeamRequest;
use grpc\EditTeam\EditTeamServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\EditTeamHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserTeam;

class EditTeamHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
		$this->artisan('migrate');
		$this->artisan('app:setup-environment');

		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->andReturn($userJson);

		$init = new ActionByMiddleware();
		$init->initializeActionByUser(1, 'test');
		$molde = new UserTeam();
		$molde->team_name = "Team 1";
		$molde->description = "Team 1 description: test";
		$molde->save();
	}
	
	public function test_edit_team(): void {
		Log::info("EditTeamHandlerTest running...");
		
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$in = new EditTeamRequest();
		$in->setActionByUserId(1);
		$in->setTeamName("Team 2");
		$in->setDescription("Team 2 description: test");
		$in->setTeamId(1);
		$in->setTimezone('test');

		$ctx = $this->createMock(ContextInterface::class);
		$editTeamHandler = new EditTeamHandler(new CommonFunctions());
		$result = $editTeamHandler->EditTeam($ctx, $in);

		$this->assertInstanceOf(EditTeamResponse::class, $result);
		$this->assertEquals(1, $result->getResult());

		$model = UserTeam::find(1);
		$this->assertEquals("Team 2", $model->team_name);
		$this->assertEquals("Team 2 description: test", $model->description);
	}

}
