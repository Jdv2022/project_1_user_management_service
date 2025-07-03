<?php

namespace App\Grpc\Services;

use App\Models\UserDetail;
use App\Models\UserDetailUserRole;
use App\Models\UserRole;
use Log;
class CommonFunctions {

	public function setUserDetailReturn(int $fk, $class) {
		$userDetailsObj = UserDetail::with(['userRoles', 'userDepartments'])
			->where('user_id', $fk)
			->first();

		$responseModel = $class;
		if($userDetailsObj) {
			$responseModel->setUserDetailsId($userDetailsObj->id);
			$responseModel->setUserDetailsFirstName($userDetailsObj->first_name);
			$responseModel->setUserDetailsMiddleName($userDetailsObj->middle_name);
			$responseModel->setUserDetailsLastName($userDetailsObj->last_name);
			$responseModel->setUserDetailsEmail($userDetailsObj->email);
			$responseModel->setUserDetailsPhone($userDetailsObj->phone);
			$responseModel->setUserDetailsAddress($userDetailsObj->address);
			$responseModel->setUserDetailsDateOfBirth($userDetailsObj->date_of_birth);
			$responseModel->setUserDetailsGender($userDetailsObj->gender);
			$responseModel->setUserDetailsProfileImageURL($userDetailsObj->profile_image_url);
			$responseModel->setUserDetailsProfileImageName($userDetailsObj->profile_image_name);
			$responseModel->setUserDetailsCreatedAt($userDetailsObj->created_at);
			$responseModel->setUserDetailsCreatedAtTimezone($userDetailsObj->created_at_timezone);
			$responseModel->setUserDetailsCreatedByUserId($userDetailsObj->created_by_user_id);
			$responseModel->setUserDetailsCreatedByUsername($userDetailsObj->created_by_username);
			$responseModel->setUserDetailsCreatedByUserType($userDetailsObj->created_by_user_type);
			$responseModel->setUserDetailsUpdatedAt($userDetailsObj->updated_at);
			$responseModel->setUserDetailsUpdatedAtTimezone($userDetailsObj->updated_at_timezone);
			$responseModel->setUserDetailsUpdatedByUserId($userDetailsObj->updated_by_user_id);
			$responseModel->setUserDetailsUpdatedByUsername($userDetailsObj->updated_by_username);
			$responseModel->setUserDetailsUpdatedByUserType($userDetailsObj->updated_by_user_type);
			$responseModel->setUserDetailsEnabled($userDetailsObj->enabled);
			$responseModel->setUserDetailsUserId($userDetailsObj->user_id);

			if($userDetailsObj->userRoles->toArray()) {
				$userRoles = $userDetailsObj->userRoles[0] ?? [];
				if ($userRoles) {
					$responseModel->setUserRolesId($userRoles->id);
					$responseModel->setUserRolesType1($userRoles->type_1);
					$responseModel->setUserRolesDescription($userRoles->description);
					$responseModel->setUserRolesLevel($userRoles->level);
					$responseModel->setUserRolesStatus($userRoles->status);
					$responseModel->setUserRolesCreatedAt($userRoles->created_at);
					$responseModel->setUserRolesCreatedAtTimezone($userRoles->created_at_timezone);
					$responseModel->setUserRolesCreatedByUserId($userRoles->created_by_user_id);
					$responseModel->setUserRolesCreatedByUsername($userRoles->created_by_username);
					$responseModel->setUserRolesCreatedByUserType($userRoles->created_by_user_type);
					$responseModel->setUserRolesUpdatedAt($userRoles->updated_at);
					$responseModel->setUserRolesUpdatedAtTimezone($userRoles->updated_at_timezone);
					$responseModel->setUserRolesUpdatedByUserId($userRoles->updated_by_user_id);
					$responseModel->setUserRolesUpdatedByUsername($userRoles->updated_by_username);
					$responseModel->setUserRolesUpdatedByUserType($userRoles->updated_by_user_type);
					$responseModel->setUserRolesEnabled($userRoles->enabled);
				}

				if($userDetailsObj->userRoles[0]->pivot->toArray()) {
					$userDetailUserRole = $userDetailsObj->userRoles[0]->pivot;
					$responseModel->setUserDetailUserRolesId($userDetailUserRole->id);
					$responseModel->setUserDetailUserRolesCreatedAt($userDetailUserRole->created_at?->toDateTimeString() ?? '');
					$responseModel->setUserDetailUserRolesCreatedAtTimezone($userDetailUserRole->created_at_timezone);
					$responseModel->setUserDetailUserRolesCreatedByUserId($userDetailUserRole->created_by_user_id);
					$responseModel->setUserDetailUserRolesCreatedByUsername($userDetailUserRole->created_by_username);
					$responseModel->setUserDetailUserRolesCreatedByUserType($userDetailUserRole->created_by_user_type);
					$responseModel->setUserDetailUserRolesUpdatedAt($userDetailUserRole->updated_at?->toDateTimeString() ?? '');
					$responseModel->setUserDetailUserRolesUpdatedAtTimezone($userDetailUserRole->updated_at_timezone);
					$responseModel->setUserDetailUserRolesUpdatedByUserId($userDetailUserRole->updated_by_user_id);
					$responseModel->setUserDetailUserRolesUpdatedByUsername($userDetailUserRole->updated_by_username);
					$responseModel->setUserDetailUserRolesUpdatedByUserType($userDetailUserRole->updated_by_user_type);
					$responseModel->setUserDetailUserRolesEnabled($userDetailUserRole->enabled);
					$responseModel->setUserDetailUserRolesUserDetailId($userDetailUserRole->user_detail_id);
					$responseModel->setUserDetailUserRolesUserRoleId($userDetailUserRole->user_role_id);
				}
			}

			if($userDetailsObj->userDepartments->toArray()) {
				$userDepartments = $userDetailsObj->userDepartments[0] ?? [];

				if($userDepartments) {
					$responseModel->setUserDepartmentsId($userDepartments->id);
					$responseModel->setUserDepartmentsDepartmentName($userDepartments->department_name);
					$responseModel->setUserDepartmentsDescription($userDepartments->description);
					$responseModel->setUserDepartmentsLevel($userDepartments->level);
					$responseModel->setUserDepartmentsStatus($userDepartments->status);
					$responseModel->setUserDepartmentsCreatedAt($userDepartments->created_at);
					$responseModel->setUserDepartmentsCreatedAtTimezone($userDepartments->created_at_timezone);
					$responseModel->setUserDepartmentsCreatedByUserId($userDepartments->created_by_user_id);
					$responseModel->setUserDepartmentsCreatedByUsername($userDepartments->created_by_username);
					$responseModel->setUserDepartmentsCreatedByUserType($userDepartments->created_by_user_type);
					$responseModel->setUserDepartmentsUpdatedAt($userDepartments->updated_at);
					$responseModel->setUserDepartmentsUpdatedAtTimezone($userDepartments->updated_at_timezone);
					$responseModel->setUserDepartmentsUpdatedByUserId($userDepartments->updated_by_user_id);
					$responseModel->setUserDepartmentsUpdatedByUsername($userDepartments->updated_by_username);
					$responseModel->setUserDepartmentsUpdatedByUserType($userDepartments->updated_by_user_type);
					$responseModel->setUserDepartmentsEnabled($userDepartments->enabled);
				}

				if($userDetailsObj->userDepartments[0]->pivot->toArray()) {
					$userDetailUserDepartment = $userDetailsObj->userDepartments[0]->pivot;
					$responseModel->setUserDetailUserDepartmentsCreatedAt($userDetailUserDepartment->created_at?->toDateTimeString() ?? '');
					$responseModel->setUserDetailUserDepartmentsCreatedAtTimezone($userDetailUserDepartment->created_at_timezone);
					$responseModel->setUserDetailUserDepartmentsCreatedByUserId($userDetailUserDepartment->created_by_user_id);
					$responseModel->setUserDetailUserDepartmentsCreatedByUsername($userDetailUserDepartment->created_by_username);
					$responseModel->setUserDetailUserDepartmentsCreatedByUserType($userDetailUserDepartment->created_by_user_type);
					$responseModel->setUserDetailUserDepartmentsUpdatedAt($userDetailUserDepartment->updated_at?->toDateTimeString() ?? '');
					$responseModel->setUserDetailUserDepartmentsUpdatedAtTimezone($userDetailUserDepartment->updated_at_timezone);
					$responseModel->setUserDetailUserDepartmentsUpdatedByUserId($userDetailUserDepartment->updated_by_user_id);
					$responseModel->setUserDetailUserDepartmentsUpdatedByUsername($userDetailUserDepartment->updated_by_username);
					$responseModel->setUserDetailUserDepartmentsUpdatedByUserType($userDetailUserDepartment->updated_by_user_type);
					$responseModel->setUserDetailUserDepartmentsEnabled($userDetailUserDepartment->enabled);
					$responseModel->setUserDetailUserDepartmentsUserDetailId($userDetailUserDepartment->user_detail_id);
					$responseModel->setUserDetailUserDepartmentsUserDepartmentId($userDetailUserDepartment->user_department_id);
				}
			}

		}

		return $responseModel;
	}
	
}
