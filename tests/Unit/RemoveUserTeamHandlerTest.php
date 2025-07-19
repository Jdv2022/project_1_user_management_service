<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use grpc\RemoveUserTeam\RemoveUserTeamResponse;
use grpc\RemoveUserTeam\RemoveUserTeamRequest;
use grpc\RemoveUserTeam\fK;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\RemoveUserTeamHandler;
use App\Grpc\Services\CommonFunctions;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBType;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserDetailUserTeam;
use App\Models\UserTeam;
use Log;
use Illuminate\Support\Facades\Redis;

class RemoveUserTeamHandlerTest extends TestCase {
	use RefreshDatabase;

	private $team_name = "TEST team";
	private $description = "TEST DESCRIPTION";
	private $tz = "TEST";
	private $user_detail_id = 1;
	private $user_team_id = 1;
	private $action_by_user_id = 1;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->times(2)
            ->with($redisKey)
            ->andReturn($userJson);
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		UserTeam::create([
			'team_name' => $this->team_name,
			'description' => $this->description
		]);
		UserDetailUserTeam::create([
			'user_detail_id' => 1,
			'user_team_id' => 1
		]);
		Log::info("Migrating Database END");
	}

	public function test_remove_user() {
		Log::info("Removing User START");

		Log::info("CreateTeamHandlerTest running...");

		$in = new RemoveUserTeamRequest();
		$in->setActionByUserId($this->action_by_user_id);
		$in->setFk(1);
		$in->setTeamId($this->user_team_id);
		$in->setTimezone($this->tz);

		$ctx = $this->createMock(ContextInterface::class);
		$createTeamHandler = new RemoveUserTeamHandler(new CommonFunctions());
		$result = $createTeamHandler->RemoveUserTeam($ctx, $in);

		$this->assertInstanceOf(RemoveUserTeamResponse::class, $result);
		$this->assertTrue($result->getResult());

		$res = UserDetailUserTeam::first();
		$this->assertNull($res);
	}


}
