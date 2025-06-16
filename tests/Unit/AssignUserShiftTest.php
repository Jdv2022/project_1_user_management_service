<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Handlers\AssignUserShiftHandler;
use grpc\AssignUserShift\AssignUserShiftRequest;
use grpc\AssignUserShift\AssignUserShiftResponse;
use grpc\AssignUserShift\AssignUserShiftServiceInterface;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Models\UserDetailUserShift;
use App\Models\UserShift;
use App\Models\UserDetail;
use Log;

class AssignUserShiftTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database AssignUserShiftTest [start]");
		$this->artisan('migrate');
		$this->artisan('db:seed');
		UserShift::create([
			'shift_name' => 'Test shift',
			'description' => 'This is test for shifts.'
		]);
		Log::info("Migrating Database AssignUserShiftTest [end]");
	}

	public function test_assign_user_shift() {
		Log::info("Testing Assign User Shift");

		$in = new AssignUserShiftRequest();
		$in->setActionByUserId(1);
		$in->setUserIds([1]);
		$in->setShiftId(1);
		$in->setTimezone("TEST");

		$ctx = $this->createMock(ContextInterface::class);
		$handler = new AssignUserShiftHandler(new CommonFunctions());
		$response = $handler->assignUserShift($ctx, $in);

		$res = UserDetailUserShift::first();
		$this->assertTrue($response->getResult());
		$this->assertNotNull($res);
	}

}
