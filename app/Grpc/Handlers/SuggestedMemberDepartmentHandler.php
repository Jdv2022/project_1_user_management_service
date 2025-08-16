<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\SuggestedMemberDepartment\SuggestedMemberDepartmentRequest;
use grpc\SuggestedMemberDepartment\SuggestedMemberDepartmentResponse;
use grpc\SuggestedMemberDepartment\departmentMember;
use grpc\SuggestedMemberDepartment\SuggestedMemberDepartmentServiceInterface;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use Log;

class SuggestedMemberDepartmentHandler extends ActionByMiddleware implements SuggestedMemberDepartmentServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function SuggestedMemberDepartment(ContextInterface $ctx, SuggestedMemberDepartmentRequest $in): SuggestedMemberDepartmentResponse {
		Log::info("SuggestedMember running...");

		$response = new SuggestedMemberDepartmentResponse();

		$suggestedMember = UserDetail::with('userTeams', 'userDepartments')->get();

		$departmentArray = [];
		foreach($suggestedMember as $departmentList) {
			$is_on_department = 'true';
			if($departmentList->userDepartments->isEmpty()) {
				$is_on_department = 'false';
			}
			$name = $departmentList->first_name . " " . $departmentList->last_name;

			$departments = new departmentMember([
				'id' => $departmentList->id,
				'name' => $name,
				'team' => $departmentList->userTeams['team_name'] ?? '-',
				'is_on_department' => $is_on_department,
				'profile_image_url' => $departmentList->profile_image_url,
				'profile_image_name' => $departmentList->profile_image_name,
			]);
			$departmentArray[] = $departments;
		}

		$response->setDepartmentLists($departmentArray);

		return $response;
	}	

}