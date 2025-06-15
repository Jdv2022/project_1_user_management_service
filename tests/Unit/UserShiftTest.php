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
use Log;

class UserShiftTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

    public function test_create_user_shift(): void {
		Log::info("test_create_user_shift START");

		$commonFunctions = new CommonFunctions();
		$ctx = $this->getMockBuilder(ContextInterface::class)->getMock();

		$in = new CreateShiftRequest();
		$in->setShiftName("Test Shift");
		$in->setDescription("Test Shift Description");

		$createShiftHandler = new CreateShiftHandler($commonFunctions);

		$result = $createShiftHandler->CreateShift($ctx, $in);

		$this->assertInstanceOf(CreateShiftResponse::class, $result);
		$this->assertTrue($result->getResult());
    }

}
