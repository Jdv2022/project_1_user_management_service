<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\DeleteDepartment\DeleteDepartmentResponse;
use grpc\DeleteDepartment\DeleteDepartmentRequest;
use grpc\DeleteDepartment\DeleteDepartmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\DeleteDepartmentHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserDepartment;
use App\Models\UserDetail;
use App\Models\UserDetailUserDepartment;

class DeleteDepartmentHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");

		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$init = new ActionByMiddleware();
		$init->initializeActionByUser(1, 'test');

		$this->artisan('migrate');
		$this->artisan('app:setup-environment');

		UserDetail::customInsert([
			[
				'id' => 1,
				'first_name' => 'John',
				'middle_name' => 'A',
				'last_name' => 'Doe',
				'email' => 'QKt2j@example.com',
				'phone' => '1234567890',
				'address' => '123 Main St',
				'date_of_birth' => '1990-01-01',
				'gender' => 'M',
				'profile_image_url' => 'https://example.com/profile.jpg',
				'profile_image_name' => 'profile.jpg',
				'user_id' => 1
			]
		]);
		UserDepartment::customInsert(
			[
				'department_name' => 'Department 1',
				'description' => 'Department 1 description: test'
			]
		);
		UserDetailUserDepartment::customInsert(
			[
				'user_department_id' => 1,
				'user_detail_id' => 1
			]
			);
	}

    public function test_delete_department(): void {
		Log::info("DeleteDepartmentHandlerTest running...");
		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->once()
            ->with($redisKey)
            ->andReturn($userJson);

		$in = new DeleteDepartmentRequest();
		$in->setActionByUserId(1);
		$in->setDepartmentId(1);
		$in->setUserId(1);
		$in->setTimezone('test');

		$ctx = $this->createMock(ContextInterface::class);
		$deleteDepartmentHandler = new DeleteDepartmentHandler(new CommonFunctions());
		$result = $deleteDepartmentHandler->deleteDepartment($ctx, $in);

		$this->assertInstanceOf(DeleteDepartmentResponse::class, $result);
		$this->assertTrue($result->getResult());
    }

}
