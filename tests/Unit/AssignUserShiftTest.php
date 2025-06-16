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

	private $shift_name = "TEST SHIFT";
	private $description = "TEST DESCRIPTION";
	private $tz = "TEST";
	private $action_by_user_id = 1;
	private $user_detail_id = 1;
	private $user_shift_id = 1;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database AssignUserShiftTest [start]");
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		UserShift::create([
			'shift_name' => $this->shift_name,
			'description' => $this->description
		]);
		Log::info("Migrating Database AssignUserShiftTest [end]");
	}

	public function test_assign_user_shift() {
		Log::info("Testing Assign User Shift");

		$in = new AssignUserShiftRequest();
		$in->setActionByUserId(1);
		$in->setUserIds([$this->user_detail_id]);
		$in->setShiftId($this->user_shift_id);
		$in->setTimezone("TEST");

		$ctx = $this->createMock(ContextInterface::class);
		$handler = new AssignUserShiftHandler(new CommonFunctions());
		$response = $handler->assignUserShift($ctx, $in);

		$res = UserDetailUserShift::first();
		$this->assertTrue($response->getResult());
		$this->assertNotNull($res);
		$this->assertEquals($this->action_by_user_id, $res->created_by_user_id);
		$this->assertEquals($this->tz, $res->created_at_timezone);
		$this->assertEquals($this->action_by_user_id, $res->updated_by_user_id);
		$this->assertEquals($this->tz, $res->updated_at_timezone);
		$this->assertEquals($this->user_detail_id, $res->user_detail_id);
		$this->assertEquals($this->user_shift_id, $res->user_shift_id);
	}

}
