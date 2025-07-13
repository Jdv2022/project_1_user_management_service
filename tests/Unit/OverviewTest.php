<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use grpc\Overview\OverviewRequest;
use grpc\Overview\OverviewResponse;
use grpc\Overview\UserData;
use App\Grpc\Handlers\OverviewHandler;
use App\Models\UserAccessCounter;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserDetailUserRole;
use Log;

class OverviewTest extends TestCase {

	private $siteVisits = [
		[
			'created_at' => '2025-07',
			'count' => 5,
		]
	];
	
	private $userData = [
		[
			'date' => '2025-07',
			'totalUser' => 12,
		],
	];

	private $userDetail = [
		[
			"first_name" => "Master",
			"middle_name" => "Sudo",
			"last_name" => "User",
			"email" => "superuser@superuser",
			"phone" => "0000000000",
			"address" => "root",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 1
		],
		[
			"first_name" => "Master1",
			"middle_name" => "Sudo1",
			"last_name" => "User1",
			"email" => "superuser@superuser1",
			"phone" => "00000000001",
			"address" => "root1",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 2
		],
		[
			"first_name" => "Master3",
			"middle_name" => "Sudo3",
			"last_name" => "User3",
			"email" => "superuser@superuser3",
			"phone" => "00000000003",
			"address" => "root3",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 3
		],
		[
			"first_name" => "Master4",
			"middle_name" => "Sudo4",
			"last_name" => "User4",
			"email" => "superuser@superuser4",
			"phone" => "00000000004",
			"address" => "root4",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 4
		],
		[
			"first_name" => "Master5",
			"middle_name" => "Sudo5",
			"last_name" => "User5",
			"email" => "superuser@superuser5",
			"phone" => "00000000005",
			"address" => "root5",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 5
		],
		[
			"first_name" => "Master6",
			"middle_name" => "Sudo6",
			"last_name" => "User6",
			"email" => "superuser@superuser6",
			"phone" => "00000000006",
			"address" => "root6",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 6
		],
		[
			"first_name" => "Master7",
			"middle_name" => "Sudo7",
			"last_name" => "User7",
			"email" => "superuser@superuser7",
			"phone" => "00000000007",
			"address" => "root7",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 7
		],
		[
			"first_name" => "Master8",
			"middle_name" => "Sudo8",
			"last_name" => "User8",
			"email" => "superuser@superuser8",
			"phone" => "00000000008",
			"address" => "root8",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 8
		],
		[
			"first_name" => "Master9",
			"middle_name" => "Sudo9",
			"last_name" => "User9",
			"email" => "superuser@superuser9",
			"phone" => "00000000009",
			"address" => "root9",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 9
		],
		[
			"first_name" => "Master10",
			"middle_name" => "Sudo10",
			"last_name" => "User10",
			"email" => "superuser@superuser10",
			"phone" => "000000000010",
			"address" => "root10",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 10
		],
		[
			"first_name" => "Master11",
			"middle_name" => "Sudo11",
			"last_name" => "User11",
			"email" => "superuser@superuser11",
			"phone" => "000000000011",
			"address" => "root11",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 11
		],
		[
			"first_name" => "Master12",
			"middle_name" => "Sudo12",
			"last_name" => "User12",
			"email" => "superuser@superuser12",
			"phone" => "000000000012",
			"address" => "root12",
			"date_of_birth" => "2025-05-25",
			"gender" => "1",
			"profile_image_url" => "null",
			"user_id" => 12
		]
	];

	private $userRole = [
		[
			'type_1' => 'Admin',
			'description' => 'TEST',
			'level' => '1'
		],
		[
			'type_1' => 'Manager',
			'description' => 'TEST',
			'level' => '2'
		],
		[
			'type_1' => 'Team Leader',
			'description' => 'TEST',
			'level' => '3'
		],
		[
			'type_1' => 'Team Member',
			'description' => 'TEST',
			'level' => '4'
		],
		[
			'type_1' => 'Guest',
			'description' => 'TEST',
			'level' => '5'
		],
	];

	private $monthlyAccess = [
		[
			'created_at' => '2022-07',
			'count' => 5
		]
	];

	private $userDetailUserRole = [
		[
			'user_detail_id' => 1,
			'user_role_id' => 1,
		],
		[
			'user_detail_id' => 2,
			'user_role_id' => 1,
		],
		[
			'user_detail_id' => 4,
			'user_role_id' => 2,
		],
		[
			'user_detail_id' => 5,
			'user_role_id' => 3,
		],
		[
			'user_detail_id' => 6,
			'user_role_id' => 3,
		],
		[
			'user_detail_id' => 7,
			'user_role_id' => 4,
		],
		[
			'user_detail_id' => 8,
			'user_role_id' => 4,
		],
		[
			'user_detail_id' => 9,
			'user_role_id' => 4,
		],
		[
			'user_detail_id' => 10,
			'user_role_id' => 4,
		],
		[
			'user_detail_id' => 11,
			'user_role_id' => 4,
		],
		[
			'user_detail_id' => 12,
			'user_role_id' => 5,
		],
	];

	private $userDetailUserRoleCheck = [
		[
			'user_role_id' => 1,
			'count' => 2,
			'user_role' => 'Admin'
		],
		[
			'user_role_id' => 2,
			'count' => 1,
			'user_role' => 'Manager'
		],
		[
			'user_role_id' => 3,
			'count' => 2,
			'user_role' => 'Team Leader'
		],
		[
			'user_role_id' => 4,
			'count' => 5,
			'user_role' => 'Team Member'
		],
		[
			'user_role_id' => 5,
			'count' => 1,
			'user_role' => 'Guest'
		],
	];

	public function setUp(): void {
		parent::setUp();
		Log::info("Migrating Database");
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
		UserDetail::customInsert($this->userDetail);
		UserRole::customInsert($this->userRole);
		UserAccessCounter::customInsert($this->monthlyAccess);
		UserDetailUserRole::customInsert($this->userDetailUserRole);
	}

    public function test_overview(): void {
		Log::info('test_overview');

		$overviewHandler = new OverviewHandler(new CommonFunctions());

		$in = new OverviewRequest();
		$in->setActionByUserId(1);
		$in->setTimezone('+08:00');
		
		$ctx = $this->createMock(ContextInterface::class);
		$result = $overviewHandler->overview($ctx, $in);

		$this->assertInstanceOf(OverviewResponse::class, $result);
		
		$getUserAccessCounter = $result->getUserAccessCounter();
		$userDataArray1 = iterator_to_array($getUserAccessCounter);
		$plainArray1 = array_map(function ($item) {
			return [
				'count' => $item->getCount(),
				'created_at' => $item->getCreatedAt(),
			];
		}, $userDataArray1);
		$this->assertEquals($plainArray1, $this->siteVisits);

		$getUserData = $result->getUserDetailUserRole();
		$userDataArray2 = iterator_to_array($getUserData);
		$plainArray2 = array_map(function ($item) {
			return [
				'user_role_id' => $item->getUserRoleId(),
				'count' => $item->getCount(),
				'user_role' => $item->getUserRole(),
			];
		}, $userDataArray2);
		
		$this->assertEquals($plainArray2, $this->userDetailUserRoleCheck);

		$monthlyAccess = $result->getUserDetail();
		$userDataArray3 = iterator_to_array($monthlyAccess);
		$plainArray3 = array_map(function ($item) {
			return [
				'date' => $item->getDate(),
				'totalUser' => $item->getTotalUser(),
			];
		}, $userDataArray3);
		$this->assertEquals($plainArray3, $this->userData);
    }
}