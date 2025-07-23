<?php

namespace Tests\Unit;

use Tests\TestCase;
use grpc\EditUserDetails\EditServiceInterface;
use grpc\EditUserDetails\EditUserDetailsRequest;
use grpc\EditUserDetails\EditUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Handlers\EditUserDetailsHandler;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Models\UserRole;
use App\Models\UserDetail;
use App\Models\UserDepartment;
use App\Models\UserDetailUserRole;
use App\Models\UserDetailUserDepartment;
use Log;
use Illuminate\Support\Facades\Redis;

class EditUserDetailsHandlerTest extends TestCase {
	use RefreshDatabase;

	private $action_by_user_id = 1;
	private $tz = "TEST";
	private $first_name = "Test JD edited";
	private $middle_name = "JD edited";
	private $last_name = "JD edited";
	private $email = "JD@com.edited";
	private $phone = "0219370914";
	private $address = "TEST ADdress edited";
	private $department = "Pending Selection";
	private $date_of_birth = '2025-05-25 00:00:00';
	private $gender = true;
	private $position = "Manager";
	private $profile_image = "TEST PROFILE IMAGE edited";
	private $profile_image_u_r_l = "TEST SET PROFILE IMAGE URL edited";
	private $profile_image_Name = "TEST SET PROFILE IMAGE NAME edited";
	private $fk = 1;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");

		$userId = 1;
        $redisKey = 'user_' . $userId;

        $userDataArray = json_decode(file_get_contents(base_path('tests/Fixtures/user.json')), true);
        $userJson = json_encode($userDataArray);

        Redis::shouldReceive('get')
            ->times(2)
            ->with($redisKey)
            ->andReturn($userJson);

		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_edit_user_details() {
		Log::info("EditUserDetailHandlerTest running...");

		$registerUserHandler = new EditUserDetailsHandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new EditUserDetailsRequest();
		$in->setActionByUserId($this->action_by_user_id);
		$in->setTimeZone($this->tz);
		$in->setFirstName($this->first_name);
		$in->setMiddleName($this->middle_name);
		$in->setLastName($this->last_name);
		$in->setEmail($this->email);
		$in->setPhone($this->phone);
		$in->setAddress($this->address);
		$in->setDateOfBirth($this->date_of_birth);
		$in->setGender($this->gender);
		$in->setFk($this->fk);
		$in->setPosition($this->position);
		$in->setDepartment($this->department);
		$in->setProfileImageURL($this->profile_image_u_r_l);
		$in->setProfileImageName($this->profile_image_Name);

		$result = $registerUserHandler->EditUserDetails($ctx, $in);

		$this->assertInstanceOf(EditUserDetailsResponse::class, $result);
		$this->assertTrue($result->getResult());
		Log::debug(UserDetail::first()->toArray());
		$model = UserDetail::find(1);
		$model2 = UserRole::where('type_1', $this->position)->first();
		$model3 = UserDepartment::where('department_name', $this->department)->first();
		$this->assertEquals($this->first_name, $model->first_name);
		$this->assertEquals($this->middle_name, $model->middle_name);
		$this->assertEquals($this->last_name, $model->last_name);
		$this->assertEquals($this->email, $model->email);
		$this->assertEquals($this->phone, $model->phone);
		$this->assertEquals($this->address, $model->address);
		$this->assertEquals($this->date_of_birth, $model->date_of_birth);
		$this->assertEquals($this->gender, $model->gender);
		$this->assertEquals($this->position, $model2->type_1);
		$this->assertEquals($this->department, $model3->department_name);
		$this->assertEquals($this->profile_image_u_r_l, $model->profile_image_url);
		$this->assertEquals($this->profile_image_Name, $model->profile_image_name);
	}

}
