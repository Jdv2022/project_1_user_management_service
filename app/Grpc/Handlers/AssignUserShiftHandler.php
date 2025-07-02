<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\AssignUserShift\AssignUserShiftRequest;
use grpc\AssignUserShift\AssignUserShiftResponse;
use grpc\AssignUserShift\AssignUserShiftServiceInterface;
use App\Models\UserDetailUserShift;
use Illuminate\Support\Facades\DB;
use Log;

class AssignUserShiftHandler extends ActionByMiddleware implements AssignUserShiftServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function AssignUserShift(ContextInterface $ctx, AssignUserShiftRequest $in): AssignUserShiftResponse {
		Log::info("Assigning User Shift");
		$howManyPerchunks = 50;
		$response = new AssignUserShiftResponse();

		$userIdLists = $in->getUserIds();
		$shiftId = $in->getShiftId();
		$actionByUserId = $in->getActionByUserId();
		$timezone = $in->getTimezone();
		
		$this->initializeActionByUser((int)$actionByUserId, $timezone);

		$arrToBeSaved = [];
		foreach($userIdLists as $userIdList) {
			$arrToBeSaved[] = [
				'user_detail_id' => $userIdList,
				'user_shift_id' => $shiftId
			];
		}
		$chunkUserFks = array_chunk($arrToBeSaved, $howManyPerchunks);

		try {
			DB::transaction(function () use($chunkUserFks) {
				foreach($chunkUserFks as $chunkUserFk) {
					UserDetailUserShift::customInsert($chunkUserFk);
				}
			});
			Log::info("Users assigned to shift!");
			return $response->setResult(true);
		} 
		catch (\Throwable $e) {
			Log::error("Users not assigned to shift! " . $e->getMessage());
			return $response->setResult(false);
		}		
	}

}