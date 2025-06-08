<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Handlers\UserClockInHandler;
use grpc\userClockIn\UserClockInRequest;
use grpc\userClockIn\UserClockOutResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Log;

class UserClockInHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_user_clock_in_handler() {
		Log::info("UserClockInHandlerTest running...");

		Log::info("Time in! Should Be True!");
		$userClockInHandler = new UserClockInHandler(new CommonFunctions());

		$in = new UserClockInRequest();
		$in->setFk(1);
		$in->setTimezone('UTC');

		$result = $userClockInHandler->UserClockInService($this->createMock(ContextInterface::class), $in);
		$this->assertTrue($result->getResult());

		Log::info("Re-Time in! Should Be False");
		$result = $userClockInHandler->UserClockInService($this->createMock(ContextInterface::class), $in);
		$this->assertFalse($result->getResult());
	}	

}
