<?php

namespace App\Grpc\Handlers;

use grpc\GetDepartment\GetDepartmentRequest;
use grpc\GetDepartment\GetDepartmentResponse;
use grpc\GetDepartment\GetDepartmentServiceInterface;
use grpc\GetDepartment\Departments;
use grpc\GetDepartment\User;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDepartment;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class GetDepartmentHandler extends ActionByMiddleware implements GetDepartmentServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetDepartment(ContextInterface $ctx, GetDepartmentRequest $in): GetDepartmentResponse {
		Log::info("Get Archives running...");
		   
		$sada = UserDepartment::get();
		$res = new GetDepartmentResponse();

		$arr = [];
		foreach($sada as $item) {
			$arc = new Departments([
				'id' => $item->id,
				'department_name' => $item->department_name,
				'description' => $item->description,
				'created_at' => $item->created_at,
				'updated_at' => $item->updated_at,
			]);
			$arr[] = $arc;
		}
		$res->setDepartments($arr);

		return $res;
	}

}
