<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;
use Illuminate\Support\Facades\Redis;
use grpc\RemoveArchive\RemoveArchiveRequest;
use grpc\RemoveArchive\RemoveArchiveResponse;
use App\Grpc\Handlers\RemoveArchiveHandler;
use Log;

class RemoveArchiveTest extends TestCase {

	private $tz = "TEST";
	private $action_by_user_id = 1;

    public function test_remove_archive(): void {
		Log::info("RemoveArchive");
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

		$in = new RemoveArchiveRequest();
		$in->setUserId(1);
		$in->setTimezone('test');
		$in->setActionByUserId(1);

		$ctx = $this->createMock(ContextInterface::class);
		$addArchiveHandler = new RemoveArchiveHandler(new CommonFunctions());
		$result = $addArchiveHandler->removeArchive($ctx, $in);

		$this->assertInstanceOf(RemoveArchiveResponse::class, $result);
		$this->assertTrue($result->getResult());
    }
}
