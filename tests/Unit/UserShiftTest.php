<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Handlers\CreateShiftHandler;
use grpc\CreateShift\CreateShiftRequest;
use grpc\CreateShift\CreateShiftResponse;
use grpc\CreateShift\CreateShiftServiceInterface;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Models\UserShift;
use Log;

class UserShiftTest extends TestCase {
	use RefreshDatabase;

	private $shift_name = "TEST SHIFT";
	private $description = "TEST DESCRIPTION";
	private $tz = "TEST";
	private $action_by_user_id = 1;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

    public function test_create_user_shift(): void {
		Log::info("test_create_user_shift START");

		$commonFunctions = new CommonFunctions();
		$ctx = $this->createMock(ContextInterface::class);

		$in = new CreateShiftRequest();
		$in->setShiftName($this->shift_name);
		$in->setDescription($this->description);
		$in->setTimezone($this->tz);
		$in->setActionByUserId($this->action_by_user_id);

		$createShiftHandler = new CreateShiftHandler($commonFunctions);

		$result = $createShiftHandler->CreateShift($ctx, $in);

		$this->assertInstanceOf(CreateShiftResponse::class, $result);
		$this->assertTrue($result->getResult());
		$res = UserShift::first();
		$this->assertEquals($this->shift_name, $res->shift_name);
		$this->assertEquals($this->description, $res->description);
		$this->assertEquals($this->action_by_user_id, $res->created_by_user_id);
		$this->assertEquals($this->tz, $res->created_at_timezone);
		$this->assertEquals($this->action_by_user_id, $res->updated_by_user_id);
		$this->assertEquals($this->tz, $res->updated_at_timezone);
    }

}
