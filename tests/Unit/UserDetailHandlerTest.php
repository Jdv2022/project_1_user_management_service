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
use Log;

class UserDetailHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
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
