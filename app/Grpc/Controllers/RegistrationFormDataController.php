<?php

namespace App\Grpc\Controllers;

use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserRole;
use App\Models\UserDepartment;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;
use grpc\userRegistrationFormData\UserRegistrationFormDataRequest;
use grpc\userRegistrationFormData\UserRegistrationFormDataResponse;
use grpc\userRegistrationFormData\UserRegistrationFormDataServiceInterface;

class RegistrationFormDataController extends ActionByMiddleware implements UserRegistrationFormDataServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function UserRegistrationFormData(ContextInterface $ctx, UserRegistrationFormDataRequest $in): UserRegistrationFormDataResponse {
		$getUserDetailsResponseInstance = new UserRegistrationFormDataResponse();
		$userRoles = UserRole::select(['id', 'type_1'])->get()->toArray();
		$userDepartments = UserDepartment::select(['id', 'department_name'])->get()->toArray();
		$getUserDetailsResponseInstance->setRoles(json_encode($userRoles));
		$getUserDetailsResponseInstance->setDepartments(json_encode($userDepartments));
		return $getUserDetailsResponseInstance; 
	}
	
}

