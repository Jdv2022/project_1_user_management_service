<?php

namespace App\Grpc\Handlers;

use grpc\GetDepartmentDetail\GetDepartmentDetailRequest;
use grpc\GetDepartmentDetail\GetDepartmentDetailResponse;
use grpc\GetDepartmentDetail\GetDepartmentDetailServiceInterface;
use grpc\GetDepartmentDetail\Departments;
use grpc\GetDepartmentDetail\User;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDepartment;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class GetDepartmentDetailHandler extends ActionByMiddleware implements GetDepartmentDetailServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetDepartmentDetail(ContextInterface $ctx, GetDepartmentDetailRequest $in): GetDepartmentDetailResponse {
		Log::info("Get department running...");

		$id = $in->getDepartmentId();
		   
		$sada = UserDepartment::find($id);

		$res = new GetDepartmentDetailResponse();
		$res->setId($sada->id);
		$res->setDepartmentName($sada->department_name);
		$res->setDescription($sada->description);
		$res->setCreatedAt($sada->created_at);
		$res->setUpdatedAt($sada->updated_at);

		return $res;
	}

}
