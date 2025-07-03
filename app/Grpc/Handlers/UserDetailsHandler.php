<?php

namespace App\Grpc\Handlers;

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

class UserDetailsHandler extends ActionByMiddleware implements GetUserDetailsServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetUserDetails(ContextInterface $ctx, GetUserDetailsRequest $in): GetUserDetailsResponse {
		$id = $in->getFk();
		Log::info("[UserDetailsHandler Handler] User details id " . $id);

		$plainRecord = $this->commonFunctions->setUserDetailReturn($id, new GetUserDetailsResponse());
		$plainRecordArr = json_decode($plainRecord->serializeToJsonString(), true);
		$returnList = [
			'userDetailsId',
			'userDetailsPhone',
			'userDetailsEmail',
			'userRolesType1',
			'userDetailsDateOfBirth',
			'daysTillBirthday',
			'userRolesDescription',
			'userDetailsAddress',
			'userDetailsGender',
			'created_at',
			'updated_at',
			'created_by',
			'updated_by',
			'userDetailsFirstName',
			'userDetailsMiddleName',
			'userDetailsLastName',
			'userDetailsProfileImageURL',
			'userDepartmentsDepartmentName'
		];

		$selectedItems = collect($plainRecordArr)->filter(function ($value, $key) use($returnList) {
			return in_array($key, $returnList);
		})
		->mapWithKeys(function ($value, $key) {
			if($key == 'userDetailsProfileImageURL') {
				return ['user_details_profile_image_URL' => $value];
			}
			$newKey = strtolower(preg_replace('/(?<=\D)(?=\d)|(?<!^)(?=[A-Z])/', '_', $key));
			return [$newKey => $value];
		});
			
		return new GetUserDetailsResponse($selectedItems->toArray());   
	}

}
