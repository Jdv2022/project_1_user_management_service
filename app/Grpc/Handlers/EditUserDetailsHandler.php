<?php

namespace App\Grpc\Handlers;

use grpc\EditUserDetails\EditUserDetailsServiceInterface;
use grpc\EditUserDetails\EditUserDetailsRequest;
use grpc\EditUserDetails\EditUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserRole;
use App\Models\UserDetail;
use App\Models\UserDepartment;
use App\Models\UserDetailUserRole;
use App\Models\UserDetailUserDepartment;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class EditUserDetailsHandler extends ActionByMiddleware implements EditUserDetailsServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function EditUserDetails(ContextInterface $ctx, EditUserDetailsRequest $in): EditUserDetailsResponse {
		Log::info("[Register Handler] User details id " . $in->getFk());

		$id = $in->getActionByUserId();

		$res = new EditUserDetailsResponse();

		$this->initializeActionByUser((int)$id, $in->getTimezone());

		if(UserDetail::where('email', $in->getEmail())->exists()) {
			throw new \Exception('Email details already exists');
		}
		
		$data = [
			'first_name' => $in->getFirstName(),
			'middle_name' => $in->getMiddleName(),
			'last_name' => $in->getLastName(),
			'email' => $in->getEmail(),
			'phone' => $in->getPhone(),
			'address' => $in->getAddress(),
			'date_of_birth' => date('Y-m-d H:i:s', strtotime($in->getDateOfBirth())),
			'gender' => $in->getGender(),
			'enabled' => true,
			'user_id' => $in->getFk(),
			'profile_image_url' => $in->getProfileImageURL(),
			'profile_image_name' => $in->getProfileImageName(),
		];

		$userDetail = UserDetail::where('user_id', $data['user_id'])->first();
		$userRole = UserRole::where('type_1', $in->getPosition())->first();

		if($userDetail) {
			$userDetailUserRole = new UserDetailUserRole();
			$userDetailUserRole->user_detail_id = $data['user_id'];
			$userDetailUserRole->user_role_id = $userRole->id;
			$userDetailUserRole->save();

			$userDepartment = UserDepartment::where('department_name', $in->getDepartment())->first();
			if($userDepartment) {
				$userDetailUserDepartment = new UserDetailUserDepartment();
				$userDetailUserDepartment->user_detail_id = $userDetail->id;
				$userDetailUserDepartment->user_department_id = $userDepartment->id;
				$userDetailUserDepartment->save();
			}
		}
		
		if($userDetail) {
			$userDetail->first_name = $data['first_name'];
			$userDetail->middle_name = $data['middle_name'];
			$userDetail->last_name = $data['last_name'];
			$userDetail->email = $data['email'];
			$userDetail->phone = $data['phone'];
			$userDetail->address = $data['address'];
			$userDetail->date_of_birth = $data['date_of_birth'];
			$userDetail->gender = $data['gender'];
			$userDetail->enabled = $data['enabled'];
			$userDetail->profile_image_url = $data['profile_image_url'];
			$userDetail->profile_image_name = $data['profile_image_name'];
			$userDetail->save();
			Log::info("[Register Handler] User details id " . $in->getFk() . " updated");
			return $res->setResult(true);
		}
		else {
			Log::error("[Register Handler] User details id " . $in->getFk() . " not found");
			return $res->setResult(false);
		}

	}

}