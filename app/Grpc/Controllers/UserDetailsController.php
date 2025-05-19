<?php

namespace App\Grpc\Controllers;

use grpc\GetUserDetails\GetUserDetailsServiceInterface;
use grpc\GetUserDetails\GetUserDetailsRequest;
use grpc\GetUserDetails\GetUserDetailsResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDetail;
use App\Models\UserDetailUserRole;
use App\Models\UserRole;
use Log;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Middlewares\ActionByMiddleware;

class UserDetailsController extends ActionByMiddleware implements GetUserDetailsServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetUserDetails(ContextInterface $ctx, GetUserDetailsRequest $in): GetUserDetailsResponse {
		$id = $in->getActionByUserId();
		Log::info("[UserDetailsController Controller] User details id " . $id);
		$this->initializeActionByUser($id);

		$userDetailsObj = UserDetail::with(['userRoles'])
			->where('user_id', $id)
			->first();

		return $this->commonFunctions->setUserDetailReturn($id, new GetUserDetailsResponse());    
	}

}
