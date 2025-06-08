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
use Log;

class RegisterUserhandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database START");
		$this->artisan('migrate');
		$this->artisan('db:seed');
		Log::info("Migrating Database END");
	}

	public function test_register_user_details_handler() {
		Log::info("RegisterUserHandlerTest running...");

		$registerUserHandler = new RegisterUserHandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new RegisterUserDetailsRequest();
		$in->setActionByUserId(1);
		$in->setTimeZone('UTC');
		$in->setFirstName('John');
		$in->setMiddleName('Test');
		$in->setLastName('Admin');
		$in->setEmail('test@test.com');
		$in->setPhone('0912301294');
		$in->setAddress('test, test, test');
		$in->setDateOfBirth('2025-05-25');
		$in->setGender(true);
		$in->setFk(1);
		$in->setSetProfileImageURL('test');
		$in->setSetProfileImageName('test');

		$result = $registerUserHandler->RegisterUserDetails($ctx, $in);

		$this->assertInstanceOf(RegisterUserDetailsResponse::class, $result);
		$this->assertTrue($result->getResult());
	}

}
