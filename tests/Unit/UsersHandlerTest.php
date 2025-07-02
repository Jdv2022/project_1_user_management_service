<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Handlers\UsersHandler;
use grpc\getUsers\GetUsersRequest;
use grpc\getUsers\GetUsersResponse;
use grpc\getUsers\GetUsersServiceInterface;
use grpc\getUsers\User;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Log;

class UsersHandlerTest extends TestCase {
	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
		$this->artisan('migrate');
		$this->artisan('app:setup-environment');
	}

	public function test_get_users() {
		Log::info("Testing get_users");
		$commonFunctions = new CommonFunctions();
		$usersHandler = new UsersHandler($commonFunctions);
		$ctx = $this->getMockBuilder(ContextInterface::class)->getMock();
		$in = new GetUsersRequest();
		$response = $usersHandler->GetUsers($ctx, $in);
		$this->assertInstanceOf(GetUsersResponse::class, $response);
	}
	
}
