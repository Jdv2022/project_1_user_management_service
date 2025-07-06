<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\GetArchivesHandler;
use grpc\GetArchives\GetArchivesRequest;
use grpc\GetArchives\GetArchivesResponse;
use grpc\GetArchives\GetArchives;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;
use Illuminate\Support\Facades\Redis;
use App\Models\UserDetail;

class GetArchivesTest extends TestCase {

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
	
    public function test_get_archives(): void{
		Log::info("Get Archives running...");

		$getArchivesHandler = new GetArchivesHandler(new CommonFunctions());
		$in = new GetArchivesRequest();
		$ctx = $this->createMock(ContextInterface::class);
		$result = $getArchivesHandler->GetArchives($ctx, $in);

		$this->assertInstanceOf(GetArchivesResponse::class, $result);
		$payload = $result->GetArchives();
		foreach ($payload as $item) {
			$this->assertInstanceOf(GetArchives::class, $item);
		}
    }
}
