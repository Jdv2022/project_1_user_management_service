<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\GetDepartmentMember\GetDepartmentMemberRequest;
use grpc\GetDepartmentMember\GetDepartmentMemberResponse;
use grpc\GetDepartmentMember\DepartmentMember;
use grpc\GetDepartmentMember\GetDepartmentMemberServiceInterface;
use App\Models\UserDetailUserShift;
use Illuminate\Support\Facades\DB;
use App\Models\UserDepartment;
use App\Models\UserDetailUserDepartment;
use Log;

class GetDepartmentMemberHandler extends ActionByMiddleware implements GetDepartmentMemberServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetDepartmentMember(ContextInterface $ctx, GetDepartmentMemberRequest $in): GetDepartmentMemberResponse {
		Log::info("Getting department Users List");

		$response = new GetDepartmentMemberResponse();

		$departmentId = $in->getDepartmentID();
		$id = $in->getActionByUserId();

		$allUsersOnThisDepartment = UserDepartment::with('userDetails', 'userDetails.userRoles')
			->find($departmentId);

		$returnArray = [];
		foreach($allUsersOnThisDepartment->userDetails as $user_detail) {
			$type1Values = $user_detail->userRoles->pluck('type_1')->toArray();
			$returnArray[] = new DepartmentMember([
				'id' => $user_detail->id,
				'first_name' => $user_detail->first_name,
				'middle_name' => $user_detail->middle_name,
				'last_name' => $user_detail->last_name,
				'profile_image_url' => $user_detail->profile_image_url,
				'profile_image_name' => $user_detail->profile_image_name,
				'position' => implode(',', $type1Values),
			
				'created_at' => (string)$user_detail->pivot->created_at,
				'updated_at' => (string)$user_detail->pivot->updated_at,
			]);
		}
		$response->setDepartmentLists($returnArray ?? []);
		$response->setId($allUsersOnThisDepartment->id);
		$response->setDepartmentName($allUsersOnThisDepartment->department_name);
		$response->setDescription($allUsersOnThisDepartment->description);
		$response->setCreatedAt($allUsersOnThisDepartment->created_at);
		$response->setUpdatedAt($allUsersOnThisDepartment->updated_at);

		return $response;
	}

}