<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\TeamUsersLists\TeamUsersListsResponse;
use grpc\TeamUsersLists\TeamUsersListsRequest;
use grpc\TeamUsersLists\teamUsersLists;
use grpc\TeamUsersLists\TeamUsersListsServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\GetTeamUsersListsHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserTeam;
use App\Models\UserDetailUserTeam;
use Log;
use Illuminate\Support\Facades\Redis;

class GetTeamUsersListTest extends TestCase {
	use RefreshDatabase;

	private $action_by_user_id = 1;
	private $tz = "TEST";

	private $id = 1;
	private $team_name = "Team 1";
	private $description = "Description 1";
	private $created_at = "2023-01-01 00:00:00";
	private $updated_at = "2023-01-01 00:00:00";
	private $team_lists;

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
		$this->team_lists = new teamUsersLists([
			'id' => 1,
			'first_name' => 'TEST FIRST NAME',
			'middle_name' => 'TEST MIDDLE NAME',
			'last_name' => 'TEST LAST NAME',
			'position' => 'Admin',
			'created_at' => '2023-01-01 00:00:00',
			'updated_at' => '2023-01-01 00:00:00',
		]);
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		UserTeam::create([
			'team_name' => $this->team_name,
			'description' => $this->description,
		]);
		UserDetailUserTeam::create([
			'user_detail_id' => 1,
			'user_team_id' => 1
		]);
	}
	
	public function test_get_team_users_list(): void {
		Log::info("Testing Get Team Users List");

		$handler = new GetTeamUsersListshandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new TeamUsersListsRequest();
		$in->setTeamId($this->id);
		$in->setTimezone($this->tz);
		$in->setActionByUserId($this->action_by_user_id);
		$result = $handler->TeamUsersLists($ctx, $in);

		$this->assertInstanceOf(TeamUsersListsResponse::class, $result);
		$payload = $result->getTeamLists();
		$this->assertEquals($this->id, $result->getId());
		$this->assertEquals($this->team_name, $result->getTeamName());
		$this->assertEquals($this->description, $result->getDescription());
		foreach ($payload as $teamList) {
			$this->assertInstanceOf(teamUsersLists::class, $teamList);
			$this->assertEquals($this->team_lists->getId(), $teamList->getId());
		}
	}	

}
