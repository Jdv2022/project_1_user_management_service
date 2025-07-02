<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\GetUserDetails\GetUserDetailsServiceInterface;
use grpc\GetUserDetails\GetUserDetailsRequest;
use grpc\GetUserDetails\GetUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Handlers\UserDetailsHandler;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;

class UserDetailHandlerTest extends TestCase {
	use RefreshDatabase;

	private $action_by_user_id = 1;
	private $tz = "TEST";

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_get_user_details() {
		Log::info("UserDetailsHandlerTest running...");

		$ctx = $this->createMock(ContextInterface::class);
		$in = new GetUserDetailsRequest();
		$in->setFk(1);
		$userDetailsHandler = new UserDetailsHandler(new CommonFunctions());
		$result = $userDetailsHandler->GetUserDetails($ctx, $in);

		$this->assertInstanceOf(GetUserDetailsResponse::class, $result);
	}

}
