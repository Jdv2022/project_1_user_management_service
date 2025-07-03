<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\RegistrationFormDataHandler;
use grpc\userRegistrationFormData\UserRegistrationFormDataRequest;
use grpc\userRegistrationFormData\UserRegistrationFormDataResponse;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;
use Illuminate\Support\Facades\Redis;

class RegistrationFormDataHandlerTest extends TestCase {
	use RefreshDatabase;
	
	private $action_by_user_id = 1;
	private $tz = "TEST";
    
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

	public function test_user_registration_form_data_handler() {
		Log::info("RegistrationFormDataHandlerTest running...");

		$registrationFormDataHandler = new RegistrationFormDataHandler(new CommonFunctions());
		$ctx = $this->createMock(ContextInterface::class);
		$in = new UserRegistrationFormDataRequest();
		$result = $registrationFormDataHandler->UserRegistrationFormData($ctx, $in);

		$this->assertInstanceOf(UserRegistrationFormDataResponse::class, $result);
	}

}
