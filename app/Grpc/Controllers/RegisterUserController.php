<?php

namespace App\Grpc\Controllers;

use grpc\Register\RegisterServiceInterface;
use grpc\Register\RegisterUserDetailsRequest;
use grpc\Register\RegisterUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDetail;
use App\Models\UserDetailUserRole;
use App\Models\UserRole;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class RegisterUserController extends ActionByMiddleware implements RegisterServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function RegisterUserDetails(ContextInterface $ctx, RegisterUserDetailsRequest $in): RegisterUserDetailsResponse {
		Log::info("[Register Controller] User details id " . $in->getFk());

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
			'date_of_birth' => $in->getDateOfBirth(),
			'gender' => $in->getGender(),
			'enabled' => true,
			'user_id' => $in->getFk(),
			'profile_image_url' => $in->getSetProfileImageURL(),
			'profile_image_name' => $in->getSetProfileImageName(),
		];

		$actionByUser = Redis::get($id);	
		$userDetail = new UserDetail();
		$userDetail->create($data);

		return $this->commonFunctions->setUserDetailReturn($in->getFk(), new RegisterUserDetailsResponse());
	}

}