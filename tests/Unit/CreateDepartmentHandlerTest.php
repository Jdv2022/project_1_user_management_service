<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\CreateDepartment\CreateDepartmentResponse;
use grpc\CreateDepartment\CreateDepartmentRequest;
use grpc\CreateDepartment\CreateDepartmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\CreateDepartmentHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Log;
use Illuminate\Support\Facades\Redis;

class CreateDepartmentHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
		$this->artisan('migrate');
		$this->artisan('app:setup-environment');
	}

    public function test_create_department(): void {
		Log::info("CreateDepartmentHandlerTest running...");

		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$in = new CreateDepartmentRequest();
		$in->setActionByUserId(1);
		$in->setDepartmentName("Department 1");
		$in->setDescription("Department 1 description: test");

		$ctx = $this->createMock(ContextInterface::class);
		$createDepartmentHandler = new CreateDepartmentHandler(new CommonFunctions());
		$result = $createDepartmentHandler->CreateDepartment($ctx, $in);

		$this->assertInstanceOf(CreateDepartmentResponse::class, $result);
		$this->assertEquals(1, $result->getResult());
    }

}
