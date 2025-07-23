<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\CreateDepartment\CreateDepartmentResponse;
use grpc\CreateDepartment\CreateDepartmentRequest;
use grpc\CreateDepartment\CreateDepartmentServiceInterface;
use App\Models\UserDepartment;
use Log;

class CreateDepartmentHandler extends ActionByMiddleware implements CreateDepartmentServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function CreateDepartment(ContextInterface $ctx, CreateDepartmentRequest $in): CreateDepartmentResponse {
		Log::info("CreateDepartmentHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$departmentName = $in->getDepartmentName();
		$description = $in->getDescription();
		$tz = $in->getTimezone();

		$this->initializeActionByUser((int)$actionByUserId, $tz);

		if(UserDepartment::where('department_name', $departmentName)->exists()) {
			Log::debug("Department Already Exists!");
			return new CreateDepartmentResponse(['result' => "Department Already Exists!"]);
		}

		$result = UserDepartment::create([
			"department_name" => $departmentName,
			"description" => $description
		]);
		Log::debug("Department Created: " . json_encode($result, JSON_PRETTY_PRINT));
		$response = new CreateDepartmentResponse();
		if($result) {
			Log::info("Department Created! ID: " . $result->id);
			return $response->setResult($result->id);
		}
		else {
			Log::info("Department Not Created!");
			return $response->setResult("Department Not Created!");
		}
	}

}