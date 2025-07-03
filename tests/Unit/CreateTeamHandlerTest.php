<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\CreateTeam\CreateTeamResponse;
use grpc\CreateTeam\CreateTeamRequest;
use grpc\CreateTeam\CreateTeamServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\CreateTeamHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Log;

class CreateTeamHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
		$this->artisan('migrate');
		$this->artisan('app:setup-environment');
	}

    public function test_create_team(): void {
		Log::info("CreateTeamHandlerTest running...");

		$in = new CreateTeamRequest();
		$in->setActionByUserId(1);
		$in->setTeamName("Team 1");
		$in->setDescription("Team 1 description: test");

		$ctx = $this->createMock(ContextInterface::class);
		$createTeamHandler = new CreateTeamHandler(new CommonFunctions());
		$result = $createTeamHandler->CreateTeam($ctx, $in);

		$this->assertInstanceOf(CreateTeamResponse::class, $result);
		$this->assertTrue($result->getResult());
    }

}
