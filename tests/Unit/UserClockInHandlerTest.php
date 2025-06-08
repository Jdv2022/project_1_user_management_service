<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Handlers\UserClockInHandler;
use App\Grpc\Handlers\UserClockOutHandler;
use grpc\userClockIn\UserClockInRequest;
use grpc\userClockIn\UserClockInResponse;
use grpc\userClockOut\UserClockOutRequest;
use grpc\userClockOut\UserClockOutResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Models\UserAttendance;
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
		$ctx = $this->createMock(ContextInterface::class);
		$in = new UserClockInRequest();
		$in->setFk(1);
		$in->setTimezone('Asia/Singapore');

		$result = $userClockInHandler->UserClockInService($ctx, $in);
		$this->assertInstanceOf(UserClockInResponse::class, $result);
		$this->assertTrue($result->getResult());

		Log::info("Re-Time in! Should Be False");
		$result = $userClockInHandler->UserClockInService($this->createMock(ContextInterface::class), $in);
		$this->assertInstanceOf(UserClockInResponse::class, $result);
		$this->assertFalse($result->getResult());

		$modelInstance = UserAttendance::first();
		$this->assertEquals($modelInstance->id, 1);
		$created_at = $modelInstance->created_at;
		$updated_at = $modelInstance->updated_at;
		$this->assertEquals($created_at, $updated_at);
		sleep(1);
		Log::info("Clock Out! Should Be True");
		$handler = new UserClockOutHandler(new CommonFunctions());
		$in = new UserClockOutRequest();
		$in->setFk(1);
		$in->setTimezone('Asia/Singapore');
		$result = $handler->setUserClockOut($ctx, $in);
		$this->assertInstanceOf(UserClockOutResponse::class, $result);
		$this->assertTrue($result->getResult());

		$modelInstance2 = UserAttendance::first();
		$this->assertEquals($modelInstance2->id, 1);
		$created_at = $modelInstance2->created_at;
		$updated_at = $modelInstance2->updated_at;
		$this->assertNotEquals($updated_at, $created_at);
	}	

}
