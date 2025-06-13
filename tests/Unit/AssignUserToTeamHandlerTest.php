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
use App\Models\UserTeam;
use Log;

class AssignUserToTeamHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$this->artisan('migrate');
		UserTeam::create([
			'team_name' => 'Test Team',
			'description' => 'This is test for teams.'
		]);
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_assign_user_to_team() {
		Log::info("CreateTeamHandlerTest running...");

		$in = new AssignUserToTeamRequest();
		$in->setActionByUserId(1);
		$in->getFk()[] = new fK(['fk' => 1]);
		$in->setTeamId(1);

		$ctx = $this->createMock(ContextInterface::class);
		$createTeamHandler = new AssignUserToTeamHandler(new CommonFunctions());
		$result = $createTeamHandler->assignUserToTeam($ctx, $in);

		$this->assertInstanceOf(AssignUserToTeamResponse::class, $result);
		$this->assertTrue($result->getResult());
	}

}
