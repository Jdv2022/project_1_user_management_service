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
use Log;

class TeamListsHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
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
