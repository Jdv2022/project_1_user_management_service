<?php

namespace App\Grpc\Handlers;

use grpc\getUsers\GetUsersRequest;
use grpc\getUsers\GetUsersResponse;
use grpc\getUsers\GetUsersServiceInterface;
use grpc\getUsers\User;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class UsersHandler extends ActionByMiddleware implements GetUsersServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetUsers(ContextInterface $ctx, GetUsersRequest $in): GetUsersResponse {
		Log::info("[UsersHandler Handler] User details id " . $id);
		   
		$mainObject = UserDetail::with(['userRoles', 'userDepartments'])->get();
		$responseObj = new GetUsersResponse();
		$selectedColumns = [
			'userDetailsId',
			'userDetailsAddress', 
			'userDetailsDateOfBirth', 
			'userDetailsEmail',
			'userDetailsFirstName', 
			'userDetailsGender', 
			'userDetailsLastName', 
			'userDetailsMiddleName',
			'userDetailsPhone', 
			'userDetailsProfileImageURL', 
			'userRolesType1', 
			'userDepartmentsDepartmentName'
		];
		$userModel = new User();
		foreach($mainObject as $record) {
			$plainRecord = $this->commonFunctions->setUserDetailReturn($record->id, $userModel);
			$plainRecordArr = json_decode($plainRecord->serializeToJsonString(), true);
			$selectedItems = collect($plainRecordArr)->filter(function ($value, $key) use($selectedColumns) {
				return in_array($key, $selectedColumns);
			})
			->mapWithKeys(function ($value, $key) {
				if($key == 'userDetailsProfileImageURL') {
					return ['user_details_profile_image_URL' => $value];
				}
				$newKey = strtolower(preg_replace('/(?<=\D)(?=\d)|(?<!^)(?=[A-Z])/', '_', $key));
				return [$newKey => $value];
			});
			$returnData = $selectedItems->toArray();
			$returnData['user_details_date_of_birth'] = date('F j, Y', strtotime($returnData['user_details_date_of_birth']));
			$responseObj->getUsers()[] = new User($returnData);
		}
		return $responseObj;
	}

}
