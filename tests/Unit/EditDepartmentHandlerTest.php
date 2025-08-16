<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\EditDepartment\EditDepartmentResponse;
use grpc\EditDepartment\EditDepartmentRequest;
use grpc\EditDepartment\EditDepartmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\EditDepartmentHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserDepartment;

class EditDepartmentHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
		$this->artisan('migrate');
		$this->artisan('app:setup-environment');

		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->andReturn($userJson);

		$init = new ActionByMiddleware();
		$init->initializeActionByUser(1, 'test');
		$molde = new UserDepartment();
		$molde->department_name = "Department 1";
		$molde->description = "Department 1 description: test";
		$molde->save();
	}
 
	public function test_edit_department(): void {
		Log::info("EditDepartmentHandlerTest running...");
		
		Log::info("EditDepartmentHandlerTest running...");
		
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$in = new EditDepartmentRequest();
		$in->setActionByUserId(1);
		$in->setDepartmentName("Department 2");
		$in->setDescription("Department 2 description: test");
		$in->setDepartmentId(1);
		$in->setTimezone('test');

		$ctx = $this->createMock(ContextInterface::class);
		$editDepartmentHandler = new EditDepartmentHandler(new CommonFunctions());
		$result = $editDepartmentHandler->EditDepartment($ctx, $in);

		$this->assertInstanceOf(EditDepartmentResponse::class, $result);
		$this->assertEquals(1, $result->getResult());

		$model = UserDepartment::find(1);
		$this->assertEquals("Department 2", $model->department_name);
		$this->assertEquals("Department 2 description: test", $model->description);
	}

}
