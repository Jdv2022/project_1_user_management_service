<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\RegistrationFormDataHandler;
use grpc\userRegistrationFormData\UserRegistrationFormDataRequest;
use grpc\userRegistrationFormData\UserRegistrationFormDataResponse;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Log;

class RegistrationFormDataHandlerTest extends TestCase {
	use RefreshDatabase;
    
	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_user_registration_form_data_handler() {
		Log::info("RegistrationFormDataHandlerTest running...");

		$registrationFormDataHandler = new RegistrationFormDataHandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new UserRegistrationFormDataRequest();
		$result = $registrationFormDataHandler->UserRegistrationFormData($ctx, $in);

		$this->assertInstanceOf(UserRegistrationFormDataResponse::class, $result);
	}

}
