<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\CreateShift\CreateShiftResponse;
use grpc\CreateShift\CreateShiftRequest;
use App\Models\UserShift;
use Log;

class CreateShiftHandler extends ActionByMiddleware {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function CreateShift(ContextInterface $ctx, CreateShiftRequest $in): CreateShiftResponse {
		Log::info("CreateTeamHandler running...");

		$stats = UserShift::create([
			'shift_name' => $in->getShiftName(),
			'description' => $in->getDescription()
		]);

		if($stats) {
			return new CreateShiftResponse(['result' => true]);
		}
		else {
			return new CreateShiftResponse(['result' => false]);
		}
	}

}