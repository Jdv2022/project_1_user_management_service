<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\DeleteDepartment\DeleteDepartmentResponse;
use grpc\DeleteDepartment\DeleteDepartmentRequest;
use grpc\DeleteDepartment\DeleteDepartmentServiceInterface;
use App\Models\UserDepartment;
use App\Models\UserDetail;
use App\Models\UserDetailUserDepartment;
use Log;

class DeleteDepartmentHandler extends ActionByMiddleware implements DeleteDepartmentServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function DeleteDepartment(ContextInterface $ctx, DeleteDepartmentRequest $in): DeleteDepartmentResponse {
		Log::info("DeleteDepartmentHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$departmentId = $in->getDepartmentId();
		$userId = $in->getUserId();
		$tz = $in->getTimezone();
		$actionByUserId = $in->getActionByUserId();

		$this->initializeActionByUser((int)$actionByUserId, $tz);

		$deletePivotRecord = UserDetailUserDepartment::where('user_department_id', $departmentId)
			->delete();

		$deleteDepartmentRecord = UserDepartment::where('id', $departmentId)->delete();
		$response = new DeleteDepartmentResponse();
		return $response->setResult(true);
	}

}