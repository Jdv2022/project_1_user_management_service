<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\GetDepartmentHandler;
use grpc\GetDepartment\GetDepartmentRequest;
use grpc\GetDepartment\GetDepartmentResponse;
use grpc\GetDepartment\Departments;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Models\UserDetail;

class GetDepartmentHandlerTest extends TestCase {
	use RefreshDatabase;

	private $tz = "TEST";
	private $action_by_user_id = 1;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_get_department_handler_test() {
		Log::info("GetDepartmentHandlerTest running...");

		$sada = new GetDepartmentHandler(new CommonFunctions());
		$in = new GetDepartmentRequest();
		$ctx = $this->createMock(ContextInterface::class);
		$result = $sada->GetDepartment($ctx, $in);

		$this->assertInstanceOf(GetDepartmentResponse::class, $result);
		$payload = $result->getDepartments();
		foreach ($payload as $item) {
			$this->assertInstanceOf(Departments::class, $item);
		}
	}

}
