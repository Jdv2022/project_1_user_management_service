<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;
use grpc\Register\RegisterServiceInterface;
use grpc\Register\RegisterUserDetailsRequest;
use grpc\Register\RegisterUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\RegisterUserHandler;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Illuminate\Support\Facades\Redis;
use App\Models\UserDetail;
use Log;

class RegisterUserhandlerTest extends TestCase {
	use RefreshDatabase;

	private $action_by_user_id = 1;
	private $tz = "TEST";
	private $first_name = "Test JD";
	private $middle_name = "JD";
	private $last_name = "JD";
	private $email = "JD@com";
	private $phone = "0219370914";
	private $address = "TEST ADdress";
	private $department = "Pending Selection";
	private $date_of_birth = '2025-05-25 00:00:00';
	private $gender = true;
	private $position = "Admin";
	private $profile_image = "TEST PROFILE IMAGE";
	private $set_profile_image_u_r_l = "TEST SET PROFILE IMAGE URL";
	private $set_profile_image_Name = "TEST SET PROFILE IMAGE NAME";
	private $fk = 1;
	
	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$init = new ActionByMiddleware();
		$init->initializeActionByUser($this->action_by_user_id, $this->tz);
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_register_user_details_handler() {
		Log::info("RegisterUserHandlerTest running...");

		$registerUserHandler = new RegisterUserHandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new RegisterUserDetailsRequest();
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
		$in->setSetProfileImageURL($this->set_profile_image_u_r_l);
		$in->setSetProfileImageName($this->set_profile_image_Name);

		$result = $registerUserHandler->RegisterUserDetails($ctx, $in);

		$this->assertInstanceOf(RegisterUserDetailsResponse::class, $result);
		$this->assertTrue($result->getResult());

		$model = UserDetail::find(2);
		Log::debug($model->toArray());
		$this->assertEquals($this->first_name, $model->first_name);
		$this->assertEquals($this->middle_name, $model->middle_name);
		$this->assertEquals($this->last_name, $model->last_name);
		$this->assertEquals($this->email, $model->email);
		$this->assertEquals($this->phone, $model->phone);
		$this->assertEquals($this->address, $model->address);
		$this->assertEquals($this->date_of_birth, $model->date_of_birth);
		$this->assertEquals($this->gender, $model->gender);
		$this->assertEquals($this->set_profile_image_u_r_l, $model->profile_image_url);
		$this->assertEquals($this->set_profile_image_Name, $model->profile_image_name);

		$this->assertEquals($this->action_by_user_id, $model->created_by_user_id);
		$this->assertEquals($this->action_by_user_id, $model->updated_by_user_id);
		$this->assertEquals($this->tz, $model->created_at_timezone);
		$this->assertEquals($this->tz, $model->updated_at_timezone);

		$this->assertEquals($this->fk, $model->user_id);
	}

}
