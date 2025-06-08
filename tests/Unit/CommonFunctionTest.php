<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use grpc\GetUserDetails\GetUserDetailsResponse;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Log;

class CommonFunctionTest extends TestCase {
	use RefreshDatabase;
   
	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
		$this->artisan('migrate');
		$this->artisan('app:setup-environment');
	}

    public function test_common_function_service() {
		Log::info("test_common_function_service");
		$commonFunctions = new CommonFunctions();
		$return = $commonFunctions->setUserDetailReturn(1, new GetUserDetailsResponse());
		
		$this->assertInstanceOf(GetUserDetailsResponse::class, $return);
	}
}
