<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Handlers\GetAttendanceHandler;
use Spiral\RoadRunner\GRPC\ContextInterface;
use grpc\getAttendance\GetAttendanceRequest;
use grpc\getAttendance\GetAttendanceResponse;
use grpc\getAttendance\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase; 

class GetAttendancehandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_get_attendance_handler_test() {
		Log::info("GetAttendanceHandlerTest running...");
		
		$getAttendanceHandler = new GetAttendanceHandler(new CommonFunctions());
		$id = 1;
		$in = new GetAttendanceRequest();
		$in->getFk($id);
		$in->getMonth('2025-06-08');
		$in->getTimeZone('+08:00');

		$ctx = $this->createMock(ContextInterface::class);
		$result = $getAttendanceHandler->GetAttendance($ctx, $in);
		
		$repeatedField = $result->getAttendance();
		$array = iterator_to_array($repeatedField);

		$this->assertInstanceOf(GetAttendanceResponse::class, $result);
		$this->assertIsArray($array); 
		$this->assertInstanceOf(Attendance::class, $array[0]); 
	}
}
