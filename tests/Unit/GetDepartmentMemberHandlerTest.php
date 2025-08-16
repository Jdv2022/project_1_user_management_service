<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\GetDepartmentMember\GetDepartmentMemberResponse;
use grpc\GetDepartmentMember\GetDepartmentMemberRequest;
use grpc\GetDepartmentMember\DepartmentMember;
use grpc\GetDepartmentMember\GetDepartmentMemberServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Handlers\GetDepartmentMemberHandler;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserDepartment;
use App\Models\UserDetailUserDepartment;
use Log;
use Illuminate\Support\Facades\Redis;

class GetDepartmentMemberHandlerTest extends TestCase {
	use RefreshDatabase;

	private $action_by_user_id = 1;
	private $tz = "TEST";

	private $id = 2;
	private $department_name = "Department 1";
	private $description = "Description 1";
	private $created_at = "2023-01-01 00:00:00";
	private $updated_at = "2023-01-01 00:00:00";
	private $Department_lists;

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
		$this->Department_lists = new DepartmentMember([
			'id' => 1,
			'first_name' => 'TEST FIRST NAME',
			'middle_name' => 'TEST MIDDLE NAME',
			'last_name' => 'TEST LAST NAME',
			'position' => 'Admin',
			'created_at' => '2023-01-01 00:00:00',
			'updated_at' => '2023-01-01 00:00:00',
		]);
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		UserDepartment::create([
			'department_name' => $this->department_name,
			'description' => $this->description,
		]);
		UserDetailUserDepartment::create([
			'user_detail_id' => 1,
			'user_department_id' => 1
		]);
	}
	
	public function test_get_department_users_list(): void {
		Log::info("Testing Get department Users List");

		$handler = new GetDepartmentMemberHandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new GetDepartmentMemberRequest();
		$in->setDepartmentId($this->id);
		$in->setTimezone($this->tz);
		$in->setActionByUserId($this->action_by_user_id);
		$result = $handler->GetDepartmentMember($ctx, $in);

		$this->assertInstanceOf(GetDepartmentMemberResponse::class, $result);
		$payload = $result->getDepartmentLists();
		$this->assertEquals($this->id, $result->getId());
		$this->assertEquals($this->department_name, $result->getDepartmentName());
		$this->assertEquals($this->description, $result->getDescription());
		foreach ($payload as $departmentList) {
			$this->assertInstanceOf(DepartmentMember::class, $departmentList);
			$this->assertEquals($this->department_lists->getId(), $departmentList->getId());
		}
	}	

}
