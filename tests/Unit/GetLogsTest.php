<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Handlers\GetLogsHandler;
use Spiral\RoadRunner\GRPC\ContextInterface;
use grpc\GetLogs\GetLogsRequest;
use grpc\GetLogs\GetLogsResponse;
use grpc\GetLogs\Logs;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Illuminate\Support\Facades\Redis;
use App\Models\UserLog;

class GetLogsTest extends TestCase {

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
		$init->initializeActionByUser(1, 'test');
		$this->artisan('migrate');
		$this->artisan('db:seed');
		UserLog::customInsert([
			"description" => "Test log",
		]);
		Log::info("Migrating Database END");
	}

    public function test_get_logs(): void {
		Log::info("Test get logs");

		$getLogsHandler = new GetLogsHandler(new CommonFunctions());

		$in = new GetLogsRequest();
		$in->setTimezone('America/New_York');

		$ctx = $this->createMock(ContextInterface::class);
		$result = $getLogsHandler->GetLogs($ctx, $in);
		
		$repeatedField = $result->getLogs();
		$array = iterator_to_array($repeatedField);
		$plainArray = array_map(function ($item) {
			return [
				'id' => $item->getId(),
				'description' => $item->getDescription(),
				'created_at' => $item->getCreatedAt(),
				'created_by' => $item->getCreatedBy(),
			];
		}, $array);

		$this->assertInstanceOf(GetLogsResponse::class, $result);
		$this->assertIsArray($plainArray); 
		$this->assertInstanceOf(Logs::class, $array[0]); 
    }

}
