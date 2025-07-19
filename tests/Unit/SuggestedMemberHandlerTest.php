<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\CommonFunctions;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Handlers\SuggestedMemberHandler;
use grpc\SuggestedMember\SuggestedMemberRequest;
use grpc\SuggestedMember\SuggestedMemberResponse;
use grpc\SuggestedMember\member;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use App\Grpc\Middlewares\ActionByMiddleware;
use Log;
use Illuminate\Support\Facades\Redis;

class SuggestedMemberHandlerTest extends TestCase {
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

	public function test_suggested_member(): void {
		Log::info('test_suggested_member');	

		$SuggestedMemberHandler = new SuggestedMemberHandler(new CommonFunctions());
		$in = new SuggestedMemberRequest();
		$ctx = $this->createMock(ContextInterface::class);
		$result = $SuggestedMemberHandler->SuggestedMember($ctx, $in);

		$this->assertInstanceOf(SuggestedMemberResponse::class, $result);
		$payload = $result->getTeamlists();
		foreach ($payload as $teamList) {
			$this->assertInstanceOf(member::class, $teamList);
		}
	}

}
