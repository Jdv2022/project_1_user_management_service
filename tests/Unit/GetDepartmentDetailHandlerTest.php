<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\GetDepartmentDetailHandler;
use grpc\GetDepartmentDetail\GetDepartmentDetailRequest;
use grpc\GetDepartmentDetail\GetDepartmentDetailResponse;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Models\UserDetail;

class GetDepartmentDetailHandlerTest extends TestCase {
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

	public function test_get_department_detail_handler() {
		Log::info("GetDepartmentDetailHandlerTest running...");

		$sada = new GetDepartmentDetailHandler(new CommonFunctions());
		$in = new GetDepartmentDetailRequest();
		$in->setDepartmentId(1);
		$ctx = $this->createMock(ContextInterface::class);
		$result = $sada->GetDepartmentDetail($ctx, $in);

		$this->assertInstanceOf(GetDepartmentDetailResponse::class, $result);
	}

}
