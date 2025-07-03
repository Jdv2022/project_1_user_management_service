<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use grpc\AssignUserToTeam\AssignUserToTeamResponse;
use grpc\AssignUserToTeam\AssignUserToTeamRequest;
use grpc\AssignUserToTeam\fK;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\AssignUserToTeamHandler;
use App\Grpc\Services\CommonFunctions;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBType;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserDetailUserTeam;
use App\Models\UserTeam;
use Log;
use Illuminate\Support\Facades\Redis;

class AssignUserToTeamHandlerTest extends TestCase {
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
		UserTeam::create([
			'team_name' => $this->team_name,
			'description' => $this->description
		]);
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_assign_user_to_team() {
		Log::info("CreateTeamHandlerTest running...");

		$in = new AssignUserToTeamRequest();
		$in->setActionByUserId($this->action_by_user_id);
		$in->getFk()[] = new fK(['fk' => $this->user_detail_id]);
		$in->setTeamId($this->user_team_id);
		$in->setTimezone($this->tz);

		$ctx = $this->createMock(ContextInterface::class);
		$createTeamHandler = new AssignUserToTeamHandler(new CommonFunctions());
		$result = $createTeamHandler->assignUserToTeam($ctx, $in);

		$this->assertInstanceOf(AssignUserToTeamResponse::class, $result);
		$this->assertTrue($result->getResult());

		$res = UserDetailUserTeam::first();
		$this->assertNotNull($res);
		$this->assertEquals($this->action_by_user_id, $res->created_by_user_id);
		$this->assertEquals($this->tz, $res->created_at_timezone);
		$this->assertEquals($this->action_by_user_id, $res->updated_by_user_id);
		$this->assertEquals($this->tz, $res->updated_at_timezone);
		$this->assertEquals($this->user_detail_id, $res->user_detail_id);
		$this->assertEquals($this->user_team_id, $res->user_team_id);
	}

}
