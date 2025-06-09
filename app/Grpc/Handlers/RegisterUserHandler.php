<?php

namespace App\Grpc\Handlers;

use grpc\Register\RegisterServiceInterface;
use grpc\Register\RegisterUserDetailsRequest;
use grpc\Register\RegisterUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDetail;
use App\Models\UserDetailUserRole;
use App\Models\UserRole;
use App\Models\UserDepartment;
use App\Models\UserDetailUserDepartment;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class RegisterUserHandler extends ActionByMiddleware implements RegisterServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function RegisterUserDetails(ContextInterface $ctx, RegisterUserDetailsRequest $in): RegisterUserDetailsResponse {
		Log::info("[Register Handler] User details id " . $in->getFk());

		$id = $in->getActionByUserId();

		$this->initializeActionByUser((int)$id);

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
			'profile_image_url' => $in->getSetProfileImageURL(),
			'profile_image_name' => $in->getSetProfileImageName(),
		];

		$actionByUser = Redis::get($id);	
		$userDetail = new UserDetail();
		$createdUser = $userDetail->create($data);
		$userDetail = UserRole::where('type_1', $in->getPosition())->first();
		if($userDetail && $createdUser) {
			$userDetailUserRole = new UserDetailUserRole();
			$savedUserDetailUserRole = $userDetailUserRole->create(
				[
					'user_detail_id' => $createdUser->id, 
					'user_role_id' => $userDetail->id
				]
			);

			$userDepartment = UserDepartment::where('department_name', $in->getDepartment())->first();
			if($userDepartment && $savedUserDetailUserRole) {
				$userDetailUserDepartment = new UserDetailUserDepartment();
				$userDetailUserDepartment->create(
					[
						'user_detail_id' => $createdUser->id, 
						'user_department_id' => $userDepartment->id
					]
				);
			}
			else {
				$userDetail = UserDetail::find($createdUser->id);
				$userDetail->delete();
				$userDetailUserRole = UserDetailUserRole::find($savedUserDetailUserRole->id);
				$userDetailUserRole->delete();
			}
		}
		else {
			$userDetail = UserDetail::find($createdUser->id);
			$userDetail->delete();
		}

		$response = new RegisterUserDetailsResponse();
		if($createdUser) {
			return $response->setResult(true);
		}
		else {
			return $response->setResult(false);
		}
	}

}