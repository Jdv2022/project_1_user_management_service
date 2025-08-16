<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\EditDepartment\EditDepartmentResponse;
use grpc\EditDepartment\EditDepartmentRequest;
use grpc\EditDepartment\EditDepartmentServiceInterface;
use App\Models\UserDepartment;
use Log;

class EditDepartmentHandler extends ActionByMiddleware implements EditDepartmentServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function EditDepartment(ContextInterface $ctx, EditDepartmentRequest $in): EditDepartmentResponse {
		Log::info("EditDepartmentHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$departmentName = $in->getDepartmentName();
		$description = $in->getDescription();
		$tz = $in->getTimezone();
		$department_id = $in->getDepartmentId();

		$this->initializeActionByUser((int)$actionByUserId, $tz);

		$model = UserDepartment::find($department_id);
		if($model->department_name == $departmentName) {
			Log::debug("Department Already Exists!");
			return new EditDepartmentResponse(['result' => "Department Already Exists!"]);
		}
		$model->department_name = $departmentName;
		$model->description = $description;
		$model->save();

		$response = new EditDepartmentResponse();
		if($model->save()) {
			Log::info("Department Edited! ID: " . $model->id);
			return $response->setResult($model->id);
		}
		else {
			Log::info("Department Not Edited!");
			return $response->setResult("Department Not Edited!");
		}
	}

}