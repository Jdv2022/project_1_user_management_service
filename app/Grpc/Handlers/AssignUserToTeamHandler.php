<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\AssignUserToTeam\AssignUserToTeamResponse;
use grpc\AssignUserToTeam\AssignUserToTeamRequest;
use App\Models\UserDetailUserTeam;
use Illuminate\Support\Facades\DB;
use Log;

class AssignUserToTeamHandler extends ActionByMiddleware {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function assignUserToTeam(ContextInterface $ctx, AssignUserToTeamRequest $in): AssignUserToTeamResponse {
		Log::info("assignUserToTeam running...");
		$howManyPerchunks = 10;

		$actionByUserId = $in->getActionByUserId();
		$userFks = $in->getFk();
		$teamId = $in->getTeamId();
		$this->initializeActionByUser((int)$actionByUserId);

		$arrToBeSaved = [];
		foreach($userFks as $userFk) {
			$arrToBeSaved[] = [
				'user_detail_id' => $userFk->getFk(),
				'user_team_id' => $teamId
			];
		}
		$chunkUserFks = array_chunk($arrToBeSaved, $howManyPerchunks);

		try {
			DB::transaction(function () use($chunkUserFks) {
				foreach($chunkUserFks as $chunkUserFk) {
					UserDetailUserTeam::customInsert($chunkUserFk);
				}
			});
			Log::info("Users assigned to team!");
			$response = new AssignUserToTeamResponse();
			return $response->setResult(true);
		} 
		catch (\Throwable $e) {
			return $response->setResult(false);
		}
	}

}