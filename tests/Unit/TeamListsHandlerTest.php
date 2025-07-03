<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\TeamListsHandler;
use grpc\TeamLists\TeamListsRequest;
use grpc\TeamLists\TeamListsResponse;
use grpc\TeamLists\teamLists;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;
use Illuminate\Support\Facades\Redis;

class TeamListsHandlerTest extends TestCase {
	use RefreshDatabase;

	private $action_by_user_id = 1;
	private $tz = "TEST";

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
			->once()
            ->with($redisKey)
            ->andReturn($userJson);
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

    public function test_get_team_list(): void {
        Log::info("TeamListsHandlerTest running...");

		$teamListsHandler = new TeamListsHandler(new CommonFunctions());
		$in = new TeamListsRequest();
		$ctx = $this->createMock(ContextInterface::class);
		$result = $teamListsHandler->TeamLists($ctx, $in);

		$this->assertInstanceOf(TeamListsResponse::class, $result);
		$payload = $result->getTeamLists();
		foreach ($payload as $teamList) {
			$this->assertInstanceOf(teamLists::class, $teamList);
		}
    }

}
